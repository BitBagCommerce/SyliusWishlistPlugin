<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Context\WishlistContextInterface;
use BitBag\SyliusWishlistPlugin\Controller\Action\ListWishlistProductsAction;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Form\Type\AddProductsToCartType;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

final class ListWishlistProductsActionSpec extends ObjectBehavior
{
    function let(
        WishlistContextInterface $wishlistContext,
        CartContextInterface $cartContext,
        FormFactoryInterface $formFactory,
        OrderModifierInterface $orderModifier,
        EntityManagerInterface $cartManager,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        Environment $twigEnvironment
    ): void {
        $this->beConstructedWith(
            $wishlistContext,
            $cartContext,
            $formFactory,
            $orderModifier,
            $cartManager,
            $flashBag,
            $translator,
            $twigEnvironment
        );
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(ListWishlistProductsAction::class);
    }

    function it_lists_wishlist_items(
        WishlistContextInterface $wishlistContext,
        Request $request,
        WishlistInterface $wishlist,
        CartContextInterface $cartContext,
        OrderInterface $cart,
        Collection $wishlistProducts,
        FormFactoryInterface $formFactory,
        FormInterface $form,
        FormErrorIterator $formErrorIterator,
        FormView $formView,
        Environment $twigEnvironment,
        Response $response
    ): void {
        $wishlistContext->getWishlist($request)->willReturn($wishlist);
        $cartContext->getCart()->willReturn($cart);
        $wishlist->getWishlistProducts()->willReturn($wishlistProducts);
        $formFactory
            ->create(
                AddProductsToCartType::class,
                null,
                [
                    'cart' => $cart,
                    'wishlist_products' => $wishlistProducts,
                ]
            )
            ->willReturn($form)
        ;
        $form->isSubmitted()->willReturn(false);
        $form->createView()->willReturn($formView);
        $form->getErrors()->willReturn($formErrorIterator);
        $twigEnvironment
            ->render(
                '@BitBagSyliusWishlistPlugin/WishlistDetails/index.html.twig',
                [
                    'wishlist' => $wishlist,
                    'form' => $formView,
                ]
            )
            ->willReturn('CONTENT')
        ;

        $form->handleRequest($request)->shouldBeCalled();

        $this->__invoke($request)->shouldHaveType(Response::class);
    }

    function it_adds_wishlist_items_to_the_cart(
        WishlistContextInterface $wishlistContext,
        Request $request,
        WishlistInterface $wishlist,
        CartContextInterface $cartContext,
        OrderInterface $cart,
        Collection $wishlistProducts,
        FormFactoryInterface $formFactory,
        FormInterface $form,
        FormErrorIterator $formErrorIterator,
        FormView $formView,
        AddToCartCommandInterface $addToCartCommand,
        OrderItemInterface $cartItem,
        OrderModifierInterface $orderModifier,
        EntityManagerInterface $cartManager,
        Environment $twigEnvironment,
        Response $response
    ): void {
        $wishlistContext->getWishlist($request)->willReturn($wishlist);
        $cartContext->getCart()->willReturn($cart);
        $wishlist->getWishlistProducts()->willReturn($wishlistProducts);
        $formFactory
            ->create(
                AddProductsToCartType::class,
                null,
                [
                    'cart' => $cart,
                    'wishlist_products' => $wishlistProducts,
                ]
            )
            ->willReturn($form)
        ;
        $form->isSubmitted()->willReturn(true);
        $form->isValid()->willReturn(true);
        $form->createView()->willReturn($formView);
        $form->getData()->willReturn([$addToCartCommand]);
        $form->getErrors()->willReturn($formErrorIterator);
        $addToCartCommand->getCart()->willReturn($cartItem);
        $cartItem->getQuantity()->willReturn(1);
        $twigEnvironment
            ->render(
                '@BitBagSyliusWishlistPlugin/WishlistDetails/index.html.twig',
                [
                    'wishlist' => $wishlist,
                    'form' => $formView,
                ]
            )->willReturn('CONTENT')
        ;
        $addToCartCommand->getCart()->willReturn($cart);
        $addToCartCommand->getCartItem()->willReturn($cartItem);

        $form->handleRequest($request)->shouldBeCalled();
        $orderModifier->addToOrder($cart, $cartItem)->shouldBeCalled();
        $cartManager->persist($cart)->shouldBeCalled();
        $cartManager->flush()->shouldBeCalled();
        $form->getErrors()->shouldNotBeCalled();

        $this->__invoke($request)->shouldHaveType(Response::class);
    }
}
