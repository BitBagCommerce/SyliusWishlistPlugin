<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Context\WishlistContextInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistProductFactoryInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\TranslatorInterface;

final class AddProductToWishlistAction
{
    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var WishlistContextInterface */
    private $wishlistContext;

    /** @var WishlistProductFactoryInterface */
    private $wishlistProductFactory;

    /** @var ObjectManager */
    private $wishlistManager;

    /** @var FlashBagInterface */
    private $flashBag;

    /** @var TranslatorInterface */
    private $translator;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var string */
    private $wishlistCookieToken;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        WishlistContextInterface $wishlistContext,
        WishlistProductFactoryInterface $wishlistProductFactory,
        ObjectManager $wishlistManager,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator,
        string $wishlistCookieToken
    ) {
        $this->productRepository = $productRepository;
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
        /** @var ProductInterface|null $product */
        $product = $this->productRepository->find($request->get('productId'));

        if (null === $product) {
            throw new NotFoundHttpException();
        }

        $wishlist = $this->wishlistContext->getWishlist($request);

        /** @var WishlistProductInterface $wishlistProduct */
        $wishlistProduct = $this->wishlistProductFactory->createForWishlistAndProduct($wishlist, $product);

        $wishlist->addWishlistProduct($wishlistProduct);

        if (null === $wishlist->getId()) {
            $this->wishlistManager->persist($wishlist);
        }

        $this->wishlistManager->flush();

        $this->flashBag->add('success', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.added_wishlist_item'));

        $response = new RedirectResponse($this->urlGenerator->generate('bitbag_sylius_wishlist_plugin_shop_wishlist_list_products'));

        $this->addWishlistToResponseCookie($wishlist, $response, $request->getUser());

        return $response;
    }

    private function addWishlistToResponseCookie(WishlistInterface $wishlist, Response $response, ?string $user): void
    {
        if (null !== $user) {
            return;
        }

        $cookie = new Cookie($this->wishlistCookieToken, $wishlist->getToken(), strtotime('+1 year'));

        $response->headers->setCookie($cookie);
    }
}
