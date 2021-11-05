<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddWishlistProduct;
use BitBag\SyliusWishlistPlugin\Context\WishlistContextInterface;
use BitBag\SyliusWishlistPlugin\Form\Type\WishlistCollectionType;
use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

final class ExportSelectedProductsToCsvAction
{
    private WishlistContextInterface $wishlistContext;

    private CartContextInterface $cartContext;

    private FormFactoryInterface $formFactory;

    private Environment $twigEnvironment;

    private FlashBagInterface $flashBag;

    private TranslatorInterface $translator;

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        WishlistContextInterface $wishlistContext,
        CartContextInterface $cartContext,
        FormFactoryInterface $formFactory,
        Environment $twigEnvironment,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->wishlistContext = $wishlistContext;
        $this->cartContext = $cartContext;
        $this->formFactory = $formFactory;
        $this->twigEnvironment = $twigEnvironment;
        $this->flashBag = $flashBag;
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
    }

    public function __invoke(Request $request): Response
    {
        $wishlist = $this->wishlistContext->getWishlist($request);
        $cart = $this->cartContext->getCart();

        $commandsArray = new ArrayCollection();

        foreach ($wishlist->getWishlistProducts() as $wishlistProductItem) {
            $wishlistProductCommand = new AddWishlistProduct();
            $wishlistProductCommand->setWishlistProduct($wishlistProductItem);
            $commandsArray->add($wishlistProductCommand);
        }

        $form = $this->formFactory->create(WishlistCollectionType::class, ['items' => $commandsArray], [
                'cart' => $cart,

        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = fopen('php://temp', 'w');

            if ($this->exportToCSV($form->getData(), $file)) {
                $this->flashBag->add('success', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.selected_wishlist_items_successfully_exported'));
                rewind($file);
                $response = new Response(stream_get_contents($file));
                fclose($file);

                $response->headers->set('Content-Type', 'text/csv');
                $response->headers->set('Content-Disposition', 'attachment; filename=export.csv');

                return $response;
            } else {
                $this->flashBag->add('error', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.select_products'));
                return new RedirectResponse($this->urlGenerator->generate('bitbag_sylius_wishlist_plugin_shop_wishlist_list_products'));
            }
        }

        foreach ($form->getErrors() as $error) {
            $this->flashBag->add('error', $error->getMessage());
        }

        return new Response(
            $this->twigEnvironment->render('@BitBagSyliusWishlistPlugin/WishlistDetails/index.html.twig', [
                        'wishlist' => $wishlist,
                        'form' => $form->createView(),
                ])
        );
    }

    private function exportToCSV(array $wishlistProductsCommands, $file): bool
    {
        $result = false;

        foreach ($wishlistProductsCommands as $wishlistProducts) {
            /** @var AddWishlistProduct $wishlistProduct */
            foreach ($wishlistProducts as $wishlistProduct) {
                if ($wishlistProduct->isSelected()) {
                    $result = true;
                    $csvWishlistItem = [
                            $wishlistProduct->getCartItem()->getCartItem()->getVariant()->getId(),
                            $wishlistProduct->getWishlistProduct()->getProduct()->getId(),
                            $wishlistProduct->getCartItem()->getCartItem()->getVariant()->getCode()
                    ];
                    fputcsv($file, $csvWishlistItem);
                }
            }
        }
        return $result;
    }
}
