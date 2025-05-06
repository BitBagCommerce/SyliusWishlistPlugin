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

namespace Sylius\WishlistPlugin\Controller\Action;

final class RenderHeaderTemplateAction extends BaseWishlistsListingAction
{
    private const FILE_TO_RENDER = '@SyliusWishlistPlugin/common/widget.html.twig';

    protected function getTemplateToRender(): string
    {
        return self::FILE_TO_RENDER;
    }
}
