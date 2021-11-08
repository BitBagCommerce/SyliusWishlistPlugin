<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\CreateNewWishlist;
use BitBag\SyliusWishlistPlugin\Form\Type\CreateNewWishlistType;
use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

final class CreateNewWishlistHandler implements MessageHandlerInterface
{
    private ObjectManager $wishlistManager;

    private FlashBagInterface $flashBag;

    private TranslatorInterface $translator;

    private UrlGeneratorInterface $urlGenerator;

    private Environment $twigEnvironment;

    public function __construct(
        ObjectManager $wishlistManager,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator,
        Environment $twigEnvironment
    ) {
        $this->wishlistManager = $wishlistManager;
        $this->flashBag = $flashBag;
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
        $this->twigEnvironment = $twigEnvironment;
    }

    public function __invoke(CreateNewWishlist $createNewWishlist)
    {
        $token = $createNewWishlist->getTokenStorage();
        $wishlistFactory = $createNewWishlist->getWishlistFactory();
        $user = $token->getToken() ? $token->getToken()->getUser() : null;

        if ($user instanceof ShopUserInterface) {
            $wishlist = $wishlistFactory->createForUser($user);
        } else {
            $wishlist = $wishlistFactory->createNew();
        }
        $wishlist->setName('amen');
        $this->wishlistManager->persist($wishlist);
        $this->wishlistManager->flush();
    }
}