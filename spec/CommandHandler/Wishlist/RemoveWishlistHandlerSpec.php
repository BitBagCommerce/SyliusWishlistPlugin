<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\RemoveWishlistInterface;
use BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist\RemoveWishlistHandler;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class RemoveWishlistHandlerSpec extends ObjectBehavior
{
    public function let(
        WishlistRepositoryInterface $wishlistRepository,
        ObjectManager $wishlistManager
    ): void {
        $this->beConstructedWith(
            $wishlistRepository,
            $wishlistManager
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(RemoveWishlistHandler::class);
        $this->shouldImplement(MessageHandlerInterface::class);
    }

    public function it_throws_404_when_wishlist_not_found(
        RemoveWishlistInterface $removeWishlist,
        WishlistRepositoryInterface $wishlistRepository
    ): void
    {
        $removeWishlist->getWishlistTokenValue()->willReturn('token');
        $wishlistRepository->findByToken('token')->willReturn(null);

        $this->shouldThrow(NotFoundHttpException::class)->during('__invoke', [$removeWishlist]);
    }

    public function it_removes_the_wishlist(
        RemoveWishlistInterface $removeWishlist,
        WishlistRepositoryInterface $wishlistRepository,
        WishlistInterface $wishlist,
        ObjectManager $wishlistManager
    ): void
    {
        $removeWishlist->getWishlistTokenValue()->willReturn('token');
        $wishlistRepository->findByToken('token')->willReturn($wishlist);

        $wishlistManager->remove($wishlist)->shouldBeCalled();
        $wishlistManager->flush()->shouldBeCalled();

        $this->__invoke($removeWishlist);
    }
}
