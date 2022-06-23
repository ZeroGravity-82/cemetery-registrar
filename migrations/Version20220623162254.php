<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220623162254 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE columbarium_niche CHANGE columbarium_niche_number niche_number VARCHAR(255) NOT NULL COMMENT \'(DC2Type:columbarium_niche_number)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE columbarium_niche CHANGE niche_number columbarium_niche_number VARCHAR(255) NOT NULL COMMENT \'(DC2Type:columbarium_niche_number)\'');
    }
}
