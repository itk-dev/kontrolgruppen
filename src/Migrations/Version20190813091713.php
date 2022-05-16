<?php

declare(strict_types=1);

/*
 * This file is part of itk-dev/kontrolgruppen.
 *
 * (c) 2019–2021 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190813091713 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE process_type ADD default_process_status_id INT NOT NULL, ADD default_process_status_on_empty_case_worker_id INT NOT NULL');
        $this->addSql('ALTER TABLE process_type ADD CONSTRAINT FK_6337851C43E7BCA FOREIGN KEY (default_process_status_id) REFERENCES process_status (id)');
        $this->addSql('ALTER TABLE process_type ADD CONSTRAINT FK_6337851C52436242 FOREIGN KEY (default_process_status_on_empty_case_worker_id) REFERENCES process_status (id)');
        $this->addSql('CREATE INDEX IDX_6337851C43E7BCA ON process_type (default_process_status_id)');
        $this->addSql('CREATE INDEX IDX_6337851C52436242 ON process_type (default_process_status_on_empty_case_worker_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE process_type DROP FOREIGN KEY FK_6337851C43E7BCA');
        $this->addSql('ALTER TABLE process_type DROP FOREIGN KEY FK_6337851C52436242');
        $this->addSql('DROP INDEX IDX_6337851C43E7BCA ON process_type');
        $this->addSql('DROP INDEX IDX_6337851C52436242 ON process_type');
        $this->addSql('ALTER TABLE process_type DROP default_process_status_id, DROP default_process_status_on_empty_case_worker_id');
    }
}
