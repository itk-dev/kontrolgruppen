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
final class Version20210628114239 extends AbstractMigration
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

        $this->addSql('ALTER TABLE car DROP FOREIGN KEY FK_773DE69D83A22BC9');
        $this->addSql('ALTER TABLE car CHANGE client_id client_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE car ADD CONSTRAINT FK_773DE69D83A22BC9 FOREIGN KEY (process_client_id) REFERENCES process_client (id)');
    }

    /**
     * {@inheritdoc}
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        // There is no going back (from null to not null) …
        // $this->addSql('ALTER TABLE car DROP FOREIGN KEY FK_773DE69D83A22BC9');
        // $this->addSql('ALTER TABLE car CHANGE client_id client_id INT NOT NULL');
        // $this->addSql('ALTER TABLE car ADD CONSTRAINT FK_773DE69D83A22BC9 FOREIGN KEY (process_client_id) REFERENCES process (id)');
    }
}
