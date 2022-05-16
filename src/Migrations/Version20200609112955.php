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
final class Version20200609112955 extends AbstractMigration
{
    /**
     * {@inheritdoc}
     */
    public function getDescription(): string
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE process_group (id INT AUTO_INCREMENT NOT NULL, primary_process_id INT NOT NULL, INDEX IDX_3B9C693440661912 (primary_process_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB ENCRYPTED = YES');
        $this->addSql('CREATE TABLE process_group_process (process_group_id INT NOT NULL, process_id INT NOT NULL, INDEX IDX_51A9F2F58A587C6 (process_group_id), INDEX IDX_51A9F2F57EC2F574 (process_id), PRIMARY KEY(process_group_id, process_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB ENCRYPTED = YES');
        $this->addSql('ALTER TABLE process_group ADD CONSTRAINT FK_3B9C693440661912 FOREIGN KEY (primary_process_id) REFERENCES process (id)');
        $this->addSql('ALTER TABLE process_group_process ADD CONSTRAINT FK_51A9F2F58A587C6 FOREIGN KEY (process_group_id) REFERENCES process_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE process_group_process ADD CONSTRAINT FK_51A9F2F57EC2F574 FOREIGN KEY (process_id) REFERENCES process (id) ON DELETE CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE process_group_process DROP FOREIGN KEY FK_51A9F2F58A587C6');
        $this->addSql('DROP TABLE process_group');
        $this->addSql('DROP TABLE process_group_process');
    }
}
