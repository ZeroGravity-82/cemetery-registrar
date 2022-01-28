<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220128084314 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE deceased (id VARCHAR(255) NOT NULL COMMENT \'(DC2Type:deceased_id)\', natural_person_id VARCHAR(255) NOT NULL COMMENT \'(DC2Type:natural_person_id)\', died_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', death_certificate_id VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:death_certificate_id)\', cause_of_death VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:cause_of_death)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE deceased');
    }
}
