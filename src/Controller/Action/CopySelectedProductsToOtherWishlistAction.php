<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\CopySelectedProductsToOtherWishlist;
use BitBag\SyliusWishlistPlugin\Exception\WishlistProductsActionFailedException;
use BitBag\SyliusWishlistPlugin\Form\Type\WishlistCollectionType;
use BitBag\SyliusWishlistPlugin\Processor\WishlistCommandProcessorInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CopySelectedProductsToOtherWishlistAction
{
    private CartContextInterface $cartContext;

    private FormFactoryInterface $formFactory;

    private FlashBagInterface $flashBag;

    private MessageBusInterface $messageBus;

    private WishlistCommandProcessorInterface $wishlistCommandProcessor;

    private UrlGeneratorInterface $urlGenerator;

    private WishlistRepositoryInterface $wishlistRepository;

    private TranslatorInterface $translator;

    public function __construct(
        CartContextInterface $cartContext,
        FormFactoryInterface $formFactory,
        FlashBagInterface $flashBag,
        MessageBusInterface $messageBus,
        WishlistCommandProcessorInterface $wishlistCommandProcessor,
        UrlGeneratorInterface $urlGenerator,
        WishlistRepositoryInterface $wishlistRepository,
        TranslatorInterface $translator
    ) {
        $this->cartContext = $cartContext;
        $this->formFactory = $formFactory;
        $this->flashBag = $flashBag;
        $this->messageBus = $messageBus;
        $this->wishlistCommandProcessor = $wishlistCommandProcessor;
        $this->urlGenerator = $urlGenerator;
        $this->wishlistRepository = $wishlistRepository;
        $this->translator = $translator;
    }

    public function __invoke(
        int $wishlistId,
        int $destinedWishlistId,
        Request $request
    ): Response {
        $form = $this->createForm($wishlistId);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->copySelectedProductsToOtherWishlist($form, $destinedWishlistId);

            return new RedirectResponse(
                $this->urlGenerator->generate('bitbag_sylius_wishlist_plugin_shop_wishlist_show_chosen_wishlist', [
                    'wishlistId' => $destinedWishlistId,
                ])
            );
        }

        foreach ($form->getErrors() as $error) {
            $this->flashBag->add('error', $error->getMessage());
        }

        return new RedirectResponse(
            $this->urlGenerator->generate('bitbag_sylius_wishlist_plugin_shop_wishlist_show_chosen_wishlist', [
                'wishlistId' => $wishlistId,
            ])
        );
    }

    private function createForm(int $wishlistId): FormInterface
    {
        $wishlist = $this->wishlistRepository->find($wishlistId);
        $cart = $this->cartContext->getCart();

        $commandsArray = $this->wishlistCommandProcessor->createWishlistItemsCollection($wishlist->getWishlistProducts());

        return $this->formFactory->create(WishlistCollectionType::class, ['items' => $commandsArray], [
            'cart' => $cart,
        ]);
    }

    private function copySelectedProductsToOtherWishlist(FormInterface $form, int $destinedWishlistId): void
    {
        $failedProductsName = [];
        $command = new CopySelectedProductsToOtherWishlist($form->getData(), $destinedWishlistId);

        try {
            $this->messageBus->dispatch($command);
        } catch (HandlerFailedException $exception) {
            /** @var WishlistProductsActionFailedException $wishlistProductsActionFailedException */
            $wishlistProductsActionFailedException = $exception->getPrevious();
            $failedProductsName = $wishlistProductsActionFailedException->getFailedProductsName();

            foreach ($failedProductsName as $failedProductName) {
                $message = sprintf('%s %s', $failedProductName, $wishlistProductsActionFailedException->getMessage());
                $this->flashBag->add('error', $this->translator->trans($message));
            }
        }

        if (count($failedProductsName) != count($form->getData())) {
            $this->flashBag->add('success', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.copied_selected_wishlist_items'));
        }
    }
}
