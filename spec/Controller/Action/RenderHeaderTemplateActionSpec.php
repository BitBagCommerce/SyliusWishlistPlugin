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
        $twigEnvironment->render('@SyliusWishlistPlugin/Common/widget.html.twig', [
            'wishlists' => $wishlists,
        ])->willReturn('TEMPLATE');
        $this->__invoke($request)->shouldImplement(Response::class);
    }
}
