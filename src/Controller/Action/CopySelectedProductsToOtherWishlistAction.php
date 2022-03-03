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
use BitBag\SyliusWishlistPlugin\Exception\WishlistProductsActionFailedException;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CopySelectedProductsToOtherWishlistAction
{
    private MessageBusInterface $commandBus;

    private FlashBagInterface $flashBag;

    private TranslatorInterface $translator;

    public function __construct(
        MessageBusInterface $commandBus,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator
    ) {
        $this->commandBus = $commandBus;
        $this->flashBag = $flashBag;
        $this->translator = $translator;
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

        $this->flashBag->add(
            'success',
            $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.copied_selected_wishlist_items')
        );

        return new JsonResponse();
    }
}
