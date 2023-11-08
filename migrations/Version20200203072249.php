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
final class Version20200203072249 extends AbstractMigration
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

        $this->addSql('CREATE TABLE forwarded_to_authority (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB ');
        $this->addSql('CREATE TABLE forwarded_to_authority_process (forwarded_to_authority_id INT NOT NULL, process_id INT NOT NULL, INDEX IDX_A5AC7E346BE23C97 (forwarded_to_authority_id), INDEX IDX_A5AC7E347EC2F574 (process_id), PRIMARY KEY(forwarded_to_authority_id, process_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB ');
        $this->addSql('CREATE TABLE forwarded_to_authority_process_type (forwarded_to_authority_id INT NOT NULL, process_type_id INT NOT NULL, INDEX IDX_DA802736BE23C97 (forwarded_to_authority_id), INDEX IDX_DA802738D345646 (process_type_id), PRIMARY KEY(forwarded_to_authority_id, process_type_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB ');
        $this->addSql('ALTER TABLE forwarded_to_authority_process ADD CONSTRAINT FK_A5AC7E346BE23C97 FOREIGN KEY (forwarded_to_authority_id) REFERENCES forwarded_to_authority (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE forwarded_to_authority_process ADD CONSTRAINT FK_A5AC7E347EC2F574 FOREIGN KEY (process_id) REFERENCES process (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE forwarded_to_authority_process_type ADD CONSTRAINT FK_DA802736BE23C97 FOREIGN KEY (forwarded_to_authority_id) REFERENCES forwarded_to_authority (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE forwarded_to_authority_process_type ADD CONSTRAINT FK_DA802738D345646 FOREIGN KEY (process_type_id) REFERENCES process_type (id) ON DELETE CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE forwarded_to_authority_process DROP FOREIGN KEY FK_A5AC7E346BE23C97');
        $this->addSql('ALTER TABLE forwarded_to_authority_process_type DROP FOREIGN KEY FK_DA802736BE23C97');
        $this->addSql('DROP TABLE forwarded_to_authority');
        $this->addSql('DROP TABLE forwarded_to_authority_process');
        $this->addSql('DROP TABLE forwarded_to_authority_process_type');
    }
}
