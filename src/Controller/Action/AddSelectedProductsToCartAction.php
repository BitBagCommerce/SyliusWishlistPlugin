<?php

/*
* This file was created by developers working at BitBag
* Do you need more information about us and what we do? Visit our https://bitbag.io website!
* We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddSelectedProductsToCart;
use BitBag\SyliusWishlistPlugin\Exception\ProductCantBeAddedToCartException;
use BitBag\SyliusWishlistPlugin\Processor\WishlistCommandProcessorInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AddSelectedProductsToCartAction extends BaseWishlistProductsAction
{
    private TranslatorInterface $translator;

    public function __construct(
        CartContextInterface $cartContext,
        FormFactoryInterface $formFactory,
        FlashBagInterface $flashBag,
        WishlistCommandProcessorInterface $wishlistCommandProcessor,
        MessageBusInterface $messageBus,
        UrlGeneratorInterface $urlGenerator,
        WishlistRepositoryInterface $wishlistRepository,
        TranslatorInterface $translator
    ) {
        parent::__construct(
            $cartContext,
            $formFactory,
            $flashBag,
            $wishlistCommandProcessor,
            $messageBus,
            $urlGenerator,
            $wishlistRepository,
            $translator
        );
        $this->translator = $translator;
    }

    protected function handleCommand(FormInterface $form): void
    {
        try {
            $command = new AddSelectedProductsToCart($form->getData());
            $this->messageBus->dispatch($command);
            $this->flashBag->add('success', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.added_to_cart'));
        } catch (HandlerFailedException $exception) {
            $this->flashBag->add('error', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.increase_quantity'));
        }
    }
}
