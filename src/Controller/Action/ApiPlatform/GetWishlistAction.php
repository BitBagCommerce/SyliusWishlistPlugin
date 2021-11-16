<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action\ApiPlatform;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistFactoryInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Doctrine\Persistence\ObjectManager;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\RemoveWishlist;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

final class GetWishlistAction
{
    private MessageBusInterface $messageBus;

    public function __construct(
        TokenStorageInterface $tokenStorage, 
        WishlistRepositoryInterface $wishlistRepository,
        WishlistFactoryInterface $wishlistFactory,
        ObjectManager $wishlistManager,
        string $wishlistCookieToken)
    {
        $this->tokenStorage = $tokenStorage;
        $this->wishlistRepository = $wishlistRepository;
        $this->wishlistFactory = $wishlistFactory;
        $this->wishlistManager = $wishlistManager;
        $this->wishlistCookieToken = $wishlistCookieToken;
    }

    public function __invoke(Request $request)
    {
        $token = $this->tokenStorage->getToken();
        $user = $token ? $token->getUser() : null;
        $wishlist = null;
        if ($user instanceof ShopUserInterface) {
            $wishlist = $this->wishlistRepository->findOneByShopUser($user);
            return $wishlist;
        }
        else{
            $cookieWishlistToken = $request->cookies->get($this->wishlistCookieToken);
            if($cookieWishlistToken){
                $wishlist = $this->wishlistRepository->findByToken($cookieWishlistToken);    
            }
        }

        if(!$wishlist){
            $wishlist = $this->wishlistFactory->createNew();
            if ($user instanceof ShopUserInterface) {
                $wishlist->setShopUser($user);
            }
            $this->wishlistManager->persist($wishlist);
            $this->wishlistManager->flush();
        }

        return $wishlist;
        
    }
}
