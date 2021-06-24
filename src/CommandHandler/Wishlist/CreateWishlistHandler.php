<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\CreateWishlist;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistFactoryInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class CreateWishlistHandler implements MessageHandlerInterface
{
    private TokenStorageInterface $tokenStorage;

    private WishlistFactoryInterface $wishlistFactory;

    private WishlistRepositoryInterface $wishlistRepository;

    private ObjectManager $wishlistManager;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        WishlistFactoryInterface $wishlistFactory,
        WishlistRepositoryInterface $wishlistRepository,
        ObjectManager $wishlistManager
    )
    {
        $this->tokenStorage = $tokenStorage;
        $this->wishlistFactory = $wishlistFactory;
        $this->wishlistRepository = $wishlistRepository;
        $this->wishlistManager = $wishlistManager;
    }

    public function __invoke(CreateWishlist $createWishlist): WishlistInterface
    {
        $token = $this->tokenStorage->getToken();
        $user = $token ? $token->getUser() : null;

        if($user instanceof ShopUserInterface) {
            $wishlist = $this->wishlistRepository->findByShopUser($user);

            if (!$wishlist) {
                $wishlist = $this->wishlistFactory->createForUser($user);
            }
        }
        else {
            $wishlist = $this->wishlistFactory->createNew();
        }

        $this->wishlistManager->persist($wishlist);
        $this->wishlistManager->flush();

        return $wishlist;
    }
}
