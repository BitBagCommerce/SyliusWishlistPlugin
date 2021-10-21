<?php
declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use ApiPlatform\Core\Api\UrlGeneratorInterface;
use BitBag\SyliusWishlistPlugin\Context\WishlistContextInterface;
use BitBag\SyliusWishlistPlugin\Form\Type\AddProductsToCartType;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

final class AddSelectedProductsToCart
{
    private WishlistContextInterface $wishlistContext;

    private CartContextInterface $cartContext;

    private FormFactoryInterface $formFactory;

    private OrderModifierInterface $orderModifier;

    private EntityManagerInterface $cartManager;

    private FlashBagInterface $flashBag;

    private TranslatorInterface $translator;

    private Environment $twigEnvironment;

    private OrderItemQuantityModifierInterface $itemQuantityModifier;

    public function __construct(
        WishlistContextInterface $wishlistContext,
        CartContextInterface $cartContext,
        FormFactoryInterface $formFactory,
        OrderModifierInterface $orderModifier,
        EntityManagerInterface $cartManager,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        Environment $twigEnvironment,
        OrderItemQuantityModifierInterface $itemQuantityModifier
    ) {
        $this->wishlistContext = $wishlistContext;
        $this->cartContext = $cartContext;
        $this->formFactory = $formFactory;
        $this->orderModifier = $orderModifier;
        $this->flashBag = $flashBag;
        $this->twigEnvironment = $twigEnvironment;
        $this->cartManager = $cartManager;
        $this->translator = $translator;
        $this->itemQuantityModifier = $itemQuantityModifier;
    }

    public function __invoke(Request $request): Response
    {
        $wishlist = $this->wishlistContext->getWishlist($request);
        $cart = $this->cartContext->getCart();

        $form = $this->formFactory->create(AddProductsToCartType::class, null, [
            'cart' => $cart,
            'wishlist_products' => $wishlist->getWishlistProducts(),
        ]);

        $variantIds = $request->request->get('variantId');

        if ($variantIds) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->handleCartItems($form, $variantIds);

                $this->flashBag->add('success', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.added_selected_wishlist_items_to_cart'));

                return new Response(
                    $this->twigEnvironment->render('@BitBagSyliusWishlistPlugin/WishlistDetails/index.html.twig', [
                        'wishlist' => $wishlist,
                        'form' => $form->createView(),
                    ])
                );
            }
        } else {
            $this->flashBag->add('error', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.select_products'));
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

    private function handleCartItems(FormInterface $form, array $variantIds): void
    {

        /** @var AddToCartCommandInterface $command */
        foreach ($form->getData() as $command) {
            if (in_array($command->getCartItem()->getVariant()->getId(), $variantIds)) {
                if (0 === $command->getCartItem()->getQuantity()) {
                    $this->itemQuantityModifier->modify($command->getCartItem(), 1);
                }
                $this->orderModifier->addToOrder($command->getCart(), $command->getCartItem());
                $this->cartManager->persist($command->getCart());
            }
        }

        $this->cartManager->flush();
    }
}
