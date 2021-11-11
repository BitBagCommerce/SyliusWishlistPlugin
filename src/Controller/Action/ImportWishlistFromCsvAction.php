<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Form\Type\ImportWishlistFromCsvType;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

final class ImportWishlistFromCsvAction
{
    private FormFactoryInterface $formFactory;

    private AddProductVariantToWishlistAction $addProductVariantToWishlistAction;

    private ProductVariantRepositoryInterface $productVariantRepository;

    private Environment $twigEnvironment;

    private FlashBagInterface $flashBag;

    private TranslatorInterface $translator;

    public function __construct(
        FormFactoryInterface $formFactory,
        Environment $twigEnvironment,
        AddProductVariantToWishlistAction $addProductVariantToWishlistAction,
        FlashBagInterface $flashBag,
        ProductVariantRepositoryInterface $productVariantRepository,
        TranslatorInterface $translator
    ) {
        $this->formFactory = $formFactory;
        $this->twigEnvironment = $twigEnvironment;
        $this->addProductVariantToWishlistAction = $addProductVariantToWishlistAction;
        $this->flashBag = $flashBag;
        $this->productVariantRepository = $productVariantRepository;
        $this->translator = $translator;
    }

    public function __invoke(Request $request): Response
    {
        $form = $this->formFactory->create(ImportWishlistFromCsvType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('wishlist_file')->getData();

            if ($this->handleUploadedFile($file, $request)) {
                return $this->addProductVariantToWishlistAction->__invoke($request);
            }
            $this->flashBag->add(
                    'error',
                    $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.upload_valid_csv')
                );

            return new Response(
                    $this->twigEnvironment->render('@BitBagSyliusWishlistPlugin/importWishlist.html.twig', [
                                'form' => $form->createView(),
                        ])
                );
        }

        foreach ($form->getErrors() as $error) {
            $this->flashBag->add('error', $error->getMessage());
        }

        return new Response(
            $this->twigEnvironment->render('@BitBagSyliusWishlistPlugin/importWishlist.html.twig', [
                        'form' => $form->createView(),
                ])
        );
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
