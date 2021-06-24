<?php

declare(strict_types=1);


namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;


use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddProductToWishlist;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddProductVariantToWishlist;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistProductFactoryInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use BitBag\SyliusWishlistPlugin\Resolver\AnonymousWishlistResolverInterface;
use BitBag\SyliusWishlistPlugin\Resolver\ShopUserWishlistResolverInterface;
use Doctrine\Persistence\ObjectManager;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductVariantRepository;
use Sylius\Component\Core\Model\ProductVariant;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class AddProductVariantToWishlistHandler implements MessageHandlerInterface
{
    private WishlistProductFactoryInterface $wishlistProductFactory;

    private ObjectManager $wishlistManager;

    private ProductVariantRepositoryInterface $productVariantRepository;

    private WishlistRepositoryInterface $wishlistRepository;

    public function __construct(
        WishlistProductFactoryInterface $wishlistProductFactory,
        WishlistRepositoryInterface $wishlistRepository,
        ObjectManager $wishlistManager,
        ProductVariantRepositoryInterface $productVariantRepository
    ) {
        $this->wishlistProductFactory = $wishlistProductFactory;
        $this->wishlistManager = $wishlistManager;
        $this->productVariantRepository = $productVariantRepository;
        $this->wishlistRepository = $wishlistRepository;
    }

    public function __invoke(AddProductVariantToWishlist $addProductVariantToWishlist): WishlistInterface
    {
        $variant = $this->productVariantRepository->find($addProductVariantToWishlist->productVariant);
        $wishlist = $this->wishlistRepository->findByToken($addProductVariantToWishlist->getWishlistTokenValue());

        if (!$variant && !$wishlist) {
            throw new NotFoundHttpException();
        }

        $wishlistProduct = $this->wishlistProductFactory->createForWishlistAndVariant($wishlist, $variant);

        $wishlist->addWishlistProduct($wishlistProduct);

        $this->wishlistManager->persist($wishlist);
        $this->wishlistManager->flush();

        return $wishlist;
    }
}
