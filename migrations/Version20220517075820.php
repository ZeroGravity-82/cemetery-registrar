<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220517075820 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE natural_person ADD phone VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:phone_number)\', ADD phone_additional VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:phone_number)\', ADD email VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:email)\', ADD address VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:address)\', ADD place_of_birth VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:place_of_birth)\', ADD passport JSON DEFAULT NULL COMMENT \'(DC2Type:passport)\', CHANGE full_name full_name VARCHAR(255) NOT NULL COMMENT \'(DC2Type:full_name)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE natural_person DROP phone, DROP phone_additional, DROP email, DROP address, DROP place_of_birth, DROP passport, CHANGE full_name full_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:full_name)\'');
    }
}
