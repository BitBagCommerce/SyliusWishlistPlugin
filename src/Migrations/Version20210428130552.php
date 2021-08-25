<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @deprecated Since bitbag/wishlist-plugin 2.0: Doctrine migrations existing in a bundle will be removed, move migrations to the project directory.
 */
final class Version20210428130552 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add missing constraint to bitbag_wishlist_product';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE bitbag_wishlist_product DROP FOREIGN KEY FK_3DBE67A0FB8E54CD');
        $this->addSql('ALTER TABLE bitbag_wishlist_product ADD CONSTRAINT FK_3DBE67A0FB8E54CD FOREIGN KEY (wishlist_id) REFERENCES bitbag_wishlist (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE bitbag_wishlist_product DROP FOREIGN KEY FK_3DBE67A0FB8E54CD');
        $this->addSql('ALTER TABLE bitbag_wishlist_product ADD CONSTRAINT FK_3DBE67A0FB8E54CD FOREIGN KEY (wishlist_id) REFERENCES bitbag_wishlist (id)');
    }
}
