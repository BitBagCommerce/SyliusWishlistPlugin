<?php
declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\ImportWishlistFromCsv;
use BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist\ImportWishlistFromCsvHandler;
use BitBag\SyliusWishlistPlugin\Controller\Action\AddProductVariantToWishlistAction;
use BitBag\SyliusWishlistPlugin\Factory\CsvSerializerFactoryInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistProductFactoryInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ImportWishlistFromCsvHandlerSpec extends ObjectBehavior
{
    private array $allowedMimeTypes = [
        'file'
    ];

    public function let(
        ProductVariantRepositoryInterface $productVariantRepository,
        CsvSerializerFactoryInterface $csvSerializerFactory,
        RequestStack $requestStack,
        TranslatorInterface $translator,
        WishlistProductFactoryInterface $wishlistProductFactory,
        UrlGeneratorInterface $urlGenerator,
        WishlistRepositoryInterface $wishlistRepository
    ): void
    {
        $addProductVariantToWishlistAction = new AddProductVariantToWishlistAction(
            $productVariantRepository,
            $wishlistProductFactory,
            $requestStack,
            $translator,
            $urlGenerator,
            $wishlistRepository
        );

        $this->beConstructedWith(
            $addProductVariantToWishlistAction,
            $productVariantRepository,
            $this->allowedMimeTypes,
            $csvSerializerFactory,
            $requestStack,
            $translator
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ImportWishlistFromCsvHandler::class);
    }

    public function it_imports_wishlist_from_file(
        \SplFileInfo $fileInfo,
        Request $request,

        AddProductVariantToWishlistAction $addProductVariantToWishlistAction
    ): void {
        $wishlistId = 1;

        $importWishlistFromCsv = new ImportWishlistFromCsv($fileInfo, $request, 1);

        $fileInfo->getRealPath()->willReturn('path');

        $addProductVariantToWishlistAction->__invoke($wishlistId, $request)->shouldBeCalled();
    }
}
