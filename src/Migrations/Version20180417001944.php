<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180417001944 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE proposal DROP INDEX UNIQ_BFE594721C6E3E76, ADD INDEX IDX_BFE594721C6E3E76 (ally_id)');
        $this->addSql('ALTER TABLE proposal DROP INDEX UNIQ_BFE59472A76ED395, ADD INDEX IDX_BFE59472A76ED395 (user_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE proposal DROP INDEX IDX_BFE594721C6E3E76, ADD UNIQUE INDEX UNIQ_BFE594721C6E3E76 (ally_id)');
        $this->addSql('ALTER TABLE proposal DROP INDEX IDX_BFE59472A76ED395, ADD UNIQUE INDEX UNIQ_BFE59472A76ED395 (user_id)');
    }
}
