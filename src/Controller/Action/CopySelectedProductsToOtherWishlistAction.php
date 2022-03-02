<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\CopySelectedProductsToOtherWishlist;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

final class CopySelectedProductsToOtherWishlistAction
{
    private MessageBusInterface $commandBus;

    public function __construct(
        MessageBusInterface $commandBus
    ) {
        $this->commandBus = $commandBus;
    }

    public function __invoke(Request $request): Response
    {
        $destinedWishlist = $request->attributes->getInt('destinedWishlistId');
        $wishlistProducts = new ArrayCollection((array)$request->request->get("wishlist_collection")['items']);
        $selectedProducts = new ArrayCollection();

        foreach ($wishlistProducts as $wishlistProduct) {
            if (array_key_exists('selected', $wishlistProduct)) {
                $selectedProducts->add($wishlistProduct);
            }
        }
        $copyProductsToAnotherWishlist = new CopySelectedProductsToOtherWishlist($selectedProducts, $destinedWishlist);
        $this->commandBus->dispatch($copyProductsToAnotherWishlist);

        return new JsonResponse();
    }
}
