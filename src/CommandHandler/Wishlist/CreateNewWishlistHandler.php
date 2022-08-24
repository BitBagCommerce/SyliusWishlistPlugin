<?php
/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/
declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Checker\WishlistNameCheckerInterface;
use BitBag\SyliusWishlistPlugin\Checker\WishlistCanBeCreatedCheckerInterface;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\CreateNewWishlist;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Exception\WishlistNameIsTakenException;
use BitBag\SyliusWishlistPlugin\Factory\WishlistFactoryInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use BitBag\SyliusWishlistPlugin\Resolver\WishlistCookieTokenResolverInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class CreateNewWishlistHandler implements MessageHandlerInterface
{
    private WishlistRepositoryInterface $wishlistRepository;

    private TokenStorageInterface $tokenStorage;

    private WishlistFactoryInterface $wishlistFactory;

    private WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver;

    private ChannelRepositoryInterface $channelRepository;

    private WishlistNameCheckerInterface $wishlistNameChecker;

    public function __construct(
        WishlistRepositoryInterface $wishlistRepository,
        TokenStorageInterface $tokenStorage,
        WishlistFactoryInterface $wishlistFactory,
        WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver,
        ChannelRepositoryInterface $channelRepository,
        WishlistNameCheckerInterface $wishlistNameChecker
    ) {
        $this->wishlistRepository = $wishlistRepository;
        $this->tokenStorage = $tokenStorage;
        $this->wishlistFactory = $wishlistFactory;
        $this->wishlistCookieTokenResolver = $wishlistCookieTokenResolver;
        $this->channelRepository = $channelRepository;
        $this->wishlistNameChecker = $wishlistNameChecker;
    }

    public function __invoke(CreateNewWishlist $createNewWishlist): void
    {
        $user = $this->tokenStorage->getToken() ? $this->tokenStorage->getToken()->getUser() : null;
        $wishlistCookieToken = $this->wishlistCookieTokenResolver->resolve();

        if ($user instanceof ShopUserInterface) {
            $wishlist = $this->wishlistFactory->createForUser($user);
            $wishlists = $this->wishlistRepository->findAllByShopUser($wishlist->getShopUser()->getId());
        } else {
            $wishlist = $this->wishlistFactory->createNew();
            $wishlists = $this->wishlistRepository->findAllByAnonymous($wishlistCookieToken);
        }

        if ($wishlistCookieToken) {
            $wishlist->setToken($wishlistCookieToken);
        }

        if (null !== $createNewWishlist->getChannelCode()) {
            $channel = $this->channelRepository->findOneByCode($createNewWishlist->getChannelCode());
            $wishlist->setChannel($channel);
        }

        /** @var WishlistInterface $wishlist */
        foreach ($wishlists as $newWishlist) {
            if (!$this->wishlistNameChecker->check($newWishlist->getName(), $createNewWishlist->getName())) {
                $wishlist->setName($createNewWishlist->getName());
            } else {
                throw new WishlistNameIsTakenException();
            }
        }
        $this->wishlistRepository->add($wishlist);
    }
}
