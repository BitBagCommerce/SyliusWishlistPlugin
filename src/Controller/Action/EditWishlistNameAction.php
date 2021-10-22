<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Form\Type\CreateNewWishlistType;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

final class EditWishlistNameAction
{
    private WishlistRepositoryInterface $wishlistRepository;

    private EntityManagerInterface $wishlistManager;

    private FlashBagInterface $flashBag;

    private TranslatorInterface $translator;

    private UrlGeneratorInterface $urlGenerator;

    private FormFactoryInterface $formFactory;

    private Environment $twigEnvironment;

    public function __construct(
        WishlistRepositoryInterface $wishlistRepository,
        EntityManagerInterface $wishlistManager,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator,
        FormFactoryInterface $formFactory,
        Environment $twigEnvironment
    ) {
        $this->wishlistRepository = $wishlistRepository;
        $this->wishlistManager = $wishlistManager;
        $this->urlGenerator = $urlGenerator;
        $this->flashBag = $flashBag;
        $this->translator = $translator;
        $this->formFactory = $formFactory;
        $this->twigEnvironment = $twigEnvironment;
    }

    public function __invoke(int $wishlistId, Request $request): Response
    {
        /** @var WishlistInterface $wishlist */
        $wishlist = $this->wishlistRepository->find($wishlistId);

        $form = $this->formFactory->create(CreateNewWishlistType::class, $wishlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $wishlist = $form->getData();
            $this->wishlistManager->flush();

            $this->flashBag->add('success', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.edit_wishlist_name'));

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