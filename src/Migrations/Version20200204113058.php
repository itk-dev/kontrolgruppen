<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200204113058 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE revenue_entry (id INT AUTO_INCREMENT NOT NULL, process_id INT NOT NULL, service_id INT NOT NULL, amount DOUBLE PRECISION NOT NULL, type ENUM(\'FUTURE_SAVINGS\', \'REPAYMENT\') NOT NULL COMMENT \'(DC2Type:RevenueTypeEnumType)\', future_savings_type ENUM(\'FIXED_VALUE\', \'SANCTION\', \'PR_MND_X_12\', \'SELF_SUPPORT\') DEFAULT NULL COMMENT \'(DC2Type:RevenueFutureTypeEnumType)\', INDEX IDX_FD30B51E7EC2F574 (process_id), INDEX IDX_FD30B51EED5CA9E6 (service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB ENCRYPTED = YES');
        $this->addSql('ALTER TABLE revenue_entry ADD CONSTRAINT FK_FD30B51E7EC2F574 FOREIGN KEY (process_id) REFERENCES process (id)');
        $this->addSql('ALTER TABLE revenue_entry ADD CONSTRAINT FK_FD30B51EED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE revenue_entry');
    }
}
