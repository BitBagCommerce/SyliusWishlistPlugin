<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Twig\Components\Wishlist;

use BitBag\SyliusWishlistPlugin\Resolver\WishlistsResolverInterface;
use Sylius\Bundle\UiBundle\Twig\Component\ResourceLivePropTrait;
use Sylius\Bundle\UiBundle\Twig\Component\TemplatePropTrait;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Resource\Model\ResourceInterface;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsLiveComponent]
class WidgetComponent
{
    use DefaultActionTrait;
    use HookableLiveComponentTrait;
    use TemplatePropTrait;

    /** @use ResourceLivePropTrait<OrderInterface> */
    use ResourceLivePropTrait;

    #[LiveProp(hydrateWith: 'hydrateResource', dehydrateWith: 'dehydrateResource')]
    public ?ResourceInterface $wishlist = null;

    public array $wishlists = [];

    public function __construct(
        protected WishlistsResolverInterface $wishlistsResolver,
    ) {
    }

    #[PreMount]
    public function initializeCart(): void
    {
        $this->wishlists = $this->getWishlists();
    }

    public function getWishlists(): array
    {
        return $this->wishlistsResolver->resolve();
    }

    #[LiveListener(WishlistCartFormComponent::WISHLIST_CART_CHANGED)]
    public function refreshWishlist(#[LiveArg] mixed $wishlistId = null): void
    {
        dd($wishlistId);
        $this->wishlist = $this->hydrateResource($wishlistId);
    }
}
