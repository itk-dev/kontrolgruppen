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
final class Version20210628114104 extends AbstractMigration
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

        $this->addSql('ALTER TABLE car ADD process_client_id INT NOT NULL');
        $this->addSql('UPDATE car SET process_client_id = client_id');
        $this->addSql('ALTER TABLE car ADD CONSTRAINT FK_773DE69D83A22BC9 FOREIGN KEY (process_client_id) REFERENCES process_client (id)');
        $this->addSql('CREATE INDEX IDX_773DE69D83A22BC9 ON car (process_client_id)');

        $this->addSql('ALTER TABLE company ADD process_client_id INT NOT NULL');
        $this->addSql('UPDATE company SET process_client_id = client_id');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094F83A22BC9 FOREIGN KEY (process_client_id) REFERENCES process_client (id)');
        $this->addSql('CREATE INDEX IDX_4FBF094F83A22BC9 ON company (process_client_id)');
    }

    /**
     * {@inheritdoc}
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE car DROP FOREIGN KEY FK_773DE69D83A22BC9');
        $this->addSql('DROP INDEX IDX_773DE69D83A22BC9 ON car');
        $this->addSql('ALTER TABLE car DROP process_client_id');

        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094F83A22BC9');
        $this->addSql('DROP INDEX IDX_4FBF094F83A22BC9 ON company');
        $this->addSql('ALTER TABLE company DROP process_client_id');
    }
}
