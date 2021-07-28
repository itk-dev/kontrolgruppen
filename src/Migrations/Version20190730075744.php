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
final class Version20190730075744 extends AbstractMigration
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

        $this->addSql('CREATE TABLE process_type_channel (process_type_id INT NOT NULL, channel_id INT NOT NULL, INDEX IDX_E066B4838D345646 (process_type_id), INDEX IDX_E066B48372F5A1AA (channel_id), PRIMARY KEY(process_type_id, channel_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB ENCRYPTED = YES');
        $this->addSql('ALTER TABLE process_type_channel ADD CONSTRAINT FK_E066B4838D345646 FOREIGN KEY (process_type_id) REFERENCES process_type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE process_type_channel ADD CONSTRAINT FK_E066B48372F5A1AA FOREIGN KEY (channel_id) REFERENCES channel (id) ON DELETE CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE process_type_channel');
    }
}
