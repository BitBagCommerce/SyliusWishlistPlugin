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
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

final class AddProductToSelectedWishlistAction
{
    private WishlistContextInterface $wishlistContext;

    private WishlistRepositoryInterface $wishlistRepository;

    private EntityManagerInterface $wishlistManager;

    private FlashBagInterface $flashBag;

    private TranslatorInterface $translator;

    private UrlGeneratorInterface $urlGenerator;

    private Environment $twigEnvironment;

    private ProductRepositoryInterface $productRepository;

    private WishlistProductFactoryInterface $wishlistProductFactory;

    public function __construct(
        WishlistContextInterface $wishlistContext,
        WishlistRepositoryInterface $wishlistRepository,
        EntityManagerInterface $wishlistManager,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator,
        Environment $twigEnvironment,
        ProductRepositoryInterface $productRepository,
        WishlistProductFactoryInterface $wishlistProductFactory
    ) {
        $this->wishlistContext = $wishlistContext;
        $this->wishlistRepository = $wishlistRepository;
        $this->wishlistManager = $wishlistManager;
        $this->urlGenerator = $urlGenerator;
        $this->flashBag = $flashBag;
        $this->translator = $translator;
        $this->twigEnvironment = $twigEnvironment;
        $this->productRepository = $productRepository;
        $this->wishlistProductFactory = $wishlistProductFactory;
    }

    public function __invoke(int $wishlistId,Request $request): Response
    {
        /** @var ProductInterface|null $product */
        $product = $this->productRepository->find($request->get('productId'));

        if (null === $product) {
            throw new NotFoundHttpException();
        }

        /** @var WishlistInterface $wishlist */
        $wishlist = $this->wishlistRepository->find($wishlistId);

        /** @var WishlistProductInterface $wishlistProduct */
        $wishlistProduct = $this->wishlistProductFactory->createForWishlistAndProduct($wishlist, $product);

        $wishlist->addWishlistProduct($wishlistProduct);
        $this->wishlistManager->flush();

        $this->flashBag->add('success', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.added_wishlist_item'));
        return new RedirectResponse($this->urlGenerator->generate('bitbag_sylius_wishlist_plugin_shop_wishlist_list_wishlists'));

    }

}