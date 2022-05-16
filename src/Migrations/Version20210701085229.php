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
final class Version20210701085229 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE income_type ADD client_types LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', DROP client_type');
        $this->addSql('ALTER TABLE account ADD client_types LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', DROP client_type');
        $this->addSql('ALTER TABLE channel ADD client_types LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', DROP client_type');
        $this->addSql('ALTER TABLE forwarded_to_authority ADD client_types LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', DROP client_type');
        $this->addSql('ALTER TABLE process_status ADD client_types LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', DROP client_type');
        $this->addSql('ALTER TABLE process_type ADD client_types LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', DROP client_type');
        $this->addSql('ALTER TABLE quick_link ADD client_types LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', DROP client_type');
        $this->addSql('ALTER TABLE reason ADD client_types LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', DROP client_type');
        $this->addSql('ALTER TABLE service ADD client_types LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', DROP client_type');

        $this->addSql('UPDATE income_type SET client_types = \'["person"]\'');
        $this->addSql('UPDATE account SET client_types = \'["person"]\'');
        $this->addSql('UPDATE channel SET client_types = \'["person"]\'');
        $this->addSql('UPDATE forwarded_to_authority SET client_types = \'["person"]\'');
        $this->addSql('UPDATE process_status SET client_types = \'["person"]\'');
        $this->addSql('UPDATE process_type SET client_types = \'["person"]\'');
        $this->addSql('UPDATE quick_link SET client_types = \'["person"]\'');
        $this->addSql('UPDATE reason SET client_types = \'["person"]\'');
        $this->addSql('UPDATE service SET client_types = \'["person"]\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE account ADD client_type VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP client_types');
        $this->addSql('ALTER TABLE channel ADD client_type VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP client_types');
        $this->addSql('ALTER TABLE forwarded_to_authority ADD client_type VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP client_types');
        $this->addSql('ALTER TABLE income_type ADD client_type VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP client_types');
        $this->addSql('ALTER TABLE process_status ADD client_type VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP client_types');
        $this->addSql('ALTER TABLE process_type ADD client_type VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP client_types');
        $this->addSql('ALTER TABLE quick_link ADD client_type VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP client_types');
        $this->addSql('ALTER TABLE reason ADD client_type VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP client_types');
        $this->addSql('ALTER TABLE service ADD client_type VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP client_types');

        $this->addSql('UPDATE income_type SET client_type = \'person\'');
        $this->addSql('UPDATE account SET client_type = \'person\'');
        $this->addSql('UPDATE channel SET client_type = \'person\'');
        $this->addSql('UPDATE forwarded_to_authority SET client_type = \'person\'');
        $this->addSql('UPDATE process_status SET client_type = \'person\'');
        $this->addSql('UPDATE process_type SET client_type = \'person\'');
        $this->addSql('UPDATE quick_link SET client_type = \'person\'');
        $this->addSql('UPDATE reason SET client_type = \'person\'');
        $this->addSql('UPDATE service SET client_type = \'person\'');
    }
}
