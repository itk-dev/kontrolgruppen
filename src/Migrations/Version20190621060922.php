<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190621060922 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE economy_entry (id INT AUTO_INCREMENT NOT NULL, process_id INT NOT NULL, service_id INT DEFAULT NULL, amount DOUBLE PRECISION NOT NULL, type ENUM(\'SERVICE\', \'ACCOUNT\', \'ARREAR\') NOT NULL COMMENT \'(DC2Type:EconomyEntryEnumType)\', created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, discr VARCHAR(255) NOT NULL, text VARCHAR(255) DEFAULT NULL, date DATETIME DEFAULT NULL, period_from DATETIME DEFAULT NULL, period_to DATETIME DEFAULT NULL, amount_period INT DEFAULT NULL, INDEX IDX_EE7979A27EC2F574 (process_id), INDEX IDX_EE7979A2ED5CA9E6 (service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE economy_entry ADD CONSTRAINT FK_EE7979A27EC2F574 FOREIGN KEY (process_id) REFERENCES process (id)');
        $this->addSql('ALTER TABLE economy_entry ADD CONSTRAINT FK_EE7979A2ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE economy_entry');
    }
}
