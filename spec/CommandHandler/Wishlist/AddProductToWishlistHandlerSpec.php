<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddProductToWishlist;
use BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist\AddProductToWishlistHandler;
use BitBag\SyliusWishlistPlugin\Factory\WishlistProductFactoryInterface;
use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class AddProductToWishlistHandlerSpec extends ObjectBehavior
{
    public function let(
        WishlistProductFactoryInterface $wishlistProductFactory,
        ProductRepositoryInterface $productRepository,
        ObjectManager $wishlistManager
    ): void {
        $this->beConstructedWith(
            $wishlistProductFactory,
            $productRepository,
            $wishlistManager
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AddProductToWishlistHandler::class);
        $this->shouldImplement(MessageHandlerInterface::class);
    }

//    public function it_adds_product_to_wishlist(
//        AddProductToWishlist $addProductToWishlist
//    ): void
//    {
//
//    }
}


//    public function __invoke(AddProductToWishlist $addProductToWishlist): WishlistInterface
//    {
//        $productId = $addProductToWishlist->productId;
//
//        /** @var ?ProductInterface $product */
//        $product = $this->productRepository->find($productId);
//        $wishlist = $addProductToWishlist->getWishlist();
//
//        if (null === $product) {
//            throw new ProductNotFoundException(
//                sprintf('The Product %s does not exist', $productId)
//            );
//        }
//
//        if (null === $wishlist) {
//            throw new WishlistNotFoundException(
//                'bitbag_sylius_wishlist_plugin.ui.wishlist_for_channel_not_found'
//            );
//        }
//
//        $wishlistProduct = $this->wishlistProductFactory->createForWishlistAndProduct($wishlist, $product);
//
//        $wishlist->addWishlistProduct($wishlistProduct);
//
//        $this->wishlistManager->persist($wishlistProduct);
//        $this->wishlistManager->flush();
//
//        return $wishlist;
//    }
//}
