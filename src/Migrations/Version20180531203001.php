<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180531203001 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fleet ADD moonMaker INT DEFAULT NULL, ADD radarShip INT DEFAULT NULL, ADD brouilleurShip INT DEFAULT NULL, ADD motherShip INT DEFAULT NULL');
        $this->addSql('ALTER TABLE planet ADD moonMaker INT DEFAULT NULL, ADD radarShip INT DEFAULT NULL, ADD brouilleurShip INT DEFAULT NULL, ADD motherShip INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD moonMaker INT DEFAULT NULL, ADD radarShip INT DEFAULT NULL, ADD brouilleurShip INT DEFAULT NULL, ADD motherShip INT DEFAULT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fleet DROP moonMaker, DROP radarShip, DROP brouilleurShip, DROP motherShip');
        $this->addSql('ALTER TABLE planet DROP moonMaker, DROP radarShip, DROP brouilleurShip, DROP motherShip');
        $this->addSql('ALTER TABLE product DROP moonMaker, DROP radarShip, DROP brouilleurShip, DROP motherShip');
    }
}
