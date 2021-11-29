<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Command\Wishlist;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;

final class ImportWishlistFromCsv
{
    private File $file;

    private Request $request;

    public function __construct(File $file, Request $request)
    {
        $this->file = $file;
        $this->request = $request;
    }

    public function getFile(): File
    {
        return $this->file;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }
}
