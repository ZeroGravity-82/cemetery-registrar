<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220602090314 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE funeral_company (id VARCHAR(255) NOT NULL COMMENT \'(DC2Type:funeral_company_id)\', organization_id JSON NOT NULL COMMENT \'(DC2Type:organization_id)\', note VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:funeral_company_note)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', removed_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE burial ADD funeral_company_id VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:funeral_company_id)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE funeral_company');
        $this->addSql('ALTER TABLE burial DROP funeral_company_id');
    }
}
