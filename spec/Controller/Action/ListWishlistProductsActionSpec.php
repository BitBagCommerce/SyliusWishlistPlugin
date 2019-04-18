<?php

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Context\WishlistContextInterface;
use BitBag\SyliusWishlistPlugin\Controller\Action\ListWishlistProductsAction;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use BitBag\SyliusWishlistPlugin\Form\Type\AddProductsToCartType;
use BitBag\SyliusWishlistPlugin\Form\Type\WishlistType;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Factory\CartItemFactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Translation\TranslatorInterface;

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
        CartItemFactoryInterface $cartItemFactory,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        EngineInterface $templatingEngine
    ): void {
        $this->beConstructedWith(
            $wishlistContext,
            $cartContext,
            $formFactory,
            $orderModifier,
            $cartManager,
            $flashBag,
            $translator,
            $cartItemFactory,
            $orderItemQuantityModifier,
            $templatingEngine
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
        Collection $products,
        FormFactoryInterface $formFactory,
        FormInterface $form,
        FormErrorIterator $formErrorIterator,
        FormView $formView,
        EngineInterface $templatingEngine,
        Response $response
    ): void {
        $wishlistContext->getWishlist($request)->willReturn($wishlist);
        $cartContext->getCart()->willReturn($cart);
        $wishlist->getProducts()->willReturn($products);
        $formFactory
            ->create(
                WishlistType::class,
                null,
                [
                    'wishlist' => $wishlist,
                ]
            )
            ->willReturn($form)
        ;
        $form->isSubmitted()->willReturn(false);
        $form->createView()->willReturn($formView);
        $form->getErrors()->willReturn($formErrorIterator);
        $templatingEngine
            ->renderResponse(
                '@BitBagSyliusWishlistPlugin/wishlist.html.twig',
                [
                    'wishlist' => $wishlist,
                    'form' => $formView,
                ]
            )
            ->willReturn($response)
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
        Collection $products,
        WishlistProductInterface $wishlistProduct,
        FormFactoryInterface $formFactory,
        FormInterface $form,
        FormErrorIterator $formErrorIterator,
        FormView $formView,
        OrderItemInterface $cartItem,
        OrderModifierInterface $orderModifier,
        EntityManagerInterface $cartManager,
        EngineInterface $templatingEngine,
        Response $response,
        SubmitButton $saveButton,
        ProductInterface $product,
        ProductVariantInterface $productVariant,
        CartItemFactoryInterface $cartItemFactory
    ): void {
        $wishlistProducts = [$wishlistProduct];

        $wishlistContext->getWishlist($request)->willReturn($wishlist);
        $cartContext->getCart()->willReturn($cart);
        $wishlist->getProducts()->willReturn($products);
        $formFactory
            ->create(
                WishlistType::class,
                null,
                [
                    'wishlist' => $wishlist,
                ]
            )
            ->willReturn($form)
        ;

        $form->isSubmitted()->willReturn(true);
        $form->isValid()->willReturn(true);
        $form->get('saveButton')->willReturn($saveButton);
        $saveButton->isClicked()->willReturn(false);

        $form->createView()->willReturn($formView);
        $form->getData()->willReturn(['wishlistProducts' => $wishlistProducts]);
        $form->getErrors()->willReturn($formErrorIterator);

        $wishlistProduct->getProduct()->willReturn($product);
        $wishlistProduct->getVariant()->willReturn($productVariant);
        $wishlistProduct->getQuantity()->willReturn(2);

        $cartItemFactory->createForProduct($product)->willReturn($cartItem);
        $cartItem->setVariant($productVariant)->shouldBeCalled();

        $templatingEngine
            ->renderResponse(
                '@BitBagSyliusWishlistPlugin/wishlist.html.twig',
                [
                    'wishlist' => $wishlist,
                    'form' => $formView,
                ]
            )->willReturn($response)
        ;

        $form->handleRequest($request)->shouldBeCalled();
        $orderModifier->addToOrder($cart, $cartItem)->shouldBeCalled();
        $cartManager->persist($cart)->shouldBeCalled();
        $cartManager->flush()->shouldBeCalled();
        $form->getErrors()->shouldBeCalled();

        $this->__invoke($request)->shouldHaveType(Response::class);
    }
}
