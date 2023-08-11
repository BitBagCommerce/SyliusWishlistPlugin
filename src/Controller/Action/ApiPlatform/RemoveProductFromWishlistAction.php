<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action\ApiPlatform;

use BitBag\SyliusWishlistPlugin\Checker\WishlistAccessCheckerInterface;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\RemoveProductFromWishlist;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

final class RemoveProductFromWishlistAction
{
    private MessageBusInterface $messageBus;

    private WishlistAccessCheckerInterface $wishlistAccessChecker;

    public function __construct(MessageBusInterface $messageBus, WishlistAccessCheckerInterface $wishlistAccessChecker)
    {
        $this->messageBus = $messageBus;
        $this->wishlistAccessChecker = $wishlistAccessChecker;
    }

    public function __invoke(Request $request): JsonResponse
    {
        $wishlistToken = (string) $request->attributes->get('token');
        $productId = (int) $request->attributes->get('productId');

        $wishlist = $this->wishlistAccessChecker->resolveWishlistByToken($wishlistToken);

        if (false === $wishlist instanceof WishlistInterface) {
            return new JsonResponse([], Response::HTTP_FORBIDDEN);
        }

        $removeProductFromWishlist = new RemoveProductFromWishlist($productId, $wishlistToken);
        $this->messageBus->dispatch($removeProductFromWishlist);

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }
}
