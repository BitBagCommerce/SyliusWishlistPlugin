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
use Sylius\WishlistPlugin\Resolver\WishlistsResolverInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

final class ListWishlistProductsAction
{
    private CartContextInterface $cartContext;

    private FormFactoryInterface $formFactory;

    private Environment $twigEnvironment;

    private WishlistCommandProcessorInterface $wishlistCommandProcessor;

    private WishlistsResolverInterface $wishlistsResolver;

    private TranslatorInterface $translator;

    private UrlGeneratorInterface $generator;

    public function __construct(
        CartContextInterface $cartContext,
        FormFactoryInterface $formFactory,
        Environment $twigEnvironment,
        WishlistCommandProcessorInterface $wishlistCommandProcessor,
        WishlistsResolverInterface $wishlistsResolver,
        TranslatorInterface $translator,
        UrlGeneratorInterface $generator,
    ) {
        $this->cartContext = $cartContext;
        $this->formFactory = $formFactory;
        $this->twigEnvironment = $twigEnvironment;
        $this->wishlistCommandProcessor = $wishlistCommandProcessor;
        $this->wishlistsResolver = $wishlistsResolver;
        $this->translator = $translator;
        $this->generator = $generator;
    }

    public function __invoke(Request $request): Response
    {
        $wishlists = $this->wishlistsResolver->resolveAndCreate();

        /** @var ?WishlistInterface $wishlist */
        $wishlist = array_shift($wishlists);

        if (null === $wishlist) {
            $homepageUrl = $this->generator->generate('sylius_shop_homepage');

            /** @var Session $session */
            $session = $request->getSession();
            $session->getFlashBag()->add('error', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.go_to_wishlist_failure'));

            return new RedirectResponse($homepageUrl);
        }

        try {
            $cart = $this->cartContext->getCart();
        } catch (CartNotFoundException $exception) {
            $cart = null;
        }

        $commandsArray = $this->wishlistCommandProcessor->createWishlistItemsCollection($wishlist->getWishlistProducts());

        $form = $this->formFactory->create(WishlistCollectionType::class, ['items' => $commandsArray], [
            'cart' => $cart,
        ]);

        return new Response(
            $this->twigEnvironment->render('@BitBagSyliusWishlistPlugin/WishlistDetails/index.html.twig', [
                'wishlist' => $wishlist,
                'form' => $form->createView(),
            ]),
        );
    }
}
