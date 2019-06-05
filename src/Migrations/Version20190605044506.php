<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190605044506 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE process_type_service (process_type_id INT NOT NULL, service_id INT NOT NULL, INDEX IDX_A302A0168D345646 (process_type_id), INDEX IDX_A302A016ED5CA9E6 (service_id), PRIMARY KEY(process_type_id, service_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE process_type_service ADD CONSTRAINT FK_A302A0168D345646 FOREIGN KEY (process_type_id) REFERENCES process_type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE process_type_service ADD CONSTRAINT FK_A302A016ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE process_type_service');
    }
}
