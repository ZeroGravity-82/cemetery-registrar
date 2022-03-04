<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220304054831 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sole_proprietor CHANGE bank_details_bank_name bank_details_bank_name VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:organization_name)\', CHANGE bank_details_bik bank_details_bik VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:bik)\', CHANGE bank_details_current_account bank_details_current_account VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:current_account)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sole_proprietor CHANGE bank_details_bank_name bank_details_bank_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:organization_name)\', CHANGE bank_details_bik bank_details_bik VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:bik)\', CHANGE bank_details_current_account bank_details_current_account VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:current_account)\'');
    }
}
