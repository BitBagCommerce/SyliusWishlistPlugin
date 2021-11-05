<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Exporter;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddWishlistProductInterface;
use BitBag\SyliusWishlistPlugin\Model\Factory\VariantPdfModelFactoryInterface;
use BitBag\SyliusWishlistPlugin\Resolver\VariantImagePathResolverInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Dompdf\Dompdf;
use Dompdf\Options;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;

final class ExporterWishlistToPdf implements ExporterWishlistToPdfInterface
{
    private ProductVariantRepositoryInterface $productVariantRepository;

    private VariantImagePathResolverInterface $variantImagePathResolver;

    private VariantPdfModelFactoryInterface $variantPdfModelFactory;

    private Environment $twigEnvironment;

    public function __construct(
        ProductVariantRepositoryInterface   $productVariantRepository,
        VariantImagePathResolverInterface   $variantImagePathResolver,
        VariantPdfModelFactoryInterface     $variantPdfModelFactory,
        Environment                         $twigEnvironment
    )
    {
        $this->productVariantRepository = $productVariantRepository;
        $this->variantImagePathResolver = $variantImagePathResolver;
        $this->variantPdfModelFactory = $variantPdfModelFactory;
        $this->twigEnvironment = $twigEnvironment;
    }

    public function handleCartItems(ArrayCollection $wishlistProducts, Request $request): bool
    {
        $result = false;
        $selectedProducts = [];

        /** @var AddWishlistProductInterface $wishlistProduct */
        foreach ($wishlistProducts as $wishlistProduct) {
            if ($wishlistProduct->isSelected()) {
                $result = true;
                $variant = $this->productVariantRepository->find($wishlistProduct->getWishlistProduct()->getVariant());

                if (null === $variant) {
                    throw new NotFoundHttpException();
                }

                $cartItem = $wishlistProduct->getCartItem()->getCartItem();
                $quantity = $cartItem->getQuantity();
                $baseUrl = $request->getSchemeAndHttpHost();
                $urlToImage = $this->variantImagePathResolver->resolve($variant, $baseUrl);
                $actualVariant = $cartItem->getVariant()->getCode();
                $selectedProducts[] = $this->variantPdfModelFactory->createWithVariantAndImagePath($variant,$urlToImage,$quantity,$actualVariant);
            }
        }

        if (true === $result) {
            $this->exportToPdf($selectedProducts);
        }

        return $result;
    }

    private function exportToPdf(array $selectedProducts): void
    {
        $pdfOptions = new Options();
        $pdfOptions->set('isRemoteEnabled', true);
        $pdfOptions->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($pdfOptions);
        $html = $this->twigEnvironment->render('@BitBagSyliusWishlistPlugin/_wishlist_pdf.html.twig', [
            'title' => "My wishlist products",
            'date' => date("d.m.Y"),
            'products' => $selectedProducts
        ]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream('wishlist.pdf', ["Attachment" => true]);
    }
}