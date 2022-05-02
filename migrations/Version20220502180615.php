<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220502180615 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE columbarium_niche (id VARCHAR(255) NOT NULL COMMENT \'(DC2Type:columbarium_niche_id)\', columbarium_id VARCHAR(255) NOT NULL COMMENT \'(DC2Type:columbarium_id)\', row_in_columbarium INT NOT NULL COMMENT \'(DC2Type:row_in_columbarium)\', columbarium_niche_number VARCHAR(255) NOT NULL COMMENT \'(DC2Type:columbarium_niche_number)\', geo_position JSON DEFAULT NULL COMMENT \'(DC2Type:geo_position)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', removed_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX columbarium_id_niche_number_uq (columbarium_id, columbarium_niche_number), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE columbarium_niche');
    }
}
