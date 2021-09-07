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
final class Version20201029161558 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE bitbag_wishlist (id INT AUTO_INCREMENT NOT NULL, shop_user_id INT DEFAULT NULL, token VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_578D4E775F37A13B (token), UNIQUE INDEX UNIQ_578D4E77A45D93BF (shop_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bitbag_wishlist_product (id INT AUTO_INCREMENT NOT NULL, wishlist_id INT NOT NULL, product_id INT DEFAULT NULL, variant_id INT DEFAULT NULL, INDEX IDX_3DBE67A0FB8E54CD (wishlist_id), INDEX IDX_3DBE67A04584665A (product_id), INDEX IDX_3DBE67A03B69A9AF (variant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bitbag_wishlist ADD CONSTRAINT FK_578D4E77A45D93BF FOREIGN KEY (shop_user_id) REFERENCES sylius_shop_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_wishlist_product ADD CONSTRAINT FK_3DBE67A0FB8E54CD FOREIGN KEY (wishlist_id) REFERENCES bitbag_wishlist (id)');
        $this->addSql('ALTER TABLE bitbag_wishlist_product ADD CONSTRAINT FK_3DBE67A04584665A FOREIGN KEY (product_id) REFERENCES sylius_product (id)');
        $this->addSql('ALTER TABLE bitbag_wishlist_product ADD CONSTRAINT FK_3DBE67A03B69A9AF FOREIGN KEY (variant_id) REFERENCES sylius_product_variant (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE bitbag_wishlist_product DROP FOREIGN KEY FK_3DBE67A0FB8E54CD');
        $this->addSql('DROP TABLE bitbag_wishlist');
        $this->addSql('DROP TABLE bitbag_wishlist_product');
    }
}
