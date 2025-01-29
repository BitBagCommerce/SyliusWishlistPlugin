<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\AddProductVariantToWishlist;
use BitBag\SyliusWishlistPlugin\CommandHandler\AddProductVariantToWishlistHandler;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use BitBag\SyliusWishlistPlugin\Exception\ProductVariantNotFoundException;
use BitBag\SyliusWishlistPlugin\Factory\WishlistProductFactoryInterface;
use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;

final class AddProductVariantToWishlistHandlerSpec extends ObjectBehavior
{
    public function let(
        WishlistProductFactoryInterface $wishlistProductFactory,
        ProductVariantRepositoryInterface $productVariantRepository,
        ObjectManager $wishlistManager,
    ): void {
        $this->beConstructedWith(
            $wishlistProductFactory,
            $productVariantRepository,
            $wishlistManager,
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AddProductVariantToWishlistHandler::class);
    }

    public function it_adds_product_variant_to_wishlist(
        ProductVariantInterface $productVariant,
        WishlistInterface $wishlist,
        ProductVariantRepositoryInterface $productVariantRepository,
        WishlistProductFactoryInterface $wishlistProductFactory,
        WishlistProductInterface $wishlistProduct,
        ObjectManager $wishlistManager,
    ): void {
        $productVariantRepository->find(1)->willReturn($productVariant);

        $wishlistProductFactory->createForWishlistAndVariant($wishlist, $productVariant)->willReturn($wishlistProduct);
        $wishlist->addWishlistProduct($wishlistProduct)->shouldBeCalled();

        $wishlistManager->persist($wishlist)->shouldBeCalled();
        $wishlistManager->flush()->shouldBeCalled();

        $addProductVariantToWishlist = new AddProductVariantToWishlist(1);
        $addProductVariantToWishlist->setWishlist($wishlist->getWrappedObject());

        $this->__invoke($addProductVariantToWishlist);
    }

    public function it_doesnt_add_product_variant_to_wishlist_if_variant_isnt_found(
        ProductVariantInterface $productVariant,
        WishlistInterface $wishlist,
        ProductVariantRepositoryInterface $productVariantRepository,
        WishlistProductFactoryInterface $wishlistProductFactory,
        WishlistProductInterface $wishlistProduct,
        ObjectManager $wishlistManager,
    ): void {
        $productVariantRepository->find(1)->willReturn(null);

        $wishlistProductFactory->createForWishlistAndVariant($wishlist, $productVariant)->shouldNotBeCalled();
        $wishlist->addWishlistProduct($wishlistProduct)->shouldNotBeCalled();

        $wishlistManager->persist($wishlistProduct)->shouldNotBeCalled();
        $wishlistManager->flush()->shouldNotBeCalled();

        $addProductVariantToWishlist = new AddProductVariantToWishlist(1);
        $addProductVariantToWishlist->setWishlist($wishlist->getWrappedObject());

        $this
            ->shouldThrow(ProductVariantNotFoundException::class)
            ->during('__invoke', [$addProductVariantToWishlist])
        ;
    }
}
