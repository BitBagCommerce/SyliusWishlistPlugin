<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action\ApiPlatform;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\RemoveProductVariantFromWishlist;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

final class RemoveProductVariantFromWishlistAction
{
    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function __invoke(Request $request): JsonResponse
    {
        $wishlistToken = (string) $request->attributes->get('token');
        $productVariantId = (int) $request->attributes->get('productVariantId');

        $removeProductVariantFromWishlist = new RemoveProductVariantFromWishlist($productVariantId, $wishlistToken);
        $this->messageBus->dispatch($removeProductVariantFromWishlist);

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }
}
