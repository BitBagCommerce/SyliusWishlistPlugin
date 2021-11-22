<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Context\WishlistContextInterface;
use BitBag\SyliusWishlistPlugin\Controller\Action\ExportWishlistToPdfAction;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Exporter\ExporterWishlistToPdfInterface;
use BitBag\SyliusWishlistPlugin\Form\Type\WishlistCollectionType;
use BitBag\SyliusWishlistPlugin\Processor\WishlistCommandProcessorInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

final class ExportWishlistToPdfActionSpec extends ObjectBehavior
{
    function let(
        WishlistContextInterface $wishlistContext,
        CartContextInterface $cartContext,
        FormFactoryInterface $formFactory,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator,
        Environment $twigEnvironment,
        ExporterWishlistToPdfInterface $exporterWishlistToPdf,
        WishlistCommandProcessorInterface $wishlistCommandProcessor
    ): void {
        $this->beConstructedWith(
            $wishlistContext,
            $cartContext,
            $formFactory,
            $flashBag,
            $translator,
            $urlGenerator,
            $twigEnvironment,
            $exporterWishlistToPdf,
            $wishlistCommandProcessor
        );
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(ExportWishlistToPdfAction::class);
    }

    function it_renders_header_template(
        WishlistContextInterface $wishlistContext,
        Request $request,
        WishlistInterface $wishlist,
        CartContextInterface $cartContext,
        OrderInterface $cart,
        FormFactoryInterface $formFactory,
        FormInterface $form,
        FormErrorIterator $formErrorIterator,
        ExporterWishlistToPdfInterface $exporterWishlistToPdf,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator,
        ArrayCollection $arrayCollection,
        WishlistCommandProcessorInterface $wishlistCommandProcessor,
        Collection $wishlistProducts,
        ArrayCollection $commandsArray
    ): void {
        $wishlistContext->getWishlist($request)->willReturn($wishlist);
        $cartContext->getCart()->willReturn($cart);
        $wishlist->getWishlistProducts()->willReturn($wishlistProducts);

        $wishlistCommandProcessor->createFromWishlistProducts($wishlistProducts)->willReturn($commandsArray);

        $formFactory
            ->create(
                WishlistCollectionType::class,
                [
                    'items' => $commandsArray,
                ],
                [
                    'cart' => $cart,
                ]
            )
            ->willReturn($form);

        $form->handleRequest($request)->shouldBeCalled();
        $form->isSubmitted()->willReturn(true);
        $form->isValid()->willReturn(true);
        $form->get('items')->willReturn($form);
        $form->getData()->willReturn($arrayCollection);
        $form->getErrors()->willReturn($formErrorIterator);

        $exporterWishlistToPdf->handleWishlistItemsToGeneratePdf($arrayCollection, $request)->willReturn(false);
        $translator->trans('bitbag_sylius_wishlist_plugin.ui.select_products')->willReturn('Select at least one item.');
        $flashBag->add('error', 'Select at least one item.');
        $urlGenerator
            ->generate('bitbag_sylius_wishlist_plugin_shop_wishlist_list_products')
            ->willReturn('Content');

        $this->__invoke($request)->shouldHaveType(RedirectResponse::class);
    }

    function it_renders_template_with_error(
        WishlistContextInterface $wishlistContext,
        Request $request,
        WishlistInterface $wishlist,
        CartContextInterface $cartContext,
        OrderInterface $cart,
        FormFactoryInterface $formFactory,
        FormInterface $form,
        FormErrorIterator $formErrorIterator,
        FormView $formView,
        Environment $twigEnvironment,
        WishlistCommandProcessorInterface $wishlistCommandProcessor,
        Collection $wishlistProducts,
        ArrayCollection $commandsArray
    ): void {
        $wishlistContext->getWishlist($request)->willReturn($wishlist);
        $cartContext->getCart()->willReturn($cart);
        $wishlist->getWishlistProducts()->willReturn($wishlistProducts);

        $wishlistCommandProcessor->createFromWishlistProducts($wishlistProducts)->willReturn($commandsArray);

        $formFactory
            ->create(
                WishlistCollectionType::class,
                [
                    'items' => $commandsArray,
                ],
                [
                    'cart' => $cart,
                ]
            )
            ->willReturn($form);

        $form->handleRequest($request)->shouldBeCalled();
        $form->isSubmitted()->willReturn(false);
        $form->isValid()->willReturn(false);
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
            ->willReturn('CONTENT');

        $form->handleRequest($request)->shouldBeCalled();

        $this->__invoke($request)->shouldHaveType(Response::class);
    }
}
