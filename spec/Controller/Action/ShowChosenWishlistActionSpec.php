<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Controller\Action\ShowChosenWishlistAction;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Form\Type\WishlistCollectionType;
use BitBag\SyliusWishlistPlugin\Processor\WishlistCommandProcessorInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class ShowChosenWishlistActionSpec extends ObjectBehavior
{
    public function let(
        WishlistRepositoryInterface $wishlistRepository,
        CartContextInterface $cartContext,
        FormFactoryInterface $formFactory,
        Environment $twigEnvironment,
        WishlistCommandProcessorInterface $wishlistCommandProcessor
    ): void
    {
        $this->beConstructedWith(
            $wishlistRepository,
            $cartContext,
            $formFactory,
            $twigEnvironment,
            $wishlistCommandProcessor
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ShowChosenWishlistAction::class);
    }

    public function it_handles_the_request_and_shows_chosen_wishlist(
        WishlistInterface $wishlist,
        WishlistRepositoryInterface $wishlistRepository,
        FormInterface $form,
        Request $request,
        Environment $twigEnvironment,
        CartContextInterface $cartContext,
        OrderInterface $cart,
        ArrayCollection $collection,
        WishlistCommandProcessorInterface $wishlistCommandProcessor,
        ArrayCollection $commandsArray,
        FormFactoryInterface $formFactory,
        FormView $formView
    ): void
    {
        $wishlistRepository->find(1)->willReturn($wishlist);

        $cartContext->getCart()->willReturn($cart);

        $wishlist->getWishlistProducts()->willReturn($collection);

        $wishlistCommandProcessor->createWishlistItemsCollection($collection)->willReturn($commandsArray);

        $formFactory->create(WishlistCollectionType::class, ['items' => $commandsArray], ['cart' => $cart])
            ->willReturn($form);

        $form->createView()->willReturn($formView);

        $twigEnvironment->render('@BitBagSyliusWishlistPlugin/WishlistDetails/index.html.twig', [
            'wishlist' => $wishlist,
            'form' => $formView
        ])->willReturn('path');

        $this->__invoke(1, $request)->shouldHaveType(Response::class);
    }
}
