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

namespace Sylius\WishlistPlugin\Twig\Component\Product;

use Sylius\Bundle\ShopBundle\Twig\Component\Product\AddToCartFormComponent;
use Sylius\Bundle\ShopBundle\Twig\Component\Product\Trait\ProductLivePropTrait;
use Sylius\Bundle\ShopBundle\Twig\Component\Product\Trait\ProductVariantLivePropTrait;
use Sylius\Bundle\UiBundle\Twig\Component\TemplatePropTrait;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Sylius\WishlistPlugin\Form\Type\AddToWishlistType;
use Sylius\WishlistPlugin\Processor\AddProductVariantToWishlistProcessorInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\PostMount;

#[AsLiveComponent]
final class AddToWishlistComponent
{
    use ComponentToolsTrait;
    use ComponentWithFormTrait;
    use DefaultActionTrait;
    use HookableLiveComponentTrait;
    use TemplatePropTrait;
    use ProductLivePropTrait;
    use ProductVariantLivePropTrait;

    public function __construct(
        private readonly AddProductVariantToWishlistProcessorInterface $addProductVariantToWishlistProcessor,
        private readonly FormFactoryInterface $formFactory,
        protected readonly ProductVariantResolverInterface $productVariantResolver,
        ProductRepositoryInterface $productRepository,
        ProductVariantRepositoryInterface $productVariantRepository,
    ) {
        $this->initializeProduct($productRepository);
        $this->initializeProductVariant($productVariantRepository);
    }

    #[PostMount]
    public function postMount(): void
    {
        /** @var ProductVariantInterface|null $variant * */
        $variant = $this->productVariantResolver->getVariant($this->product);
        $this->variant = $variant;
    }

    #[LiveListener(AddToCartFormComponent::SYLIUS_SHOP_VARIANT_CHANGED)]
    public function updateProductVariant(#[LiveArg] mixed $variantId): void
    {
        if (null === $variantId) {
            $this->variant = null;

            return;
        }

        $changedVariant = $this->productVariantRepository->find($variantId);

        if ($changedVariant === $this->variant) {
            return;
        }

        $this->variant = $changedVariant?->isEnabled() ? $changedVariant : null;
    }

    #[LiveAction]
    public function addToWishlist(#[LiveArg] ?int $wishlistId = null): RedirectResponse
    {
        return $this->addProductVariantToWishlistProcessor->process(
            $this->variant,
            $wishlistId,
        );
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->formFactory->createNamed(
            'add_to_wishlist',
            AddToWishlistType::class,
        );
    }
}
