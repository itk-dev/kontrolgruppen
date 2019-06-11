<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190611114852 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE process_log_entry ADD process_id INT NOT NULL');
        $this->addSql('ALTER TABLE process_log_entry ADD CONSTRAINT FK_911634C57EC2F574 FOREIGN KEY (process_id) REFERENCES process (id)');
        $this->addSql('CREATE INDEX IDX_911634C57EC2F574 ON process_log_entry (process_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE process_log_entry DROP FOREIGN KEY FK_911634C57EC2F574');
        $this->addSql('DROP INDEX IDX_911634C57EC2F574 ON process_log_entry');
        $this->addSql('ALTER TABLE process_log_entry DROP process_id');
    }
}
