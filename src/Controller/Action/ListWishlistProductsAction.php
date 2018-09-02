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
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
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
        EngineInterface $templatingEngine
    ) {
        $this->wishlistContext = $wishlistContext;
        $this->cartContext = $cartContext;
        $this->formFactory = $formFactory;
        $this->orderModifier = $orderModifier;
        $this->flashBag = $flashBag;
        $this->templatingEngine = $templatingEngine;
        $this->cartManager = $cartManager;
        $this->translator = $translator;
    }

    public function __invoke(Request $request): Response
    {
        $wishlist = $this->wishlistContext->getWishlist($request);
        $cart = $this->cartContext->getCart();

        $form = $this->formFactory->create(AddProductsToCartType::class, null, [
            'cart' => $cart,
            'products' => $wishlist->getProducts(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleCartItems($form);

            $this->flashBag->add('success', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.added_to_cart'));
        }

        return $this->templatingEngine->renderResponse('@BitBagSyliusWishlistPlugin/Resources/views/wishlist.html.twig', [
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
