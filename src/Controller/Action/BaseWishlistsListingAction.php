<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Resolver\WishlistsResolverInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

abstract class BaseWishlistsListingAction
{
    public Environment $twigEnvironment;

    public WishlistsResolverInterface $wishlistsResolver;

    protected string $fileToRender;

    public function __construct(
        Environment $twigEnvironment,
        WishlistsResolverInterface $wishlistsResolver
    ) {
        $this->twigEnvironment = $twigEnvironment;
        $this->wishlistsResolver = $wishlistsResolver;
    }

    public function __invoke(Request $request): Response
    {
        $wishlists = $this->wishlistsResolver->resolve($request);

        $this->setFileToRender();

        return new Response(
            $this->twigEnvironment->render($this->fileToRender, [
                'wishlists' => $wishlists,
            ])
        );
    }

    abstract public function setFileToRender(): void;
}
