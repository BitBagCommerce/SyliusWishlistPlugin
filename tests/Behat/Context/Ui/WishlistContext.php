<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusWishlistPlugin\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\RawMinkContext;
use BitBag\SyliusWishlistPlugin\Entity\Wishlist;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\Setter\CookieSetterInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Tests\BitBag\SyliusWishlistPlugin\Behat\Page\Shop\ProductIndexPageInterface;
use Tests\BitBag\SyliusWishlistPlugin\Behat\Page\Shop\ProductShowPageInterface;
use Tests\BitBag\SyliusWishlistPlugin\Behat\Page\Shop\WishlistPageInterface;
use Tests\BitBag\SyliusWishlistPlugin\Behat\Service\LoginerInterface;
use Tests\BitBag\SyliusWishlistPlugin\Behat\Service\WishlistCreatorInterface;
use Webmozart\Assert\Assert;

final class WishlistContext extends RawMinkContext implements Context
{
    private ProductRepositoryInterface $productRepository;

    private ProductIndexPageInterface $productIndexPage;

    private ProductShowPageInterface $productShowPage;

    private WishlistPageInterface $wishlistPage;

    private NotificationCheckerInterface $notificationChecker;

    private LoginerInterface $loginer;

    private WishlistCreatorInterface $wishlistCreator;

    private ProductVariantResolverInterface $defaultVariantResolver;

    private Session $session;

    private RouterInterface $router;

    private WishlistRepositoryInterface $wishlistRepository;

    private string $wishlistCookieToken;

    private SharedStorageInterface $sharedStorage;

    private CookieSetterInterface $cookieSetter;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        ProductIndexPageInterface $productIndexPage,
        ProductShowPageInterface $productShowPage,
        WishlistPageInterface $wishlistPage,
        NotificationCheckerInterface $notificationChecker,
        LoginerInterface $loginer,
        WishlistCreatorInterface $wishlistCreator,
        ProductVariantResolverInterface $defaultVariantResolver,
        Session $session,
        RouterInterface $router,
        WishlistRepositoryInterface $wishlistRepository,
        string $wishlistCookieToken,
        SharedStorageInterface $sharedStorage,
        CookieSetterInterface $cookieSetter
    ) {
        $this->productRepository = $productRepository;
        $this->productIndexPage = $productIndexPage;
        $this->wishlistPage = $wishlistPage;
        $this->notificationChecker = $notificationChecker;
        $this->loginer = $loginer;
        $this->wishlistCreator = $wishlistCreator;
        $this->productShowPage = $productShowPage;
        $this->defaultVariantResolver = $defaultVariantResolver;
        $this->session = $session;
        $this->router = $router;
        $this->wishlistRepository = $wishlistRepository;
        $this->wishlistCookieToken = $wishlistCookieToken;
        $this->sharedStorage = $sharedStorage;
        $this->cookieSetter = $cookieSetter;
    }

    /**
     * @When I add this product to wishlist
     */
    public function iAddThisProductToWishlist(): void
    {
        $this->productIndexPage->open(['slug' => 'main']);

        /** @var ProductInterface $product */
        $product = $this->productRepository->findOneBy([]);

        $this->productIndexPage->addProductToWishlist($product->getName());
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

        $wishlist->setName($name);

        if ($cookie) {
            $wishlist->setToken($cookie);
        }

        $this->wishlistRepository->add($wishlist);
        $this->sharedStorage->set($wishlist->getName(), $wishlist);
        $this->cookieSetter->setCookie($this->wishlistCookieToken, $wishlist->getToken());
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

        if (!$wishlistCookieToken) {
            throw new \Exception('Wishlist token not found');
        }

        $wishlist = $this->wishlistRepository->findByToken($wishlistCookieToken);

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

        if (!$this->getMinkParameter('files_path')) {
            return;
        }
        $fullPath = rtrim(realpath($this->getMinkParameter('files_path')), \DIRECTORY_SEPARATOR) . \DIRECTORY_SEPARATOR . $filename;
        $fileResource = fopen($fullPath, 'w+');
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
        Assert::eq($this->getSession()->getResponseHeader('content-type'), 'text/csv');
        Assert::eq($this->getSession()->getResponseHeader('content-disposition'), 'attachment; filename=wishlist.csv');
        Assert::eq($this->getSession()->getStatusCode(), '200');
    }

    /**
     * @When I remove this product
     */
    public function iRemoveThisProduct(): void
    {
        $this->wishlistPage->removeProduct($this->productRepository->findOneBy([])->getName());
    }

    /**
     * @Then I open :wishlistName
     */
    public function iOpenChosenWishlist(string $wishlistName): void
    {
        $this->wishlistPage->showChosenWishlist($wishlistName);
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

        $cookieName = $this->session->getName();
        $sessionId = $this->session->getId();
        $baseUrl = $this->getMinkParameter('base_url');
        $domain = parse_url($baseUrl)['host'];

        $cookieJar = \GuzzleHttp\Cookie\CookieJar::fromArray([
            $cookieName => $sessionId,
        ], $domain);

        $guzzle = new \GuzzleHttp\Client([
            'timeout' => 10,
            'cookies' => $cookieJar,
        ]);
        $wishlist = $this->wishlistRepository->findOneBy([]);

        $url = $this->router->generate('bitbag_sylius_wishlist_plugin_shop_wishlist_export_to_pdf', ['wishlistId' => $wishlist->getId()], UrlGeneratorInterface::RELATIVE_PATH);
        $response = $guzzle->get(sprintf('%s%s', $baseUrl, $url));
        $driver = $this->getSession()->getDriver();
        $contentType = $response->getHeader('Content-Type')[0];

        if ('text/html; charset=UTF-8' !== $contentType) {
            throw new \Behat\Mink\Exception\ExpectationException('The content type of the downloaded file is not correct.', $driver);
        }

        Assert::eq($this->getSession()->getStatusCode(), '200');
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
            sprintf('Product %s was not found in the cart.', $productName)
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
}
