<?php

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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Twig\Environment;

final class ImportWishlistFromCsvAction
{
    use HandleTrait;

    private FormFactoryInterface $formFactory;

    private FlashBagInterface $flashBag;

    private Environment $twigEnvironment;

    private WishlistsResolverInterface $wishlistsResolver;

    public function __construct(
        FormFactoryInterface $formFactory,
        FlashBagInterface $flashBag,
        MessageBusInterface $messageBus,
        Environment $twigEnvironment,
        WishlistsResolverInterface $wishlistsResolver
    ) {
        $this->formFactory = $formFactory;
        $this->flashBag = $flashBag;
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

        foreach ($form->getErrors() as $error) {
            $this->flashBag->add('error', $error->getMessage());
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
            'wishlists' => $this->wishlistsResolver->resolve(),
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
