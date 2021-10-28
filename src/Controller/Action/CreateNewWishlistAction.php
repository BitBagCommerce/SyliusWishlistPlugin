<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Factory\WishlistFactoryInterface;
use BitBag\SyliusWishlistPlugin\Form\Type\CreateNewWishlistType;
use BitBag\SyliusWishlistPlugin\Resolver\ShopUserWishlistResolverInterface;
use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

final class CreateNewWishlistAction
{
    private ObjectManager $wishlistManager;

    private FlashBagInterface $flashBag;

    private TranslatorInterface $translator;

    private UrlGeneratorInterface $urlGenerator;

    private FormFactoryInterface $formFactory;

    private Environment $twigEnvironment;

    private WishlistFactoryInterface $wishlistFactory;

    private TokenStorageInterface $tokenStorage;

    private ShopUserWishlistResolverInterface $shopUserWishlistResolver;

    public function __construct(
        ObjectManager $wishlistManager,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator,
        FormFactoryInterface $formFactory,
        Environment $twigEnvironment,
        WishlistFactoryInterface $wishlistFactory,
        TokenStorageInterface $tokenStorage,
        ShopUserWishlistResolverInterface $shopUserWishlistResolver
    ) {
        $this->wishlistManager = $wishlistManager;
        $this->flashBag = $flashBag;
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
        $this->formFactory = $formFactory;
        $this->twigEnvironment = $twigEnvironment;
        $this->wishlistFactory = $wishlistFactory;
        $this->tokenStorage = $tokenStorage;
        $this->shopUserWishlistResolver = $shopUserWishlistResolver;
    }

    public function __invoke(Request $request): Response
    {
        $token = $this->tokenStorage->getToken();
        $user = $token ? $token->getUser() : null;
        $wishlist = $this->wishlistFactory->createNew();

        if ($user instanceof ShopUserInterface) {
            $wishlist = $this->shopUserWishlistResolver->resolve($user);
            //$wishlist = $this->wishlistFactory->createForUser($user);
        }

        $form = $this->formFactory->create(CreateNewWishlistType::class, $wishlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $wishlist = $form->getData();

            $this->wishlistManager->persist($wishlist);
            $this->wishlistManager->flush();

            $this->flashBag->add('success', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.create_new_wishlist'));

            return new RedirectResponse($this->urlGenerator->generate('bitbag_sylius_wishlist_plugin_shop_wishlist_list_wishlists'));
        }

        return new Response(
            $this->twigEnvironment->render('@BitBagSyliusWishlistPlugin/CreateWishlist/index.html.twig', [
                'wishlist' => $wishlist,
                'form' => $form->createView(),

            ])
        );
    }
}
