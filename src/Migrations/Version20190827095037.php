<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190827095037 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE economy_entry ADD future_savings_amount DOUBLE PRECISION DEFAULT NULL, ADD future_savings_period_from DATETIME DEFAULT NULL, ADD future_savings_period_to DATETIME DEFAULT NULL, ADD repayment_amount DOUBLE PRECISION DEFAULT NULL, ADD repayment_period_from DATETIME DEFAULT NULL, ADD repayment_period_to DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE economy_entry DROP future_savings_amount, DROP future_savings_period_from, DROP future_savings_period_to, DROP repayment_amount, DROP repayment_period_from, DROP repayment_period_to');
    }
}
