<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller;

use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Bundle\OrderBundle\Controller\OrderItemController as BaseController;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\CartActions;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class OrderItemController extends BaseController
{
    public function addAction(Request $request): Response
    {
        $cart = $this->getCurrentCart();
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, CartActions::ADD);
        /** @var OrderItemInterface $orderItem */
        $orderItem = $this->newResourceFactory->create($configuration, $this->factory);

        $this->getQuantityModifier()->modify($orderItem, 1);

        $form = $this->getFormFactory()->create(
            $configuration->getFormType(),
            $this->createAddToCartCommand($cart, $orderItem),
            $configuration->getFormOptions()
        );

        $form->handleRequest($request);

        /** @var SubmitButton $addToWishlist */
        $addToWishlist = $form->get('addToWishlist');

        if ($addToWishlist->isClicked()) {
            /** @var AddToCartCommandInterface $addToCartCommand */
            $addToCartCommand = $form->getData();

            /** @var OrderItemInterface $item */
            $item = $addToCartCommand->getCartItem();
            $variant = $item->getVariant();

            if ($variant === null) {
                throw new NotFoundHttpException('Could not find variant');
            }

            return new Response($this->generateUrl('bitbag_sylius_wishlist_plugin_shop_wishlist_add_product_variant', [
                'variantId' => $variant->getId(),
            ]));
        }

        return parent::addAction($request);
    }
}
