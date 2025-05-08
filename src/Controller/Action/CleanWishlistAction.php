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

use Doctrine\ORM\EntityManagerInterface;
use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\WishlistPlugin\Repository\WishlistRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CleanWishlistAction
{
    public function __construct(
        private WishlistRepositoryInterface $wishlistRepository,
        private EntityManagerInterface $wishlistManager,
        private RequestStack $requestStack,
        private TranslatorInterface $translator,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function __invoke(int $wishlistId, Request $request): Response
    {
        /** @var WishlistInterface $wishlist */
        $wishlist = $this->wishlistRepository->find($wishlistId);

        $wishlist->clear();
        $this->wishlistManager->flush();

        /** @var Session $session */
        $session = $this->requestStack->getSession();

        $session->getFlashBag()->add('success', $this->translator->trans('sylius_wishlist_plugin.ui.cleared_wishlist'));

        return new RedirectResponse(
            $this->urlGenerator->generate('sylius_wishlist_plugin_shop_locale_wishlist_show_chosen_wishlist', [
                'wishlistId' => $wishlistId,
            ]),
        );
    }
}
