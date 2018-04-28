<?php

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Context\WishlistContextInterface;
use BitBag\SyliusWishlistPlugin\Controller\Action\RenderHeaderTemplateAction;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use PhpSpec\ObjectBehavior;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class RenderHeaderTemplateActionSpec extends ObjectBehavior
{
    function let(WishlistContextInterface $wishlistContext, EngineInterface $templatingEngine): void
    {
        $this->beConstructedWith($wishlistContext, $templatingEngine);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(RenderHeaderTemplateAction::class);
    }

    function it_renders_header_template(
        Request $request,
        WishlistContextInterface $wishlistContext,
        WishlistInterface $wishlist,
        EngineInterface $templatingEngine,
        Response $response
    ): void {
        $wishlistContext->getWishlist($request)->willReturn($wishlist);
        $templatingEngine->renderResponse('@BitBagSyliusWishlistPlugin/_wishlistHeader.html.twig', [
            'wishlist' => $wishlist,
        ])->willReturn($response);

        $this->__invoke($request)->shouldReturn($response);
    }
}
