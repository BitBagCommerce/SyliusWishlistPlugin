<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Services\Exporter;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistItemInterface;
use BitBag\SyliusWishlistPlugin\Exception\ProductVariantNotFoundException;
use BitBag\SyliusWishlistPlugin\Model\Factory\VariantPdfModelFactoryInterface;
use BitBag\SyliusWishlistPlugin\Resolver\VariantImagePathResolverInterface;
use Doctrine\Common\Collections\Collection;
use Dompdf\Dompdf;
use Dompdf\Options;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

final class ExporterWishlistToPdf implements ExporterWishlistToPdfInterface
{
    private bool $isSelected = false;

    private ProductVariantRepositoryInterface $productVariantRepository;

    private VariantImagePathResolverInterface $variantImagePathResolver;

    private VariantPdfModelFactoryInterface $variantPdfModelFactory;

    private Environment $twigEnvironment;

    private FlashBagInterface $flashBag;

    private TranslatorInterface $translator;


    public function __construct(
        ProductVariantRepositoryInterface $productVariantRepository,
        VariantImagePathResolverInterface $variantImagePathResolver,
        VariantPdfModelFactoryInterface $variantPdfModelFactory,
        Environment $twigEnvironment,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator
    ) {
        $this->productVariantRepository = $productVariantRepository;
        $this->variantImagePathResolver = $variantImagePathResolver;
        $this->variantPdfModelFactory = $variantPdfModelFactory;
        $this->twigEnvironment = $twigEnvironment;
        $this->flashBag = $flashBag;
        $this->translator = $translator;
    }

    public function createModelToPdfAndExportToPdf(Collection $wishlistProducts, Request $request): void
    {
        $selectedProducts = [];

        /** @var WishlistItemInterface $wishlistProduct */
        foreach ($wishlistProducts as $wishlistProduct) {
            if ($wishlistProduct->isSelected()) {
                $this->isSelected = true;

                $variant = $this->productVariantRepository->find($wishlistProduct->getWishlistProduct()->getVariant());

                if (null === $variant || null === $wishlistProduct) {
                    throw new ProductVariantNotFoundException(
                        sprintf('The Product does not exist')
                    );
                }

                $cartItem = $wishlistProduct->getCartItem()->getCartItem();
                $quantity = $cartItem->getQuantity();
                $baseUrl = $request->getSchemeAndHttpHost();
                $urlToImage = $this->variantImagePathResolver->resolve($variant, $baseUrl);
                $actualVariant = $cartItem->getVariant()->getCode();
                $selectedProducts[] = $this->variantPdfModelFactory->createWithVariantAndImagePath(
                    $variant,
                    $urlToImage,
                    $quantity,
                    $actualVariant
                );
            }
        }
        $this->exportToPdf($selectedProducts);
    }

    private function exportToPdf(array $selectedProducts): void
    {
        if ( $this->isSelected === false) {
            $this->flashBag->add(
                'error',
                $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.select_products')
            );
            return ;
        }

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
        $dompdf->stream('wishlist.pdf', ['Attachment' => true]);
    }

}