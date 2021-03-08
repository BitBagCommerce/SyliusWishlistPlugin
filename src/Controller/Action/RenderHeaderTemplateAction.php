<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Context\WishlistContextInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class RenderHeaderTemplateAction
{
    private WishlistContextInterface $wishlistContext;

    private Environment $twigEnvironment;

    public function __construct(WishlistContextInterface $wishlistContext, Environment $twigEnvironment)
    {
        $this->wishlistContext = $wishlistContext;
        $this->twigEnvironment = $twigEnvironment;
    }

    public function __invoke(Request $request): Response
    {
        $wishlist = $this->wishlistContext->getWishlist($request);

        return new Response(
            $this->twigEnvironment->render('@BitBagSyliusWishlistPlugin/_wishlistHeader.html.twig', [
                'wishlist' => $wishlist,
            ])
        );
    }
}
