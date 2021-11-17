<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\ImportWishlistFromCsv;
use BitBag\SyliusWishlistPlugin\Controller\Action\AddProductVariantToWishlistAction;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ImportWishlistFromCsvHandler implements MessageHandlerInterface
{
    private AddProductVariantToWishlistAction $addProductVariantToWishlistAction;

    private FlashBagInterface $flashBag;

    private TranslatorInterface $translator;

    private ProductVariantRepositoryInterface $productVariantRepository;

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        AddProductVariantToWishlistAction $addProductVariantToWishlistAction,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        ProductVariantRepositoryInterface $productVariantRepository,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->addProductVariantToWishlistAction = $addProductVariantToWishlistAction;
        $this->flashBag = $flashBag;
        $this->translator = $translator;
        $this->productVariantRepository = $productVariantRepository;
        $this->urlGenerator = $urlGenerator;
    }

    public function __invoke(ImportWishlistFromCsv $importWishlistFromCsv): Response
    {
        $file = $importWishlistFromCsv->getFile();
        $request = $importWishlistFromCsv->getRequest();

        if ($this->handleUploadedFile($file, $request)) {
            return $this->addProductVariantToWishlistAction->__invoke($request);
        }
        $this->flashBag->add('error', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.upload_valid_csv'));

        return new RedirectResponse($this->urlGenerator->generate('bitbag_sylius_wishlist_plugin_shop_wishlist_import_from_csv'));
    }

    private function handleUploadedFile(UploadedFile $file, Request $request): bool
    {
        $requestData = [];
        if ($this->isValidMimeType($file)) {
            $resource = fopen($file->getRealPath(), 'r');

            while ($data = fgetcsv($resource, 1000, ',')) {
                if ($this->checkCsvProduct($data)) {
                    $requestData[] = $data[0];
                }
            }
            $request->attributes->set('variantId', $requestData);
            fclose($resource);
        } else {
            return false;
        }

        return true;
    }

    private function isValidMimeType(UploadedFile $file): bool
    {
        return 'text/csv' === $file->getClientMimeType() || 'application/octet-stream' === $file->getClientMimeType();
    }

    private function checkCsvProduct(array $data): bool
    {
        $variant = $this->productVariantRepository->find($data[0]);

        if (null === $variant) {
            throw new NotFoundHttpException();
        }

        if ($data[1] == $variant->getProduct()->getId() && $data[2] == $variant->getCode()) {
            return true;
        }

        return false;
    }
}
