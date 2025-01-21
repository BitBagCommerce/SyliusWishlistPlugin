<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Twig\Components\Product;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use Doctrine\Persistence\ObjectManager;
use Sylius\Bundle\CoreBundle\Provider\FlashBagProvider;
use Sylius\Bundle\OrderBundle\Factory\AddToCartCommandFactoryInterface;
use Sylius\Bundle\ShopBundle\Twig\Component\Product\Trait\ProductLivePropTrait;
use Sylius\Bundle\ShopBundle\Twig\Component\Product\Trait\ProductVariantLivePropTrait;
use Sylius\Bundle\UiBundle\Twig\Component\TemplatePropTrait;
use Sylius\Component\Core\Factory\CartItemFactoryInterface;
use Sylius\Component\Core\Model\OrderItem;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\SyliusCartEvents;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\Attribute\PreReRender;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\PostMount;

#[AsLiveComponent]
class AddToCartFormComponent
{
    use ComponentToolsTrait;
    use ComponentWithFormTrait;
    use DefaultActionTrait;
    use HookableLiveComponentTrait;
    use ProductLivePropTrait;
    use ProductVariantLivePropTrait;
    use TemplatePropTrait;

    public const SYLIUS_SHOP_VARIANT_CHANGED = 'sylius:shop:variant_changed';

    #[LiveProp]
    public string $routeName = 'sylius_shop_cart_summary';

    /** @var array<string, mixed> */
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

        $this->emitUp(self::SYLIUS_SHOP_VARIANT_CHANGED, ['variantId' => $this->variant?->getId()]);
    }

    /** @param array<string, mixed> $routeParameters */
    #[LiveAction]
    public function addToCart(
        #[LiveArg]
        ?string $routeName = null,
        #[LiveArg]
        array $routeParameters = [],
        #[LiveArg]
        ?string $idRouteParameter = null,
        #[LiveArg]
        bool $addFlashMessage = true,
    ): RedirectResponse {
        $this->submitForm();
        $addToCartCommand = $this->getForm()->getData();

        $this->eventDispatcher->dispatch(new GenericEvent($addToCartCommand), SyliusCartEvents::CART_ITEM_ADD);
        $this->manager->persist($addToCartCommand->getCart());
        $this->manager->flush();

        if ($addFlashMessage) {
            FlashBagProvider::getFlashBag($this->requestStack)->add('success', 'sylius.cart.add_item');
        }

        if (null !== $idRouteParameter) {
            $routeParameters[$idRouteParameter] = $addToCartCommand->getCart()->getId();
        }

        return new RedirectResponse($this->router->generate(
            $routeName ?? $this->routeName,
            array_merge($this->routeParameters, $routeParameters),
        ));
    }

    #[LiveAction]
    public function addToWishlist(): RedirectResponse
    {
        $this->submitForm();
        $addToCartCommand = $this->getForm()->getData();
        /** @var OrderItemInterface $item */
        $item = $addToCartCommand->getCartItem();
        /** @var ?ProductVariantInterface $variant */
        $variant = $item->getVariant();

        /** @var ?WishlistInterface $wishlist */
        $wishlist = $this->getForm()->get('wishlists')->getData();

        if (null === $wishlist) {
            FlashBagProvider::getFlashBag($this->requestStack)->add('error', 'bitbag_sylius_wishlist_plugin.ui.go_to_wishlist_failure');

            return new RedirectResponse($this->router->generate('sylius_shop_homepage'));
        }

        return new RedirectResponse($this->router->generate('bitbag_sylius_wishlist_plugin_shop_locale_wishlist_add_product_variant', [
            'wishlistId' => $wishlist->getId(),
            'variantId' => $variant->getId(),
        ]));
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
