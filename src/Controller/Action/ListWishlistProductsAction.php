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
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use BitBag\SyliusWishlistPlugin\Form\Type\AddProductsToCartType;
use BitBag\SyliusWishlistPlugin\Form\Type\WishlistType;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Factory\CartItemFactoryInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Translation\TranslatorInterface;

final class ListWishlistProductsAction
{
    /** @var WishlistContextInterface */
    private $wishlistContext;

    /** @var CartContextInterface */
    private $cartContext;

    /** @var FormFactoryInterface */
    private $formFactory;

    /** @var OrderModifierInterface */
    private $orderModifier;

    /** @var EntityManagerInterface */
    private $cartManager;

    /** @var FlashBagInterface */
    private $flashBag;

    /** @var TranslatorInterface */
    private $translator;

    /** @var CartItemFactoryInterface */
    private $cartItemFactory;

    /** @var OrderItemQuantityModifierInterface */
    private $orderItemQuantityModifier;

    /** @var EngineInterface */
    private $templatingEngine;

    public function __construct(
        WishlistContextInterface $wishlistContext,
        CartContextInterface $cartContext,
        FormFactoryInterface $formFactory,
        OrderModifierInterface $orderModifier,
        EntityManagerInterface $cartManager,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        CartItemFactoryInterface $cartItemFactory,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        EngineInterface $templatingEngine
    ) {
        $this->wishlistContext = $wishlistContext;
        $this->cartContext = $cartContext;
        $this->formFactory = $formFactory;
        $this->orderModifier = $orderModifier;
        $this->flashBag = $flashBag;
        $this->templatingEngine = $templatingEngine;
        $this->cartManager = $cartManager;
        $this->cartItemFactory = $cartItemFactory;
        $this->orderItemQuantityModifier = $orderItemQuantityModifier;
        $this->translator = $translator;
    }

    public function __invoke(Request $request): Response
    {
        $wishlist = $this->wishlistContext->getWishlist($request);

        $form = $this->formFactory->create(WishlistType::class, null, [
            'wishlist' => $wishlist,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var SubmitButton $saveButton */
            $saveButton = $form->get('saveButton');

            if (!$saveButton->isClicked()) {

                $this->handleCartItems($form);

                $this->flashBag->add('success', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.added_to_cart'));
            } else {
                $items = $form->getData()['wishlistProducts'];

                $wishlist->setWishlistProducts($items);

                $this->cartManager->persist($wishlist);
                $this->cartManager->flush();
            }
        }

        foreach ($form->getErrors() as $error) {
            $this->flashBag->add('error', $error->getMessage());
        }

        return $this->templatingEngine->renderResponse('@BitBagSyliusWishlistPlugin/wishlist.html.twig', [
            'wishlist' => $wishlist,
            'form' => $form->createView(),
        ]);
    }

    private function handleCartItems(FormInterface $form): void
    {
        $cart = $this->cartContext->getCart();

        /** @var WishlistProductInterface $wishlistProduct */
        foreach ($form->getData()['wishlistProducts'] as $wishlistProduct) {
            $cartItem = $this->cartItemFactory->createForProduct($wishlistProduct->getProduct());
            $cartItem->setVariant($wishlistProduct->getVariant());

            $this->orderItemQuantityModifier->modify($cartItem, $wishlistProduct->getQuantity());

            if (0 < $wishlistProduct->getQuantity()) {
                $this->orderModifier->addToOrder($cart, $cartItem);
            }
        }

        $this->cartManager->persist($cart);
        $this->cartManager->flush();
    }
}
