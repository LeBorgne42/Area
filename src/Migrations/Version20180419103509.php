<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180419103509 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE planet ADD groundPlace INT DEFAULT NULL, ADD skyPlace INT DEFAULT NULL, CHANGE land ground INT DEFAULT NULL');
        $this->addSql('ALTER TABLE x_extractor ADD ground INT NOT NULL');
        $this->addSql('ALTER TABLE x_miner ADD ground INT NOT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE planet ADD land INT DEFAULT NULL, DROP ground, DROP groundPlace, DROP skyPlace');
        $this->addSql('ALTER TABLE x_extractor DROP ground');
        $this->addSql('ALTER TABLE x_miner DROP ground');
    }
}
