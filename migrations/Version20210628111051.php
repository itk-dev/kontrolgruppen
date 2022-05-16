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
final class Version20210628111051 extends AbstractMigration
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

        $this->addSql('ALTER TABLE process_client ADD has_car TINYINT(1) DEFAULT NULL');

        // Copy legacy client data to person clients.
        $this->addSql("
INSERT INTO process_client(discr, id, process_id, type, identifier, address, postal_code, city, telephone, created_by, updated_by, created_at, updated_at, first_name, last_name, number_of_children, receives_public_aid, employed, has_own_company, has_drivers_license, has_car, name)
     SELECT 'person', client.id, client.process_id, 'person', process.client_cpr, client.address, client.postal_code, client.city, client.telephone, client.created_by, client.updated_by, client.created_at, client.updated_at, client.first_name, client.last_name, client.number_of_children, client.receives_public_aid, client.employed, client.has_own_company, client.has_drivers_license, client.has_car, CONCAT(client.first_name, ' ', client.last_name) FROM client
 INNER JOIN process ON client.process_id = process.id");
    }

    /**
     * {@inheritdoc}
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE process_client DROP has_car');
    }
}
