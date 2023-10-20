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
use BitBag\SyliusWishlistPlugin\Exception\WishlistNotFoundException;
use BitBag\SyliusWishlistPlugin\Factory\WishlistProductFactoryInterface;
use BitBag\SyliusWishlistPlugin\Resolver\WishlistCookieTokenResolverInterface;
use BitBag\SyliusWishlistPlugin\Resolver\WishlistsResolverInterface;
use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AddProductToWishlistAction
{
    private ProductRepositoryInterface $productRepository;

    private WishlistProductFactoryInterface $wishlistProductFactory;

    private RequestStack $requestStack;

    private TranslatorInterface $translator;

    private WishlistsResolverInterface $wishlistsResolver;

    private ObjectManager $wishlistManager;

    private ChannelContextInterface $channelContext;

    private WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        WishlistProductFactoryInterface $wishlistProductFactory,
        RequestStack $requestStack,
        TranslatorInterface $translator,
        WishlistsResolverInterface $wishlistsResolver,
        ObjectManager $wishlistManager,
        ChannelContextInterface $channelContext,
        WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver
    ) {
        $this->productRepository = $productRepository;
        $this->wishlistProductFactory = $wishlistProductFactory;
        $this->requestStack = $requestStack;
        $this->translator = $translator;
        $this->wishlistsResolver = $wishlistsResolver;
        $this->wishlistManager = $wishlistManager;
        $this->channelContext = $channelContext;
        $this->wishlistCookieTokenResolver = $wishlistCookieTokenResolver;
    }

    public function __invoke(Request $request): Response
    {
        /** @var ProductInterface|null $product */
        $product = $this->productRepository->find($request->get('productId'));

        if (null === $product) {
            throw new NotFoundHttpException();
        }

        $wishlists = $this->wishlistsResolver->resolveAndCreate();

        /** @var WishlistInterface $wishlist */
        $wishlist = array_shift($wishlists);

        if (null === $wishlist) {
            throw new WishlistNotFoundException(
                $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.wishlist_not_found')
            );
        }

        try {
            $channel = $this->channelContext->getChannel();
        } catch (ChannelNotFoundException $exception) {
            $channel = null;
        }

        if (null !== $channel && $wishlist->getChannel()->getId() !== $channel->getId()) {
            throw new WishlistNotFoundException(
                $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.wishlist_for_channel_not_found')
            );
        }

        /** @var WishlistProductInterface $wishlistProduct */
        $wishlistProduct = $this->wishlistProductFactory->createForWishlistAndProduct($wishlist, $product);

        $wishlist->addWishlistProduct($wishlistProduct);

        $this->wishlistManager->flush();

        /** @var Session $session */
        $session = $this->requestStack->getSession();

        $session->getFlashBag()->add('success', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.added_wishlist_item'));

        $referer = $request->headers->get('referer');
        $refererPathInfo = Request::create($referer)->getPathInfo();

        return new RedirectResponse($refererPathInfo);
    }
}
