<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class RenderHeaderTemplateAction
{
    private WishlistRepositoryInterface $wishlistRepository;

    private Environment $twigEnvironment;

    public function __construct(WishlistRepositoryInterface $wishlistRepository, Environment $twigEnvironment)
    {
        $this->wishlistRepository = $wishlistRepository;
        $this->twigEnvironment = $twigEnvironment;
    }

    public function __invoke(Request $request): Response
    {
        $wishlist = $this->wishlistRepository->findAll();;

        return new Response(
            $this->twigEnvironment->render('@BitBagSyliusWishlistPlugin/Common/widget.html.twig', [
                'wishlist' => $wishlist,
            ])
        );
    }
}
