<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\CreateNewWishlist;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CreateNewWishlistAction
{
    public function __construct(
        private MessageBusInterface $commandBus,
        private RequestStack $requestStack,
        private TranslatorInterface $translator,
        private ChannelContextInterface $channelContext,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $wishlistName = $request->get('create_new_wishlist')['name'];

        try {
            $channel = $this->channelContext->getChannel();
        } catch (ChannelNotFoundException $exception) {
            $channel = null;
        }

        try {
            if (null !== $channel) {
                $createNewWishlist = new CreateNewWishlist($wishlistName, $channel->getCode());
            } else {
                $createNewWishlist = new CreateNewWishlist($wishlistName, null);
            }
            $this->commandBus->dispatch($createNewWishlist);

            /** @var Session $session */
            $session = $this->requestStack->getSession();

            $session->getFlashBag()->add(
                'success',
                $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.create_new_wishlist'),
            );
        } catch (HandlerFailedException $exception) {
            /** @var Session $session */
            $session = $this->requestStack->getSession();

            $session->getFlashBag()->add(
                'error',
                $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.wishlist_name_already_exists'),
            );
        }

        return new JsonResponse();
    }
}
