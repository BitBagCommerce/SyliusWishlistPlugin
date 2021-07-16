<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210706083559 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bitbag_acl_role_translation DROP FOREIGN KEY FK_5E54EA372C2AC5D3');
        $this->addSql('ALTER TABLE bitbag_admin_users_roles_resources DROP FOREIGN KEY FK_56F911DBB65C5AC');
        $this->addSql('ALTER TABLE automatic_blacklisting_configuration_channel DROP FOREIGN KEY FK_EB48A61142145D53');
        $this->addSql('ALTER TABLE bitbag_automatic_blacklisting_rule DROP FOREIGN KEY FK_1881023273F32DD8');
        $this->addSql('ALTER TABLE blacklisting_rule_channel DROP FOREIGN KEY FK_13115922968538BF');
        $this->addSql('ALTER TABLE blacklisting_rule_customer_group DROP FOREIGN KEY FK_C82ADBCA968538BF');
        $this->addSql('ALTER TABLE bitbag_catalog_plugin_catalog_translation DROP FOREIGN KEY FK_BD0CEC362C2AC5D3');
        $this->addSql('ALTER TABLE bitbag_catalog_rule DROP FOREIGN KEY FK_7324734CC3C66FC');
        $this->addSql('ALTER TABLE bitbag_cms_block_channels DROP FOREIGN KEY FK_8417B073E9ED820C');
        $this->addSql('ALTER TABLE bitbag_cms_block_products DROP FOREIGN KEY FK_C4B9089FE9ED820C');
        $this->addSql('ALTER TABLE bitbag_cms_block_sections DROP FOREIGN KEY FK_5C95115DE9ED820C');
        $this->addSql('ALTER TABLE bitbag_cms_block_taxonomies DROP FOREIGN KEY FK_10C3E429E9ED820C');
        $this->addSql('ALTER TABLE bitbag_cms_block_translation DROP FOREIGN KEY FK_32897FDF2C2AC5D3');
        $this->addSql('ALTER TABLE bitbag_cms_faq_channels DROP FOREIGN KEY FK_FF6D59AC81BEC8C2');
        $this->addSql('ALTER TABLE bitbag_cms_faq_translation DROP FOREIGN KEY FK_8B30DD2E2C2AC5D3');
        $this->addSql('ALTER TABLE bitbag_cms_media_channels DROP FOREIGN KEY FK_D109622EE9ED820C');
        $this->addSql('ALTER TABLE bitbag_cms_media_products DROP FOREIGN KEY FK_91A7DAC2EA9FDD75');
        $this->addSql('ALTER TABLE bitbag_cms_media_sections DROP FOREIGN KEY FK_98BC300EA9FDD75');
        $this->addSql('ALTER TABLE bitbag_cms_media_translation DROP FOREIGN KEY FK_1FEC58972C2AC5D3');
        $this->addSql('ALTER TABLE bitbag_cms_page_translation DROP FOREIGN KEY FK_FDD074A63DA5256D');
        $this->addSql('ALTER TABLE bitbag_cms_page_channels DROP FOREIGN KEY FK_DCA4269C4663E4');
        $this->addSql('ALTER TABLE bitbag_cms_page_products DROP FOREIGN KEY FK_4D64FA85C4663E4');
        $this->addSql('ALTER TABLE bitbag_cms_page_sections DROP FOREIGN KEY FK_D548E347E9ED820C');
        $this->addSql('ALTER TABLE bitbag_cms_page_translation DROP FOREIGN KEY FK_FDD074A62C2AC5D3');
        $this->addSql('ALTER TABLE bitbag_cms_block_sections DROP FOREIGN KEY FK_5C95115DD823E37A');
        $this->addSql('ALTER TABLE bitbag_cms_media_sections DROP FOREIGN KEY FK_98BC300D823E37A');
        $this->addSql('ALTER TABLE bitbag_cms_page_sections DROP FOREIGN KEY FK_D548E347D823E37A');
        $this->addSql('ALTER TABLE bitbag_cms_section_translation DROP FOREIGN KEY FK_F99CA8582C2AC5D3');
        $this->addSql('ALTER TABLE bitbag_product_bundle_item DROP FOREIGN KEY FK_F429FEB69F5A6F5E');
        $this->addSql('ALTER TABLE bitbag_product_bundle_order_item DROP FOREIGN KEY FK_A615CDA9B7FE950B');
        $this->addSql('ALTER TABLE bitbag_shipping_export DROP FOREIGN KEY FK_20E62D9FEF84DE5E');
        $this->addSql('ALTER TABLE bitbag_shipping_gateway_method DROP FOREIGN KEY FK_8606B9CBEF84DE5E');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice DROP FOREIGN KEY FK_3AA279BF5CDB2AEB');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_line_item DROP FOREIGN KEY FK_C91408292989F1FD');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_tax_item DROP FOREIGN KEY FK_2951C61C2989F1FD');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice DROP FOREIGN KEY FK_3AA279BFCFE4AA36');
        $this->addSql('DROP TABLE automatic_blacklisting_configuration_channel');
        $this->addSql('DROP TABLE bitbag_acl_role');
        $this->addSql('DROP TABLE bitbag_acl_role_translation');
        $this->addSql('DROP TABLE bitbag_admin_users_roles_resources');
        $this->addSql('DROP TABLE bitbag_automatic_blacklisting_configuration');
        $this->addSql('DROP TABLE bitbag_automatic_blacklisting_rule');
        $this->addSql('DROP TABLE bitbag_blacklisting_rule');
        $this->addSql('DROP TABLE bitbag_catalog_catalog');
        $this->addSql('DROP TABLE bitbag_catalog_plugin_catalog_translation');
        $this->addSql('DROP TABLE bitbag_catalog_rule');
        $this->addSql('DROP TABLE bitbag_cms_block');
        $this->addSql('DROP TABLE bitbag_cms_block_channels');
        $this->addSql('DROP TABLE bitbag_cms_block_products');
        $this->addSql('DROP TABLE bitbag_cms_block_sections');
        $this->addSql('DROP TABLE bitbag_cms_block_taxonomies');
        $this->addSql('DROP TABLE bitbag_cms_block_translation');
        $this->addSql('DROP TABLE bitbag_cms_faq');
        $this->addSql('DROP TABLE bitbag_cms_faq_channels');
        $this->addSql('DROP TABLE bitbag_cms_faq_translation');
        $this->addSql('DROP TABLE bitbag_cms_media');
        $this->addSql('DROP TABLE bitbag_cms_media_channels');
        $this->addSql('DROP TABLE bitbag_cms_media_products');
        $this->addSql('DROP TABLE bitbag_cms_media_sections');
        $this->addSql('DROP TABLE bitbag_cms_media_translation');
        $this->addSql('DROP TABLE bitbag_cms_page');
        $this->addSql('DROP TABLE bitbag_cms_page_channels');
        $this->addSql('DROP TABLE bitbag_cms_page_products');
        $this->addSql('DROP TABLE bitbag_cms_page_sections');
        $this->addSql('DROP TABLE bitbag_cms_page_translation');
        $this->addSql('DROP TABLE bitbag_cms_section');
        $this->addSql('DROP TABLE bitbag_cms_section_translation');
        $this->addSql('DROP TABLE bitbag_fraud_suspicion');
        $this->addSql('DROP TABLE bitbag_product_bundle');
        $this->addSql('DROP TABLE bitbag_product_bundle_item');
        $this->addSql('DROP TABLE bitbag_product_bundle_order_item');
        $this->addSql('DROP TABLE bitbag_shipping_export');
        $this->addSql('DROP TABLE bitbag_shipping_gateway');
        $this->addSql('DROP TABLE bitbag_shipping_gateway_method');
        $this->addSql('DROP TABLE blacklisting_rule_channel');
        $this->addSql('DROP TABLE blacklisting_rule_customer_group');
        $this->addSql('DROP TABLE sylius_invoicing_plugin_billing_data');
        $this->addSql('DROP TABLE sylius_invoicing_plugin_invoice');
        $this->addSql('DROP TABLE sylius_invoicing_plugin_line_item');
        $this->addSql('DROP TABLE sylius_invoicing_plugin_sequence');
        $this->addSql('DROP TABLE sylius_invoicing_plugin_shop_billing_data');
        $this->addSql('DROP TABLE sylius_invoicing_plugin_tax_item');
        $this->addSql('DROP TABLE sylius_paypal_plugin_pay_pal_credentials');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE automatic_blacklisting_configuration_channel (automatic_blacklisting_configuration_id INT NOT NULL, channel_id INT NOT NULL, INDEX IDX_EB48A61142145D53 (automatic_blacklisting_configuration_id), INDEX IDX_EB48A61172F5A1AA (channel_id), PRIMARY KEY(automatic_blacklisting_configuration_id, channel_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE bitbag_acl_role (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, permissions LONGTEXT CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci` COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_BB098F3F77153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE bitbag_acl_role_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, name VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, locale VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, UNIQUE INDEX bitbag_acl_role_translation_uniq_trans (translatable_id, locale), INDEX IDX_5E54EA372C2AC5D3 (translatable_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE bitbag_admin_users_roles_resources (admin_user_id INT NOT NULL, role_resource_id INT NOT NULL, INDEX IDX_56F911D6352511C (admin_user_id), INDEX IDX_56F911DBB65C5AC (role_resource_id), PRIMARY KEY(admin_user_id, role_resource_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE bitbag_automatic_blacklisting_configuration (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, enabled TINYINT(1) NOT NULL, add_fraud_suspicion TINYINT(1) NOT NULL, permitted_fraud_suspicions_number INT DEFAULT NULL, permitted_fraud_suspicions_time VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE bitbag_automatic_blacklisting_rule (id INT AUTO_INCREMENT NOT NULL, configuration_id INT DEFAULT NULL, type VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, settings LONGTEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci` COMMENT \'(DC2Type:array)\', INDEX IDX_1881023273F32DD8 (configuration_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE bitbag_blacklisting_rule (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, attributes LONGTEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci` COMMENT \'(DC2Type:array)\', permitted_strikes INT NOT NULL, only_for_guests TINYINT(1) NOT NULL, enabled TINYINT(1) NOT NULL, for_unassigned_customers TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE bitbag_catalog_catalog (id INT AUTO_INCREMENT NOT NULL, start_date DATETIME DEFAULT NULL, end_date DATETIME DEFAULT NULL, code VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, connecting_rules VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, product_connecting_rules VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, template VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, sorting_type VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, display_products INT NOT NULL, UNIQUE INDEX UNIQ_5DAE312F77153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE bitbag_catalog_plugin_catalog_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, name VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, locale VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, UNIQUE INDEX bitbag_catalog_plugin_catalog_translation_uniq_trans (translatable_id, locale), INDEX IDX_BD0CEC362C2AC5D3 (translatable_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE bitbag_catalog_rule (id INT AUTO_INCREMENT NOT NULL, catalog_id INT DEFAULT NULL, type VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, configuration LONGTEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci` COMMENT \'(DC2Type:array)\', target VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, INDEX IDX_7324734CC3C66FC (catalog_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE bitbag_cms_block (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(64) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, enabled TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_321C84CF77153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE bitbag_cms_block_channels (block_id INT NOT NULL, channel_id INT NOT NULL, INDEX IDX_8417B073E9ED820C (block_id), INDEX IDX_8417B07372F5A1AA (channel_id), PRIMARY KEY(block_id, channel_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE bitbag_cms_block_products (block_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_C4B9089FE9ED820C (block_id), INDEX IDX_C4B9089F4584665A (product_id), PRIMARY KEY(block_id, product_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE bitbag_cms_block_sections (block_id INT NOT NULL, section_id INT NOT NULL, INDEX IDX_5C95115DD823E37A (section_id), INDEX IDX_5C95115DE9ED820C (block_id), PRIMARY KEY(block_id, section_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE bitbag_cms_block_taxonomies (block_id INT NOT NULL, taxon_id INT NOT NULL, INDEX IDX_10C3E429DE13F470 (taxon_id), INDEX IDX_10C3E429E9ED820C (block_id), PRIMARY KEY(block_id, taxon_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE bitbag_cms_block_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, name VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, content LONGTEXT CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, link LONGTEXT CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, locale VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, UNIQUE INDEX bitbag_cms_block_translation_uniq_trans (translatable_id, locale), INDEX IDX_32897FDF2C2AC5D3 (translatable_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE bitbag_cms_faq (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, position INT NOT NULL, enabled TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE bitbag_cms_faq_channels (faq_id INT NOT NULL, channel_id INT NOT NULL, INDEX IDX_FF6D59AC72F5A1AA (channel_id), INDEX IDX_FF6D59AC81BEC8C2 (faq_id), PRIMARY KEY(faq_id, channel_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE bitbag_cms_faq_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, question LONGTEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, answer LONGTEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, locale VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, UNIQUE INDEX bitbag_cms_faq_translation_uniq_trans (translatable_id, locale), INDEX IDX_8B30DD2E2C2AC5D3 (translatable_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE bitbag_cms_media (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(250) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, type VARCHAR(250) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, path VARCHAR(250) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, mime_type VARCHAR(250) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, enabled TINYINT(1) NOT NULL, width INT DEFAULT NULL, height INT DEFAULT NULL, UNIQUE INDEX UNIQ_DB2BB2E177153098 (code), UNIQUE INDEX UNIQ_DB2BB2E1B548B0F (path), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE bitbag_cms_media_channels (block_id INT NOT NULL, channel_id INT NOT NULL, INDEX IDX_D109622E72F5A1AA (channel_id), INDEX IDX_D109622EE9ED820C (block_id), PRIMARY KEY(block_id, channel_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE bitbag_cms_media_products (media_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_91A7DAC24584665A (product_id), INDEX IDX_91A7DAC2EA9FDD75 (media_id), PRIMARY KEY(media_id, product_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE bitbag_cms_media_sections (media_id INT NOT NULL, section_id INT NOT NULL, INDEX IDX_98BC300D823E37A (section_id), INDEX IDX_98BC300EA9FDD75 (media_id), PRIMARY KEY(media_id, section_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE bitbag_cms_media_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, name VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, content LONGTEXT CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, alt LONGTEXT CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, link LONGTEXT CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, locale VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, UNIQUE INDEX bitbag_cms_media_translation_uniq_trans (translatable_id, locale), INDEX IDX_1FEC58972C2AC5D3 (translatable_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE bitbag_cms_page (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(250) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, enabled TINYINT(1) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, publish_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_18F07F1B77153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE bitbag_cms_page_channels (page_id INT NOT NULL, channel_id INT NOT NULL, INDEX IDX_DCA426972F5A1AA (channel_id), INDEX IDX_DCA4269C4663E4 (page_id), PRIMARY KEY(page_id, channel_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE bitbag_cms_page_products (page_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_4D64FA854584665A (product_id), INDEX IDX_4D64FA85C4663E4 (page_id), PRIMARY KEY(page_id, product_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE bitbag_cms_page_sections (block_id INT NOT NULL, section_id INT NOT NULL, INDEX IDX_D548E347D823E37A (section_id), INDEX IDX_D548E347E9ED820C (block_id), PRIMARY KEY(block_id, section_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE bitbag_cms_page_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, image_id INT DEFAULT NULL, slug VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, name VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, breadcrumb VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, name_when_linked VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, description_when_linked VARCHAR(1000) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, meta_keywords VARCHAR(1000) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, meta_description VARCHAR(5000) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, content LONGTEXT CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, title VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, locale VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, UNIQUE INDEX bitbag_cms_page_translation_uniq_trans (translatable_id, locale), INDEX IDX_FDD074A62C2AC5D3 (translatable_id), INDEX IDX_FDD074A63DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE bitbag_cms_section (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(250) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, UNIQUE INDEX UNIQ_421D079777153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE bitbag_cms_section_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, name VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, locale VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, UNIQUE INDEX bitbag_cms_section_translation_uniq_trans (translatable_id, locale), INDEX IDX_F99CA8582C2AC5D3 (translatable_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE bitbag_fraud_suspicion (id INT AUTO_INCREMENT NOT NULL, order_id INT DEFAULT NULL, customer_id INT NOT NULL, company VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, first_name VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, last_name VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, email VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, phone_number VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, city VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, street VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, province VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, country VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, postcode VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, customer_ip VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, address_type VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, comment VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, status VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_7B5CF7C08D9F6D38 (order_id), INDEX IDX_7B5CF7C09395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE bitbag_product_bundle (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, is_packed_product TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_9EBE7ABF4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE bitbag_product_bundle_item (id INT AUTO_INCREMENT NOT NULL, product_variant_id INT NOT NULL, product_bundle_id INT NOT NULL, quantity INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_F429FEB6A80EF684 (product_variant_id), INDEX IDX_F429FEB69F5A6F5E (product_bundle_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE bitbag_product_bundle_order_item (id INT AUTO_INCREMENT NOT NULL, product_variant_id INT NOT NULL, order_item_id INT NOT NULL, product_bundle_item_id INT NOT NULL, quantity INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_A615CDA9A80EF684 (product_variant_id), INDEX IDX_A615CDA9B7FE950B (product_bundle_item_id), INDEX IDX_A615CDA9E415FB15 (order_item_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE bitbag_shipping_export (id INT AUTO_INCREMENT NOT NULL, shipment_id INT DEFAULT NULL, shipping_gateway_id INT DEFAULT NULL, exported_at DATETIME DEFAULT NULL, label_path VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, state VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, external_id VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, UNIQUE INDEX UNIQ_20E62D9F7BE036FC (shipment_id), INDEX IDX_20E62D9FEF84DE5E (shipping_gateway_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE bitbag_shipping_gateway (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, config JSON NOT NULL COMMENT \'(DC2Type:json_array)\', name VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE bitbag_shipping_gateway_method (shipping_gateway_id INT NOT NULL, shipping_method_id INT NOT NULL, INDEX IDX_8606B9CB5F7D6850 (shipping_method_id), INDEX IDX_8606B9CBEF84DE5E (shipping_gateway_id), PRIMARY KEY(shipping_gateway_id, shipping_method_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE blacklisting_rule_channel (blacklisting_rule_id INT NOT NULL, channel_id INT NOT NULL, INDEX IDX_13115922968538BF (blacklisting_rule_id), INDEX IDX_1311592272F5A1AA (channel_id), PRIMARY KEY(blacklisting_rule_id, channel_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE blacklisting_rule_customer_group (blacklisting_rule_id INT NOT NULL, customer_group_id INT NOT NULL, INDEX IDX_C82ADBCA968538BF (blacklisting_rule_id), INDEX IDX_C82ADBCAD2919A68 (customer_group_id), PRIMARY KEY(blacklisting_rule_id, customer_group_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE sylius_invoicing_plugin_billing_data (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, last_name VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, company VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, street VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, city VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, postcode VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, country_code VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, province_code VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, province_name VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE sylius_invoicing_plugin_invoice (id VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, billing_data_id INT DEFAULT NULL, shop_billing_data_id INT DEFAULT NULL, channel_id INT DEFAULT NULL, order_number VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, issued_at DATETIME NOT NULL, currency_code VARCHAR(3) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, total INT NOT NULL, number VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, locale_code VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, UNIQUE INDEX UNIQ_3AA279BF5CDB2AEB (billing_data_id), INDEX IDX_3AA279BF551F0F81 (order_number), UNIQUE INDEX UNIQ_3AA279BFB5282EDF (shop_billing_data_id), INDEX IDX_3AA279BF72F5A1AA (channel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE sylius_invoicing_plugin_line_item (id INT AUTO_INCREMENT NOT NULL, invoice_id VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, name VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, quantity INT NOT NULL, unit_price INT NOT NULL, subtotal INT NOT NULL, tax_total INT NOT NULL, total INT NOT NULL, variant_code VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, variant_name VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, INDEX IDX_C91408292989F1FD (invoice_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE sylius_invoicing_plugin_sequence (id INT AUTO_INCREMENT NOT NULL, idx INT NOT NULL, version INT DEFAULT 1 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE sylius_invoicing_plugin_shop_billing_data (id INT AUTO_INCREMENT NOT NULL, company VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, tax_id VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, street VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, city VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, postcode VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, country_code VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, representative VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE sylius_invoicing_plugin_tax_item (id INT AUTO_INCREMENT NOT NULL, invoice_id VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, label VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, amount INT NOT NULL, INDEX IDX_2951C61C2989F1FD (invoice_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE sylius_paypal_plugin_pay_pal_credentials (id VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, payment_method_id INT DEFAULT NULL, access_token VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, creation_time DATETIME NOT NULL, expiration_time DATETIME NOT NULL, INDEX IDX_C56F54AD5AA1164F (payment_method_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE automatic_blacklisting_configuration_channel ADD CONSTRAINT FK_EB48A61142145D53 FOREIGN KEY (automatic_blacklisting_configuration_id) REFERENCES bitbag_automatic_blacklisting_configuration (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE automatic_blacklisting_configuration_channel ADD CONSTRAINT FK_EB48A61172F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_acl_role_translation ADD CONSTRAINT FK_5E54EA372C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES bitbag_acl_role (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_admin_users_roles_resources ADD CONSTRAINT FK_56F911D6352511C FOREIGN KEY (admin_user_id) REFERENCES sylius_admin_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_admin_users_roles_resources ADD CONSTRAINT FK_56F911DBB65C5AC FOREIGN KEY (role_resource_id) REFERENCES bitbag_acl_role (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_automatic_blacklisting_rule ADD CONSTRAINT FK_1881023273F32DD8 FOREIGN KEY (configuration_id) REFERENCES bitbag_automatic_blacklisting_configuration (id)');
        $this->addSql('ALTER TABLE bitbag_catalog_plugin_catalog_translation ADD CONSTRAINT FK_BD0CEC362C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES bitbag_catalog_catalog (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_catalog_rule ADD CONSTRAINT FK_7324734CC3C66FC FOREIGN KEY (catalog_id) REFERENCES bitbag_catalog_catalog (id)');
        $this->addSql('ALTER TABLE bitbag_cms_block_channels ADD CONSTRAINT FK_8417B07372F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_cms_block_channels ADD CONSTRAINT FK_8417B073E9ED820C FOREIGN KEY (block_id) REFERENCES bitbag_cms_block (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_cms_block_products ADD CONSTRAINT FK_C4B9089F4584665A FOREIGN KEY (product_id) REFERENCES sylius_product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_cms_block_products ADD CONSTRAINT FK_C4B9089FE9ED820C FOREIGN KEY (block_id) REFERENCES bitbag_cms_block (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_cms_block_sections ADD CONSTRAINT FK_5C95115DD823E37A FOREIGN KEY (section_id) REFERENCES bitbag_cms_section (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_cms_block_sections ADD CONSTRAINT FK_5C95115DE9ED820C FOREIGN KEY (block_id) REFERENCES bitbag_cms_block (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_cms_block_taxonomies ADD CONSTRAINT FK_10C3E429DE13F470 FOREIGN KEY (taxon_id) REFERENCES sylius_taxon (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_cms_block_taxonomies ADD CONSTRAINT FK_10C3E429E9ED820C FOREIGN KEY (block_id) REFERENCES bitbag_cms_block (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_cms_block_translation ADD CONSTRAINT FK_32897FDF2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES bitbag_cms_block (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_cms_faq_channels ADD CONSTRAINT FK_FF6D59AC72F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_cms_faq_channels ADD CONSTRAINT FK_FF6D59AC81BEC8C2 FOREIGN KEY (faq_id) REFERENCES bitbag_cms_faq (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_cms_faq_translation ADD CONSTRAINT FK_8B30DD2E2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES bitbag_cms_faq (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_cms_media_channels ADD CONSTRAINT FK_D109622E72F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_cms_media_channels ADD CONSTRAINT FK_D109622EE9ED820C FOREIGN KEY (block_id) REFERENCES bitbag_cms_media (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_cms_media_products ADD CONSTRAINT FK_91A7DAC24584665A FOREIGN KEY (product_id) REFERENCES sylius_product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_cms_media_products ADD CONSTRAINT FK_91A7DAC2EA9FDD75 FOREIGN KEY (media_id) REFERENCES bitbag_cms_media (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_cms_media_sections ADD CONSTRAINT FK_98BC300D823E37A FOREIGN KEY (section_id) REFERENCES bitbag_cms_section (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_cms_media_sections ADD CONSTRAINT FK_98BC300EA9FDD75 FOREIGN KEY (media_id) REFERENCES bitbag_cms_media (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_cms_media_translation ADD CONSTRAINT FK_1FEC58972C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES bitbag_cms_media (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_cms_page_channels ADD CONSTRAINT FK_DCA426972F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_cms_page_channels ADD CONSTRAINT FK_DCA4269C4663E4 FOREIGN KEY (page_id) REFERENCES bitbag_cms_page (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_cms_page_products ADD CONSTRAINT FK_4D64FA854584665A FOREIGN KEY (product_id) REFERENCES sylius_product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_cms_page_products ADD CONSTRAINT FK_4D64FA85C4663E4 FOREIGN KEY (page_id) REFERENCES bitbag_cms_page (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_cms_page_sections ADD CONSTRAINT FK_D548E347D823E37A FOREIGN KEY (section_id) REFERENCES bitbag_cms_section (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_cms_page_sections ADD CONSTRAINT FK_D548E347E9ED820C FOREIGN KEY (block_id) REFERENCES bitbag_cms_page (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_cms_page_translation ADD CONSTRAINT FK_FDD074A62C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES bitbag_cms_page (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_cms_page_translation ADD CONSTRAINT FK_FDD074A63DA5256D FOREIGN KEY (image_id) REFERENCES bitbag_cms_media (id)');
        $this->addSql('ALTER TABLE bitbag_cms_section_translation ADD CONSTRAINT FK_F99CA8582C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES bitbag_cms_section (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_fraud_suspicion ADD CONSTRAINT FK_7B5CF7C08D9F6D38 FOREIGN KEY (order_id) REFERENCES sylius_order (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE bitbag_fraud_suspicion ADD CONSTRAINT FK_7B5CF7C09395C3F3 FOREIGN KEY (customer_id) REFERENCES sylius_customer (id)');
        $this->addSql('ALTER TABLE bitbag_product_bundle ADD CONSTRAINT FK_9EBE7ABF4584665A FOREIGN KEY (product_id) REFERENCES sylius_product (id)');
        $this->addSql('ALTER TABLE bitbag_product_bundle_item ADD CONSTRAINT FK_F429FEB69F5A6F5E FOREIGN KEY (product_bundle_id) REFERENCES bitbag_product_bundle (id)');
        $this->addSql('ALTER TABLE bitbag_product_bundle_item ADD CONSTRAINT FK_F429FEB6A80EF684 FOREIGN KEY (product_variant_id) REFERENCES sylius_product_variant (id)');
        $this->addSql('ALTER TABLE bitbag_product_bundle_order_item ADD CONSTRAINT FK_A615CDA9A80EF684 FOREIGN KEY (product_variant_id) REFERENCES sylius_product_variant (id)');
        $this->addSql('ALTER TABLE bitbag_product_bundle_order_item ADD CONSTRAINT FK_A615CDA9B7FE950B FOREIGN KEY (product_bundle_item_id) REFERENCES bitbag_product_bundle_item (id)');
        $this->addSql('ALTER TABLE bitbag_product_bundle_order_item ADD CONSTRAINT FK_A615CDA9E415FB15 FOREIGN KEY (order_item_id) REFERENCES sylius_order_item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_shipping_export ADD CONSTRAINT FK_20E62D9F7BE036FC FOREIGN KEY (shipment_id) REFERENCES sylius_shipment (id)');
        $this->addSql('ALTER TABLE bitbag_shipping_export ADD CONSTRAINT FK_20E62D9FEF84DE5E FOREIGN KEY (shipping_gateway_id) REFERENCES bitbag_shipping_gateway (id)');
        $this->addSql('ALTER TABLE bitbag_shipping_gateway_method ADD CONSTRAINT FK_8606B9CB5F7D6850 FOREIGN KEY (shipping_method_id) REFERENCES sylius_shipping_method (id)');
        $this->addSql('ALTER TABLE bitbag_shipping_gateway_method ADD CONSTRAINT FK_8606B9CBEF84DE5E FOREIGN KEY (shipping_gateway_id) REFERENCES bitbag_shipping_gateway (id)');
        $this->addSql('ALTER TABLE blacklisting_rule_channel ADD CONSTRAINT FK_1311592272F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blacklisting_rule_channel ADD CONSTRAINT FK_13115922968538BF FOREIGN KEY (blacklisting_rule_id) REFERENCES bitbag_blacklisting_rule (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blacklisting_rule_customer_group ADD CONSTRAINT FK_C82ADBCA968538BF FOREIGN KEY (blacklisting_rule_id) REFERENCES bitbag_blacklisting_rule (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blacklisting_rule_customer_group ADD CONSTRAINT FK_C82ADBCAD2919A68 FOREIGN KEY (customer_group_id) REFERENCES sylius_customer_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice ADD CONSTRAINT FK_3AA279BF5CDB2AEB FOREIGN KEY (billing_data_id) REFERENCES sylius_invoicing_plugin_billing_data (id)');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice ADD CONSTRAINT FK_3AA279BF72F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id)');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice ADD CONSTRAINT FK_3AA279BFCFE4AA36 FOREIGN KEY (shop_billing_data_id) REFERENCES sylius_invoicing_plugin_shop_billing_data (id)');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_line_item ADD CONSTRAINT FK_C91408292989F1FD FOREIGN KEY (invoice_id) REFERENCES sylius_invoicing_plugin_invoice (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_tax_item ADD CONSTRAINT FK_2951C61C2989F1FD FOREIGN KEY (invoice_id) REFERENCES sylius_invoicing_plugin_invoice (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_paypal_plugin_pay_pal_credentials ADD CONSTRAINT FK_C56F54AD5AA1164F FOREIGN KEY (payment_method_id) REFERENCES sylius_payment_method (id)');
    }
}
