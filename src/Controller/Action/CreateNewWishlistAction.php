<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Form\Type\CreateNewWishlistType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

final class CreateNewWishlistAction
{
    private FormFactoryInterface $formFactory;

    private MessageBusInterface $commandBus;

    private Environment $twigEnvironment;

    private FlashBagInterface $flashBag;

    private TranslatorInterface $translator;

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        FormFactoryInterface $formFactory,
        MessageBusInterface $commandBus,
        Environment $twigEnvironment,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->formFactory = $formFactory;
        $this->commandBus = $commandBus;
        $this->twigEnvironment = $twigEnvironment;
        $this->flashBag = $flashBag;
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
    }

    public function __invoke(Request $request): Response
    {
        $form = $this->formFactory->create(CreateNewWishlistType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $createNewWishlist = $form->getData();
            $this->commandBus->dispatch($createNewWishlist);

            $this->flashBag->add(
                'success',
                $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.create_new_wishlist')
            );

            return new RedirectResponse($this->urlGenerator->generate('bitbag_sylius_wishlist_plugin_shop_wishlist_list_wishlists'));
        }

        return new Response(
            $this->twigEnvironment->render('@BitBagSyliusWishlistPlugin/CreateWishlist/index.html.twig', [
                'form' => $form->createView(),
            ])
        );
    }
}
