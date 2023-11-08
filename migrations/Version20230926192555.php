<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230926192555 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE visitation (id INT AUTO_INCREMENT NOT NULL, case_worker_id INT DEFAULT NULL, completed_at DATETIME DEFAULT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_B39D86A4503CACD (case_worker_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE visitation_log_entry (id INT AUTO_INCREMENT NOT NULL, visitation_id INT NOT NULL, log_entry_id INT NOT NULL, creator_name VARCHAR(180) DEFAULT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_B75CBC0435E54CD2 (visitation_id), INDEX IDX_B75CBC04D465829D (log_entry_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE visitation ADD CONSTRAINT FK_B39D86A4503CACD FOREIGN KEY (case_worker_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE visitation_log_entry ADD CONSTRAINT FK_B75CBC0435E54CD2 FOREIGN KEY (visitation_id) REFERENCES visitation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE visitation_log_entry ADD CONSTRAINT FK_B75CBC04D465829D FOREIGN KEY (log_entry_id) REFERENCES ext_log_entries (id)');
        $this->addSql('ALTER TABLE ext_log_entries CHANGE object_class object_class VARCHAR(191) NOT NULL, CHANGE username username VARCHAR(191) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE visitation DROP FOREIGN KEY FK_B39D86A4503CACD');
        $this->addSql('ALTER TABLE visitation_log_entry DROP FOREIGN KEY FK_B75CBC0435E54CD2');
        $this->addSql('ALTER TABLE visitation_log_entry DROP FOREIGN KEY FK_B75CBC04D465829D');
        $this->addSql('DROP TABLE visitation');
        $this->addSql('DROP TABLE visitation_log_entry');
        $this->addSql('ALTER TABLE ext_log_entries CHANGE object_class object_class VARCHAR(255) NOT NULL, CHANGE username username VARCHAR(255) DEFAULT NULL');
    }
}
