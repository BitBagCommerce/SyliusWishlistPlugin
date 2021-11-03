<?php
declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddWishlistProduct;
use BitBag\SyliusWishlistPlugin\Context\WishlistContextInterface;
use BitBag\SyliusWishlistPlugin\Form\Type\WishlistCollectionType;
use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Twig\Environment;

final class ExportSelectedProductsToCsv
{
    private WishlistContextInterface $wishlistContext;

    private CartContextInterface $cartContext;

    private FormFactoryInterface $formFactory;

    private Environment $twigEnvironment;

    private FlashBagInterface $flashBag;

    public function __construct(
        WishlistContextInterface $wishlistContext,
        CartContextInterface $cartContext,
        FormFactoryInterface $formFactory,
        Environment $twigEnvironment,
        FlashBagInterface $flashBag

    ) {
        $this->wishlistContext = $wishlistContext;
        $this->cartContext = $cartContext;
        $this->formFactory = $formFactory;
        $this->twigEnvironment = $twigEnvironment;
        $this->flashBag = $flashBag;
    }

    public function __invoke(Request $request): Response
    {
        $wishlist = $this->wishlistContext->getWishlist($request);
        $cart = $this->cartContext->getCart();

        $commandsArray = new ArrayCollection();

        foreach ($wishlist->getWishlistProducts() as $wishlistProductItem) {
            $wishlistProductCommand = new AddWishlistProduct();
            $wishlistProductCommand->setWishlistProduct($wishlistProductItem);
            $commandsArray->add($wishlistProductCommand);
        }

        $form = $this->formFactory->create(WishlistCollectionType::class, ['items' => $commandsArray], [
            'cart' => $cart,

        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = fopen('php://temp', 'w');

            $this->exportToCSV($form->getData(), $file);

            rewind($file);
            $response = new Response(stream_get_contents($file));
            fclose($file);

            $response->headers->set('Content-Type', 'text/csv');
            $dateTime = new \DateTime('now');
            $dateTimeString = $dateTime->format('Y-m-d H:i:s');
            $response->headers->set('Content-Disposition', 'attachment; filename=export' .$dateTimeString .'.csv');

            return $response;
        }

        foreach ($form->getErrors() as $error) {
            $this->flashBag->add('error', $error->getMessage());
        }

        return new Response(
            $this->twigEnvironment->render('@BitBagSyliusWishlistPlugin/WishlistDetails/index.html.twig', [
                'wishlist' => $wishlist,
                'form' => $form->createView(),
            ])
        );
    }

    private function exportToCSV(array $wishlistProductsCommands, $file): void
    {
        $csvWishlistHeaders = [
            'variantId',
            'quantity',
        ];

        fputcsv($file, $csvWishlistHeaders);
        $csvWishlistBody = [];
        foreach ($wishlistProductsCommands as $wishlistProducts) {
            /** @var AddWishlistProduct $wishlistProduct */
            foreach ($wishlistProducts as $wishlistProduct) {
                if ($wishlistProduct->isSelected()) {
                    $csvWishlistItem = [
                        $wishlistProduct->getCartItem()->getCartItem()->getVariant()->getId(),
                        $wishlistProduct->getCartItem()->getCartItem()->getQuantity(),
                    ];
                    $csvWishlistBody[] = $csvWishlistItem;
                }
            }
        }
        foreach ($csvWishlistBody as $data) {
            fputcsv($file, $data);
        }
    }
}
