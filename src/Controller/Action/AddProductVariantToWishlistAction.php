<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Context\WishlistContextInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistProductFactoryInterface;
use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AddProductVariantToWishlistAction
{
    private TokenStorageInterface $tokenStorage;

    private ProductVariantRepositoryInterface $productVariantRepository;

    private WishlistContextInterface $wishlistContext;

    private WishlistProductFactoryInterface $wishlistProductFactory;

    private ObjectManager $wishlistManager;

    private FlashBagInterface $flashBag;

    private TranslatorInterface $translator;

    private UrlGeneratorInterface $urlGenerator;

    private string $wishlistCookieToken;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        ProductVariantRepositoryInterface $productVariantRepository,
        WishlistContextInterface $wishlistContext,
        WishlistProductFactoryInterface $wishlistProductFactory,
        ObjectManager $wishlistManager,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator,
        string $wishlistCookieToken
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->productVariantRepository = $productVariantRepository;
        $this->wishlistContext = $wishlistContext;
        $this->wishlistProductFactory = $wishlistProductFactory;
        $this->wishlistManager = $wishlistManager;
        $this->urlGenerator = $urlGenerator;
        $this->wishlistCookieToken = $wishlistCookieToken;
        $this->flashBag = $flashBag;
        $this->translator = $translator;
    }

    public function __invoke(Request $request): Response
    {
        $wishlist = $this->wishlistContext->getWishlist($request);

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

        if (null === $wishlist->getId()) {
            $this->wishlistManager->persist($wishlist);
        }

        $this->wishlistManager->flush();

        $response = new RedirectResponse(
            $this->urlGenerator->generate('bitbag_sylius_wishlist_plugin_shop_wishlist_list_products')
        );

        $token = $this->tokenStorage->getToken();

        if (null === $token || !is_object($token->getUser())) {
            $this->addWishlistToResponseCookie($wishlist, $response);
        }

        return $response;
    }

    private function addProductToWishlist(
        WishlistInterface $wishlist,
        ProductVariantInterface $variant,
        WishlistProductInterface $wishlistProduct
    ): void {
        if ($wishlist->hasProductVariant($variant)) {
            $message = sprintf('%s variant is already in wishlist.', $wishlistProduct->getProduct()->getName());
            $this->flashBag->add('error', $this->translator->trans($message));
        } else {
            $wishlist->addWishlistProduct($wishlistProduct);
            $this->flashBag->add('success', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.added_wishlist_item'));
        }
    }

    private function addWishlistToResponseCookie(WishlistInterface $wishlist, Response $response): void
    {
        $cookie = new Cookie($this->wishlistCookieToken, $wishlist->getToken(), strtotime('+1 year'));

        $response->headers->setCookie($cookie);
    }
}
