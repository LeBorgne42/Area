<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190706160449 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE server ADD pvp TINYINT(1) NOT NULL, DROP nbr_message, DROP nbr_colonize, DROP nbr_salon_message, DROP nbr_invasion, DROP nbr_sell, DROP nbr_battle, DROP nbr_building, DROP nbr_research, DROP nbr_zombie, DROP nbr_loot');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE server ADD nbr_message BIGINT NOT NULL, ADD nbr_colonize BIGINT NOT NULL, ADD nbr_salon_message BIGINT NOT NULL, ADD nbr_invasion BIGINT NOT NULL, ADD nbr_sell BIGINT NOT NULL, ADD nbr_battle BIGINT NOT NULL, ADD nbr_building BIGINT NOT NULL, ADD nbr_research BIGINT NOT NULL, ADD nbr_zombie BIGINT NOT NULL, ADD nbr_loot BIGINT NOT NULL, DROP pvp');
    }
}
