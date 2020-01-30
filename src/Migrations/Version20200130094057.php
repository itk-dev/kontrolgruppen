<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200130094057 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_settings DROP INDEX UNIQ_5C844C5A76ED395, ADD INDEX IDX_5C844C5A76ED395 (user_id)');
        $this->addSql('ALTER TABLE user_settings ADD settings_key VARCHAR(255) NOT NULL, ADD settings_value LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', DROP process_index_sort');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_settings DROP INDEX IDX_5C844C5A76ED395, ADD UNIQUE INDEX UNIQ_5C844C5A76ED395 (user_id)');
        $this->addSql('ALTER TABLE user_settings ADD process_index_sort VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP settings_key, DROP settings_value');
    }
}
