<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Twig\Components\Wishlist;

use BitBag\SyliusWishlistPlugin\Form\Type\WishlistCollectionType;
use BitBag\SyliusWishlistPlugin\Processor\WishlistCommandProcessorInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use BitBag\SyliusWishlistPlugin\Resolver\TokenUserResolverInterface;
use BitBag\SyliusWishlistPlugin\Resolver\WishlistCookieTokenResolverInterface;
use Doctrine\Persistence\ObjectManager;
use Sylius\Bundle\UiBundle\Twig\Component\ResourceFormComponentTrait;
use Sylius\Bundle\UiBundle\Twig\Component\TemplatePropTrait;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\ComponentToolsTrait;

#[AsLiveComponent]
class WishlistCartFormComponent
{
    use ComponentToolsTrait;

    /** @use ResourceFormComponentTrait<OrderInterface> */
    use ResourceFormComponentTrait;

    use TemplatePropTrait;

    public const WISHLIST_CART_CHANGED = 'wishlist:form:cart';

    public const WISHLIST_CART_CLEARED = 'wishlist:cart_cleared';

    public bool $shouldSaveCart = true;

    public function __construct(
        private WishlistRepositoryInterface $wishlistRepository,
        FormFactoryInterface $formFactory,
        string $resourceClass,
        string $formClass,
        protected readonly ObjectManager $manager,
        protected readonly EventDispatcherInterface $eventDispatcher,
        private CartContextInterface $cartContext,
        private WishlistCommandProcessorInterface $wishlistCommandProcessor,
        private WishlistCookieTokenResolverInterface $wishlistCookieTokenResolver,
        private TokenStorageInterface $tokenStorage,
        private TokenUserResolverInterface $tokenUserResolver,
    ) {
        $this->initialize($wishlistRepository, $formFactory, $resourceClass, $formClass);
    }

    protected function instantiateForm(): FormInterface
    {
        $wishlistId = $this->resource->getId();

        $cart = $this->cartContext->getCart();

        $wishlist = $this->wishlistRepository->find($wishlistId);

        $commandsArray = $this->wishlistCommandProcessor->createWishlistItemsCollection($wishlist->getWishlistProducts());

        return $this->formFactory->create(WishlistCollectionType::class, ['items' => $commandsArray], [
            'cart' => $cart,
        ]);
    }

    private function getDataModelValue(): string
    {
        return 'debounce(500)|*';
    }

    public static function getComponentName(): string
    {
        return 'wishlist';
    }
}
