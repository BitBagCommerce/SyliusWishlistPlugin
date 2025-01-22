<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Twig\Components\Wishlist;

use Doctrine\Persistence\ObjectManager;
use Sylius\Bundle\OrderBundle\Factory\AddToCartCommandFactoryInterface;
use Sylius\Bundle\ShopBundle\Twig\Component\Product\Trait\ProductLivePropTrait;
use Sylius\Bundle\ShopBundle\Twig\Component\Product\Trait\ProductVariantLivePropTrait;
use Sylius\Bundle\UiBundle\Twig\Component\TemplatePropTrait;
use Sylius\Component\Core\Factory\CartItemFactoryInterface;
use Sylius\Component\Core\Model\OrderItem;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\Attribute\PreReRender;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\PostMount;

#[AsLiveComponent]
class ProductComponent
{
    use ComponentToolsTrait;
    use ComponentWithFormTrait;
    use DefaultActionTrait;
    use HookableLiveComponentTrait;
    use ProductLivePropTrait;
    use ProductVariantLivePropTrait;
    use TemplatePropTrait;

    public const WISHLIST_VARIANT_CHANGED = 'wishlist:variant_changed';

    #[LiveProp]
    public string $routeName = 'wishlist_cart';

    public const WISHLIST_CHANGED = 'wishlist:cart_changed';

    #[LiveProp]
    public array $routeParameters = [];

    /**
     * @param CartItemFactoryInterface<OrderItem> $cartItemFactory
     * @param class-string $formClass
     * @param ProductRepositoryInterface<ProductInterface> $productRepository
     * @param ProductVariantRepositoryInterface<ProductVariantInterface> $productVariantRepository
     */
    public function __construct(
        protected readonly FormFactoryInterface $formFactory,
        protected readonly ObjectManager $manager,
        protected readonly RouterInterface $router,
        protected readonly RequestStack $requestStack,
        protected readonly EventDispatcherInterface $eventDispatcher,
        protected readonly CartContextInterface $cartContext,
        protected readonly AddToCartCommandFactoryInterface $addToCartCommandFactory,
        protected readonly CartItemFactoryInterface $cartItemFactory,
        protected readonly string $formClass,
        ProductRepositoryInterface $productRepository,
        ProductVariantRepositoryInterface $productVariantRepository,
    ) {
        $this->initializeProduct($productRepository);
        $this->initializeProductVariant($productVariantRepository);
    }

    #[PostMount(priority: 100)]
    public function postMount(): void
    {
        $this->isValidated = true;
    }

    #[PreReRender(priority: -100)]
    public function variantChanged(): void
    {
        $addToCartCommand = $this->getForm()->getData();

        $newVariant = $addToCartCommand->getCartItem()->getVariant();
        if ($newVariant === $this->variant) {
            return;
        }
        $this->variant = $newVariant;

        $this->emitUp(self::WISHLIST_VARIANT_CHANGED, ['variantId' => $this->variant?->getId()]);
    }

    protected function instantiateForm(): FormInterface
    {
        $addToCartCommand = $this->addToCartCommandFactory->createWithCartAndCartItem(
            $this->cartContext->getCart(),
            $this->cartItemFactory->createForProduct($this->product),
        );

        return $this->formFactory->create($this->formClass, $addToCartCommand, ['product' => $this->product]);
    }
}
