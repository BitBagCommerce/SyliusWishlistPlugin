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
use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\WishlistPlugin\Form\Type\WishlistCollectionType;
use Sylius\WishlistPlugin\Processor\WishlistCommandProcessorInterface;
use Sylius\WishlistPlugin\Repository\WishlistRepositoryInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class BaseWishlistProductsAction
{
    public function __construct(
        protected CartContextInterface $cartContext,
        protected FormFactoryInterface $formFactory,
        protected RequestStack $requestStack,
        protected WishlistCommandProcessorInterface $wishlistCommandProcessor,
        protected MessageBusInterface $messageBus,
        protected UrlGeneratorInterface $urlGenerator,
        protected WishlistRepositoryInterface $wishlistRepository,
        protected TranslatorInterface $translator,
    ) {
    }

    public function __invoke(int $wishlistId, Request $request): Response
    {
        if (null === $this->createForm($wishlistId)) {
            return new RedirectResponse(
                $this->urlGenerator->generate('sylius_wishlist_plugin_shop_locale_wishlist_list_wishlists'),
            );
        }
        $form = $this->createForm($wishlistId);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleCommand($form);

            return new RedirectResponse(
                $this->urlGenerator->generate('sylius_wishlist_plugin_shop_locale_wishlist_show_chosen_wishlist', [
                        'wishlistId' => $wishlistId,
                    ]),
            );
        }

        /** @var Session $session */
        $session = $this->requestStack->getSession();

        /** @var FormError $error */
        foreach ($form->getErrors(true) as $error) {
            $session->getFlashBag()->add('error', $error->getMessage());
        }

        return new RedirectResponse(
            $this->urlGenerator->generate('sylius_wishlist_plugin_shop_locale_wishlist_show_chosen_wishlist', [
                'wishlistId' => $wishlistId,
            ]),
        );
    }

    abstract protected function handleCommand(FormInterface $form): void;

    protected function getFlashBag(): FlashBagInterface
    {
        /** @var Session $session */
        $session = $this->requestStack->getSession();

        return $session->getFlashBag();
    }

    private function createForm(int $wishlistId): ?FormInterface
    {
        /** @var ?WishlistInterface $wishlist */
        $wishlist = $this->wishlistRepository->find($wishlistId);
        $cart = $this->cartContext->getCart();

        if (null === $wishlist) {
            /** @var Session $session */
            $session = $this->requestStack->getSession();

            $session->getFlashBag()->add('error', $this->translator->trans('sylius_wishlist_plugin.ui.wishlist_not_exists'));

            return null;
        }
        $commandsArray = $this->wishlistCommandProcessor->createWishlistItemsCollection($wishlist->getWishlistProducts());

        return $this->formFactory->create(WishlistCollectionType::class, ['items' => $commandsArray], [
                'cart' => $cart,
        ]);
    }
}
