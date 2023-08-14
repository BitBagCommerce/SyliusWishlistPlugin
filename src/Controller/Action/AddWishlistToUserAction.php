<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Checker\WishlistAccessCheckerInterface;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddWishlistToUser;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AddWishlistToUserAction
{
    private MessageBusInterface $commandBus;

    private RequestStack $requestStack;

    private TranslatorInterface $translator;

    private WishlistRepositoryInterface $wishlistRepository;

    private UrlGeneratorInterface $urlGenerator;

    private TokenStorageInterface $tokenStorage;

    private WishlistAccessCheckerInterface $wishlistAccessChecker;

    public function __construct(
        MessageBusInterface $commandBus,
        RequestStack $requestStack,
        TranslatorInterface $translator,
        WishlistRepositoryInterface $wishlistRepository,
        UrlGeneratorInterface $urlGenerator,
        TokenStorageInterface $tokenStorage,
        WishlistAccessCheckerInterface $wishlistAccessChecker,
        ) {
        $this->commandBus = $commandBus;
        $this->requestStack = $requestStack;
        $this->translator = $translator;
        $this->wishlistRepository = $wishlistRepository;
        $this->urlGenerator = $urlGenerator;
        $this->tokenStorage = $tokenStorage;
        $this->wishlistAccessChecker = $wishlistAccessChecker;
    }

    public function __invoke(Request $request): Response
    {
        /** @var ShopUserInterface $shopUser */
        $shopUser = $this->tokenStorage->getToken() ? $this->tokenStorage->getToken()->getUser() : null;
        $wishlistId = $request->attributes->getInt('id');

        $wishlist = $this->wishlistAccessChecker->resolveWishlist($wishlistId);

        if (false === $wishlist instanceof WishlistInterface) {
            /** @var Session $session */
            $session = $this->requestStack->getSession();
            $session->getFlashBag()->add('info', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.you_have_no_access_to_that_wishlist'));

            return new RedirectResponse($this->urlGenerator->generate('bitbag_sylius_wishlist_plugin_shop_wishlist_list_wishlists'));
        }

        $wishlist = $this->wishlistRepository->find($wishlistId);

        /** @var Session $session */
        $session = $this->requestStack->getSession();

        try {
            $updateWishlistName = new AddWishlistToUser($wishlist, $shopUser);
            $this->commandBus->dispatch($updateWishlistName);

            $session->getFlashBag()->add(
                'success',
                $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.wishlist_saved')
            );
        } catch (HandlerFailedException $exception) {
            $session->getFlashBag()->add(
                'error',
                $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.you_have_no_access_to_that_wishlist')
            );
        }

        return new RedirectResponse($this->urlGenerator->generate('bitbag_sylius_wishlist_plugin_shop_wishlist_list_wishlists'));
    }
}
