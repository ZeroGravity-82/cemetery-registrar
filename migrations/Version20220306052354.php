<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220306052354 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE funeral_company (id VARCHAR(255) NOT NULL COMMENT \'(DC2Type:funeral_company_id)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', organization_id_value VARCHAR(255) DEFAULT NULL, organization_id_type VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:organization_type)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE burial ADD funeral_company_id VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:funeral_company_id)\', DROP funeral_company_id_value, DROP funeral_company_id_type');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE funeral_company');
        $this->addSql('ALTER TABLE burial ADD funeral_company_id_value VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD funeral_company_id_type VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:funeral_company_type)\', DROP funeral_company_id');
    }
}
