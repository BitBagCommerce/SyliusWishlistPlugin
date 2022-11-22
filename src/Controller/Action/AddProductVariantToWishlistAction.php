<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistProductFactoryInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AddProductVariantToWishlistAction
{
    private ProductVariantRepositoryInterface $productVariantRepository;

    private WishlistProductFactoryInterface $wishlistProductFactory;

    private RequestStack $requestStack;

    private TranslatorInterface $translator;

    private UrlGeneratorInterface $urlGenerator;

    private WishlistRepositoryInterface $wishlistRepository;

    public function __construct(
        ProductVariantRepositoryInterface $productVariantRepository,
        WishlistProductFactoryInterface $wishlistProductFactory,
        RequestStack $requestStack,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator,
        WishlistRepositoryInterface $wishlistRepository
    ) {
        $this->productVariantRepository = $productVariantRepository;
        $this->wishlistProductFactory = $wishlistProductFactory;
        $this->urlGenerator = $urlGenerator;
        $this->requestStack = $requestStack;
        $this->translator = $translator;
        $this->wishlistRepository = $wishlistRepository;
    }

    public function __invoke(int $wishlistId, Request $request): Response
    {
        $wishlist = $this->wishlistRepository->find($wishlistId);

        foreach ((array) $request->get('variantId') as $variantId) {
            /** @var ProductVariantInterface|null $variant */
            $variant = $this->productVariantRepository->find($variantId);

            if (null === $variant) {
                throw new NotFoundHttpException();
            }

            /** @var WishlistProductInterface $wishlistProduct */
            $wishlistProduct = $this->wishlistProductFactory->createForWishlistAndVariant($wishlist, $variant);

            $this->addProductToWishlist($wishlist, $variant, $wishlistProduct);
        }

        return new RedirectResponse(
            $this->urlGenerator->generate('bitbag_sylius_wishlist_plugin_shop_wishlist_show_chosen_wishlist', [
                'wishlistId' => $wishlistId,
            ])
        );
    }

    private function addProductToWishlist(
        WishlistInterface $wishlist,
        ProductVariantInterface $variant,
        WishlistProductInterface $wishlistProduct
    ): void {
        /** @var Session $session */
        $session = $this->requestStack->getSession();

        $flashBag = $session->getFlashBag();

        if ($wishlist->hasProductVariant($variant)) {
            $message = sprintf('%s variant is already in wishlist.', $wishlistProduct->getProduct()->getName());
            $flashBag->add('error', $this->translator->trans($message));

            return;
        }

        $wishlist->addWishlistProduct($wishlistProduct);
        $this->wishlistRepository->add($wishlist);
        $flashBag->add('success', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.added_wishlist_item'));
    }
}
