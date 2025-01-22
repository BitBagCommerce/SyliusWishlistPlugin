<?php

namespace BitBag\SyliusWishlistPlugin\Twig\Components\Wishlist;

use Sylius\Bundle\ShopBundle\Twig\Component\Product\Trait\ProductLivePropTrait;
use Sylius\Bundle\ShopBundle\Twig\Component\Product\Trait\ProductVariantLivePropTrait;
use Sylius\Bundle\UiBundle\Twig\Component\TemplatePropTrait;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class WishlistVariantComponent
{
    use DefaultActionTrait;
    use HookableLiveComponentTrait;
    use ProductVariantLivePropTrait;
    use TemplatePropTrait;
    use ProductLivePropTrait;

    /**
     * @param ProductRepositoryInterface<ProductInterface> $productRepository
     * @param ProductVariantRepositoryInterface<ProductVariantInterface> $productVariantRepository
     */
    public function __construct(
        protected readonly ProductVariantResolverInterface $productVariantResolver,
        ProductRepositoryInterface $productRepository,
        ProductVariantRepositoryInterface $productVariantRepository,
    ) {
        $this->initializeProduct($productRepository);
        $this->initializeProductVariant($productVariantRepository);
    }

    #[LiveListener(ProductComponent::WISHLIST_VARIANT_CHANGED)]
    public function updateProductVariant(#[LiveArg] mixed $variantId): void
    {
        dd('ddd');
        if (null === $variantId) {
            return;
        }

        $changedVariant = $this->productVariantRepository->find($variantId);

        if ($changedVariant === $this->variant) {
            return;
        }

        $this->variant = $changedVariant->isEnabled() ? $changedVariant : null;
    }
}
