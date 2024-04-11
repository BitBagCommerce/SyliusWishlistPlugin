<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\ImportWishlistFromCsv;
use BitBag\SyliusWishlistPlugin\Entity\Wishlist;
use BitBag\SyliusWishlistPlugin\Form\Type\ImportWishlistFromCsvType;
use BitBag\SyliusWishlistPlugin\Resolver\WishlistsResolverInterface;
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

    private FormFactoryInterface $formFactory;

    private RequestStack $requestStack;

    private Environment $twigEnvironment;

    private WishlistsResolverInterface $wishlistsResolver;

    public function __construct(
        FormFactoryInterface $formFactory,
        RequestStack $requestStack,
        MessageBusInterface $messageBus,
        Environment $twigEnvironment,
        WishlistsResolverInterface $wishlistsResolver
    ) {
        $this->formFactory = $formFactory;
        $this->requestStack = $requestStack;
        $this->messageBus = $messageBus;
        $this->twigEnvironment = $twigEnvironment;
        $this->wishlistsResolver = $wishlistsResolver;
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

        foreach ($form->getErrors() as $error) {
            $session->getFlashBag()->add('error', $error->getMessage());
        }

        return new Response(
            $this->twigEnvironment->render('@BitBagSyliusWishlistPlugin/importWishlist.html.twig', [
                'form' => $form->createView(),
            ])
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

        $command = new ImportWishlistFromCsv($file->getFileInfo(), $request, $wishlist->getId());

        return $this->handle($command);
    }
}
