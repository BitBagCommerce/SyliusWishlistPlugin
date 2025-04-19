<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\WishlistPlugin\Controller\Action;

use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\WishlistPlugin\Command\Wishlist\AddSelectedProductsToCart;
use Sylius\WishlistPlugin\Exception\InsufficientProductStockException;
use Sylius\WishlistPlugin\Exception\InvalidProductQuantityException;
use Sylius\WishlistPlugin\Processor\WishlistCommandProcessorInterface;
use Sylius\WishlistPlugin\Repository\WishlistRepositoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AddSelectedProductsToCartAction extends BaseWishlistProductsAction
{
    public function __construct(
        CartContextInterface $cartContext,
        FormFactoryInterface $formFactory,
        RequestStack $requestStack,
        WishlistCommandProcessorInterface $wishlistCommandProcessor,
        MessageBusInterface $messageBus,
        UrlGeneratorInterface $urlGenerator,
        WishlistRepositoryInterface $wishlistRepository,
        TranslatorInterface $translator,
    ) {
        parent::__construct(
            $cartContext,
            $formFactory,
            $requestStack,
            $wishlistCommandProcessor,
            $messageBus,
            $urlGenerator,
            $wishlistRepository,
            $translator,
        );
    }

    protected function handleCommand(FormInterface $form): void
    {
        try {
            $command = new AddSelectedProductsToCart($form->getData());
            $this->messageBus->dispatch($command);
            if (false === $this->getFlashBag()->has('success')) {
                $this->getFlashBag()->add('success', $this->translator->trans('sylius_wishlist_plugin.ui.added_to_cart'));
            }
        } catch (HandlerFailedException $exception) {
            $this->getFlashBag()->add('error', $this->getExceptionMessage($exception));
        }
    }

    private function getExceptionMessage(HandlerFailedException $exception): string
    {
        $previous = $exception->getPrevious();
        if ($previous instanceof InsufficientProductStockException) {
            return $this->translator->trans('sylius_wishlist_plugin.ui.insufficient_stock', ['%productName%' => $previous->getProductName()]);
        }
        if ($previous instanceof InvalidProductQuantityException) {
            return $this->translator->trans('sylius_wishlist_plugin.ui.increase_quantity');
        }

        return $exception->getMessage();
    }

    protected function getFlashBag(): FlashBagInterface
    {
        /** @var Session $session */
        $session = $this->requestStack->getSession();

        return $session->getFlashBag();
    }
}
