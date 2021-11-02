<?php
declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ExportSelectedProductsToCsv
{
    public function __invoke(Request $request): Response
    {
        return new Response("html");
    }
}

