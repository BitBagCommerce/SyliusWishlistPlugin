<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Form\Type\WishlistCollectionType;
use BitBag\SyliusWishlistPlugin\Processor\WishlistCommandProcessorInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
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
    public CartContextInterface $cartContext;

    public FormFactoryInterface $formFactory;

    public RequestStack $requestStack;

    public WishlistCommandProcessorInterface $wishlistCommandProcessor;

    public MessageBusInterface $messageBus;

    public UrlGeneratorInterface $urlGenerator;

    private WishlistRepositoryInterface $wishlistRepository;

    private TranslatorInterface $translator;

    public function __construct(
        CartContextInterface $cartContext,
        FormFactoryInterface $formFactory,
        RequestStack $requestStack,
        WishlistCommandProcessorInterface $wishlistCommandProcessor,
        MessageBusInterface $messageBus,
        UrlGeneratorInterface $urlGenerator,
        WishlistRepositoryInterface $wishlistRepository,
        TranslatorInterface $translator
    ) {
        $this->cartContext = $cartContext;
        $this->formFactory = $formFactory;
        $this->requestStack = $requestStack;
        $this->wishlistCommandProcessor = $wishlistCommandProcessor;
        $this->messageBus = $messageBus;
        $this->urlGenerator = $urlGenerator;
        $this->wishlistRepository = $wishlistRepository;
        $this->translator = $translator;
    }

    public function __invoke(int $wishlistId, Request $request): Response
    {
        if ($this->createForm($wishlistId) == null) {
            return new RedirectResponse(
                $this->urlGenerator->generate('bitbag_sylius_wishlist_plugin_shop_wishlist_list_wishlists'));
        } else {
            $form = $this->createForm($wishlistId);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->handleCommand($form);

                return new RedirectResponse(
                    $this->urlGenerator->generate('bitbag_sylius_wishlist_plugin_shop_wishlist_show_chosen_wishlist', [
                        'wishlistId' => $wishlistId,
                    ])
                );
            }
        }

        /** @var Session $session */
        $session = $this->requestStack->getSession();

        foreach ($form->getErrors() as $error) {
            $session->getFlashBag()->add('error', $error->getMessage());
        }

        return new RedirectResponse(
            $this->urlGenerator->generate('bitbag_sylius_wishlist_plugin_shop_wishlist_show_chosen_wishlist', [
                'wishlistId' => $wishlistId,
            ])
        );
    }

    abstract protected function handleCommand(FormInterface $form): void;

    private function createForm(int $wishlistId): ?FormInterface
    {
        $wishlist = $this->wishlistRepository->find($wishlistId);
        $cart = $this->cartContext->getCart();
        if ($wishlist == null) {
            /** @var Session $session */
            $session = $this->requestStack->getSession();

            $session->getFlashBag()->add('error', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.wishlist_not_exists'));
            return null;
        } else {
            $commandsArray = $this->wishlistCommandProcessor->createWishlistItemsCollection($wishlist->getWishlistProducts());

            return $this->formFactory->create(WishlistCollectionType::class, ['items' => $commandsArray], [
                'cart' => $cart,
            ]);
        }
    }
}
