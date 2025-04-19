<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace spec\Sylius\WishlistPlugin\Controller\Action;

use Sylius\WishlistPlugin\Controller\Action\RenderHeaderTemplateAction;
use Sylius\WishlistPlugin\Resolver\WishlistsResolverInterface;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class RenderHeaderTemplateActionSpec extends ObjectBehavior
{
    public function let(
        Environment $twigEnvironment,
        WishlistsResolverInterface $wishlistsResolver,
    ): void {
        $this->beConstructedWith(
            $twigEnvironment,
            $wishlistsResolver,
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(RenderHeaderTemplateAction::class);
    }

    public function it_renders_header_template(
        Request $request,
        WishlistsResolverInterface $wishlistsResolver,
        Environment $twigEnvironment,
    ): void {
        $wishlists = [];
        $wishlistsResolver->resolve()->willReturn($wishlists);
        $twigEnvironment->render('@BitBagSyliusWishlistPlugin/Common/widget.html.twig', [
            'wishlists' => $wishlists,
        ])->willReturn('TEMPLATE');
        $this->__invoke($request)->shouldImplement(Response::class);
    }
}
