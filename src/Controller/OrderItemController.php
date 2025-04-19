<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\WishlistPlugin\Controller;

use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Bundle\OrderBundle\Controller\OrderItemController as BaseController;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Order\CartActions;
use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

//final class OrderItemController extends BaseController
//{
//    public function addAction(Request $request): Response
//    {
//        $cart = $this->getCurrentCart();
//        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
//
//        $this->isGrantedOr403($configuration, CartActions::ADD);
//        /** @var OrderItemInterface $orderItem */
//        $orderItem = $this->newResourceFactory->create($configuration, $this->factory);
//
//        $this->getQuantityModifier()->modify($orderItem, 1);
//
//        /** @var string $formType */
//        $formType = $configuration->getFormType();
//
//        $form = $this->getFormFactory()->create(
//            $formType,
//            $this->createAddToCartCommand($cart, $orderItem),
//            $configuration->getFormOptions(),
//        );
//
//        $form->handleRequest($request);
//
//        /** @var SubmitButton $addToWishlist */
//        $addToWishlist = $form->get('addToWishlist');
//
//        if ($addToWishlist->isClicked()) {
//            /** @var AddToCartCommandInterface $addToCartCommand */
//            $addToCartCommand = $form->getData();
//
//            /** @var OrderItemInterface $item */
//            $item = $addToCartCommand->getCartItem();
//            /** @var ?ProductVariantInterface $variant */
//            $variant = $item->getVariant();
//
//            /** @var ?WishlistInterface $wishlist */
//            $wishlist = $form->get('wishlists')->getData();
//
//            if (null === $variant) {
//                throw new NotFoundHttpException('Could not find variant');
//            }
//
//            if (null === $wishlist) {
//                /** @var Session $session */
//                $session = $request->getSession();
//                /** @var ?TranslatorInterface $translator */
//                $translator = $this->get('translator');
//
//                if (null !== $translator) {
//                    $session->getFlashBag()->add('error', $translator->trans('sylius_wishlist_plugin.ui.go_to_wishlist_failure'));
//                }
//
//                return new Response($this->generateUrl('sylius_shop_homepage'));
//            }
//
//            return new Response($this->generateUrl('sylius_wishlist_plugin_shop_locale_wishlist_add_product_variant', [
//                'wishlistId' => $wishlist->getId(),
//                'variantId' => $variant->getId(),
//            ]));
//        }
//
//        return parent::addAction($request);
//    }
//}
