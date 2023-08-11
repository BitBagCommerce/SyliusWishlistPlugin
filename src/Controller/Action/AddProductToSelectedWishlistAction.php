<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Checker\WishlistAccessCheckerInterface;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddProductToSelectedWishlist;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Exception\ProductNotFoundException;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AddProductToSelectedWishlistAction
{
    private WishlistAccessCheckerInterface $wishlistAccessChecker;

    private ProductRepositoryInterface $productRepository;

    private RequestStack $requestStack;

    private TranslatorInterface $translator;

    private UrlGeneratorInterface $urlGenerator;

    private MessageBusInterface $commandBus;

    public function __construct(
        WishlistAccessCheckerInterface $wishlistAccessChecker,
        ProductRepositoryInterface $productRepository,
        RequestStack $requestStack,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator,
        MessageBusInterface $commandBus
    ) {
        $this->wishlistAccessChecker = $wishlistAccessChecker;
        $this->productRepository = $productRepository;
        $this->requestStack = $requestStack;
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
        $this->commandBus = $commandBus;
    }

    public function __invoke(int $wishlistId, int $productId): Response
    {
        $wishlist = $this->wishlistAccessChecker->resolveWishlist($wishlistId);

        if (false === $wishlist instanceof WishlistInterface) {
            /** @var Session $session */
            $session = $this->requestStack->getSession();
            $session->getFlashBag()->add('info', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.you_have_no_access_to_that_wishlist'));

            return new RedirectResponse($this->urlGenerator->generate('bitbag_sylius_wishlist_plugin_shop_wishlist_list_wishlists'));
        }

        /** @var ProductInterface $product */
        $product = $this->productRepository->find($productId);

        if (null === $product) {
            throw new ProductNotFoundException(
                sprintf('The Product %s does not exist', $productId)
            );
        }

        $addProductToSelectedWishlist = new AddProductToSelectedWishlist($wishlist, $product);
        $this->commandBus->dispatch($addProductToSelectedWishlist);

        /** @var Session $session */
        $session = $this->requestStack->getSession();

        $session->getFlashBag()->add('success', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.added_wishlist_item'));

        return new RedirectResponse(
            $this->urlGenerator->generate('bitbag_sylius_wishlist_plugin_shop_wishlist_show_chosen_wishlist', [
                'wishlistId' => $wishlistId,
            ])
        );
    }
}
