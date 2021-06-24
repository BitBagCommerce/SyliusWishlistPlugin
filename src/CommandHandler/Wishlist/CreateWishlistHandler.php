<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\CreateWishlist;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistFactoryInterface;
use BitBag\SyliusWishlistPlugin\Resolver\ShopUserWishlistResolverInterface;
use BitBag\SyliusWishlistPlugin\Updater\WishlistUpdaterInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class CreateWishlistHandler implements MessageHandlerInterface
{
    private TokenStorageInterface $tokenStorage;

    private WishlistFactoryInterface $wishlistFactory;

    private ShopUserWishlistResolverInterface $shopUserWishlistResolver;

    private WishlistUpdaterInterface $wishlistUpdater;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        WishlistFactoryInterface $wishlistFactory,
        ShopUserWishlistResolverInterface $shopUserWishlistResolver,
        WishlistUpdaterInterface $wishlistUpdater
    )
    {
        $this->tokenStorage = $tokenStorage;
        $this->wishlistFactory = $wishlistFactory;
        $this->shopUserWishlistResolver = $shopUserWishlistResolver;
        $this->wishlistUpdater = $wishlistUpdater;
    }

    public function __invoke(CreateWishlist $createWishlist): WishlistInterface
    {
        $token = $this->tokenStorage->getToken();
        $user = $token ? $token->getUser() : null;

        if($user instanceof ShopUserInterface) {
            $wishlist = $this->shopUserWishlistResolver->resolve($user);
        }
        else {
            $wishlist = $this->wishlistFactory->createNew();
        }

        $this->wishlistUpdater->updateWishlist($wishlist);

        return $wishlist;
    }
}
