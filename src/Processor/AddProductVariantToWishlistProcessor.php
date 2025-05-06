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

namespace Sylius\WishlistPlugin\Processor;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\WishlistPlugin\Entity\WishlistProductInterface;
use Sylius\WishlistPlugin\Factory\WishlistProductFactoryInterface;
use Sylius\WishlistPlugin\Repository\WishlistRepositoryInterface;
use Sylius\WishlistPlugin\Twig\WishlistExtension;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class AddProductVariantToWishlistProcessor implements AddProductVariantToWishlistProcessorInterface
{
    public function __construct(
        private Security $security,
        private WishlistExtension $wishlistExtension,
        private ChannelContextInterface $channelContext,
        private WishlistProductFactoryInterface $wishlistProductFactory,
        private RequestStack $requestStack,
        private TranslatorInterface $translator,
        private UrlGeneratorInterface $urlGenerator,
        private WishlistRepositoryInterface $wishlistRepository,
    ) {
    }

    public function process(ProductVariantInterface $productVariant, ?int $wishlistId = null): RedirectResponse
    {
        $user = $this->security->getUser();

        $wishlists = null !== $user
            ? $this->wishlistExtension->findAllByShopUserAndToken()
            : $this->wishlistExtension->findAllByAnonymousAndChannel($this->channelContext->getChannel());

        $isSingleWishlist = count($wishlists) < 2;

        /** @var ?WishlistInterface $wishlist */
        $wishlist = $isSingleWishlist ? ($wishlists[0] ?? null) : $this->wishlistRepository->find($wishlistId);

        if (null === $wishlist) {
            throw new ResourceNotFoundException();
        }

        $wishlistProduct = $this->wishlistProductFactory->createForWishlistAndVariant($wishlist, $productVariant);

        $this->addProductToWishlist($wishlist, $productVariant, $wishlistProduct);

        return new RedirectResponse(
            $this->urlGenerator->generate('sylius_wishlist_plugin_shop_locale_wishlist_show_chosen_wishlist', [
                'wishlistId' => $wishlist->getId(),
            ]),
        );
    }

    private function addProductToWishlist(
        WishlistInterface $wishlist,
        ProductVariantInterface $variant,
        WishlistProductInterface $wishlistProduct,
    ): void {
        /** @var Session $session */
        $session = $this->requestStack->getSession();

        $flashBag = $session->getFlashBag();

        if ($wishlist->hasProductVariant($variant)) {
            $flashBag->add(
                'error',
                $this->translator->trans(
                    'sylius_wishlist_plugin.ui.wishlist_has_product_variant',
                    ['%productName%' => $wishlistProduct->getProduct()->getName()],
                ),
            );

            return;
        }

        $wishlist->addWishlistProduct($wishlistProduct);
        $this->wishlistRepository->add($wishlist);
        $flashBag->add('success', $this->translator->trans('sylius_wishlist_plugin.ui.added_wishlist_item'));
    }
}
