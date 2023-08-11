<?php

/*
* This file was created by developers working at BitBag
* Do you need more information about us and what we do? Visit our https://bitbag.io website!
* We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Checker\WishlistAccessCheckerInterface;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddSelectedProductsToCart;
use BitBag\SyliusWishlistPlugin\Processor\WishlistCommandProcessorInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
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
        RequestStack $requestStack,
        WishlistCommandProcessorInterface $wishlistCommandProcessor,
        MessageBusInterface $messageBus,
        UrlGeneratorInterface $urlGenerator,
        WishlistAccessCheckerInterface $wishlistAccessChecker,
        TranslatorInterface $translator
    ) {
        parent::__construct(
            $cartContext,
            $formFactory,
            $requestStack,
            $wishlistCommandProcessor,
            $messageBus,
            $urlGenerator,
            $wishlistAccessChecker,
            $translator
        );
        $this->translator = $translator;
    }

    protected function handleCommand(FormInterface $form): void
    {
        /** @var Session $session */
        $session = $this->requestStack->getSession();

        try {
            $command = new AddSelectedProductsToCart($form->getData());
            $this->messageBus->dispatch($command);
            $session->getFlashBag()->add('success', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.added_to_cart'));
        } catch (HandlerFailedException $exception) {
            $session->getFlashBag()->add('error', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.increase_quantity'));
        }
    }
}
