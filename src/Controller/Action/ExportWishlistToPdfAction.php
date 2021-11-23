<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddWishlistProduct;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\ExportSelectedProductsFromWishlistToPdf;
use BitBag\SyliusWishlistPlugin\Context\WishlistContextInterface;
use BitBag\SyliusWishlistPlugin\Exporter\ExporterWishlistToPdfInterface;
use BitBag\SyliusWishlistPlugin\Factory\Command\CommandFactory;
use BitBag\SyliusWishlistPlugin\Factory\Command\CommandFactoryInterface;
use BitBag\SyliusWishlistPlugin\Form\Type\WishlistCollectionType;
use BitBag\SyliusWishlistPlugin\Processor\WishlistCommandProcessorInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

final class ExportWishlistToPdfAction
{
    use HandleTrait;

    private WishlistContextInterface $wishlistContext;

    private CartContextInterface $cartContext;

    private FormFactoryInterface $formFactory;

    private FlashBagInterface $flashBag;

    private TranslatorInterface $translator;

    private UrlGeneratorInterface $urlGenerator;

    private Environment $twigEnvironment;

    private WishlistCommandProcessorInterface $wishlistCommandProcessor;

    private CommandFactoryInterface $commandFactory;

    public function __construct(
        WishlistContextInterface $wishlistContext,
        CartContextInterface $cartContext,
        FormFactoryInterface $formFactory,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator,
        Environment $twigEnvironment,
        WishlistCommandProcessorInterface $wishlistCommandProcessor,
        MessageBusInterface $messageBus,
        CommandFactoryInterface $commandFactory
    ) {
        $this->wishlistContext = $wishlistContext;
        $this->cartContext = $cartContext;
        $this->formFactory = $formFactory;
        $this->flashBag = $flashBag;
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
        $this->twigEnvironment = $twigEnvironment;
        $this->wishlistCommandProcessor = $wishlistCommandProcessor;
        $this->messageBus = $messageBus;
        $this->commandFactory = $commandFactory;
    }

    public function __invoke(Request $request): Response
    {
        $wishlist = $this->wishlistContext->getWishlist($request);
        $cart = $this->cartContext->getCart();

        $commandsArray = $this->wishlistCommandProcessor->createFromWishlistProducts($wishlist->getWishlistProducts());

        $form = $this->formFactory->create(
            WishlistCollectionType::class,
            [
                'items' => $commandsArray,
            ],
            [
                'cart' => $cart,
            ]
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $wishlistProducts = $form->get('items')->getData();
            $command = $this->commandFactory->createFrom($wishlistProducts, $request);

            if (!$this->handle($command)) {
                $this->flashBag->add(
                    'error',
                    $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.select_products')
                );
            }
        }

        foreach ($form->getErrors() as $error) {
            $this->flashBag->add('error', $error->getMessage());
        }
        return new Response(
            $this->twigEnvironment->render('@BitBagSyliusWishlistPlugin/WishlistDetails/index.html.twig',
                [
                    'wishlist' => $wishlist,
                    'form' => $form->createView(),
                ]
            )
        );
    }
}
