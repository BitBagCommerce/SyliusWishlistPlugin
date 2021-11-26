<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\ExportWishlistToCsv;
use BitBag\SyliusWishlistPlugin\Context\WishlistContextInterface;
use BitBag\SyliusWishlistPlugin\Form\Type\WishlistCollectionType;
use BitBag\SyliusWishlistPlugin\Processor\WishlistCommandProcessorInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
        $wishlist = $this->wishlistContext->getWishlist($request);
        $cart = $this->cartContext->getCart();

        $commandsArray = $this->wishlistCommandProcessor->createAddCommandCollectionFromWishlistProducts($wishlist->getWishlistProducts());

        $form = $this->formFactory->create(WishlistCollectionType::class, ['items' => $commandsArray], [
                'cart' => $cart,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->handleCommand($form);
        }

        foreach ($form->getErrors() as $error) {
            $this->flashBag->add('error', $error->getMessage());
        }

        return new RedirectResponse($this->urlGenerator->generate('bitbag_sylius_wishlist_plugin_shop_wishlist_list_products'));
    }

    private function handleCommand(FormInterface $form): Response
    {
        $file = new \SplFileObject('php://temp', 'w');
        $command = new ExportWishlistToCsv($form->get('items')->getData(), $file);

        return $this->handle($command);
    }
}
