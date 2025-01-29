<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Action;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use BitBag\SyliusWishlistPlugin\Exception\WishlistNotFoundException;
use BitBag\SyliusWishlistPlugin\Factory\WishlistProductFactoryInterface;
use BitBag\SyliusWishlistPlugin\Resolver\WishlistsResolverInterface;
use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class AddProductToWishlistAction
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private WishlistProductFactoryInterface $wishlistProductFactory,
        private RequestStack $requestStack,
        private TranslatorInterface $translator,
        private WishlistsResolverInterface $wishlistsResolver,
        private ObjectManager $wishlistManager,
        private ChannelContextInterface $channelContext,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        /** @var ProductInterface|null $product */
        $product = $this->productRepository->find($request->get('productId'));

        if (null === $product) {
            throw new NotFoundHttpException();
        }

        $wishlists = $this->wishlistsResolver->resolveAndCreate();

        /** @var ?WishlistInterface $wishlist */
        $wishlist = array_shift($wishlists);

        if (null === $wishlist) {
            throw new WishlistNotFoundException(
                $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.wishlist_not_found'),
            );
        }

        try {
            $channel = $this->channelContext->getChannel();
        } catch (ChannelNotFoundException $exception) {
            $channel = null;
        }

        /** @var ?ChannelInterface $wishlistChannel */
        $wishlistChannel = $wishlist->getChannel();

        if (null === $wishlistChannel) {
            throw new ChannelNotFoundException();
        }

        if (null !== $channel && $wishlistChannel->getId() !== $channel->getId()) {
            throw new WishlistNotFoundException(
                $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.wishlist_for_channel_not_found'),
            );
        }

        $wishlistProduct = $this->wishlistProductFactory->createForWishlistAndProduct($wishlist, $product);

        $wishlist->addWishlistProduct($wishlistProduct);

        $this->wishlistManager->flush();

        /** @var Session $session */
        $session = $this->requestStack->getSession();

        $session->getFlashBag()->add('success', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.added_wishlist_item'));

        $referer = $request->headers->get('referer');
        $refererPathInfo = Request::create((string) $referer)->getPathInfo();

        return new RedirectResponse($refererPathInfo);
    }
}
