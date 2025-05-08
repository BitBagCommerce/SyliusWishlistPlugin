<?php

declare(strict_types=1);

namespace Sylius\WishlistPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250427090022 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'This migration renames the wishlist tables from BitBag to Sylius.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('RENAME TABLE bitbag_wishlist TO sylius_wishlist');
        $this->addSql('RENAME TABLE bitbag_wishlist_product TO sylius_wishlist_product');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('RENAME TABLE sylius_wishlist TO bitbag_wishlist');
        $this->addSql('RENAME TABLE sylius_wishlist_product TO bitbag_wishlist_product');
    }
}
