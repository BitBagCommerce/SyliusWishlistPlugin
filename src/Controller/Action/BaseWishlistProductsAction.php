<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Form\Type\WishlistCollectionType;
use BitBag\SyliusWishlistPlugin\Processor\WishlistCommandProcessorInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class BaseWishlistProductsAction
{
    public function __construct(
        protected CartContextInterface $cartContext,
        protected FormFactoryInterface $formFactory,
        protected RequestStack $requestStack,
        protected WishlistCommandProcessorInterface $wishlistCommandProcessor,
        protected MessageBusInterface $messageBus,
        protected UrlGeneratorInterface $urlGenerator,
        protected WishlistRepositoryInterface $wishlistRepository,
        protected TranslatorInterface $translator,
    ) {
    }

    public function __invoke(int $wishlistId, Request $request): Response
    {

        if (null === $this->createForm($wishlistId)) {
            return new RedirectResponse(
                $this->urlGenerator->generate('bitbag_sylius_wishlist_plugin_shop_locale_wishlist_list_wishlists'),
            );
        }

        $form = $this->createForm($wishlistId);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleCommand($form);
            return new RedirectResponse(
                $this->urlGenerator->generate('bitbag_sylius_wishlist_plugin_shop_locale_wishlist_show_chosen_wishlist', [
                        'wishlistId' => $wishlistId,
                    ]),
            );
        }

        /** @var Session $session */
        $session = $this->requestStack->getSession();

        /** @var FormError $error */
        foreach ($form->getErrors() as $error) {
            $session->getFlashBag()->add('error', $error->getMessage());
        }

        return new RedirectResponse(
            $this->urlGenerator->generate('bitbag_sylius_wishlist_plugin_shop_locale_wishlist_show_chosen_wishlist', [
                'wishlistId' => $wishlistId,
            ]),
        );
    }

    abstract protected function handleCommand(FormInterface $form): void;

    private function createForm(int $wishlistId): ?FormInterface
    {
        /** @var ?WishlistInterface $wishlist */
        $wishlist = $this->wishlistRepository->find($wishlistId);
        $cart = $this->cartContext->getCart();

        if (null === $wishlist) {
            /** @var Session $session */
            $session = $this->requestStack->getSession();

            $session->getFlashBag()->add('error', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.wishlist_not_exists'));

            return null;
        }
        $commandsArray = $this->wishlistCommandProcessor->createWishlistItemsCollection($wishlist->getWishlistProducts());

        return $this->formFactory->create(WishlistCollectionType::class, ['items' => $commandsArray], [
                'cart' => $cart,
        ]);
    }
}
