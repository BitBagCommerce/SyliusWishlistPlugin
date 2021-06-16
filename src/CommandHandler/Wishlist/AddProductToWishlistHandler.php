<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddProductToWishlist;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistFactoryInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistProductFactoryInterface;
use BitBag\SyliusWishlistPlugin\Resolver\UserWishlistResolverInterface;
use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AddProductToWishlistHandler implements MessageHandlerInterface
{
    private TokenStorageInterface $tokenStorage;

    private UserWishlistResolverInterface $userWishlistResolver;

    private WishlistFactoryInterface $wishlistFactory;

    private WishlistProductFactoryInterface $wishlistProductFactory;

    private ObjectManager $wishlistManager;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        UserWishlistResolverInterface $userWishlistResolver,
        WishlistFactoryInterface $wishlistFactory,
        WishlistProductFactoryInterface $wishlistProductFactory,
        ObjectManager $wishlistManager
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->userWishlistResolver = $userWishlistResolver;
        $this->wishlistFactory = $wishlistFactory;
        $this->wishlistProductFactory = $wishlistProductFactory;
        $this->wishlistManager = $wishlistManager;
    }

    public function __invoke(AddProductToWishlist $addProductToWishlist): WishlistInterface
    {
        $token = $this->tokenStorage->getToken();
        $user = $token ? $token->getUser() : null;

        $product = $addProductToWishlist->product;

        if ($user instanceof ShopUserInterface) {
            $wishlist = $this->userWishlistResolver->resolve($user);
        } else {
            /** @var WishlistInterface $wishlist */
            $wishlist = $this->wishlistFactory->createNew();
        }

        $wishlistProduct = $this->wishlistProductFactory->createForWishlistAndProduct($wishlist, $product);
        $wishlist->addWishlistProduct($wishlistProduct);

        $this->wishlistManager->persist($wishlist);
        $this->wishlistManager->flush();

        return $wishlist;
    }
}
