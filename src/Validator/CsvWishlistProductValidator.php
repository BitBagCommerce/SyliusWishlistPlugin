<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Validator;

use BitBag\SyliusWishlistPlugin\Model\DTO\CsvWishlistProductInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;

final class CsvWishlistProductValidator implements CsvWishlistProductValidatorInterface
{
    private ProductVariantRepositoryInterface $productVariantRepository;

    public function __construct(
        ProductVariantRepositoryInterface $productVariantRepository
    ) {
        $this->productVariantRepository = $productVariantRepository;
    }

    public function csvWishlistProductIsValid(CsvWishlistProductInterface $csvWishlistProduct): bool
    {
        $wishlistProduct = $this->productVariantRepository->findOneBy([
            'id' => $csvWishlistProduct->getVariantId(),
            'product' => $csvWishlistProduct->getProductId(),
            'code' => $csvWishlistProduct->getVariantCode(),
        ]);

        if (null === $wishlistProduct) {
            return false;
        }

        return true;
    }
}
