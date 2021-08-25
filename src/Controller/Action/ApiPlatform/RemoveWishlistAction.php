<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action\ApiPlatform;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\RemoveWishlist;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
        $wishlistToken = (string) $request->attributes->get('token');

        $removeWishlist = new RemoveWishlist($wishlistToken);
        $this->messageBus->dispatch($removeWishlist);

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }
}
