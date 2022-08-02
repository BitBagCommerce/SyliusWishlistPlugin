<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\CreateNewWishlist;
use BitBag\SyliusWishlistPlugin\Exception\WishlistNameIsTakenException;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CreateNewWishlistAction
{
    private MessageBusInterface $commandBus;

    private FlashBagInterface $flashBag;

    private TranslatorInterface $translator;

    private ChannelContextInterface $channelContext;

    public function __construct(
        MessageBusInterface $commandBus,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        ChannelContextInterface $channelContext
    ) {
        $this->commandBus = $commandBus;
        $this->flashBag = $flashBag;
        $this->translator = $translator;
        $this->channelContext = $channelContext;
    }

    public function __invoke(Request $request): Response
    {
        $wishlistName = $request->request->get('create_new_wishlist')['name'];

        try {
            $channel = $this->channelContext->getChannel();
        } catch (ChannelNotFoundException $exception) {
            $channel = null;
        }

        try {
            if (null !== $channel) {
                $createNewWishlist = new CreateNewWishlist($wishlistName, $channel->getCode());
                $this->commandBus->dispatch($createNewWishlist);
            } else {
                $createNewWishlist = new CreateNewWishlist($wishlistName, null);
                $this->commandBus->dispatch($createNewWishlist);
            }

            $this->flashBag->add(
                'success',
                $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.create_new_wishlist')
            );
        } catch (HandlerFailedException $exception) {
            $this->flashBag->add(
                'error',
                $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.wishlist_name_already_exists')
            );
        }


        return new JsonResponse();
    }
}
