<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180418195553 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE building ADD miner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE building ADD CONSTRAINT FK_E16F61D4BF88117 FOREIGN KEY (miner_id) REFERENCES x_miner (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E16F61D4BF88117 ON building (miner_id)');
        $this->addSql('ALTER TABLE x_miner DROP FOREIGN KEY FK_7C7799BD4D2A7E12');
        $this->addSql('DROP INDEX UNIQ_7C7799BD4D2A7E12 ON x_miner');
        $this->addSql('ALTER TABLE x_miner DROP building_id');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE building DROP FOREIGN KEY FK_E16F61D4BF88117');
        $this->addSql('DROP INDEX UNIQ_E16F61D4BF88117 ON building');
        $this->addSql('ALTER TABLE building DROP miner_id');
        $this->addSql('ALTER TABLE x_miner ADD building_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE x_miner ADD CONSTRAINT FK_7C7799BD4D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7C7799BD4D2A7E12 ON x_miner (building_id)');
    }
}
