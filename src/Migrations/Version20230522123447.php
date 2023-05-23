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
final class Version20230522123447 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add missing fields and indexes to wishlists';
    }

    public function up(Schema $schema): void
    {
        # Missing fields
        $this->addSql('ALTER TABLE bitbag_wishlist ADD channel_id INT DEFAULT NULL, ADD name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE bitbag_wishlist ADD CONSTRAINT FK_578D4E7772F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_578D4E7772F5A1AA ON bitbag_wishlist (channel_id)');

        # Missing indexes
        $this->addSql('ALTER TABLE bitbag_wishlist DROP INDEX UNIQ_578D4E77A45D93BF, ADD INDEX IDX_578D4E77A45D93BF (shop_user_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE bitbag_wishlist DROP FOREIGN KEY FK_578D4E7772F5A1AA');
        $this->addSql('DROP INDEX IDX_578D4E7772F5A1AA ON bitbag_wishlist');
        $this->addSql('ALTER TABLE bitbag_wishlist DROP channel_id, DROP name');

        $this->addSql('ALTER TABLE bitbag_wishlist DROP INDEX IDX_578D4E77A45D93BF, ADD UNIQUE INDEX UNIQ_578D4E77A45D93BF (shop_user_id)');
    }
}
