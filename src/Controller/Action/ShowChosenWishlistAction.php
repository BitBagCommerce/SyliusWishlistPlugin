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
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

final class ShowChosenWishlistAction
{
    private WishlistRepositoryInterface $wishlistRepository;

    private CartContextInterface $cartContext;

    private FormFactoryInterface $formFactory;

    private Environment $twigEnvironment;

    private WishlistCommandProcessorInterface $wishlistCommandProcessor;

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        WishlistRepositoryInterface $wishlistRepository,
        CartContextInterface $cartContext,
        FormFactoryInterface $formFactory,
        Environment $twigEnvironment,
        WishlistCommandProcessorInterface $wishlistCommandProcessor,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->wishlistRepository = $wishlistRepository;
        $this->cartContext = $cartContext;
        $this->formFactory = $formFactory;
        $this->twigEnvironment = $twigEnvironment;
        $this->wishlistCommandProcessor = $wishlistCommandProcessor;
        $this->urlGenerator = $urlGenerator;
    }

    public function __invoke(string $wishlistId, Request $request): Response
    {
        /** @var WishlistInterface $wishlist */
        $wishlist = $this->wishlistRepository->find($wishlistId);

        if ($wishlist instanceof WishlistInterface) {
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
