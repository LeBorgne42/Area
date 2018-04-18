<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180418185820 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE planet CHANGE niobium niobium NUMERIC(28, 5) NOT NULL, CHANGE water water NUMERIC(28, 5) NOT NULL');
        $this->addSql('ALTER TABLE app_users CHANGE bitcoin bitcoin NUMERIC(28, 5) NOT NULL');
        $this->addSql('ALTER TABLE worker CHANGE amount amount NUMERIC(28, 5) NOT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE app_users CHANGE bitcoin bitcoin NUMERIC(30, 5) NOT NULL');
        $this->addSql('ALTER TABLE planet CHANGE niobium niobium BIGINT NOT NULL, CHANGE water water BIGINT NOT NULL');
        $this->addSql('ALTER TABLE worker CHANGE amount amount BIGINT NOT NULL');
    }
}
