<?php

declare(strict_types=1);


namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;


use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddProductVariantToWishlist;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistProductFactoryInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use BitBag\SyliusWishlistPlugin\Resolver\AnonymousWishlistResolverInterface;
use BitBag\SyliusWishlistPlugin\Resolver\ShopUserWishlistResolverInterface;
use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class AddProductVariantToWishlistHandler implements MessageHandlerInterface
{
    private TokenStorageInterface $tokenStorage;

    private ShopUserWishlistResolverInterface $shopUserWishlistResolver;

    private AnonymousWishlistResolverInterface $anonymousWishlistResolver;

    private WishlistProductFactoryInterface $wishlistProductFactory;

    private ObjectManager $wishlistManager;

    private ProductVariantRepositoryInterface $productVariantRepository;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        ShopUserWishlistResolverInterface $shopUserWishlistResolver,
        AnonymousWishlistResolverInterface $anonymousWishlistResolver,
        WishlistProductFactoryInterface $wishlistProductFactory,
        ObjectManager $wishlistManager,
        ProductVariantRepositoryInterface $productVariantRepository
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->shopUserWishlistResolver = $shopUserWishlistResolver;
        $this->anonymousWishlistResolver = $anonymousWishlistResolver;
        $this->wishlistProductFactory = $wishlistProductFactory;
        $this->wishlistManager = $wishlistManager;
        $this->productVariantRepository = $productVariantRepository;
    }

    public function __invoke(AddProductVariantToWishlist $addProductVariantToWishlist): WishlistInterface
    {
        $token = $this->tokenStorage->getToken();
        $user = $token ? $token->getUser() : null;

        $productVariant = $this->productVariantRepository->find($addProductVariantToWishlist->productVariant);

        if (!$productVariant) {
            throw new NotFoundHttpException();
        }

        if ($user instanceof ShopUserInterface) {
            $wishlist = $this->shopUserWishlistResolver->resolve($user);
        } else {
            $wishlistToken = $addProductVariantToWishlist->getWishlistTokenValue();

            $wishlist = $this->anonymousWishlistResolver->resolve($wishlistToken);
        }

        $wishlistProduct = $this->wishlistProductFactory->createForWishlistAndVariant($wishlist, $productVariant);
        $wishlist->addWishlistProduct($wishlistProduct);

        $this->wishlistManager->persist($wishlist);
        $this->wishlistManager->flush();

        return $wishlist;
    }
}
