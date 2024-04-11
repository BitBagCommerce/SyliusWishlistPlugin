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

final class Version20231030194730 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds token_idx and channel_shop_user_token_idx indexes to database';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE INDEX token_idx ON bitbag_wishlist (token)');
        $this->addSql('CREATE INDEX channel_shop_user_token_idx ON bitbag_wishlist (channel_id, shop_user_id, token)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX token_idx ON bitbag_wishlist');
        $this->addSql('DROP INDEX channel_shop_user_token_idx ON bitbag_wishlist');
    }
}
