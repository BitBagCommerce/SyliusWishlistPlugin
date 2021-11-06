<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\CreateNewWishlist;
use BitBag\SyliusWishlistPlugin\Factory\WishlistFactoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class CreateNewWishlistAction
{
    private TokenStorageInterface $tokenStorage;

    private WishlistFactoryInterface $wishlistFactory;

    private FormFactoryInterface $formFactory;

    private MessageBusInterface $commandBus;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        WishlistFactoryInterface $wishlistFactory,
        FormFactoryInterface $formFactory,
        MessageBusInterface $commandBus
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->wishlistFactory = $wishlistFactory;
        $this->formFactory = $formFactory;
        $this->commandBus = $commandBus;
    }

    public function __invoke(Request $request): Response
    {
        $token = $this->tokenStorage;
        $wishlist = $this->wishlistFactory;
        $form = $this->formFactory;

        $createNewWishlist = new CreateNewWishlist($token, $wishlist, $form);
        $this->commandBus->dispatch($createNewWishlist);

        return new Response('success');
    }
}
