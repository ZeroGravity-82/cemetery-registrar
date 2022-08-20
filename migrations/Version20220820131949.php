<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220820131949 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE burial DROP person_in_charge_id');
        $this->addSql('ALTER TABLE columbarium_niche ADD person_in_charge_id VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:natural_person_id)\'');
        $this->addSql('ALTER TABLE grave_site ADD person_in_charge_id VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:natural_person_id)\'');
        $this->addSql('ALTER TABLE memorial_tree ADD person_in_charge_id VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:natural_person_id)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE burial ADD person_in_charge_id VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:natural_person_id)\'');
        $this->addSql('ALTER TABLE columbarium_niche DROP person_in_charge_id');
        $this->addSql('ALTER TABLE grave_site DROP person_in_charge_id');
        $this->addSql('ALTER TABLE memorial_tree DROP person_in_charge_id');
    }
}
