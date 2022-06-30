<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\CreateNewWishlist;
use BitBag\SyliusWishlistPlugin\Controller\Action\CreateNewWishlistAction;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CreateNewWishlistActionSpec extends ObjectBehavior
{
    public function let(
        MessageBusInterface $commandBus,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        ChannelContextInterface $channelContext
    ): void
    {
        $this->beConstructedWith(
            $commandBus,
            $flashBag,
            $translator,
            $channelContext
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(CreateNewWishlistAction::class);
    }

    public function it_handles_the_request_and_creates_new_wishlist(
        Request $request,
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        MessageBusInterface $commandBus,
        TranslatorInterface $translator,
        FlashBagInterface $flashBag,
        ParameterBag $bag
    ): void
    {
        $request->request = $bag;
        $bag->get('create_new_wishlist')->willReturn(['name' => 'wishlist_name']);

        $channelContext->getChannel()->willReturn($channel);
        $envelope = new Envelope(new \stdClass());
        $commandBus->dispatch(Argument::type(CreateNewWishlist::class))->willReturn($envelope);
        $translator->trans('bitbag_sylius_wishlist_plugin.ui.create_new_wishlist')->willReturn('translation message');

        $flashBag->add('success', 'translation message')->shouldBeCalled();

        $this->__invoke($request)->shouldHaveType(JsonResponse::class);
    }
}


//public function __invoke(Request $request): Response
//{
//    $wishlistName = $request->request->get('create_new_wishlist')['name'];
//
//    try {
//        $channel = $this->channelContext->getChannel();
//    } catch (ChannelNotFoundException $exception) {
//        $channel = null;
//    }
//
//    if (null !== $channel) {
//        $createNewWishlist = new CreateNewWishlist($wishlistName, $channel->getCode());
//        $this->commandBus->dispatch($createNewWishlist);
//    } else {
//        $createNewWishlist = new CreateNewWishlist($wishlistName, null);
//        $this->commandBus->dispatch($createNewWishlist);
//    }
//
//    $this->flashBag->add(
//        'success',
//        $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.create_new_wishlist')
//    );
//
//    return new JsonResponse();
//}