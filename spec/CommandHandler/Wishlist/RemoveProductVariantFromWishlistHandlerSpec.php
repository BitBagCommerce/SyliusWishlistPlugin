<?php

declare(strict_types=1);

namespace spec\Sylius\WishlistPlugin\CommandHandler\Wishlist;

use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Resource\ResourceActions;
use Sylius\WishlistPlugin\Command\Wishlist\RemoveProductVariantFromWishlist;
use Sylius\WishlistPlugin\CommandHandler\Wishlist\RemoveProductVariantFromWishlistHandler;
use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\WishlistPlugin\Entity\WishlistProductInterface;
use Sylius\WishlistPlugin\Exception\ProductVariantNotFoundException;
use Sylius\WishlistPlugin\Exception\WishlistNotFoundException;
use Sylius\WishlistPlugin\Repository\WishlistRepositoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class RemoveProductVariantFromWishlistHandlerSpec extends ObjectBehavior
{
    public function let(
        WishlistRepositoryInterface $wishlistRepository,
        ProductVariantRepositoryInterface $productVariantRepository,
        RepositoryInterface $wishlistProductRepository,
        ObjectManager $wishlistManager,
        AuthorizationCheckerInterface $authorizationChecker,
    ): void {
        $this->beConstructedWith(
            $wishlistRepository,
            $productVariantRepository,
            $wishlistProductRepository,
            $wishlistManager,
            $authorizationChecker,
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(RemoveProductVariantFromWishlistHandler::class);
    }

    public function it_removes_product_variant_from_wishlist(
        WishlistRepositoryInterface $wishlistRepository,
        ProductVariantRepositoryInterface $productVariantRepository,
        RepositoryInterface $wishlistProductRepository,
        ObjectManager $wishlistManager,
        ProductVariantInterface $variant,
        WishlistInterface $wishlist,
        WishlistProductInterface $wishlistProduct,
        AuthorizationCheckerInterface $authorizationChecker,
    ): void {
        $removeProductVariantCommand = new RemoveProductVariantFromWishlist(1, 'wishlist_token');

        $productVariantRepository->find(1)->willReturn($variant);
        $wishlistRepository->findByToken('wishlist_token')->willReturn($wishlist);
        $wishlistProductRepository->findOneBy(['variant' => $variant, 'wishlist' => $wishlist])->willReturn($wishlistProduct);

        $authorizationChecker->isGranted(ResourceActions::DELETE, $wishlist)->willReturn(true);

        $wishlist->removeProduct($wishlistProduct)->shouldBeCalled();
        $wishlistManager->flush()->shouldBeCalled();

        $this->__invoke($removeProductVariantCommand)->shouldReturn($wishlist);
    }

    public function it_throws_exception_when_product_variant_not_found(
        ProductVariantRepositoryInterface $productVariantRepository,
    ): void {
        $removeProductVariantCommand = new RemoveProductVariantFromWishlist(1, 'wishlist_token');

        $productVariantRepository->find(1)->willReturn(null);

        $this->shouldThrow(ProductVariantNotFoundException::class)->during('__invoke', [$removeProductVariantCommand]);
    }

    public function it_throws_exception_when_wishlist_not_found(
        ProductVariantRepositoryInterface $productVariantRepository,
        WishlistRepositoryInterface $wishlistRepository,
        RepositoryInterface $wishlistProductRepository,
        ProductVariantInterface $variant,
        AuthorizationCheckerInterface $authorizationChecker,
    ): void {
        $removeProductVariantCommand = new RemoveProductVariantFromWishlist(1, 'wishlist_token');

        $productVariantRepository->find(1)->willReturn($variant);
        $wishlistRepository->findByToken('wishlist_token')->willReturn(null);

        $authorizationChecker->isGranted(ResourceActions::DELETE, null)->willReturn(true);

        $this->shouldThrow(WishlistNotFoundException::class)->during('__invoke', [$removeProductVariantCommand]);
    }
}
