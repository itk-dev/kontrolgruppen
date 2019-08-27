<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190826104023 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE economy_entry ADD income_type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE economy_entry ADD CONSTRAINT FK_EE7979A27D0EFAEA FOREIGN KEY (income_type_id) REFERENCES income_type (id)');
        $this->addSql('CREATE INDEX IDX_EE7979A27D0EFAEA ON economy_entry (income_type_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE economy_entry DROP FOREIGN KEY FK_EE7979A27D0EFAEA');
        $this->addSql('DROP INDEX IDX_EE7979A27D0EFAEA ON economy_entry');
        $this->addSql('ALTER TABLE economy_entry DROP income_type_id');
    }
}
