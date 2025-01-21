<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

final class ListWishlistsAction extends BaseWishlistsListingAction
{
    private const FILE_TO_RENDER = '@BitBagSyliusWishlistPlugin/Components/Wishlist/index/wishlistList.html.twig';

    protected function getTemplateToRender(): string
    {
        return self::FILE_TO_RENDER;
    }
}
