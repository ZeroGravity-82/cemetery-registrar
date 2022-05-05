<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220505043635 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE grave_site (id VARCHAR(255) NOT NULL COMMENT \'(DC2Type:grave_site_id)\', cemetery_block_id VARCHAR(255) NOT NULL COMMENT \'(DC2Type:cemetery_block_id)\', row_in_block INT NOT NULL COMMENT \'(DC2Type:row_in_block)\', position_in_row INT DEFAULT NULL COMMENT \'(DC2Type:position_in_row)\', geo_position JSON DEFAULT NULL COMMENT \'(DC2Type:geo_position)\', size VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:grave_site_size)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', removed_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX grave_site_cemetery_block_id_row_in_block_uq (cemetery_block_id, row_in_block), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE grave_site');
    }
}
