<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190702115823 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ext_log_entries (id INT AUTO_INCREMENT NOT NULL, action VARCHAR(8) NOT NULL, logged_at DATETIME NOT NULL, object_id VARCHAR(64) DEFAULT NULL, object_class VARCHAR(255) NOT NULL, version INT NOT NULL, data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', username VARCHAR(255) DEFAULT NULL, INDEX log_class_lookup_idx (object_class), INDEX log_date_lookup_idx (logged_at), INDEX log_user_lookup_idx (username), INDEX log_version_lookup_idx (object_id, object_class, version), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB ROW_FORMAT = DYNAMIC ENCRYPTED = YES');
        $this->addSql('CREATE TABLE process_status (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB ENCRYPTED = YES');
        $this->addSql('CREATE TABLE reason (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB ENCRYPTED = YES');
        $this->addSql('CREATE TABLE conclusion (id INT AUTO_INCREMENT NOT NULL, process_id INT DEFAULT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, discr VARCHAR(255) NOT NULL, arguments_for LONGTEXT DEFAULT NULL, arguments_against LONGTEXT DEFAULT NULL, conclusion LONGTEXT DEFAULT NULL, INDEX IDX_1D5819F57EC2F574 (process_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB ENCRYPTED = YES');
        $this->addSql('CREATE TABLE reminder (id INT AUTO_INCREMENT NOT NULL, process_id INT NOT NULL, message VARCHAR(255) NOT NULL, date DATETIME NOT NULL, finished TINYINT(1) DEFAULT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_40374F407EC2F574 (process_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB ENCRYPTED = YES');
        $this->addSql('CREATE TABLE service (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB ENCRYPTED = YES');
        $this->addSql('CREATE TABLE journal_entry (id INT AUTO_INCREMENT NOT NULL, process_id INT NOT NULL, title VARCHAR(255) NOT NULL, body LONGTEXT DEFAULT NULL, type ENUM(\'NOTE\', \'INTERNAL_NOTE\') NOT NULL COMMENT \'(DC2Type:JournalEntryEnumType)\', created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_C8FAAE5A7EC2F574 (process_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB ENCRYPTED = YES');
        $this->addSql('CREATE TABLE process_log_entry (id INT AUTO_INCREMENT NOT NULL, process_id INT NOT NULL, log_entry_id INT NOT NULL, level ENUM(\'INFO\', \'NOTICE\') NOT NULL COMMENT \'(DC2Type:ProcessLogEntryLevelEnumType)\', created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_911634C57EC2F574 (process_id), INDEX IDX_911634C5D465829D (log_entry_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB ENCRYPTED = YES');
        $this->addSql('CREATE TABLE fos_user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_957A647992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_957A6479A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_957A6479C05FB297 (confirmation_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB ENCRYPTED = YES');
        $this->addSql('CREATE TABLE process_type (id INT AUTO_INCREMENT NOT NULL, conclusion_class VARCHAR(255) NOT NULL, hide_in_dashboard TINYINT(1) DEFAULT NULL, net_default_value DOUBLE PRECISION NOT NULL, name VARCHAR(255) NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB ENCRYPTED = YES');
        $this->addSql('CREATE TABLE process_type_process_status (process_type_id INT NOT NULL, process_status_id INT NOT NULL, INDEX IDX_4E14449F8D345646 (process_type_id), INDEX IDX_4E14449FF1BE4255 (process_status_id), PRIMARY KEY(process_type_id, process_status_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB ENCRYPTED = YES');
        $this->addSql('CREATE TABLE process_type_service (process_type_id INT NOT NULL, service_id INT NOT NULL, INDEX IDX_A302A0168D345646 (process_type_id), INDEX IDX_A302A016ED5CA9E6 (service_id), PRIMARY KEY(process_type_id, service_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB ENCRYPTED = YES');
        $this->addSql('CREATE TABLE economy_entry (id INT AUTO_INCREMENT NOT NULL, process_id INT NOT NULL, service_id INT DEFAULT NULL, amount DOUBLE PRECISION NOT NULL, type ENUM(\'SERVICE\', \'ACCOUNT\', \'ARREAR\') NOT NULL COMMENT \'(DC2Type:EconomyEntryEnumType)\', created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, discr VARCHAR(255) NOT NULL, text VARCHAR(255) DEFAULT NULL, date DATETIME DEFAULT NULL, period_from DATETIME DEFAULT NULL, period_to DATETIME DEFAULT NULL, amount_period INT DEFAULT NULL, repayment_amount DOUBLE PRECISION DEFAULT NULL, INDEX IDX_EE7979A27EC2F574 (process_id), INDEX IDX_EE7979A2ED5CA9E6 (service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB ENCRYPTED = YES');
        $this->addSql('CREATE TABLE channel (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB ENCRYPTED = YES');
        $this->addSql('CREATE TABLE quick_link (id INT AUTO_INCREMENT NOT NULL, href VARCHAR(255) NOT NULL, weight INT DEFAULT NULL, name VARCHAR(255) NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB ENCRYPTED = YES');
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, process_id INT NOT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, postal_code VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, telephone VARCHAR(255) DEFAULT NULL, number_of_children INT DEFAULT NULL, car_registration_number VARCHAR(255) DEFAULT NULL, self_employed TINYINT(1) DEFAULT NULL, works_in_major_company TINYINT(1) DEFAULT NULL, not_employed TINYINT(1) DEFAULT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_C74404557EC2F574 (process_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB ENCRYPTED = YES');
        $this->addSql('CREATE TABLE process (id INT AUTO_INCREMENT NOT NULL, case_worker_id INT DEFAULT NULL, channel_id INT DEFAULT NULL, reason_id INT DEFAULT NULL, service_id INT DEFAULT NULL, process_type_id INT NOT NULL, process_status_id INT DEFAULT NULL, completed_at DATETIME DEFAULT NULL, case_number VARCHAR(255) NOT NULL, client_cpr VARCHAR(255) NOT NULL, police_report TINYINT(1) DEFAULT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_861D18964503CACD (case_worker_id), INDEX IDX_861D189672F5A1AA (channel_id), INDEX IDX_861D189659BB1592 (reason_id), INDEX IDX_861D1896ED5CA9E6 (service_id), INDEX IDX_861D18968D345646 (process_type_id), INDEX IDX_861D1896F1BE4255 (process_status_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB ENCRYPTED = YES');
        $this->addSql('ALTER TABLE conclusion ADD CONSTRAINT FK_1D5819F57EC2F574 FOREIGN KEY (process_id) REFERENCES process (id)');
        $this->addSql('ALTER TABLE reminder ADD CONSTRAINT FK_40374F407EC2F574 FOREIGN KEY (process_id) REFERENCES process (id)');
        $this->addSql('ALTER TABLE journal_entry ADD CONSTRAINT FK_C8FAAE5A7EC2F574 FOREIGN KEY (process_id) REFERENCES process (id)');
        $this->addSql('ALTER TABLE process_log_entry ADD CONSTRAINT FK_911634C57EC2F574 FOREIGN KEY (process_id) REFERENCES process (id)');
        $this->addSql('ALTER TABLE process_log_entry ADD CONSTRAINT FK_911634C5D465829D FOREIGN KEY (log_entry_id) REFERENCES ext_log_entries (id)');
        $this->addSql('ALTER TABLE process_type_process_status ADD CONSTRAINT FK_4E14449F8D345646 FOREIGN KEY (process_type_id) REFERENCES process_type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE process_type_process_status ADD CONSTRAINT FK_4E14449FF1BE4255 FOREIGN KEY (process_status_id) REFERENCES process_status (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE process_type_service ADD CONSTRAINT FK_A302A0168D345646 FOREIGN KEY (process_type_id) REFERENCES process_type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE process_type_service ADD CONSTRAINT FK_A302A016ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE economy_entry ADD CONSTRAINT FK_EE7979A27EC2F574 FOREIGN KEY (process_id) REFERENCES process (id)');
        $this->addSql('ALTER TABLE economy_entry ADD CONSTRAINT FK_EE7979A2ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C74404557EC2F574 FOREIGN KEY (process_id) REFERENCES process (id)');
        $this->addSql('ALTER TABLE process ADD CONSTRAINT FK_861D18964503CACD FOREIGN KEY (case_worker_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE process ADD CONSTRAINT FK_861D189672F5A1AA FOREIGN KEY (channel_id) REFERENCES channel (id)');
        $this->addSql('ALTER TABLE process ADD CONSTRAINT FK_861D189659BB1592 FOREIGN KEY (reason_id) REFERENCES reason (id)');
        $this->addSql('ALTER TABLE process ADD CONSTRAINT FK_861D1896ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE process ADD CONSTRAINT FK_861D18968D345646 FOREIGN KEY (process_type_id) REFERENCES process_type (id)');
        $this->addSql('ALTER TABLE process ADD CONSTRAINT FK_861D1896F1BE4255 FOREIGN KEY (process_status_id) REFERENCES process_status (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE process_log_entry DROP FOREIGN KEY FK_911634C5D465829D');
        $this->addSql('ALTER TABLE process_type_process_status DROP FOREIGN KEY FK_4E14449FF1BE4255');
        $this->addSql('ALTER TABLE process DROP FOREIGN KEY FK_861D1896F1BE4255');
        $this->addSql('ALTER TABLE process DROP FOREIGN KEY FK_861D189659BB1592');
        $this->addSql('ALTER TABLE process_type_service DROP FOREIGN KEY FK_A302A016ED5CA9E6');
        $this->addSql('ALTER TABLE economy_entry DROP FOREIGN KEY FK_EE7979A2ED5CA9E6');
        $this->addSql('ALTER TABLE process DROP FOREIGN KEY FK_861D1896ED5CA9E6');
        $this->addSql('ALTER TABLE process DROP FOREIGN KEY FK_861D18964503CACD');
        $this->addSql('ALTER TABLE process_type_process_status DROP FOREIGN KEY FK_4E14449F8D345646');
        $this->addSql('ALTER TABLE process_type_service DROP FOREIGN KEY FK_A302A0168D345646');
        $this->addSql('ALTER TABLE process DROP FOREIGN KEY FK_861D18968D345646');
        $this->addSql('ALTER TABLE process DROP FOREIGN KEY FK_861D189672F5A1AA');
        $this->addSql('ALTER TABLE conclusion DROP FOREIGN KEY FK_1D5819F57EC2F574');
        $this->addSql('ALTER TABLE reminder DROP FOREIGN KEY FK_40374F407EC2F574');
        $this->addSql('ALTER TABLE journal_entry DROP FOREIGN KEY FK_C8FAAE5A7EC2F574');
        $this->addSql('ALTER TABLE process_log_entry DROP FOREIGN KEY FK_911634C57EC2F574');
        $this->addSql('ALTER TABLE economy_entry DROP FOREIGN KEY FK_EE7979A27EC2F574');
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C74404557EC2F574');
        $this->addSql('DROP TABLE ext_log_entries');
        $this->addSql('DROP TABLE process_status');
        $this->addSql('DROP TABLE reason');
        $this->addSql('DROP TABLE conclusion');
        $this->addSql('DROP TABLE reminder');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE journal_entry');
        $this->addSql('DROP TABLE process_log_entry');
        $this->addSql('DROP TABLE fos_user');
        $this->addSql('DROP TABLE process_type');
        $this->addSql('DROP TABLE process_type_process_status');
        $this->addSql('DROP TABLE process_type_service');
        $this->addSql('DROP TABLE economy_entry');
        $this->addSql('DROP TABLE channel');
        $this->addSql('DROP TABLE quick_link');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE process');
    }
}
