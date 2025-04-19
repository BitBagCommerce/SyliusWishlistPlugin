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

use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\WishlistPlugin\Form\Type\WishlistCollectionType;
use Sylius\WishlistPlugin\Processor\WishlistCommandProcessorInterface;
use Sylius\WishlistPlugin\Repository\WishlistRepositoryInterface;
use Sylius\WishlistPlugin\Resolver\TokenUserResolverInterface;
use Sylius\WishlistPlugin\Resolver\WishlistCookieTokenResolverInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Environment;

final class ShowChosenWishlistAction
{
    public function __construct(
        private WishlistRepositoryInterface $wishlistRepository,
        private CartContextInterface $cartContext,
        private FormFactoryInterface $formFactory,
        private Environment $twigEnvironment,
        private WishlistCommandProcessorInterface $wishlistCommandProcessor,
        private UrlGeneratorInterface $urlGenerator,
        private WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver,
        private TokenStorageInterface $tokenStorage,
        private TokenUserResolverInterface $tokenUserResolver,
    ) {
    }

    public function __invoke(string $wishlistId, Request $request): Response
    {
        $token = $this->tokenStorage->getToken();

        /** @var ?WishlistInterface $wishlist */
        $wishlist = $this->wishlistRepository->find((int) $wishlistId);
        $wishlistCookieToken = $this->wishlistCookieTokenResolver->resolve();

        if (null === $wishlist) {
            return new RedirectResponse($this->urlGenerator->generate('bitbag_sylius_wishlist_plugin_shop_locale_wishlist_list_wishlists'));
        }

        $user = $this->tokenUserResolver->resolve($token);

        /** @var ?ShopUserInterface $wishlistUser */
        $wishlistUser = $wishlist->getShopUser();

        if ($user !== $wishlistUser) {
            return new RedirectResponse($this->urlGenerator->generate('bitbag_sylius_wishlist_plugin_shop_locale_wishlist_list_wishlists'));
        }

        if ($user instanceof ShopUserInterface ||
            $wishlist->getToken() === $wishlistCookieToken && null === $wishlistUser
        ) {
            $form = $this->createForm($wishlist);

            return new Response(
                $this->twigEnvironment->render('@BitBagSyliusWishlistPlugin/WishlistDetails/index.html.twig', [
                    'wishlist' => $wishlist,
                    'form' => $form->createView(),
                ]),
            );
        }

        return new RedirectResponse($this->urlGenerator->generate('bitbag_sylius_wishlist_plugin_shop_locale_wishlist_list_wishlists'));
    }

    private function createForm(WishlistInterface $wishlist): FormInterface
    {
        $cart = $this->cartContext->getCart();

        $commandsArray = $this->wishlistCommandProcessor->createWishlistItemsCollection($wishlist->getWishlistProducts());

        return $this->formFactory->create(WishlistCollectionType::class, ['items' => $commandsArray], [
            'cart' => $cart,
        ]);
    }
}
