<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\CreateNewWishlist;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

final class CreateNewWishlistAction
{
    private MessageBusInterface $commandBus;

    private FlashBagInterface $flashBag;

    private TranslatorInterface $translator;

    public function __construct(
        MessageBusInterface $commandBus,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator
    ) {
        $this->commandBus = $commandBus;
        $this->flashBag = $flashBag;
        $this->translator = $translator;
    }

    public function __invoke(Request $request): Response
    {
        $wishlistName = $request->request->get('create_new_wishlist')['name'];
        $createNewWishlist = new CreateNewWishlist($wishlistName);
        $this->commandBus->dispatch($createNewWishlist);

        $this->flashBag->add(
            'success',
            $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.create_new_wishlist')
        );

        return new JsonResponse();
    }
}
