<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\ExportWishlistToCsv;
use BitBag\SyliusWishlistPlugin\Context\WishlistContextInterface;
use BitBag\SyliusWishlistPlugin\Exception\NoProductSelectedException;
use BitBag\SyliusWishlistPlugin\Form\Type\WishlistCollectionType;
use BitBag\SyliusWishlistPlugin\Processor\WishlistCommandProcessorInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ExportSelectedProductsToCsvAction
{
    use HandleTrait;

    private WishlistContextInterface $wishlistContext;

    private CartContextInterface $cartContext;

    private FormFactoryInterface $formFactory;

    private FlashBagInterface $flashBag;

    private WishlistCommandProcessorInterface $wishlistCommandProcessor;

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        WishlistContextInterface $wishlistContext,
        CartContextInterface $cartContext,
        FormFactoryInterface $formFactory,
        FlashBagInterface $flashBag,
        MessageBusInterface $messageBus,
        WishlistCommandProcessorInterface $wishlistCommandProcessor,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->wishlistContext = $wishlistContext;
        $this->cartContext = $cartContext;
        $this->formFactory = $formFactory;
        $this->flashBag = $flashBag;
        $this->messageBus = $messageBus;
        $this->wishlistCommandProcessor = $wishlistCommandProcessor;
        $this->urlGenerator = $urlGenerator;
    }

    public function __invoke(Request $request): Response
    {
        $form = $this->createForm($request);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->exportSelectedWishlistProductsToCsv($form);
        }

        foreach ($form->getErrors() as $error) {
            $this->flashBag->add('error', $error->getMessage());
        }

        return new RedirectResponse($this->urlGenerator->generate('bitbag_sylius_wishlist_plugin_shop_wishlist_list_products'));
    }

    private function createForm(Request $request): FormInterface
    {
        $wishlist = $this->wishlistContext->getWishlist($request);
        $cart = $this->cartContext->getCart();

        $commandsArray = $this->wishlistCommandProcessor->createAddCommandCollectionFromWishlistProducts($wishlist->getWishlistProducts());

        return $this->formFactory->create(WishlistCollectionType::class, ['items' => $commandsArray], [
            'cart' => $cart,
        ]);
    }

    private function exportSelectedWishlistProductsToCsv(FormInterface $form): Response
    {
        try {
            /** @var \SplFileObject $file */
            $file = $this->getCsvFileFromWishlistProducts($form);
        } catch (NoProductSelectedException $e) {
            $this->flashBag->add('error', $e->getMessage());

            return new RedirectResponse($this->urlGenerator->generate('bitbag_sylius_wishlist_plugin_shop_wishlist_list_products'));
        }

        return $this->returnCsvFile($file);
    }

    private function getCsvFileFromWishlistProducts(FormInterface $form): \SplFileObject
    {
        $file = new \SplFileObject('export.csv', 'w+');
        $command = new ExportWishlistToCsv($form->get('items')->getData(), $file);

        return $this->handle($command);
    }

    private function returnCsvFile(\SplFileObject $file): Response
    {
        $file->rewind();

        $response = new BinaryFileResponse($file);

        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $file->getFilename());
        $response->headers->set('Content-Type', 'text/csv');
        $response->deleteFileAfterSend(true);

        return $response;
    }
}
