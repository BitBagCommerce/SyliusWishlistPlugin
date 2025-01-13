<?php

/*
 * This file has been created by developers from Softylines.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://softylines.com and write us
 * an email on ask@softylines.com.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action\ApiPlatform;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddProductToSelectedWishlist;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

final class AddProductToSelectedWishlistAction
{
    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function __invoke(Request $request): JsonResponse
    {
        $wishlistToken = (string) $request->attributes->get('token');
        $productId = (int) $request->get('productId');

        $addProductToSelectedWishlist = new AddProductToSelectedWishlist($wishlistToken, $productId);
        $this->messageBus->dispatch($addProductToSelectedWishlist);

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }
}
