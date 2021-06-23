<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action\ApiPlatform;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class RemoveProductFromWishlistAction
{
    public function __construct()
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        return new JsonResponse([], 204);
    }
}
