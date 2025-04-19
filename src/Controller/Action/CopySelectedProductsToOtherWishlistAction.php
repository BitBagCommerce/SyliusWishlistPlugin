<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\WishlistPlugin\Controller\Action;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\WishlistPlugin\Command\Wishlist\CopySelectedProductsToOtherWishlist;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CopySelectedProductsToOtherWishlistAction
{
    public function __construct(
        private MessageBusInterface $commandBus,
        private RequestStack $requestStack,
        private TranslatorInterface $translator,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $destinedWishlist = $request->attributes->getInt('destinedWishlistId');
        $wishlistProducts = new ArrayCollection((array) $request->get('wishlist_collection')['items']);
        $selectedProducts = new ArrayCollection();

        foreach ($wishlistProducts as $wishlistProduct) {
            if (array_key_exists('selected', $wishlistProduct)) {
                $selectedProducts->add($wishlistProduct);
            }
        }
        $copyProductsToAnotherWishlist = new CopySelectedProductsToOtherWishlist($selectedProducts, $destinedWishlist);
        $this->commandBus->dispatch($copyProductsToAnotherWishlist);

        /** @var Session $session */
        $session = $this->requestStack->getSession();

        $session->getFlashBag()->add(
            'success',
            $this->translator->trans('sylius_wishlist_plugin.ui.copied_selected_wishlist_items'),
        );

        return new JsonResponse();
    }
}
