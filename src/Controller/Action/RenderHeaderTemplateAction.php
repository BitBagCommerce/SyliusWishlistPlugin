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
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class RenderHeaderTemplateAction
{
    /** @var WishlistContextInterface */
    private $wishlistContext;

    /** @var EngineInterface */
    private $templatingEngine;

    public function __construct(WishlistContextInterface $wishlistContext, EngineInterface $templatingEngine)
    {
        $this->wishlistContext = $wishlistContext;
        $this->templatingEngine = $templatingEngine;
    }

    public function __invoke(Request $request): Response
    {
        $wishlist = $this->wishlistContext->getWishlist($request);

        return $this->templatingEngine->renderResponse('@BitBagSyliusWishlistPlugin/Resources/views/_wishlistHeader.html.twig', [
            'wishlist' => $wishlist,
        ]);
    }
}
