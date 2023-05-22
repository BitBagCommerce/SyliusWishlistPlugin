<?php

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
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bitbag_wishlist DROP INDEX UNIQ_578D4E77A45D93BF, ADD INDEX IDX_578D4E77A45D93BF (shop_user_id)');
        $this->addSql('DROP INDEX UNIQ_578D4E775F37A13B ON bitbag_wishlist');
        $this->addSql('ALTER TABLE bitbag_wishlist ADD channel_id INT DEFAULT NULL, ADD name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE bitbag_wishlist ADD CONSTRAINT FK_578D4E7772F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_578D4E7772F5A1AA ON bitbag_wishlist (channel_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bitbag_wishlist DROP INDEX IDX_578D4E77A45D93BF, ADD UNIQUE INDEX UNIQ_578D4E77A45D93BF (shop_user_id)');
        $this->addSql('ALTER TABLE bitbag_wishlist DROP FOREIGN KEY FK_578D4E7772F5A1AA');
        $this->addSql('DROP INDEX IDX_578D4E7772F5A1AA ON bitbag_wishlist');
        $this->addSql('ALTER TABLE bitbag_wishlist DROP channel_id, DROP name');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_578D4E775F37A13B ON bitbag_wishlist (token)');
    }
}
