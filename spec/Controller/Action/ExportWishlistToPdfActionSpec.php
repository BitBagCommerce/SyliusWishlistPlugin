<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\ExportSelectedProductsFromWishlistToPdfInterface;
use BitBag\SyliusWishlistPlugin\Context\WishlistContextInterface;
use BitBag\SyliusWishlistPlugin\Controller\Action\ExportWishlistToPdfAction;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Factory\Command\CommandFactory;
use BitBag\SyliusWishlistPlugin\Factory\Command\CommandFactoryInterface;
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
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
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
        WishlistCommandProcessorInterface $wishlistCommandProcessor,
        MessageBusInterface $messageBus,
        CommandFactoryInterface $commandFactory
    ): void {
        $this->beConstructedWith(
            $wishlistContext,
            $cartContext,
            $formFactory,
            $flashBag,
            $translator,
            $urlGenerator,
            $twigEnvironment,
            $wishlistCommandProcessor,
            $messageBus,
            $commandFactory
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
        ArrayCollection $arrayCollection,
        WishlistCommandProcessorInterface $wishlistCommandProcessor,
        Collection $wishlistProducts,
        FormErrorIterator $formErrorIterator,
        UrlGeneratorInterface $urlGenerator,
        ArrayCollection $commandsArray,
        MessageBusInterface $messageBus,
        ExportSelectedProductsFromWishlistToPdfInterface $exportSelectedProductsFromWishlistToPdf,
        CommandFactoryInterface $commandFactory,
        Environment $environment,
        Response $response,
        FormView $formView
    ): void {
        $wishlistContext->getWishlist($request)->willReturn($wishlist);
        $cartContext->getCart()->willReturn($cart);
        $wishlist->getWishlistProducts()->willReturn($wishlistProducts);
        $wishlistCommandProcessor->createAddCommandCollectionFromWishlistProducts($wishlistProducts)->willReturn($commandsArray);

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
        $form->get('items')->willReturn($form);// zmienic
        $form->getData()->willReturn($arrayCollection);

        $message = $exportSelectedProductsFromWishlistToPdf->getWrappedObject();

        $commandFactory->createFrom($arrayCollection, $request)->willReturn($message);
        $envelope = new Envelope($message,[new HandledStamp('result',MessageHandlerInterface::class)]);
        $messageBus->dispatch($message)->willReturn($envelope);

        $form->getErrors()->willReturn($formErrorIterator);

        $form->createView()->willReturn($formView);

        $this->__invoke($request)->shouldHaveType(Response::class);
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

        $wishlistCommandProcessor->createAddCommandCollectionFromWishlistProducts($wishlistProducts)->willReturn($commandsArray);

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
