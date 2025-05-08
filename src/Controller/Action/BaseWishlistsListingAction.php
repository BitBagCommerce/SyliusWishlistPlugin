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

use Sylius\WishlistPlugin\Resolver\WishlistsResolverInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

abstract class BaseWishlistsListingAction
{
    protected string $fileToRender;

    public function __construct(
        protected Environment $twigEnvironment,
        protected WishlistsResolverInterface $wishlistsResolver,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $wishlists = $this->wishlistsResolver->resolve();

        return new Response(
            $this->twigEnvironment->render($this->getTemplateToRender(), [
                'wishlists' => $wishlists,
            ]),
        );
    }

    abstract protected function getTemplateToRender(): string;
}
