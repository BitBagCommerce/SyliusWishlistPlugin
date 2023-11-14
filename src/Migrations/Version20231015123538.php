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

final class Version20231015123538 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adding timestampable columns to track creating and updating a wishlist';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE bitbag_wishlist ADD created_at DATETIME DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('UPDATE bitbag_wishlist SET created_at =  NOW()');
        $this->addSql('ALTER TABLE bitbag_wishlist CHANGE COLUMN created_at created_at DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE bitbag_wishlist DROP created_at, DROP updated_at');
    }
}
