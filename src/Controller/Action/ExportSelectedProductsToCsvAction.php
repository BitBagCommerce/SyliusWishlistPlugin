<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

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
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ExportSelectedProductsToCsvAction
{
    use HandleTrait;

    private string $wishlistName;

    public function __construct(
        private CartContextInterface $cartContext,
        private FormFactoryInterface $formFactory,
        private RequestStack $requestStack,
        private WishlistCommandProcessorInterface $wishlistCommandProcessor,
        private UrlGeneratorInterface $urlGenerator,
        private TranslatorInterface $translator,
        private WishlistRepositoryInterface $wishlistRepository,
        MessageBusInterface $messageBus
    ) {
        $this->messageBus = $messageBus;
    }

    public function __invoke(int $wishlistId, Request $request): Response
    {
        $form = $this->createForm($wishlistId);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->exportSelectedWishlistProductsToCsv($form);
        }

        /** @var Session $session */
        $session = $this->requestStack->getSession();

        foreach ($form->getErrors() as $error) {
            $session->getFlashBag()->add('error', $error->getMessage());
        }

        return new RedirectResponse(
            $this->urlGenerator->generate('bitbag_sylius_wishlist_plugin_shop_locale_wishlist_show_chosen_wishlist', [
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
            /** @var Session $session */
            $session = $this->requestStack->getSession();

            $session->getFlashBag()->add('error', $this->translator->trans($e->getMessage()));

            return new RedirectResponse($this->urlGenerator->generate('bitbag_sylius_wishlist_plugin_shop_locale_wishlist_list_products'));
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
