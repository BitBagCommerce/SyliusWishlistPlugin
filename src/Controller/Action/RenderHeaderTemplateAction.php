<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
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
            $this->twigEnvironment->render('@BitBagSyliusWishlistPlugin/Common/widget.html.twig', [
                'wishlist' => $wishlist,
            ])
        );
    }
}
