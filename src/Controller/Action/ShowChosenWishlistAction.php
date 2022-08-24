<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Form\Type\WishlistCollectionType;
use BitBag\SyliusWishlistPlugin\Processor\WishlistCommandProcessorInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use BitBag\SyliusWishlistPlugin\Resolver\WishlistCookieTokenResolverInterface;
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
    private WishlistRepositoryInterface $wishlistRepository;

    private CartContextInterface $cartContext;

    private FormFactoryInterface $formFactory;

    private Environment $twigEnvironment;

    private WishlistCommandProcessorInterface $wishlistCommandProcessor;

    private UrlGeneratorInterface $urlGenerator;

    private WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver;

    private TokenStorageInterface $tokenStorage;

    public function __construct(
        WishlistRepositoryInterface $wishlistRepository,
        CartContextInterface $cartContext,
        FormFactoryInterface $formFactory,
        Environment $twigEnvironment,
        WishlistCommandProcessorInterface $wishlistCommandProcessor,
        UrlGeneratorInterface $urlGenerator,
        WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver,
        TokenStorageInterface $tokenStorage
    ) {
        $this->wishlistRepository = $wishlistRepository;
        $this->cartContext = $cartContext;
        $this->formFactory = $formFactory;
        $this->twigEnvironment = $twigEnvironment;
        $this->wishlistCommandProcessor = $wishlistCommandProcessor;
        $this->urlGenerator = $urlGenerator;
        $this->wishlistCookieTokenResolver = $wishlistCookieTokenResolver;
        $this->tokenStorage = $tokenStorage;
    }

    public function __invoke(string $wishlistId, Request $request): Response
    {
        /** @var WishlistInterface $wishlist */
        $wishlist = $this->wishlistRepository->find((int)$wishlistId);
        $wishlistCookieToken = $this->wishlistCookieTokenResolver->resolve();
        $user = $this->tokenStorage->getToken() ? $this->tokenStorage->getToken()->getUser() : null;

        if ($wishlist instanceof WishlistInterface && $user instanceof ShopUserInterface
        or $wishlist instanceof WishlistInterface && $wishlist->getToken() === $wishlistCookieToken && $wishlist->getShopUser() === null) {
            $form = $this->createForm($wishlist);
            return new Response(
                $this->twigEnvironment->render('@BitBagSyliusWishlistPlugin/WishlistDetails/index.html.twig', [
                    'wishlist' => $wishlist,
                    'form' => $form->createView(),
                ])
            );
        }

        return new RedirectResponse($this->urlGenerator->generate("bitbag_sylius_wishlist_plugin_shop_wishlist_list_wishlists"));
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
