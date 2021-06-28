<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action\ApiPlatform;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\RemoveWishlist;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;

final class RemoveWishlistAction
{
    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function __invoke(Request $request): JsonResponse
    {
        $wishlistToken = (string) $request->attributes->get('id');

        $removeWishlist = new RemoveWishlist($wishlistToken);
        $this->messageBus->dispatch($removeWishlist);

        return new JsonResponse([], 204);
    }
}
