<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Validator;

use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use BitBag\SyliusWishlistPlugin\Model\DTO\CsvWishlistProductInterface;
use BitBag\SyliusWishlistPlugin\Validator\CsvWishlistProductValidator;
use BitBag\SyliusWishlistPlugin\Validator\CsvWishlistProductValidatorInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;

final class CsvWishlistProductValidatorSpec extends ObjectBehavior
{
    public function let(
        ProductVariantRepositoryInterface $productVariantRepository
    ): void {
        $this->beConstructedWith(
            $productVariantRepository
        );
    }

    public function it_is_initializable(): void {
        $this->shouldHaveType(CsvWishlistProductValidator::class);
        $this->shouldImplement(CsvWishlistProductValidatorInterface::class);
    }

    public function it_validate_csv_wishlist_product(
        CsvWishlistProductInterface $csvWishlistProduct,
        ProductVariantRepositoryInterface $productVariantRepository,
        WishlistProductInterface $wishlistProduct
    ): void {
        $csvWishlistProduct->getVariantId()->willReturn(1);
        $csvWishlistProduct->getProductId()->willReturn(1);
        $csvWishlistProduct->getVariantCode()->willReturn('one');
        $productVariantRepository->findOneBy([
            'id' => 1,
            'product' => 1,
            'code' => 'one'
        ])->willReturn($wishlistProduct);

        $this->csvWishlistProductIsValid($csvWishlistProduct)->shouldReturn(true);
    }

    public function it_validate_csv_wishlist_product_when_wishlist_product_not_found(
        CsvWishlistProductInterface $csvWishlistProduct,
        ProductVariantRepositoryInterface $productVariantRepository
    ): void {
        $csvWishlistProduct->getVariantId()->willReturn(1);
        $csvWishlistProduct->getProductId()->willReturn(1);
        $csvWishlistProduct->getVariantCode()->willReturn('one');
        $productVariantRepository->findOneBy([
            'id' => 1,
            'product' => 1,
            'code' => 'one'
        ])->willReturn(null);

        $this->csvWishlistProductIsValid($csvWishlistProduct)->shouldReturn(false);
    }
}
