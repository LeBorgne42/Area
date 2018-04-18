<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180418200200 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE x_extractor (id INT AUTO_INCREMENT NOT NULL, niobium INT NOT NULL, water INT NOT NULL, production NUMERIC(9, 5) NOT NULL, level INT NOT NULL, finishAt DATETIME DEFAULT NULL, constructTime INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE building ADD extractor_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE building ADD CONSTRAINT FK_E16F61D4DBCA8D53 FOREIGN KEY (extractor_id) REFERENCES x_extractor (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E16F61D4DBCA8D53 ON building (extractor_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE building DROP FOREIGN KEY FK_E16F61D4DBCA8D53');
        $this->addSql('DROP TABLE x_extractor');
        $this->addSql('DROP INDEX UNIQ_E16F61D4DBCA8D53 ON building');
        $this->addSql('ALTER TABLE building DROP extractor_id');
    }
}
