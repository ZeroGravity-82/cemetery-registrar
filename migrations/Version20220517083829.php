<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220517083829 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE juristic_person ADD bank_details JSON DEFAULT NULL COMMENT \'(DC2Type:bank_details)\', DROP bank_details_bank_name, DROP bank_details_bik, DROP bank_details_correspondent_account, DROP bank_details_current_account');
        $this->addSql('ALTER TABLE sole_proprietor ADD bank_details JSON DEFAULT NULL COMMENT \'(DC2Type:bank_details)\', DROP bank_details_bank_name, DROP bank_details_bik, DROP bank_details_correspondent_account, DROP bank_details_current_account');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE juristic_person ADD bank_details_bank_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:organization_name)\', ADD bank_details_bik VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:bik)\', ADD bank_details_correspondent_account VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:correspondent_account)\', ADD bank_details_current_account VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:current_account)\', DROP bank_details');
        $this->addSql('ALTER TABLE sole_proprietor ADD bank_details_bank_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:organization_name)\', ADD bank_details_bik VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:bik)\', ADD bank_details_correspondent_account VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:correspondent_account)\', ADD bank_details_current_account VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:current_account)\', DROP bank_details');
    }
}
