<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddWishlistProduct;
use BitBag\SyliusWishlistPlugin\Context\WishlistContextInterface;
use BitBag\SyliusWishlistPlugin\Form\Type\WishlistCollectionType;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

final class RemoveSelectedProductsFromWishlistAction
{
    private WishlistContextInterface $wishlistContext;

    private CartContextInterface $cartContext;

    private FormFactoryInterface $formFactory;

    private FlashBagInterface $flashBag;

    private TranslatorInterface $translator;

    private ProductVariantRepositoryInterface $productVariantRepository;

    private UrlGeneratorInterface $urlGenerator;

    private EntityManagerInterface $wishlistProductManager;

    private Environment $twigEnvironment;

    public function __construct(
        WishlistContextInterface          $wishlistContext,
        CartContextInterface              $cartContext,
        FormFactoryInterface              $formFactory,
        FlashBagInterface                 $flashBag,
        TranslatorInterface               $translator,
        ProductVariantRepositoryInterface $productVariantRepository,
        UrlGeneratorInterface             $urlGenerator,
        EntityManagerInterface            $wishlistProductManager,
        Environment                       $twigEnvironment
    ) {
        $this->wishlistContext = $wishlistContext;
        $this->cartContext = $cartContext;
        $this->formFactory = $formFactory;
        $this->flashBag = $flashBag;
        $this->translator = $translator;
        $this->productVariantRepository = $productVariantRepository;
        $this->urlGenerator = $urlGenerator;
        $this->wishlistProductManager = $wishlistProductManager;
        $this->twigEnvironment = $twigEnvironment;
    }

    public function __invoke(Request $request): Response
    {
        $wishlist = $this->wishlistContext->getWishlist($request);
        $cart = $this->cartContext->getCart();

        $commandsArray = [];

        foreach ($wishlist->getWishlistProducts() as $wishlistProductItem) {
            $wishlistProductCommand = new AddWishlistProduct();
            $wishlistProductCommand->setWishlistProduct($wishlistProductItem);
            $commandsArray[] = $wishlistProductCommand;
        }

        $form = $this->formFactory->create(WishlistCollectionType::class, ['items' => $commandsArray], [
            'cart' => $cart,

        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $wishlistProducts = $form->get("items")->getData();

            if ($this->handleCartItems($wishlistProducts, $request)) {
                $this->flashBag->add('success', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.removed_selected_wishlist_items'));
            } else {
                $this->flashBag->add('error', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.select_products'));
            }

            return new RedirectResponse($this->urlGenerator->generate('bitbag_sylius_wishlist_plugin_shop_wishlist_list_products'));
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

    private function handleCartItems(array $wishlistProducts, Request $request): bool
    {
        $result = false;

        /** @var AddWishlistProduct $wishlistProduct */
        foreach ($wishlistProducts as $wishlistProduct) {
            if ($wishlistProduct->isSelected()) {
                $result = true;
                $variant = $this->productVariantRepository->find($wishlistProduct->getWishlistProduct()->getVariant());

                if (null === $variant) {
                    throw new NotFoundHttpException();
                }

                $wishlist = $this->wishlistContext->getWishlist($request);

                foreach ($wishlist->getWishlistProducts() as $wishlistProductEntity) {
                    if ($variant === $wishlistProductEntity->getVariant()) {
                        $this->wishlistProductManager->remove($wishlistProductEntity);
                    }
                }
            }
        }

        $this->wishlistProductManager->flush();

        return $result;
    }
}
