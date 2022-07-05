<?php

namespace spec\BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Controller\Action\RemoveProductVariantFromWishlistAction;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RemoveProductVariantFromWishlistActionSpec extends ObjectBehavior
{
    public function let(
        WishlistRepositoryInterface $wishlistRepository,
        ProductVariantRepositoryInterface $productVariantRepository,
        EntityManagerInterface $wishlistProductManager,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator
    ): void {
        $this->beConstructedWith(
            $wishlistRepository,
            $productVariantRepository,
            $wishlistProductManager,
            $flashBag,
            $translator,
            $urlGenerator
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(RemoveProductVariantFromWishlistAction::class);
    }

    public function it_throws_404_if_product_variant_was_not_found(
        ProductVariantRepositoryInterface $productVariantRepository,
        Request $request
    ): void
    {
        $productVariantRepository->find(1)->willReturn(null);

        $this->shouldThrow(NotFoundHttpException::class)->during('__invoke', [1,1,$request]);
    }

    public function it_throws_404_if_wishlist_was_not_found(
        ProductVariantRepositoryInterface $productVariantRepository,
        ProductVariantInterface $productVariant,
        WishlistRepositoryInterface $wishlistRepository,
        Request $request
    ): void
    {
        $productVariantRepository->find(1)->willReturn($productVariant);
        $wishlistRepository->find(1)->willReturn(null);

        $this->shouldThrow(NotFoundHttpException::class)->during('__invoke', [1,1,$request]);
    }

    public function it_handles_request_and_redirects_to_wishlist(
        ProductVariantRepositoryInterface $productVariantRepository,
        ProductVariantInterface $productVariant,
        WishlistRepositoryInterface $wishlistRepository,
        Request $request,
        WishlistInterface $wishlist,
        WishlistProductInterface $wishlistProduct,
        EntityManagerInterface $wishlistProductManager,
        TranslatorInterface $translator,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator
    ): void {
        $productVariantRepository->find(1)->willReturn($productVariant);
        $wishlistRepository->find(1)->willReturn($wishlist);
        $wishlist->getWishlistProducts()->willReturn(new ArrayCollection([$wishlistProduct->getWrappedObject()]));
        $wishlistProduct->getVariant()->willReturn($productVariant);
        $translator->trans('bitbag_sylius_wishlist_plugin.ui.removed_wishlist_item')
            ->willReturn('translation message');

        $urlGenerator->generate('bitbag_sylius_wishlist_plugin_shop_wishlist_show_chosen_wishlist', [
            'wishlistId' => 1
        ])->willReturn('/wishlist/1');

        $wishlistProductManager->remove($wishlistProduct)->shouldBeCalled();
        $wishlistProductManager->flush()->shouldBeCalled();
        $flashBag->add('success', 'translation message')->shouldBeCalled();

        $this->__invoke(1, 1, $request)->shouldHaveType(RedirectResponse::class);
    }
}
