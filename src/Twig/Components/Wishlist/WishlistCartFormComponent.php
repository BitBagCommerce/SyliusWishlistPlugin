<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Twig\Components\Wishlist;

use Doctrine\Persistence\ObjectManager;
use Sylius\Bundle\UiBundle\Twig\Component\ResourceFormComponentTrait;
use Sylius\Bundle\UiBundle\Twig\Component\TemplatePropTrait;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\ComponentToolsTrait;

#[AsLiveComponent]
class WishlistCartFormComponent
{
    use ComponentToolsTrait;

    /** @use ResourceFormComponentTrait<OrderInterface> */
    use ResourceFormComponentTrait;

    use TemplatePropTrait;

    public const WISHLIST_CHANGED = 'sylius:shop:cart_changed';

    public const WISHLIST_CLEARED = 'sylius:shop:cart_cleared';

    public bool $shouldSaveCart = true;

//    public function __construct(
//        OrderRepositoryInterface $orderRepository,
//        FormFactoryInterface $formFactory,
//        string $resourceClass,
//        string $formClass,
//    ) {
//        $this->initialize($orderRepository, $formFactory, $resourceClass, $formClass);
//    }
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        FormFactoryInterface $formFactory,
        string $resourceClass,
        string $formClass,
        protected readonly ObjectManager $manager,
        protected readonly EventDispatcherInterface $eventDispatcher,
    ) {
        $this->initialize($orderRepository, $formFactory, $resourceClass, $formClass);
    }

    public static function getComponentName(): string
    {
        return 'wishlist';
    }

    #[LiveAction]
    public function removeItem(#[LiveArg] int $index): void
    {
    }

    #[LiveAction]
    public function changeVariant()
    {
    }

    #[LiveAction]
    public function updateSelectedVariant()
    {
        dd('dfdfdfdfd');
    }
}
