<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220304095106 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE juristic_person (id VARCHAR(255) NOT NULL COMMENT \'(DC2Type:juristic_person_id)\', name VARCHAR(255) NOT NULL COMMENT \'(DC2Type:organization_name)\', inn VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:juristic_person_inn)\', kpp VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:kpp)\', ogrn VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:ogrn)\', okpo VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:juristic_person_okpo)\', okved VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:okved)\', legal_address VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:address)\', postal_address VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:address)\', phone VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:phone_number)\', phone_additional VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:phone_number)\', fax VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:phone_number)\', general_director VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:full_name)\', email VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:email)\', website VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:website)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', bank_details_bank_name VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:organization_name)\', bank_details_bik VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:bik)\', bank_details_correspondent_account VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:correspondent_account)\', bank_details_current_account VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:current_account)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE juristic_person');
    }
}
