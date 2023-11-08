<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230929055426 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE visitation_log_entry DROP FOREIGN KEY FK_B75CBC04D465829D');
        $this->addSql('DROP INDEX IDX_B75CBC04D465829D ON visitation_log_entry');
        $this->addSql('ALTER TABLE visitation_log_entry DROP log_entry_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE visitation_log_entry ADD log_entry_id INT NOT NULL');
        $this->addSql('ALTER TABLE visitation_log_entry ADD CONSTRAINT FK_B75CBC04D465829D FOREIGN KEY (log_entry_id) REFERENCES ext_log_entries (id)');
        $this->addSql('CREATE INDEX IDX_B75CBC04D465829D ON visitation_log_entry (log_entry_id)');
    }
}
