<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180504015235 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE s_content DROP INDEX UNIQ_C088E503A76ED395, ADD INDEX IDX_C088E503A76ED395 (user_id)');
        $this->addSql('ALTER TABLE s_content CHANGE message message VARCHAR(200) NOT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE s_content DROP INDEX IDX_C088E503A76ED395, ADD UNIQUE INDEX UNIQ_C088E503A76ED395 (user_id)');
        $this->addSql('ALTER TABLE s_content CHANGE message message VARCHAR(500) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
