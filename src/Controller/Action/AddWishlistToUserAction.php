<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Checker\WishlistNameCheckerInterface;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddWishlistsToUser;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddWishlistToUser;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\UpdateWishlistName;
use BitBag\SyliusWishlistPlugin\Exception\WishlistHasAnotherShopUserException;
use BitBag\SyliusWishlistPlugin\Factory\WishlistFactoryInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use BitBag\SyliusWishlistPlugin\Resolver\WishlistCookieTokenResolverInterface;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Webmozart\Assert\Assert;

final class AddWishlistToUserAction
{
    private MessageBusInterface $commandBus;

    private FlashBagInterface $flashBag;

    private TranslatorInterface $translator;

    private WishlistRepositoryInterface $wishlistRepository;

    private UrlGeneratorInterface $urlGenerator;

    private TokenStorageInterface $tokenStorage;

    public function __construct(
        MessageBusInterface $commandBus,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        WishlistRepositoryInterface $wishlistRepository,
        UrlGeneratorInterface $urlGenerator,
        TokenStorageInterface $tokenStorage
    ) {
        $this->commandBus = $commandBus;
        $this->flashBag = $flashBag;
        $this->translator = $translator;
        $this->wishlistRepository = $wishlistRepository;
        $this->urlGenerator = $urlGenerator;
        $this->tokenStorage = $tokenStorage;
    }

    public function __invoke(Request $request): Response
    {
        $wishlistId = $request->attributes->getInt('id');
        $wishlist = $this->wishlistRepository->find($wishlistId);

        /** @var ShopUserInterface $shopUser */
        $shopUser = $this->tokenStorage->getToken()->getUser();
        try {
        $updateWishlistName = new AddWishlistToUser($wishlist, $shopUser);
        $this->commandBus->dispatch($updateWishlistName);

        $this->flashBag->add(
            'success',
            $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.wishlist_saved')
        );
        } catch (HandlerFailedException $exception) {
            $this->flashBag->add(
                'error',
                $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.you_have_no_access_to_that_wishlist')
            );
        }

        return new RedirectResponse($this->urlGenerator->generate('bitbag_sylius_wishlist_plugin_shop_wishlist_list_wishlists'));
    }
}
