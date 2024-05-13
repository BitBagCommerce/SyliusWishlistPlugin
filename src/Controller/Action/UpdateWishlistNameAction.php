<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\UpdateWishlistName;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Webmozart\Assert\Assert;

final class UpdateWishlistNameAction
{
    public function __construct(
        private MessageBusInterface $commandBus,
        private RequestStack $requestStack,
        private TranslatorInterface $translator,
        private WishlistRepositoryInterface $wishlistRepository,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $wishlistName = $request->get('edit_wishlist_name')['name'];
        Assert::string($wishlistName);
        $wishlistId = $request->attributes->getInt('id');

        /** @var ?WishlistInterface $wishlist */
        $wishlist = $this->wishlistRepository->find($wishlistId);

        if (null === $wishlist) {
            throw new ResourceNotFoundException();
        }

        /** @var Session $session */
        $session = $this->requestStack->getSession();

        try {
            $updateWishlistName = new UpdateWishlistName($wishlistName, $wishlist);
            $this->commandBus->dispatch($updateWishlistName);

            $session->getFlashBag()->add(
                'success',
                $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.wishlist_name_changed'),
            );
        } catch (HandlerFailedException $exception) {
            $session->getFlashBag()->add(
                'error',
                $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.wishlist_name_already_exists'),
            );
        }

        return new Response($this->urlGenerator->generate('bitbag_sylius_wishlist_plugin_shop_locale_wishlist_list_products'));
    }
}
