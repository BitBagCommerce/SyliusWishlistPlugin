<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Services\Exporter;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistItemInterface;
use BitBag\SyliusWishlistPlugin\Exception\NoProductSelectedException;
use BitBag\SyliusWishlistPlugin\Exception\ProductVariantNotFoundException;
use BitBag\SyliusWishlistPlugin\Model\VariantPdfModelInterface;
use BitBag\SyliusWishlistPlugin\Services\Generator\ModelCreator;
use Doctrine\Common\Collections\Collection;
use Dompdf\Dompdf;
use Dompdf\Options;
use Sylius\Component\Core\Model\ProductVariant;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

final class WishlistToPdfExporter implements WishlistToPdfExporterInterface
{
    private ProductVariantRepositoryInterface $productVariantRepository;

    private Environment $twigEnvironment;

    private ModelCreator $modelCreator;

    private string $wishlistName;

    public function __construct(
        ProductVariantRepositoryInterface $productVariantRepository,
        Environment $twigEnvironment,
        ModelCreator $modelCreator
    ) {
        $this->productVariantRepository = $productVariantRepository;
        $this->twigEnvironment = $twigEnvironment;
        $this->modelCreator = $modelCreator;
    }

    public function createModelToPdfAndExportToPdf(Collection $wishlistProducts, Request $request): void
    {
        $productsToExport = $this->createVariantModelToPdf($wishlistProducts, $request);

        if (empty($productsToExport)) {
            throw new NoProductSelectedException();
        }
        $this->exportToPdf($productsToExport);
    }

    private function createVariantModelToPdf(Collection $wishlistProducts, Request $request): array
    {
        /** @var WishlistItemInterface $wishlistProduct */
        foreach ($wishlistProducts as $key => $wishlistProduct) {
            if ($wishlistProduct->isSelected()) {
                if (0 === $key) {
                    $this->wishlistName = $wishlistProduct->getWishlistProduct()->getWishlist()->getName();
                }
                $selectedProducts[] = $this->createCollectionOfWishlistItems($wishlistProduct, $request);
            }
        }

        return $selectedProducts;
    }

    private function createCollectionOfWishlistItems(
        WishlistItemInterface $wishlistProduct,
        Request $request
    ): VariantPdfModelInterface {
        $variant = $this->productVariantRepository->find($wishlistProduct->getWishlistProduct()->getVariant());
        $this->wishlistThrowException($wishlistProduct, $variant);
        $itemWishlistModel = $this->modelCreator->createWishlistItemToPdf($wishlistProduct, $request, $variant);

        return $itemWishlistModel;
    }

    private function wishlistThrowException(WishlistItemInterface $wishlistProduct, ProductVariant $variant)
    {
        if (null === $variant || null === $wishlistProduct) {
            throw new ProductVariantNotFoundException(
                sprintf('The Product does not exist')
            );
        }
    }

    private function exportToPdf(array $selectedProducts): void
    {
        $pdfOptions = new Options();
        $pdfOptions->set('isRemoteEnabled', true);
        $pdfOptions->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($pdfOptions);
        $html = $this->twigEnvironment->render('@BitBagSyliusWishlistPlugin/_wishlist_pdf.html.twig', [
            'title' => 'My wishlist products',
            'date' => date('d.m.Y'),
            'products' => $selectedProducts,
        ]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream(sprintf('%s.csv', $this->wishlistName), ['Attachment' => true]);
    }
}
