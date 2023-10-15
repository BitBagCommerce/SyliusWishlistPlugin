<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231015123538 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adding timestampable columns to track creating and updating a wishlist';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE bitbag_wishlist ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE bitbag_wishlist DROP created_at, DROP updated_at');
    }
}
