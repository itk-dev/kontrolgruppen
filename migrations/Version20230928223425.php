<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230928223425 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE visitation DROP FOREIGN KEY FK_B39D86A4503CACD');
        $this->addSql('DROP INDEX IDX_B39D86A4503CACD ON visitation');
        $this->addSql('ALTER TABLE visitation ADD identifier VARCHAR(180) DEFAULT NULL, ADD type VARCHAR(180) DEFAULT NULL, DROP case_worker_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE visitation ADD case_worker_id INT DEFAULT NULL, DROP identifier, DROP type');
        $this->addSql('ALTER TABLE visitation ADD CONSTRAINT FK_B39D86A4503CACD FOREIGN KEY (case_worker_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_B39D86A4503CACD ON visitation (case_worker_id)');
    }
}
