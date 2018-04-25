<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180425070944 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE planet DROP FOREIGN KEY FK_68136AA54D2A7E12');
        $this->addSql('DROP INDEX UNIQ_68136AA54D2A7E12 ON planet');
        $this->addSql('ALTER TABLE planet ADD shipProduction NUMERIC(28, 5) NOT NULL, ADD workerProduction NUMERIC(28, 5) NOT NULL, ADD construct VARCHAR(255) DEFAULT NULL, ADD constructAt DATETIME DEFAULT NULL, ADD extractor INT DEFAULT NULL, ADD spaceShip INT DEFAULT NULL, ADD centerSearch INT DEFAULT NULL, ADD metropole INT DEFAULT NULL, ADD city INT DEFAULT NULL, ADD caserne INT DEFAULT NULL, ADD radar INT DEFAULT NULL, ADD skyRadar INT DEFAULT NULL, ADD skyBrouilleur INT DEFAULT NULL, ADD lightUsine INT DEFAULT NULL, ADD heavyUsine INT DEFAULT NULL, CHANGE building_id miner INT DEFAULT NULL');
        $this->addSql('ALTER TABLE app_users ADD scientistProduction NUMERIC(28, 5) NOT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE app_users DROP scientistProduction');
        $this->addSql('ALTER TABLE planet ADD building_id INT DEFAULT NULL, DROP shipProduction, DROP workerProduction, DROP construct, DROP constructAt, DROP miner, DROP extractor, DROP spaceShip, DROP centerSearch, DROP metropole, DROP city, DROP caserne, DROP radar, DROP skyRadar, DROP skyBrouilleur, DROP lightUsine, DROP heavyUsine');
        $this->addSql('ALTER TABLE planet ADD CONSTRAINT FK_68136AA54D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_68136AA54D2A7E12 ON planet (building_id)');
    }
}
