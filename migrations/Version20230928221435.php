<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230928221435 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE visitation DROP completed_at');
        $this->addSql('ALTER TABLE visitation_log_entry ADD cpr_number VARCHAR(180) DEFAULT NULL, ADD table_name VARCHAR(180) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE visitation ADD completed_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE visitation_log_entry DROP cpr_number, DROP table_name');
    }
}
