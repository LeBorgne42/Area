<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181112235620 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE server ADD nbr_research INT NOT NULL, CHANGE nbr_message nbr_message INT NOT NULL, CHANGE nbr_colonize nbr_colonize INT NOT NULL, CHANGE nbr_salon_message nbr_salon_message INT NOT NULL, CHANGE nbr_invasion nbr_invasion INT NOT NULL, CHANGE nbr_sell nbr_sell INT NOT NULL, CHANGE nbr_battle nbr_battle INT NOT NULL, CHANGE nbr_building nbr_building INT NOT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE server DROP nbr_research, CHANGE nbr_message nbr_message INT DEFAULT NULL, CHANGE nbr_colonize nbr_colonize INT DEFAULT NULL, CHANGE nbr_salon_message nbr_salon_message INT DEFAULT NULL, CHANGE nbr_invasion nbr_invasion INT DEFAULT NULL, CHANGE nbr_sell nbr_sell INT DEFAULT NULL, CHANGE nbr_battle nbr_battle INT DEFAULT NULL, CHANGE nbr_building nbr_building INT DEFAULT NULL');
    }
}
