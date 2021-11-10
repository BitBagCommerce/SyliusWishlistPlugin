<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddWishlistProduct;
use BitBag\SyliusWishlistPlugin\Context\WishlistContextInterface;
use BitBag\SyliusWishlistPlugin\Form\Type\WishlistCollectionType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

final class ListWishlistProductsAction
{
    private WishlistContextInterface $wishlistContext;

    private CartContextInterface $cartContext;

    private FormFactoryInterface $formFactory;

    private OrderModifierInterface $orderModifier;

    private EntityManagerInterface $cartManager;

    private FlashBagInterface $flashBag;

    private TranslatorInterface $translator;

    private Environment $twigEnvironment;

    public function __construct(
        WishlistContextInterface $wishlistContext,
        CartContextInterface $cartContext,
        FormFactoryInterface $formFactory,
        OrderModifierInterface $orderModifier,
        EntityManagerInterface $cartManager,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        Environment $twigEnvironment
    ) {
        $this->wishlistContext = $wishlistContext;
        $this->cartContext = $cartContext;
        $this->formFactory = $formFactory;
        $this->orderModifier = $orderModifier;
        $this->flashBag = $flashBag;
        $this->twigEnvironment = $twigEnvironment;
        $this->cartManager = $cartManager;
        $this->translator = $translator;
    }

    public function __invoke(Request $request): Response
    {
        $wishlist = $this->wishlistContext->getWishlist($request);
        $cart = $this->cartContext->getCart();

        $commandsArray = new ArrayCollection();

        foreach ($wishlist->getWishlistProducts() as $wishlistProductItem) {
            $wishlistProductCommand = new AddWishlistProduct();

            $wishlistProductCommand->setWishlistProduct($wishlistProductItem);
            $commandsArray->add($wishlistProductCommand);
        }

        $form = $this->formFactory->create(WishlistCollectionType::class, ['items' => $commandsArray], [
            'cart' => $cart,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleCartItems($form->getData());

            return new Response(
                $this->twigEnvironment->render(
                    '@BitBagSyliusWishlistPlugin/WishlistDetails/index.html.twig',
                    [
                        'wishlist' => $wishlist,
                        'form' => $form->createView(),
                    ]
                )
            );
        }

        foreach ($form->getErrors() as $error) {
            $this->flashBag->add('error', $error->getMessage());
        }

        return new Response(
            $this->twigEnvironment->render(
                '@BitBagSyliusWishlistPlugin/WishlistDetails/index.html.twig',
                [
                    'wishlist' => $wishlist,
                    'form' => $form->createView(),
                ]
            )
        );
    }

    private function handleCartItems(array $wishlistProductsCommand): void
    {
        foreach ($wishlistProductsCommand as $wishlistProducts) {
            /** @var AddWishlistProduct $wishlistProduct */
            foreach ($wishlistProducts as $wishlistProduct) {
                $addToCartCommand = $wishlistProduct->getCartItem();
                $cart = $addToCartCommand->getCart();
                $cartItem = $addToCartCommand->getCartItem();

                if (0 >= $cartItem->getVariant()->getOnHand()) {
                    $message = sprintf('%s does not have sufficient stock.', $cartItem->getProductName());
                    $this->flashBag->add('error', $this->translator->trans($message));
                } elseif (0 >= $cartItem->getQuantity()) {
                    $this->flashBag->add('error', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.increase_quantity'));
                } else {
                    $this->orderModifier->addToOrder($cart, $cartItem);
                    $this->cartManager->persist($cart);

                    if (!$this->flashBag->has('success')) {
                        $this->flashBag->add('success', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.added_to_cart'));
                    }
                }
            }
        }
        $this->cartManager->flush();
    }
}
