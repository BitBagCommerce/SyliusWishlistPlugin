<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Context\WishlistContextInterface;
use BitBag\SyliusWishlistPlugin\Controller\Action\RenderHeaderTemplateAction;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class RenderHeaderTemplateActionSpec extends ObjectBehavior
{
    function let(WishlistContextInterface $wishlistContext, Environment $twigEnvironment): void
    {
        $this->beConstructedWith($wishlistContext, $twigEnvironment);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(RenderHeaderTemplateAction::class);
    }

    function it_renders_header_template(
        Request $request,
        WishlistContextInterface $wishlistContext,
        WishlistInterface $wishlist,
        Environment $twigEnvironment,
        Response $response
    ): void {
        $wishlistContext->getWishlist($request)->willReturn($wishlist);

        $twigEnvironment->render('@BitBagSyliusWishlistPlugin/_wishlistHeader.html.twig', [
            'wishlist' => $wishlist,
        ])->willReturn('TEMPLATE');
        $this->__invoke($request)->shouldImplement(Response::class);
    }
}
