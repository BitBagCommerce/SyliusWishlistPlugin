<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action\ApiPlatform;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\RemoveProductFromWishlist;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;

final class RemoveProductFromWishlistAction
{
    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function __invoke(Request $request): JsonResponse
    {
        $wishlistToken = $request->attributes->get('id');
        $productId = (int)$request->attributes->get('productId');

        $removeProductFromWishlist = new RemoveProductFromWishlist($productId, $wishlistToken);
        $this->messageBus->dispatch($removeProductFromWishlist);

        return new JsonResponse([], 204);
    }
}
