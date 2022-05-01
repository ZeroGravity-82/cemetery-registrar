<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220501051729 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE columbarium ADD geo_position JSON DEFAULT NULL COMMENT \'(DC2Type:geo_position)\', DROP geo_accuracy, DROP geo_latitude, DROP geo_longitude');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE columbarium ADD geo_accuracy NUMERIC(5, 3) DEFAULT NULL, ADD geo_latitude NUMERIC(10, 8) DEFAULT NULL, ADD geo_longitude NUMERIC(11, 8) DEFAULT NULL, DROP geo_position');
    }
}
