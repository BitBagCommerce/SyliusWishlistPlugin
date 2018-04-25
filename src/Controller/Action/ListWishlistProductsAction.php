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
use BitBag\SyliusWishlistPlugin\Form\Type\AddProductsToCartType;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

final class ListWishlistProductsAction
{
    /** @var WishlistContextInterface */
    private $wishlistContext;

    /** @var CartContextInterface */
    private $cartContext;

    /** @var FactoryInterface */
    private $orderItemFactory;

    /** @var OrderModifierInterface */
    private $orderItemQuantityModifier;

    /** @var FormFactoryInterface */
    private $formFactory;

    /** @var OrderModifierInterface */
    private $orderModifier;

    /** @var EntityManagerInterface */
    private $cartManager;

    /** @var FlashBagInterface */
    private $flashBag;

    /** @var EngineInterface */
    private $templatingEngine;

    public function __construct(
        WishlistContextInterface $wishlistContext,
        CartContextInterface $cartContext,
        FactoryInterface $orderItemFactory,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        FormFactoryInterface $formFactory,
        OrderModifierInterface $orderModifier,
        EntityManagerInterface $cartManager,
        FlashBagInterface $flashBag,
        EngineInterface $templatingEngine
    )
    {
        $this->wishlistContext = $wishlistContext;
        $this->cartContext = $cartContext;
        $this->orderItemQuantityModifier = $orderItemQuantityModifier;
        $this->orderItemFactory = $orderItemFactory;
        $this->formFactory = $formFactory;
        $this->orderModifier = $orderModifier;
        $this->flashBag = $flashBag;
        $this->templatingEngine = $templatingEngine;
        $this->cartManager = $cartManager;
    }

    public function __invoke(Request $request): Response
    {
        $wishlist = $this->wishlistContext->getWishlist($request);
        $cart = $this->cartContext->getCart();
        /** @var OrderItemInterface $orderItem */
        $cartItem = $this->orderItemFactory->createNew();
        $this->orderItemQuantityModifier->modify($cartItem, 1);

        $form = $this->formFactory->create(AddProductsToCartType::class, null, [
            'cart' => $cart,
            'cartItem' => $cartItem,
            'products' => $wishlist->getProducts(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleCartItems($form);
        }

        return $this->templatingEngine->renderResponse('@BitBagSyliusWishlistPlugin/wishlist.html.twig', [
            'wishlist' => $wishlist,
            'form' => $form->createView(),
        ]);
    }

    private function handleCartItems(FormInterface $form): void
    {
        /** @var AddToCartCommandInterface $command */
        foreach ($form->getData() as $command) {
            if (0 < $command->getCartItem()->getQuantity()) {
                $this->orderModifier->addToOrder($command->getCart(), $command->getCartItem());
                $this->cartManager->persist($command->getCart());
            }
        }

        $this->cartManager->flush();
    }
}
