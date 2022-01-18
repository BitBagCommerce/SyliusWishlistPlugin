<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\ExportWishlistToCsv;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Exception\NoProductSelectedException;
use BitBag\SyliusWishlistPlugin\Form\Type\WishlistCollectionType;
use BitBag\SyliusWishlistPlugin\Processor\WishlistCommandProcessorInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
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
use Symfony\Contracts\Translation\TranslatorInterface;

final class ExportSelectedProductsToCsvAction
{
    use HandleTrait;

    private CartContextInterface $cartContext;

    private FormFactoryInterface $formFactory;

    private FlashBagInterface $flashBag;

    private WishlistCommandProcessorInterface $wishlistCommandProcessor;

    private UrlGeneratorInterface $urlGenerator;

    private TranslatorInterface $translator;

    private WishlistRepositoryInterface $wishlistRepository;

    private string $wishlistName;

    public function __construct(
        CartContextInterface $cartContext,
        FormFactoryInterface $formFactory,
        FlashBagInterface $flashBag,
        MessageBusInterface $messageBus,
        WishlistCommandProcessorInterface $wishlistCommandProcessor,
        UrlGeneratorInterface $urlGenerator,
        TranslatorInterface $translator,
        WishlistRepositoryInterface $wishlistRepository
    ) {
        $this->cartContext = $cartContext;
        $this->formFactory = $formFactory;
        $this->flashBag = $flashBag;
        $this->messageBus = $messageBus;
        $this->wishlistCommandProcessor = $wishlistCommandProcessor;
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
        $this->wishlistRepository = $wishlistRepository;
    }

    public function __invoke(int $wishlistId, Request $request): Response
    {
        $form = $this->createForm($wishlistId);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->exportSelectedWishlistProductsToCsv($form);
        }

        foreach ($form->getErrors() as $error) {
            $this->flashBag->add('error', $error->getMessage());
        }

        return new RedirectResponse(
            $this->urlGenerator->generate('bitbag_sylius_wishlist_plugin_shop_wishlist_show_chosen_wishlist', [
                'wishlistId' => $wishlistId,
            ])
        );
    }

    private function createForm(int $wishlistId): FormInterface
    {
        /** @var WishlistInterface $wishlist */
        $wishlist = $this->wishlistRepository->find($wishlistId);
        $cart = $this->cartContext->getCart();

        $this->wishlistName = $wishlist->getName();

        $commandsArray = $this->wishlistCommandProcessor->createWishlistItemsCollection($wishlist->getWishlistProducts());

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
            $this->flashBag->add('error', $this->translator->trans($e->getMessage()));

            return new RedirectResponse($this->urlGenerator->generate('bitbag_sylius_wishlist_plugin_shop_wishlist_list_products'));
        }

        return $this->returnCsvFile($file);
    }

    private function getCsvFileFromWishlistProducts(FormInterface $form): \SplFileObject
    {
        $file = new \SplFileObject(sprintf('%s.csv', $this->wishlistName), 'w+');
        $command = new ExportWishlistToCsv($form->getData(), $file);

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
