<?php
declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Context\WishlistContextInterface;
use BitBag\SyliusWishlistPlugin\Form\Type\AddProductsToCartType;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
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

    public function __construct(
        WishlistContextInterface $wishlistContext,
        CartContextInterface $cartContext,
        FormFactoryInterface $formFactory,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        ProductVariantRepositoryInterface $productVariantRepository,
        UrlGeneratorInterface $urlGenerator,
        EntityManagerInterface $wishlistProductManager
    ) {
        $this->wishlistContext = $wishlistContext;
        $this->cartContext = $cartContext;
        $this->formFactory = $formFactory;
        $this->flashBag = $flashBag;
        $this->translator = $translator;
        $this->productVariantRepository = $productVariantRepository;
        $this->urlGenerator = $urlGenerator;
        $this->wishlistProductManager = $wishlistProductManager;
    }

    public function __invoke(Request $request): Response
    {
        $wishlist = $this->wishlistContext->getWishlist($request);
        $cart = $this->cartContext->getCart();

        $form = $this->formFactory->create(AddProductsToCartType::class, null, [
            'cart' => $cart,
            'wishlist_products' => $wishlist->getWishlistProducts(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $variantIds = $request->request->get('variantId');

            if ($variantIds) {
                foreach ($variantIds as $variantId) {
                    /** @var ProductVariantInterface|null $variant */
                    $variant = $this->productVariantRepository->find($variantId);


                    if (null === $variant) {
                        throw new NotFoundHttpException();
                    }

                    $wishlist = $this->wishlistContext->getWishlist($request);

                    foreach ($wishlist->getWishlistProducts() as $wishlistProduct) {
                        if ($variant === $wishlistProduct->getVariant()) {

                            $this->wishlistProductManager->remove($wishlistProduct);
                        }
                    }
                }
                $this->wishlistProductManager->flush();
                $this->flashBag->add('success', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.removed_selected_wishlist_items'));

                return new RedirectResponse($this->urlGenerator->generate('bitbag_sylius_wishlist_plugin_shop_wishlist_list_products'));
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
}
