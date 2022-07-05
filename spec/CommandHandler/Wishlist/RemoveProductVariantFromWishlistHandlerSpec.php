<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\RemoveProductVariantFromWishlistInterface;
use BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist\RemoveProductVariantFromWishlistHandler;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class RemoveProductVariantFromWishlistHandlerSpec extends ObjectBehavior
{
    public function let(
        WishlistRepositoryInterface $wishlistRepository,
        ProductVariantRepositoryInterface $productVariantRepository,
        RepositoryInterface $wishlistProductRepository,
        ObjectManager $wishlistManager
    ): void
    {
        $this->beConstructedWith(
            $wishlistRepository,
            $productVariantRepository,
            $wishlistProductRepository,
            $wishlistManager
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(RemoveProductVariantFromWishlistHandler::class);
        $this->shouldImplement(MessageHandlerInterface::class);
    }

    public function it_throws_404_when_product_variant_is_not_found(
        RemoveProductVariantFromWishlistInterface $removeProductVariantFromWishlist,
        ProductVariantRepositoryInterface $productVariantRepository,
        WishlistRepositoryInterface $wishlistRepository,
        RepositoryInterface $wishlistProductRepository,
        WishlistProductInterface $wishlistProduct,
        WishlistInterface $wishlist
    ): void
    {
        $removeProductVariantFromWishlist->getProductVariantIdValue()->willReturn(1);
        $removeProductVariantFromWishlist->getWishlistTokenValue()->willReturn('one');
        $productVariantRepository->find(1)->willReturn(null);
        $wishlistProductRepository->findOneBy(['variant' => null])->willReturn($wishlistProduct);
        $wishlistRepository->findByToken('one')->willReturn($wishlist);

        $this->shouldThrow(NotFoundHttpException::class)->during('__invoke', [$removeProductVariantFromWishlist]);
    }

    public function it_throws_404_when_wishlist_product_is_not_found(
        RemoveProductVariantFromWishlistInterface $removeProductVariantFromWishlist,
        ProductVariantRepositoryInterface $productVariantRepository,
        ProductVariantInterface $productVariant,
        WishlistRepositoryInterface $wishlistRepository,
        RepositoryInterface $wishlistProductRepository,
        WishlistInterface $wishlist
    ): void
    {
        $removeProductVariantFromWishlist->getProductVariantIdValue()->willReturn(1);
        $removeProductVariantFromWishlist->getWishlistTokenValue()->willReturn('one');
        $productVariantRepository->find(1)->willReturn($productVariant);
        $wishlistProductRepository->findOneBy(['variant' => $productVariant])->willReturn(null);
        $wishlistRepository->findByToken('one')->willReturn($wishlist);

        $this->shouldThrow(NotFoundHttpException::class)->during('__invoke', [$removeProductVariantFromWishlist]);
    }

    public function it_throws_404_when_wishlist_is_not_found(
        RemoveProductVariantFromWishlistInterface $removeProductVariantFromWishlist,
        ProductVariantRepositoryInterface $productVariantRepository,
        ProductVariantInterface $productVariant,
        WishlistRepositoryInterface $wishlistRepository,
        RepositoryInterface $wishlistProductRepository,
        WishlistProductInterface $wishlistProduct
    ): void
    {
        $removeProductVariantFromWishlist->getProductVariantIdValue()->willReturn(1);
        $removeProductVariantFromWishlist->getWishlistTokenValue()->willReturn('one');
        $productVariantRepository->find(1)->willReturn($productVariant);
        $wishlistProductRepository->findOneBy(['variant' => $productVariant])->willReturn($wishlistProduct);
        $wishlistRepository->findByToken('one')->willReturn(null);

        $this->shouldThrow(NotFoundHttpException::class)->during('__invoke', [$removeProductVariantFromWishlist]);
    }

    public function it_removes_product_variant_from_wishlist(
        RemoveProductVariantFromWishlistInterface $removeProductVariantFromWishlist,
        ProductVariantInterface $productVariant,
        ProductVariantRepositoryInterface $productVariantRepository,
        WishlistProductInterface $wishlistProduct,
        RepositoryInterface $wishlistProductRepository,
        WishlistRepositoryInterface $wishlistRepository,
        WishlistInterface $wishlist,
        ObjectManager $wishlistManager
    ): void
    {
        $removeProductVariantFromWishlist->getProductVariantIdValue()->willReturn(1);
        $removeProductVariantFromWishlist->getWishlistTokenValue()->willReturn('one');
        $productVariantRepository->find(1)->willReturn($productVariant);
        $wishlistProductRepository->findOneBy(['variant' => $productVariant])->willReturn($wishlistProduct);
        $wishlistRepository->findByToken('one')->willReturn($wishlist);

        $wishlist->removeProductVariant($productVariant)->shouldBeCalled();
        $wishlistManager->flush()->shouldBeCalled();

        $this->__invoke($removeProductVariantFromWishlist)->shouldReturn($wishlist);
    }
}
