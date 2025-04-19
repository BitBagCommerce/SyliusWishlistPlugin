<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Sylius\WishlistPlugin\Controller\Action\ApiPlatform;

use Sylius\WishlistPlugin\Command\Wishlist\RemoveProductVariantFromWishlist;
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
