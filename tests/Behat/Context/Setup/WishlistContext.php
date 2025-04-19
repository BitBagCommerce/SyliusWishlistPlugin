<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\Sylius\WishlistPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Sylius\WishlistPlugin\Context\WishlistContextInterface;
use Sylius\WishlistPlugin\Entity\Wishlist;
use Sylius\WishlistPlugin\Entity\WishlistProductInterface;
use Sylius\WishlistPlugin\Factory\WishlistProductFactoryInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Behat\Service\Setter\CookieSetterInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Webmozart\Assert\Assert;

final class WishlistContext implements Context
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private WishlistContextInterface $wishlistContext,
        private WishlistProductFactoryInterface $wishlistProductFactory,
        private EntityManagerInterface $wishlistManager,
        private FactoryInterface $taxonFactory,
        private FactoryInterface $productTaxonFactory,
        private EntityManagerInterface $productTaxonManager,
        private CookieSetterInterface $cookieSetter,
        private string $wishlistCookieToken,
        private ChannelRepositoryInterface $channelRepository,
        private UserRepositoryInterface $userRepository,
    ) {
    }

    /**
     * @Given I have this product in my wishlist
     */
    public function iHaveThisProductInMyWishlist(): void
    {
        /** @var ProductInterface $product */
        $product = $this->productRepository->findOneBy([]);

        $this->addProductToWishlist($product);
    }

    /**
     * @Given I have these products in my wishlist
     */
    public function iHaveTheseProductsInMyWishlist(): void
    {
        $products = $this->productRepository->findAll();

        /** @var ProductInterface $product */
        foreach ($products as $product) {
            $this->addProductToWishlist($product);
        }
    }

    /**
     * @Given all store products appear under a main taxonomy
     */
    public function allStoreProductsAppearUnderAMainTaxonomy(): void
    {
        /** @var TaxonInterface $taxon */
        $taxon = $this->taxonFactory->createNew();
        $taxon->setCode('main');
        $taxon->setSlug('main');
        $taxon->setName('Main');

        /** @var ProductInterface $product */
        foreach ($this->productRepository->findAll() as $product) {
            /** @var ProductTaxonInterface $productTaxon */
            $productTaxon = $this->productTaxonFactory->createNew();
            $productTaxon->setTaxon($taxon);
            $productTaxon->setProduct($product);
            $product->addProductTaxon($productTaxon);

            $this->productTaxonManager->persist($taxon);
            $this->productTaxonManager->persist($productTaxon);
            $this->productTaxonManager->flush();
        }
    }

    private function addProductToWishlist(ProductInterface $product): void
    {
        $wishlist = $this->wishlistContext->getWishlist(new Request());
        /** @var WishlistProductInterface $wishlistProduct */
        $wishlistProduct = $this->wishlistProductFactory->createNew();
        /** @var Collection $productVariants */
        $productVariants = $product->getVariants();
        $channel = $this->channelRepository->findOneByCode('WEB-US');

        $wishlistProduct->setProduct($product);
        $wishlistProduct->setVariant($productVariants->first());

        $wishlist->addWishlistProduct($wishlistProduct);
        $wishlist->setName('wishlist');
        $wishlist->setChannel($channel);

        $this->wishlistManager->persist($wishlist);
        $this->wishlistManager->flush();

        $this->cookieSetter->setCookie($this->wishlistCookieToken, $wishlist->getToken());
    }

    /**
     * @Given user :email has a wishlist named :name with token :token
     */
    public function userHasAWishlistNamedWithToken(string $email, string $name, string $token): void
    {
        /** @var ?ShopUserInterface $user */
        $user = $this->userRepository->findOneByEmail($email);
        Assert::notNull($user);

        $wishlist = new Wishlist();
        $channel = $this->channelRepository->findOneByCode('WEB-US');

        $wishlist->setName($name);
        $wishlist->setChannel($channel);
        $wishlist->setToken($token);
        $wishlist->setShopUser($user);

        $this->wishlistManager->persist($wishlist);
        $this->wishlistManager->flush();
    }

    /**
     * @When user :email has a wishlist
     */
    public function userHasAWishlist(string $email): void
    {
        /** @var ?ShopUserInterface $user */
        $user = $this->userRepository->findOneByEmail($email);
        Assert::notNull($user);

        $wishlist = new Wishlist();
        $channel = $this->channelRepository->findOneByCode('WEB-US');

        $wishlist->setChannel($channel);
        $wishlist->setShopUser($user);

        $this->wishlistManager->persist($wishlist);
        $this->wishlistManager->flush();
    }

    /**
     * @When there is a guest wishlist which has been inactive for :days days
     */
    public function thereIsAGuestWishlistInactiveForDays(int $days): void
    {
        $wishlist = new Wishlist();
        $channel = $this->channelRepository->findOneByCode('WEB-US');

        $updatedAt = new \DateTime();
        $updatedAt->modify(\sprintf('-%d days', $days));

        $wishlist->setChannel($channel);
        $wishlist->setUpdatedAt($updatedAt);

        $this->wishlistManager->persist($wishlist);
        $this->wishlistManager->flush();
    }
}
