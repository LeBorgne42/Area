<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181112174551 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE statistic');
        $this->addSql('ALTER TABLE server ADD nbr_message INT DEFAULT NULL, ADD nbr_colonize INT DEFAULT NULL, ADD nbr_salon_message INT DEFAULT NULL, ADD nbr_invasion INT DEFAULT NULL, ADD nbr_sell INT DEFAULT NULL, ADD nbr_battle INT DEFAULT NULL, ADD nbr_building INT DEFAULT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE statistic (id INT AUTO_INCREMENT NOT NULL, nbr_message INT DEFAULT NULL, nbr_colonize INT DEFAULT NULL, nbr_salon_message INT DEFAULT NULL, nbr_invasion INT DEFAULT NULL, nbr_sell INT DEFAULT NULL, nbr_battle INT DEFAULT NULL, nbr_building INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE server DROP nbr_message, DROP nbr_colonize, DROP nbr_salon_message, DROP nbr_invasion, DROP nbr_sell, DROP nbr_battle, DROP nbr_building');
    }
}
