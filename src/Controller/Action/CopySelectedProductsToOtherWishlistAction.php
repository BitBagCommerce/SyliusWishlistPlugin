<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Checker\WishlistAccessCheckerInterface;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\CopySelectedProductsToOtherWishlist;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CopySelectedProductsToOtherWishlistAction
{
    private MessageBusInterface $commandBus;

    private RequestStack $requestStack;

    private TranslatorInterface $translator;

    private UrlGeneratorInterface $urlGenerator;

    private WishlistAccessCheckerInterface $wishlistAccessChecker;

    public function __construct(
        MessageBusInterface $commandBus,
        RequestStack $requestStack,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator,
        WishlistAccessCheckerInterface $wishlistAccessChecker,
        ) {
        $this->commandBus = $commandBus;
        $this->requestStack = $requestStack;
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
        $this->wishlistAccessChecker = $wishlistAccessChecker;
    }

    public function __invoke(Request $request): Response
    {
        $destinedWishlist = $request->attributes->getInt('destinedWishlistId');

        $wishlist = $this->wishlistAccessChecker->resolveWishlist($destinedWishlist);

        if (false === $wishlist instanceof WishlistInterface) {
            /** @var Session $session */
            $session = $this->requestStack->getSession();
            $session->getFlashBag()->add('info', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.you_have_no_access_to_that_wishlist'));

            return new RedirectResponse($this->urlGenerator->generate('bitbag_sylius_wishlist_plugin_shop_wishlist_list_wishlists'));
        }

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
            $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.copied_selected_wishlist_items')
        );

        return new JsonResponse();
    }
}
