<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Action\ApiPlatform;

use BitBag\SyliusWishlistPlugin\Command\RemoveWishlist;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class RemoveWishlistAction
{
    public function __construct(
        private MessageBusInterface $messageBus,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $wishlistToken = (string) $request->attributes->get('token');

        $removeWishlist = new RemoveWishlist($wishlistToken);
        $this->messageBus->dispatch($removeWishlist);

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }
}
