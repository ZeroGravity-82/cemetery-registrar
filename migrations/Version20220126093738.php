<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220126093738 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE burial (id VARCHAR(255) NOT NULL COMMENT \'(DC2Type:burial_id)\', code VARCHAR(255) NOT NULL COMMENT \'(DC2Type:burial_code)\', deceased_id VARCHAR(255) NOT NULL COMMENT \'(DC2Type:natural_person_id)\', burial_place_owner_id VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:natural_person_id)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', burial_place_id_value VARCHAR(255) DEFAULT NULL, burial_place_id_type VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:burial_place_type)\', customer_id_value VARCHAR(255) DEFAULT NULL, customer_id_type VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:customer_type)\', funeral_company_id_value VARCHAR(255) DEFAULT NULL, funeral_company_id_type VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:funeral_company_type)\', UNIQUE INDEX burial_code_uq (code), UNIQUE INDEX burial_deceased_id_uq (deceased_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE burial');
    }
}
