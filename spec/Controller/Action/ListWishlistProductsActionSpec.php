<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Controller\Action\ListWishlistProductsAction;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Form\Type\WishlistCollectionType;
use BitBag\SyliusWishlistPlugin\Processor\WishlistCommandProcessorInterface;
use BitBag\SyliusWishlistPlugin\Resolver\WishlistsResolverInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

final class ListWishlistProductsActionSpec extends ObjectBehavior
{
    public function let(
        CartContextInterface $cartContext,
        FormFactoryInterface $formFactory,
        Environment $twigEnvironment,
        WishlistCommandProcessorInterface $wishlistCommandProcessor,
        WishlistsResolverInterface $wishlistsResolver,
        TranslatorInterface $translator,
        UrlGeneratorInterface $generator,
    ): void {
        $this->beConstructedWith(
            $cartContext,
            $formFactory,
            $twigEnvironment,
            $wishlistCommandProcessor,
            $wishlistsResolver,
            $translator,
            $generator,
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ListWishlistProductsAction::class);
    }

    public function it_lists_wishlist_items(
        WishlistsResolverInterface $wishlistsResolver,
        WishlistInterface $wishlist,
        WishlistInterface $wishlist2,
        Request $request,
        CartContextInterface $cartContext,
        OrderInterface $cart,
        Collection $wishlistProducts,
        FormFactoryInterface $formFactory,
        FormInterface $form,
        FormView $formView,
        Environment $twigEnvironment,
        WishlistCommandProcessorInterface $wishlistCommandProcessor,
        ArrayCollection $commandsArray,
    ): void {
        $wishlistsResolver->resolveAndCreate()
            ->willReturn([
                $wishlist,
                $wishlist2,
            ]);
        $cartContext->getCart()->willReturn($cart);
        $wishlist->getWishlistProducts()->willReturn($wishlistProducts);

        $wishlistCommandProcessor->createWishlistItemsCollection($wishlistProducts)->willReturn($commandsArray);

        $formFactory
            ->create(
                WishlistCollectionType::class,
                ['items' => $commandsArray],
                ['cart' => $cart],
            )
            ->willReturn($form);

        $form->createView()->willReturn($formView);
        $twigEnvironment
            ->render(
                '@BitBagSyliusWishlistPlugin/shop/wishlist/index.html.twig',
                [
                    'wishlist' => $wishlist,
                    'form' => $formView,
                ],
            )
            ->willReturn('CONTENT');

        $this->__invoke($request)->shouldHaveType(Response::class);
    }
}
