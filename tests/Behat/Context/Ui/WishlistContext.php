<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusWishlistPlugin\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\RawMinkContext;
use BitBag\SyliusWishlistPlugin\Entity\Wishlist;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Exception\WishlistNotFoundException;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\Setter\CookieSetterInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Tests\BitBag\SyliusWishlistPlugin\Behat\Page\Shop\ProductIndexPageInterface;
use Tests\BitBag\SyliusWishlistPlugin\Behat\Page\Shop\ProductShowPageInterface;
use Tests\BitBag\SyliusWishlistPlugin\Behat\Page\Shop\Wishlist\IndexPageInterface;
use Tests\BitBag\SyliusWishlistPlugin\Behat\Page\Shop\WishlistPageInterface;
use Tests\BitBag\SyliusWishlistPlugin\Behat\Service\LoginerInterface;
use Tests\BitBag\SyliusWishlistPlugin\Behat\Service\WishlistCreatorInterface;
use Webmozart\Assert\Assert;

final class WishlistContext extends RawMinkContext implements Context
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private ProductIndexPageInterface $productIndexPage,
        private ProductShowPageInterface $productShowPage,
        private WishlistPageInterface $wishlistPage,
        private NotificationCheckerInterface $notificationChecker,
        private LoginerInterface $loginer,
        private WishlistCreatorInterface $wishlistCreator,
        private ProductVariantResolverInterface $defaultVariantResolver,
        private RouterInterface $router,
        private WishlistRepositoryInterface $wishlistRepository,
        private string $wishlistCookieToken,
        private SharedStorageInterface $sharedStorage,
        private CookieSetterInterface $cookieSetter,
        private ChannelRepositoryInterface $channelRepository,
        private RepositoryInterface $shopUserRepository,
        private IndexPageInterface $wishlistIndexPage,
    ) {
    }

    /**
     * @When I add this product to wishlist
     */
    public function iAddThisProductToWishlist(): void
    {
        $this->productIndexPage->open(['slug' => 'main']);

        /** @var ?ProductInterface $product */
        $product = $this->productRepository->findOneBy([]);
        Assert::notNull($product);
        /** @var ?string $productName */
        $productName = $product->getName();
        Assert::notNull($productName);

        $this->productIndexPage->addProductToWishlist($productName);
    }

    /**
     * @When I add :productName to selected wishlist :wishlistName
     */
    public function iAddThisProductToSelectedWishlist(string $productName, string $wishlistName): void
    {
        $this->productIndexPage->open(['slug' => 'main']);

        $this->wishlistPage->addProductToSelectedWishlist($productName, $wishlistName);
    }

    /**
     * @When I open modal to create new wishlist
     */
    public function iOpenModalToCreateNewWishlist(): void
    {
        $this->wishlistIndexPage->addNewWishlist();
    }

    /**
     * @When I set new wishlist name :wishlistName
     */
    public function iSetNewWishlistName(string $wishlistName): void
    {
        $this->wishlistIndexPage->fillNewWishlistName($wishlistName);
    }

    /**
     * @When I save new wishlist modal
     */
    public function iSaveNewWishlistModal(): void
    {
        $this->wishlistIndexPage->saveNewWishlist();
    }

    /**
     * @When I edit wishlist name :wishlistName
     */
    public function iEditWishlistName(string $wishlistName): void
    {
        $this->wishlistIndexPage->fillEditWishlistName($wishlistName);
    }

    /**
     * @When I edit :wishlistName
     */
    public function iEditWishlist(string $wishlistName): void
    {
        $this->wishlistIndexPage->editWishlistName($wishlistName);
    }

    /**
     * @When I save edit wishlist modal
     */
    public function iSaveEditWishlistModal(): void
    {
        $this->wishlistIndexPage->saveEditWishlist();
    }

    /**
     * @Then I should have :productName in selected wishlists :wishlistName
     */
    public function iShouldHaveProductInWishlist(string $productName, string $wishlistName): void
    {
        /** @var WishlistInterface $wishlist */
        $wishlist = $this->sharedStorage->get($wishlistName);

        $this->visitPath('/wishlists/' . $wishlist->getId());

        Assert::true($this->wishlistPage->hasProduct($productName));
    }

    /**
     * @Then I should have :count products in selected wishlist :wishlistName
     */
    public function iShouldHaveProductsInSelectedWishlist(int $count, string $wishlistName): void
    {
        /** @var WishlistInterface $wishlist */
        $wishlist = $this->sharedStorage->get($wishlistName);

        $this->visitPath('/wishlists/' . $wishlist->getId());
        Assert::eq($count, $this->wishlistPage->getProductElements());
    }

    /**
     * @Given the store has a wishlist named :name
     */
    public function theStoreHasAWishlist(string $name): void
    {
        $cookie = $this->getSession()->getCookie($this->wishlistCookieToken);
        $wishlist = new Wishlist();
        $channel = $this->channelRepository->findOneByCode('WEB-US');

        $wishlist->setName($name);
        $wishlist->setChannel($channel);

        if (null !== $cookie) {
            $wishlist->setToken($cookie);
        }

        /** @var ?string $wishlistName */
        $wishlistName = $wishlist->getName();
        Assert::notNull($wishlistName);

        $this->wishlistRepository->add($wishlist);
        $this->sharedStorage->set($wishlistName, $wishlist);
        $this->getSession()->setCookie($this->wishlistCookieToken, $wishlist->getToken());
        $this->cookieSetter->setCookie($this->wishlistCookieToken, $wishlist->getToken());
    }

    /**
     * @Then I remove wishlist cookie token
     */
    public function iRemoveWishlistCookieToken(): void
    {
        $this->getSession()->setCookie($this->wishlistCookieToken);
        $this->cookieSetter->setCookie($this->wishlistCookieToken, '');
    }

    /**
     * @Then /^I follow (edit|remove) for "([^"]+)"$/
     */
    public function iFollowActionForSelectedWishlist(string $action, string $wishlistName): void
    {
        $this->wishlistPage->selectedWishlistAction($action, $wishlistName);
    }

    /**
     * @When I add :productName product to my wishlist
     */
    public function iAddProductToMyWishlist(string $productName): void
    {
        $this->productIndexPage->open(['slug' => 'main']);

        $this->productIndexPage->addProductToWishlist($productName);
    }

    /**
     * @When I add this product variant to wishlist
     */
    public function iAddThisProductVariantToWishlist(): void
    {
        $this->productShowPage->addVariantToWishlist();
    }

    /**
     * @When I log in to my account which already has :product product in the wishlist
     */
    public function iLogInToMyAccountWhichAlreadyHasProductInTheWishlist(ProductInterface $product): void
    {
        $user = $this->loginer->createUser();
        $wishlistCookieToken = $this->getSession()->getCookie($this->wishlistCookieToken);

        if (null === $wishlistCookieToken) {
            throw new \Exception('Wishlist token not found');
        }

        /** @var ?WishlistInterface $wishlist */
        $wishlist = $this->wishlistRepository->findByToken($wishlistCookieToken);
        Assert::notNull($wishlist);

        $this->wishlistCreator->createWishlistWithProductAndUser($user, $product, $wishlist);
        $this->loginer->logIn();
    }

    /**
     * @When I log in
     */
    public function iLogIn(): void
    {
        $this->loginer->createUser();

        $this->loginer->logIn();
    }

    /**
     * @When I log in again
     */
    public function iLogInAgain(): void
    {
        $this->loginer->logIn();
    }

    /**
     * @When I log out
     */
    public function iLogOut(): void
    {
        $this->loginer->logOut();
    }

    /**
     * @When I go to the wishlist page
     */
    public function iGoToTheWishlistPage(): void
    {
        $this->wishlistPage->open();
    }

    /**
     * @When I select :quantity quantity of :productName product
     */
    public function iSelectQuantityOfProduct(int $quantity, string $productName): void
    {
        $this->wishlistPage->selectProductQuantity($productName, $quantity);
    }

    /**
     * @When I add my wishlist products to cart
     */
    public function iAddMyWishlistProductsToCart(): void
    {
        $this->wishlistPage->addProductToCart();
    }

    /**
     * @When I add selected products to cart
     */
    public function iAddSelectedProductsToCart(): void
    {
        $this->wishlistPage->addSelectedProductsToCart();
    }

    /**
     * @When I copy selected products to :wishlistName
     */
    public function iCopySelectedProducts(string $wishlistName): void
    {
        $this->wishlistPage->copySelectedProducts($wishlistName);
    }

    /**
     * @When /^the (product "([^"]+)") is stored in "(?P<filename>(?:[^"]|\\")*)"$/
     */
    public function productIsStoredInFile(ProductInterface $product, string $filename): void
    {
        /** @var ProductVariantInterface $productVariant */
        $productVariant = $this->defaultVariantResolver->getVariant($product);

        $data = [
            'variantId' => $productVariant->getId(),
            'productId' => $product->getId(),
            'variantCode' => $productVariant->getCode(),
        ];

        if ('' === (string) $this->getMinkParameter('files_path')) {
            return;
        }

        $realFilesPath = realpath($this->getMinkParameter('files_path'));
        Assert::string($realFilesPath);
        $fullPath = rtrim($realFilesPath, \DIRECTORY_SEPARATOR) . \DIRECTORY_SEPARATOR . $filename;
        $fileResource = fopen($fullPath, 'w+');
        Assert::notFalse($fileResource);
        fputcsv($fileResource, array_keys($data));
        fputcsv($fileResource, $data);
        fclose($fileResource);
    }

    /**
     * @When I export selected products to csv
     */
    public function iExportSelectedProductsToCsv(): void
    {
        $this->wishlistPage->exportSelectedProductsToCsv();
    }

    /**
     * @When I should have downloaded CSV file
     */
    public function iShouldHaveDownloadedCsvFile(): void
    {
        Assert::eq($this->getSession()->getResponseHeader('content-type'), 'text/csv; charset=UTF-8');
        Assert::eq($this->getSession()->getResponseHeader('content-disposition'), 'attachment; filename=wishlist.csv');
        Assert::eq($this->getSession()->getStatusCode(), '200');
    }

    /**
     * @When I remove this product
     */
    public function iRemoveThisProduct(): void
    {
        /** @var ?ProductInterface $product */
        $product = $this->productRepository->findOneBy([]);
        if (null === $product) {
            throw new ResourceNotFoundException();
        }

        $this->wishlistPage->removeProduct((string) $product->getName());
    }

    /**
     * @Then I open :wishlistName
     */
    public function iOpenChosenWishlist(string $wishlistName): void
    {
        $this->wishlistPage->showChosenWishlist($wishlistName);
    }

    /**
     * @Then I try to access :email wishlist :wishlistName
     */
    public function iTryToAccessCustomerWishlist(string $email, string $wishlistName): void
    {
        /** @var ?ShopUserInterface $shopUser */
        $shopUser = $this->shopUserRepository->findOneBy(['username' => $email]);

        if (null === $shopUser) {
            throw new ResourceNotFoundException();
        }

        /** @var ?WishlistInterface $wishlist */
        $wishlist = $this->wishlistRepository->findOneByShopUserAndName($shopUser, $wishlistName);

        if (null === $wishlist) {
            throw new WishlistNotFoundException();
        }

        $this->visitPath('/wishlists/' . $wishlist->getId());
    }

    /**
     * @Then I should still be on wishlist index page
     */
    public function iShouldStillBeOnWishlistIndexPage(): void
    {
        $this->wishlistIndexPage->verify();
    }

    /**
     * @When I remove selected products from wishlist
     */
    public function iRemoveSelectedProductsFromWishlist(): void
    {
        $this->wishlistPage->removeSelectedProductsFromWishlist();
    }

    /**
     * @When I export to pdf selected products from wishlist and file is downloaded
     */
    public function iExportToPdfSelectedProductsFromWishlistAndFileIsDownloaded(): void
    {
        $this->wishlistPage->exportToPdfSelectedProductsFromWishlist();

        $baseUrl = $this->getMinkParameter('base_url');

        $guzzle = new \GuzzleHttp\Client([
            'timeout' => 10,
        ]);
        /** @var ?WishlistInterface $wishlist */
        $wishlist = $this->wishlistRepository->findOneBy([]);
        Assert::notNull($wishlist);

        $url = $this->router->generate('bitbag_sylius_wishlist_plugin_shop_wishlist_export_to_pdf', ['wishlistId' => $wishlist->getId()], UrlGeneratorInterface::RELATIVE_PATH);

        $response = $guzzle->get(sprintf('%s%s', $baseUrl, $url));

        $contentType = $response->getHeader('Content-Type')[0];

        Assert::eq($contentType, 'text/html; charset=UTF-8');
    }

    /**
     * @Then I should be on my wishlist page
     */
    public function iShouldBeOnMyWishlistPage(): void
    {
        $this->wishlistPage->verify();
    }

    /**
     * @Then I should be notified that the product has been successfully added to my wishlist
     */
    public function iShouldBeNotifiedThatTheProductHasBeenSuccessfullyAddedToMyWishlist(): void
    {
        $this->notificationChecker->checkNotification('Product has been added to your wishlist.', NotificationType::success());
    }

    /**
     * @Then I should be notified that the product has been removed from my wishlist
     */
    public function iShouldBeNotifiedThatTheProductHasBeenRemovedFromMyWishlist(): void
    {
        $this->notificationChecker->checkNotification('Product has been removed from your wishlist.', NotificationType::success());
    }

    /**
     * @Then I should have one item in my wishlist
     */
    public function iShouldHaveOnItemInMyWishlist(): void
    {
        Assert::eq(1, $this->wishlistPage->getItemsCount());
    }

    /**
     * @Then I should have :count wishlists
     */
    public function iShouldHaveWishlists(int $count): void
    {
        Assert::eq($count, $this->wishlistPage->getWishlistsCount());
    }

    /**
     * @Then I should have :count products in my wishlist
     */
    public function iShouldHaveProductsInMyWishlist(int $count): void
    {
        Assert::eq($count, $this->wishlistPage->getItemsCount());
    }

    /**
     * @Then I should have :productName product in my wishlist
     */
    public function iShouldHaveProductInMyWishlist(string $productName): void
    {
        Assert::true($this->wishlistPage->hasProduct($productName));
    }

    /**
     * @Then I should have :productName product in my cart
     */
    public function iShouldHaveProductInMyCart(string $productName): void
    {
        Assert::true(
            $this->wishlistPage->hasProductInCart($productName),
            sprintf('Product %s was not found in the cart.', $productName),
        );
    }

    /**
     * @Then I should be notified that :product does not have sufficient stock
     */
    public function iShouldBeNotifiedThatThisProductDoesNotHaveSufficientStock(ProductInterface $product): void
    {
        Assert::true($this->wishlistPage->hasProductOutOfStockValidationMessage($product));
    }

    /**
     * @Then I should be notified that wishlist has been cleared
     */
    public function iShouldBeNotifiedThatWishlistHasBeenCleared(): void
    {
        Assert::true($this->wishlistPage->hasWishlistClearedValidationMessage());
    }

    /**
     * @Then I should be notified that I should add more products
     */
    public function iShouldBeNotifiedThatIShouldAddMoreProducts(): void
    {
        Assert::true($this->wishlistPage->addMoreProductsWishlistValidationMessage());
    }

    /**
     * @Then I should wait for one second
     */
    public function iShouldWaitForOneSecond(): void
    {
        $this->wishlistPage->waitForOneSecond();
    }
}
