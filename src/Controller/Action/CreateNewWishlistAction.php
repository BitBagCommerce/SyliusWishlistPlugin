<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\CreateNewWishlist;
use BitBag\SyliusWishlistPlugin\Form\Type\CreateNewWishlistType;
use BitBag\SyliusWishlistPlugin\Factory\WishlistFactoryInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

final class CreateNewWishlistAction
{
    private TokenStorageInterface $tokenStorage;

    private WishlistFactoryInterface $wishlistFactory;

    private FormFactoryInterface $formFactory;

    private MessageBusInterface $commandBus;

    private Environment $twigEnvironment;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        WishlistFactoryInterface $wishlistFactory,
        FormFactoryInterface $formFactory,
        MessageBusInterface $commandBus,
        Environment $twigEnvironment
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->wishlistFactory = $wishlistFactory;
        $this->formFactory = $formFactory;
        $this->commandBus = $commandBus;
        $this->twigEnvironment = $twigEnvironment;
    }

    public function __invoke(Request $request, FlashBagInterface $flashBag, TranslatorInterface $translator, ObjectManager $wishlistManager, UrlGeneratorInterface $urlGenerator): Response
    {
        $token = $this->tokenStorage;
        $wishlist = $this->wishlistFactory;

        $createNewWishlist = new CreateNewWishlist($token, $wishlist);
        $this->commandBus->dispatch($createNewWishlist);

        $form = $this->formFactory->create(CreateNewWishlistType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $wishlist = $form->getData();

            $flashBag->add('success', $translator->trans('bitbag_sylius_wishlist_plugin.ui.create_new_wishlist'));

            return new RedirectResponse($urlGenerator->generate('bitbag_sylius_wishlist_plugin_shop_wishlist_list_wishlists'));
        }

        return new Response(
            $this->twigEnvironment->render('@BitBagSyliusWishlistPlugin/CreateWishlist/index.html.twig', [
                'wishlist' => $wishlist,
                'form' => $form->createView(),
            ])
        );
    }
}
