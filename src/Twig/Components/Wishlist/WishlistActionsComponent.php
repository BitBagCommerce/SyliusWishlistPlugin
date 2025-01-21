<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Twig\Components\Wishlist;

use Sylius\Bundle\UiBundle\Twig\Component\ResourceFormComponentTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

//@BitBagSyliusWishlistPlugin/Components/Wishlist/index/content/sections/general/items.html.twig
#[AsLiveComponent('WishlistActions')]

class WishlistActionsComponent
{
    use DefaultActionTrait;
    use ResourceFormComponentTrait;

    #[LiveProp(writable: true)]
    public string $query = '';

    public function __construct(
    ) {
    }
}
