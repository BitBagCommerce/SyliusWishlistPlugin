<?php

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
