<?php

declare(strict_types=1);

/*
 * This file is part of aakb/kontrolgruppen.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190403084615 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE process_type_process_status (process_type_id INT NOT NULL, process_status_id INT NOT NULL, INDEX IDX_4E14449F8D345646 (process_type_id), INDEX IDX_4E14449FF1BE4255 (process_status_id), PRIMARY KEY(process_type_id, process_status_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE process_type_process_status ADD CONSTRAINT FK_4E14449F8D345646 FOREIGN KEY (process_type_id) REFERENCES process_type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE process_type_process_status ADD CONSTRAINT FK_4E14449FF1BE4255 FOREIGN KEY (process_status_id) REFERENCES process_status (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE process_type_process_status');
    }
}
