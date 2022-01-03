<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Controller\Action\RenderHeaderTemplateAction;
use BitBag\SyliusWishlistPlugin\Resolver\WishlistsResolverInterface;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class RenderHeaderTemplateActionSpec extends ObjectBehavior
{
    public function let(
        Environment $twigEnvironment,
        WishlistsResolverInterface $wishlistsResolver
    ): void {
        $this->beConstructedWith(
            $twigEnvironment,
            $wishlistsResolver
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(RenderHeaderTemplateAction::class);
    }

    public function it_renders_header_template(
        Request $request,
        WishlistsResolverInterface $wishlistsResolver,
        Environment $twigEnvironment
    ): void {
        $wishlists = [];
        $wishlistsResolver->resolve()->willReturn($wishlists);
        $twigEnvironment->render('@BitBagSyliusWishlistPlugin/Common/widget.html.twig', [
            'wishlists' => $wishlists,
        ])->willReturn('TEMPLATE');
        $this->__invoke($request)->shouldImplement(Response::class);
    }
}
