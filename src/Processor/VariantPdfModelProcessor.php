<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\WishlistPlugin\Processor;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\WishlistPlugin\Command\Wishlist\WishlistItem;
use Sylius\WishlistPlugin\Generator\ModelCreatorInterface;
use Sylius\WishlistPlugin\Model\VariantPdfModel;

final class VariantPdfModelProcessor implements VariantPdfModelProcessorInterface
{
    public function __construct(
        private ModelCreatorInterface $pdfModelCreator,
    ) {
    }

    public function createVariantPdfModelCollection(Collection $wishlistProducts): ArrayCollection
    {
        $pdfModelsCollection = new ArrayCollection();

        /** @var WishlistItem $wishlistProduct */
        foreach ($wishlistProducts as $wishlistProduct) {
            /** @var VariantPdfModel $variantPdfModel */
            $variantPdfModel = $this->pdfModelCreator->createWishlistItemToPdf($wishlistProduct);
            $pdfModelsCollection->add($variantPdfModel);
        }

        return $pdfModelsCollection;
    }
}
