<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\ImportWishlistFromCsv;
use BitBag\SyliusWishlistPlugin\Form\Type\ImportWishlistFromCsvType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Twig\Environment;

final class ImportWishlistFromCsvAction
{
    private FormFactoryInterface $formFactory;

    private Environment $twigEnvironment;

    private FlashBagInterface $flashBag;

    private MessageBusInterface $commandBus;

    public function __construct(
        FormFactoryInterface $formFactory,
        Environment $twigEnvironment,
        FlashBagInterface $flashBag,
        MessageBusInterface $commandBus
    ) {
        $this->formFactory = $formFactory;
        $this->twigEnvironment = $twigEnvironment;
        $this->flashBag = $flashBag;
        $this->commandBus = $commandBus;
    }

    public function __invoke(Request $request): Response
    {
        $form = $this->formFactory->create(ImportWishlistFromCsvType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('wishlist_file')->getData();
            $command = new ImportWishlistFromCsv($file, $request);

            $envelope = $this->commandBus->dispatch($command);
            $responseStamp = $envelope->last(HandledStamp::class);

            return $responseStamp->getResult();
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
}
