<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\WishlistPlugin\Controller\Action;

use Sylius\WishlistPlugin\Command\Wishlist\ExportWishlistToCsv;
use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\WishlistPlugin\Exception\NoProductSelectedException;
use Sylius\WishlistPlugin\Form\Type\WishlistCollectionType;
use Sylius\WishlistPlugin\Processor\WishlistCommandProcessorInterface;
use Sylius\WishlistPlugin\Repository\WishlistRepositoryInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\Form\FormError;
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
        MessageBusInterface $messageBus,
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

        /** @var FormError $error */
        foreach ($form->getErrors() as $error) {
            $session->getFlashBag()->add('error', $error->getMessage());
        }

        return new RedirectResponse(
            $this->urlGenerator->generate('sylius_wishlist_plugin_shop_locale_wishlist_show_chosen_wishlist', [
                'wishlistId' => $wishlistId,
            ]),
        );
    }

    private function createForm(int $wishlistId): FormInterface
    {
        /** @var WishlistInterface $wishlist */
        $wishlist = $this->wishlistRepository->find($wishlistId);
        $cart = $this->cartContext->getCart();

        $this->wishlistName = (string) $wishlist->getName();

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

            return new RedirectResponse($this->urlGenerator->generate('sylius_wishlist_plugin_shop_locale_wishlist_list_products'));
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
