<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190328141120 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE case_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE process ADD case_type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE process ADD CONSTRAINT FK_861D18966AEF7998 FOREIGN KEY (case_type_id) REFERENCES case_type (id)');
        $this->addSql('CREATE INDEX IDX_861D18966AEF7998 ON process (case_type_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE process DROP FOREIGN KEY FK_861D18966AEF7998');
        $this->addSql('DROP TABLE case_type');
        $this->addSql('DROP INDEX IDX_861D18966AEF7998 ON process');
        $this->addSql('ALTER TABLE process DROP case_type_id');
    }
}
