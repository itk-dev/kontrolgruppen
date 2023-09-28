<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230927190454 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE visitation_client (id INT AUTO_INCREMENT NOT NULL, visitation_id INT NOT NULL, type VARCHAR(255) NOT NULL, identifier VARCHAR(255) NOT NULL, name VARCHAR(255) DEFAULT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, discr VARCHAR(255) NOT NULL, INDEX IDX_94EC33C935E54CD2 (visitation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE visitation_client ADD CONSTRAINT FK_94EC33C935E54CD2 FOREIGN KEY (visitation_id) REFERENCES visitation (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE visitation_client DROP FOREIGN KEY FK_94EC33C935E54CD2');
        $this->addSql('DROP TABLE visitation_client');
    }
}
