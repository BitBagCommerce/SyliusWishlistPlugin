<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddWishlistProduct;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\ExportWishlistToCsv;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ExportWishlistToCsvHandler implements MessageHandlerInterface
{
    private TranslatorInterface $translator;

    private FlashBagInterface $flashBag;

    private UrlGeneratorInterface $urlGenerator;

    private int $itemsProcessed = 0;

    public function __construct(
        TranslatorInterface $translator,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->translator = $translator;
        $this->flashBag = $flashBag;
        $this->urlGenerator = $urlGenerator;
    }

    public function __invoke(ExportWishlistToCsv $exportWishlistToCsv): Response
    {
        $wishlistProducts = $exportWishlistToCsv->getWishlistProducts();
        $file = $exportWishlistToCsv->getFile();

        $this->putDataToCsv($wishlistProducts, $file);

        if (0 < $this->itemsProcessed) {
            return $this->returnCsvFileAsResponse($file);
        }
        $this->flashBag->add('error', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.select_products'));

        return new RedirectResponse($this->urlGenerator->generate('bitbag_sylius_wishlist_plugin_shop_wishlist_list_products'));
    }

    private function putDataToCsv(Collection $wishlistProducts, \SplFileObject $file): void
    {
        /** @var AddWishlistProduct $wishlistProduct */
        foreach ($wishlistProducts as $wishlistProduct) {
            if (!$wishlistProduct->isSelected()) {
                continue;
            }
            $csvWishlistItem = [
                    $wishlistProduct->getCartItem()->getCartItem()->getVariant()->getId(),
                    $wishlistProduct->getWishlistProduct()->getProduct()->getId(),
                    $wishlistProduct->getCartItem()->getCartItem()->getVariant()->getCode(),
                ];
            $file->fputcsv($csvWishlistItem);
            ++$this->itemsProcessed;
        }
    }

    private function returnCsvFileAsResponse(\SplFileObject $file): Response
    {
        $file->rewind();
        $response = new Response($file->fread(5000));

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename=export.csv');

        return $response;
    }
}
