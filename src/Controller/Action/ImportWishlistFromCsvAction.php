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

use Sylius\WishlistPlugin\Command\Wishlist\ImportWishlistFromCsv;
use Sylius\WishlistPlugin\Entity\Wishlist;
use Sylius\WishlistPlugin\Form\Type\ImportWishlistFromCsvType;
use Sylius\WishlistPlugin\Resolver\WishlistsResolverInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Twig\Environment;

final class ImportWishlistFromCsvAction
{
    use HandleTrait;

    public function __construct(
        private FormFactoryInterface $formFactory,
        private RequestStack $requestStack,
        private Environment $twigEnvironment,
        private WishlistsResolverInterface $wishlistsResolver,
        MessageBusInterface $messageBus,
    ) {
        $this->messageBus = $messageBus;
    }

    public function __invoke(Request $request): Response
    {
        $form = $this->createForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->handleCommand($form, $request);
        }

        /** @var Session $session */
        $session = $this->requestStack->getSession();

        /** @var FormError $error */
        foreach ($form->getErrors() as $error) {
            $session->getFlashBag()->add('error', $error->getMessage());
        }

        return new Response(
            $this->twigEnvironment->render('@BitBagSyliusWishlistPlugin/importWishlist.html.twig', [
                'form' => $form->createView(),
            ]),
        );
    }

    private function createForm(): FormInterface
    {
        return $this->formFactory->create(ImportWishlistFromCsvType::class, [], [
            'wishlists' => $this->wishlistsResolver->resolveAndCreate(),
        ]);
    }

    private function handleCommand(FormInterface $form, Request $request): Response
    {
        /** @var UploadedFile $file */
        $file = $form->get('wishlist_file')->getData();

        /** @var Wishlist $wishlist */
        $wishlist = $form->get('wishlists')->getData();

        $command = new ImportWishlistFromCsv($file->getFileInfo(), $request, (int) $wishlist->getId());

        return $this->handle($command);
    }
}
