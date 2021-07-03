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
final class Version20210703092838 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094F19EB6921');
        $this->addSql('DROP INDEX IDX_4FBF094F19EB6921 ON company');
        $this->addSql('ALTER TABLE company DROP client_id');
        $this->addSql('ALTER TABLE car DROP FOREIGN KEY FK_773DE69D19EB6921');
        $this->addSql('DROP INDEX IDX_773DE69D19EB6921 ON car');
        $this->addSql('ALTER TABLE car DROP client_id');
        $this->addSql('DROP TABLE client');
        $this->addSql('ALTER TABLE process DROP client_cpr');
        // Delete user settings referencing process client cpr
        $this->addSql('DELETE FROM user_settings WHERE settings_value LIKE \'%clientCPR%\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, process_id INT NOT NULL, first_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, last_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, address VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, postal_code VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, city VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, telephone VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, number_of_children INT DEFAULT NULL, created_by VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, updated_by VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, has_drivers_license TINYINT(1) DEFAULT NULL, has_car TINYINT(1) DEFAULT NULL, receives_public_aid TINYINT(1) DEFAULT NULL, employed TINYINT(1) DEFAULT NULL, has_own_company TINYINT(1) DEFAULT NULL, UNIQUE INDEX UNIQ_C74404557EC2F574 (process_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\'  ENCRYPTED = YES');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C74404557EC2F574 FOREIGN KEY (process_id) REFERENCES process (id)');

        $this->addSql('ALTER TABLE car ADD client_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE car ADD CONSTRAINT FK_773DE69D19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('CREATE INDEX IDX_773DE69D19EB6921 ON car (client_id)');
        $this->addSql('ALTER TABLE company ADD client_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094F19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('CREATE INDEX IDX_4FBF094F19EB6921 ON company (client_id)');
        $this->addSql('ALTER TABLE process ADD client_cpr VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
