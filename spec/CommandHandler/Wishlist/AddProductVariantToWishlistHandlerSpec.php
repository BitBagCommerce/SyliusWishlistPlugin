<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddProductVariantToWishlist;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddProductVariantToWishlistInterface;
use BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist\AddProductVariantToWishlistHandler;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistProductFactoryInterface;
use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class AddProductVariantToWishlistHandlerSpec extends ObjectBehavior
{
    public function let(
        WishlistProductFactoryInterface $wishlistProductFactory,
        ProductVariantRepositoryInterface $productVariantRepository,
        ObjectManager $wishlistManager
    ): void {
        $this->beConstructedWith(
            $wishlistProductFactory,
            $productVariantRepository,
            $wishlistManager
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AddProductVariantToWishlistHandler::class);
        $this->shouldImplement(MessageHandlerInterface::class);
    }

    public function it_throws_404_when_variant_not_found(
        AddProductVariantToWishlistInterface $addProductVariantToWishlist,
        ProductVariantRepositoryInterface $productVariantRepository,
        WishlistInterface $wishlist
    ): void
    {
        $addProductVariantToWishlist->getProductVariantId()->willReturn(1);
        $productVariantRepository->find(1)->willReturn(null);
        $addProductVariantToWishlist->getWishlist()->willReturn($wishlist);

        $this->shouldThrow(NotFoundHttpException::class)->during('__invoke', [$addProductVariantToWishlist]);
    }

    public function it_adds_product_variant_to_wishlist(
        AddProductVariantToWishlistInterface $addProductVariantToWishlist,
        ProductVariantRepositoryInterface $productVariantRepository,
        ProductVariantInterface $productVariant,
        WishlistInterface $wishlist,
        WishlistProductFactoryInterface $wishlistProductFactory,
        WishlistProductInterface $wishlistProduct,
        ObjectManager $wishlistManager
    ): void
    {
        $addProductVariantToWishlist->getProductVariantId()->willReturn(1);
        $productVariantRepository->find(1)->willReturn($productVariant);
        $addProductVariantToWishlist->getWishlist()->willReturn($wishlist);
        $wishlistProductFactory->createForWishlistAndVariant($wishlist, $productVariant)->willReturn($wishlistProduct);

        $wishlist->addWishlistProduct($wishlistProduct)->shouldBeCalled();
        $wishlistManager->persist($wishlist)->shouldBeCalled();
        $wishlistManager->flush()->shouldBeCalled();

        $this->__invoke($addProductVariantToWishlist)->shouldReturn($wishlist);
    }
}


//public function __invoke(AddProductVariantToWishlist $addProductVariantToWishlist): WishlistInterface
//{
//    $variantId = $addProductVariantToWishlist->productVariantId;
//
//    /** @var ?ProductVariantInterface $variant */
//    $variant = $this->productVariantRepository->find($variantId);
//    $wishlist = $addProductVariantToWishlist->getWishlist();
//
//    if (null === $variant) {
//        throw new ProductVariantNotFoundException(
//            sprintf('The ProductVariant %s does not exist', $variantId)
//        );
//    }
//
//    $wishlistProduct = $this->wishlistProductFactory->createForWishlistAndVariant($wishlist, $variant);
//
//    $wishlist->addWishlistProduct($wishlistProduct);
//
//    $this->wishlistManager->persist($wishlist);
//    $this->wishlistManager->flush();
//
//    return $wishlist;
//}