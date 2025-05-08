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

namespace spec\Sylius\WishlistPlugin\Processor;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\User\Model\UserInterface;
use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\WishlistPlugin\Entity\WishlistProductInterface;
use Sylius\WishlistPlugin\Factory\WishlistProductFactoryInterface;
use Sylius\WishlistPlugin\Processor\AddProductVariantToWishlistProcessor;
use Sylius\WishlistPlugin\Repository\WishlistRepositoryInterface;
use Sylius\WishlistPlugin\Twig\WishlistExtension;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AddProductVariantToWishlistProcessorSpec extends ObjectBehavior
{
    public function let(
        Security $security,
        WishlistExtension $wishlistExtension,
        ChannelContextInterface $channelContext,
        WishlistProductFactoryInterface $wishlistProductFactory,
        RequestStack $requestStack,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator,
        WishlistRepositoryInterface $wishlistRepository,
    ): void {
        $this->beConstructedWith(
            $security,
            $wishlistExtension,
            $channelContext,
            $wishlistProductFactory,
            $requestStack,
            $translator,
            $urlGenerator,
            $wishlistRepository,
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AddProductVariantToWishlistProcessor::class);
    }

    public function it_throws_resource_not_found_exception_when_wishlist_is_not_found_for_specific_id(
        Security $security,
        UserInterface $user,
        WishlistExtension $wishlistExtension,
        WishlistRepositoryInterface $wishlistRepository,
        ProductVariantInterface $productVariant,
        WishlistInterface $firstWishlist,
        WishlistInterface $secondWishlist,
    ): void {
        $wishlistIdToFind = 999;

        $security->getUser()->willReturn($user);
        $wishlistExtension->findAllByShopUserAndToken()->willReturn([$firstWishlist, $secondWishlist]);

        $wishlistRepository->find($wishlistIdToFind)->willReturn(null);

        $this->shouldThrow(ResourceNotFoundException::class)
            ->during('process', [$productVariant, $wishlistIdToFind]);
    }

    public function it_throws_error_if_no_wishlists_are_found_for_single_wishlist_scenario(
        Security $security,
        UserInterface $user,
        WishlistExtension $wishlistExtension,
        ProductVariantInterface $productVariant,
    ): void {
        $security->getUser()->willReturn($user);

        $wishlistExtension->findAllByShopUserAndToken()->willReturn([]);

        $this->shouldThrow(ResourceNotFoundException::class)
            ->during('process', [$productVariant, null]);
    }

    public function it_adds_product_to_the_single_wishlist_for_logged_in_user(
        Security $security,
        UserInterface $user,
        WishlistExtension $wishlistExtension,
        WishlistProductFactoryInterface $wishlistProductFactory,
        RequestStack $requestStack,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator,
        WishlistRepositoryInterface $wishlistRepository,
        ProductVariantInterface $productVariant,
        WishlistInterface $wishlist,
        WishlistProductInterface $wishlistProduct,
        Session $session,
        FlashBagInterface $flashBag,
    ): void {
        $wishlistId = 123;
        $wishlist->getId()->willReturn($wishlistId);

        $security->getUser()->willReturn($user);
        $wishlistExtension->findAllByShopUserAndToken()->willReturn([$wishlist]);

        $wishlist->hasProductVariant($productVariant)->willReturn(false);
        $wishlistProductFactory->createForWishlistAndVariant($wishlist, $productVariant)->willReturn($wishlistProduct);

        $requestStack->getSession()->willReturn($session);
        $session->getFlashBag()->willReturn($flashBag);
        $translator->trans('sylius_wishlist_plugin.ui.added_wishlist_item')->willReturn('Product added.');
        $flashBag->add('success', 'Product added.')->shouldBeCalled();

        $wishlist->addWishlistProduct($wishlistProduct)->shouldBeCalled();
        $wishlistRepository->add($wishlist)->shouldBeCalled();

        $urlGenerator->generate('sylius_wishlist_plugin_shop_locale_wishlist_show_chosen_wishlist', ['wishlistId' => $wishlistId])
            ->willReturn('/wishlist/' . $wishlistId);

        $response = $this->process($productVariant);
        $response->shouldHaveType(RedirectResponse::class);
        $response->getTargetUrl()->shouldReturn('/wishlist/' . $wishlistId);
    }

    public function it_adds_product_to_a_specific_wishlist_for_logged_in_user_with_multiple_wishlists(
        Security $security,
        UserInterface $user,
        WishlistExtension $wishlistExtension,
        WishlistProductFactoryInterface $wishlistProductFactory,
        RequestStack $requestStack,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator,
        WishlistRepositoryInterface $wishlistRepository,
        ProductVariantInterface $productVariant,
        WishlistInterface $targetWishlist,
        WishlistInterface $otherWishlist,
        WishlistProductInterface $wishlistProduct,
        Session $session,
        FlashBagInterface $flashBag,
    ): void {
        $targetWishlistId = 789;
        $targetWishlist->getId()->willReturn($targetWishlistId);

        $security->getUser()->willReturn($user);
        $wishlistExtension->findAllByShopUserAndToken()->willReturn([$otherWishlist, $targetWishlist]);

        $wishlistRepository->find($targetWishlistId)->willReturn($targetWishlist);

        $targetWishlist->hasProductVariant($productVariant)->willReturn(false);
        $wishlistProductFactory->createForWishlistAndVariant($targetWishlist, $productVariant)->willReturn($wishlistProduct);

        $requestStack->getSession()->willReturn($session);
        $session->getFlashBag()->willReturn($flashBag);
        $translator->trans('sylius_wishlist_plugin.ui.added_wishlist_item')->willReturn('Product added to specific list.');
        $flashBag->add('success', 'Product added to specific list.')->shouldBeCalled();

        $targetWishlist->addWishlistProduct($wishlistProduct)->shouldBeCalled();
        $wishlistRepository->add($targetWishlist)->shouldBeCalled();

        $urlGenerator->generate('sylius_wishlist_plugin_shop_locale_wishlist_show_chosen_wishlist', ['wishlistId' => $targetWishlistId])
            ->willReturn('/wishlist/' . $targetWishlistId);

        $response = $this->process($productVariant, $targetWishlistId);
        $response->shouldHaveType(RedirectResponse::class);
        $response->getTargetUrl()->shouldReturn('/wishlist/' . $targetWishlistId);
    }

    public function it_adds_product_to_the_single_wishlist_for_anonymous_user(
        Security $security,
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        WishlistExtension $wishlistExtension,
        WishlistProductFactoryInterface $wishlistProductFactory,
        RequestStack $requestStack,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator,
        WishlistRepositoryInterface $wishlistRepository,
        ProductVariantInterface $productVariant,
        WishlistInterface $wishlist,
        WishlistProductInterface $wishlistProduct,
        Session $session,
        FlashBagInterface $flashBag,
    ): void {
        $wishlistId = 456;
        $wishlist->getId()->willReturn($wishlistId);

        $security->getUser()->willReturn(null);
        $channelContext->getChannel()->willReturn($channel);
        $wishlistExtension->findAllByAnonymousAndChannel($channel)->willReturn([$wishlist]);

        $wishlist->hasProductVariant($productVariant)->willReturn(false);
        $wishlistProductFactory->createForWishlistAndVariant($wishlist, $productVariant)->willReturn($wishlistProduct);

        $requestStack->getSession()->willReturn($session);
        $session->getFlashBag()->willReturn($flashBag);
        $translator->trans('sylius_wishlist_plugin.ui.added_wishlist_item')->willReturn('Product added (anonymous).');
        $flashBag->add('success', 'Product added (anonymous).')->shouldBeCalled();

        $wishlist->addWishlistProduct($wishlistProduct)->shouldBeCalled();
        $wishlistRepository->add($wishlist)->shouldBeCalled();

        $urlGenerator->generate('sylius_wishlist_plugin_shop_locale_wishlist_show_chosen_wishlist', ['wishlistId' => $wishlistId])
            ->willReturn('/wishlist/anon/' . $wishlistId);

        $response = $this->process($productVariant, null);
        $response->shouldHaveType(RedirectResponse::class);
        $response->getTargetUrl()->shouldReturn('/wishlist/anon/' . $wishlistId);
    }

    public function it_adds_flash_error_if_product_variant_is_already_in_wishlist(
        Security $security,
        UserInterface $user,
        WishlistExtension $wishlistExtension,
        WishlistProductFactoryInterface $wishlistProductFactory,
        RequestStack $requestStack,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator,
        ProductVariantInterface $productVariant,
        ProductInterface $product,
        WishlistInterface $wishlist,
        WishlistProductInterface $wishlistProduct,
        Session $session,
        FlashBagInterface $flashBag,
    ): void {
        $wishlistId = 123;
        $productName = 'Awesome T-Shirt';
        $wishlist->getId()->willReturn($wishlistId);
        $productVariant->getProduct()->willReturn($product);
        $wishlistProduct->getProduct()->willReturn($product);
        $product->getName()->willReturn($productName);

        $security->getUser()->willReturn($user);
        $wishlistExtension->findAllByShopUserAndToken()->willReturn([$wishlist]);

        $wishlist->hasProductVariant($productVariant)->willReturn(true);
        $wishlistProductFactory->createForWishlistAndVariant($wishlist, $productVariant)->willReturn($wishlistProduct);

        $requestStack->getSession()->willReturn($session);
        $session->getFlashBag()->willReturn($flashBag);
        $translator->trans(
            'sylius_wishlist_plugin.ui.wishlist_has_product_variant',
            ['%productName%' => $productName],
        )->willReturn('Product already in wishlist.');
        $flashBag->add('error', 'Product already in wishlist.')->shouldBeCalled();

        $wishlist->addWishlistProduct($wishlistProduct)->shouldNotBeCalled();

        $urlGenerator->generate('sylius_wishlist_plugin_shop_locale_wishlist_show_chosen_wishlist', ['wishlistId' => $wishlistId])
            ->willReturn('/wishlist/' . $wishlistId);

        $response = $this->process($productVariant, null);
        $response->shouldHaveType(RedirectResponse::class);
        $response->getTargetUrl()->shouldReturn('/wishlist/' . $wishlistId);
    }
}
