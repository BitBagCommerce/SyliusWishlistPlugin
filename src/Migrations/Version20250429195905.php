<?php

declare(strict_types=1);

namespace Sylius\WishlistPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250429195905 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'This migration renames indexes in sylius_wishlist and sylius_wishlist_product tables.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_wishlist RENAME INDEX idx_578d4e77a45d93bf TO IDX_635A71DEA45D93BF');
        $this->addSql('ALTER TABLE sylius_wishlist RENAME INDEX idx_578d4e7772f5a1aa TO IDX_635A71DE72F5A1AA');
        $this->addSql('ALTER TABLE sylius_wishlist_product RENAME INDEX idx_3dbe67a0fb8e54cd TO IDX_8D0D7C6DFB8E54CD');
        $this->addSql('ALTER TABLE sylius_wishlist_product RENAME INDEX idx_3dbe67a04584665a TO IDX_8D0D7C6D4584665A');
        $this->addSql('ALTER TABLE sylius_wishlist_product RENAME INDEX idx_3dbe67a03b69a9af TO IDX_8D0D7C6D3B69A9AF');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_wishlist RENAME INDEX idx_635a71dea45d93bf TO IDX_578D4E77A45D93BF');
        $this->addSql('ALTER TABLE sylius_wishlist RENAME INDEX idx_635a71de72f5a1aa TO IDX_578D4E7772F5A1AA');
        $this->addSql('ALTER TABLE sylius_wishlist_product RENAME INDEX idx_8d0d7c6d4584665a TO IDX_3DBE67A04584665A');
        $this->addSql('ALTER TABLE sylius_wishlist_product RENAME INDEX idx_8d0d7c6d3b69a9af TO IDX_3DBE67A03B69A9AF');
        $this->addSql('ALTER TABLE sylius_wishlist_product RENAME INDEX idx_8d0d7c6dfb8e54cd TO IDX_3DBE67A0FB8E54CD');
    }
}
