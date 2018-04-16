<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180416155655 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ally ADD sigle VARCHAR(5) NOT NULL, ADD slogan VARCHAR(30) NOT NULL, CHANGE name name VARCHAR(15) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_382900D8776B952 ON ally (sigle)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_382900D988768C9 ON ally (slogan)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_382900D8776B952 ON ally');
        $this->addSql('DROP INDEX UNIQ_382900D988768C9 ON ally');
        $this->addSql('ALTER TABLE ally DROP sigle, DROP slogan, CHANGE name name VARCHAR(20) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
