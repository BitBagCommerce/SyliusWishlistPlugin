<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Resolver\WishlistsResolverInterface;
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
