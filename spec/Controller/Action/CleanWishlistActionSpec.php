<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Controller\Action\CleanWishlistAction;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class CleanWishlistActionSpec extends ObjectBehavior
{
    public function let(
        WishlistRepositoryInterface $wishlistRepository,
        EntityManagerInterface $wishlistManager,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator
    ): void {
        $this->beConstructedWith(
            $wishlistRepository,
            $wishlistManager,
            $flashBag,
            $translator,
            $urlGenerator
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(CleanWishlistAction::class);
    }

    public function it_clears_wishlist(
        WishlistRepositoryInterface $wishlistRepository,
        WishlistInterface $wishlist,
        EntityManagerInterface $wishlistManager,
        TranslatorInterface $translator,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator,
        Request $request
    ): void {
        $wishlistRepository->find(1)->willReturn($wishlist);
        $translator->trans('bitbag_sylius_wishlist_plugin.ui.clear_wishlist')
            ->willReturn('translation message');
        $urlGenerator->generate('bitbag_sylius_wishlist_plugin_shop_wishlist_show_chosen_wishlist', [
            'wishlistId' => 1,
        ])->willReturn('/wishlist/1');

        $wishlist->clear()->shouldBeCalled();
        $wishlistManager->flush()->shouldBeCalled();
        $flashBag->add('success', 'translation message');

        $this->__invoke(1, $request)->shouldHaveType(RedirectResponse::class);
    }
}
