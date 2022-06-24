<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220624045847 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE burial CHANGE burial_place_owner_id person_in_charge_id VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:natural_person_id)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE burial CHANGE person_in_charge_id burial_place_owner_id VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:natural_person_id)\'');
    }
}
