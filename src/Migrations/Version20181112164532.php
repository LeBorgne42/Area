<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181112164532 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE fleet (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, planet_id INT DEFAULT NULL, commander_id INT DEFAULT NULL, sector_id INT DEFAULT NULL, name VARCHAR(20) NOT NULL, attack TINYINT(1) NOT NULL, fightAt DATETIME DEFAULT NULL, newPlanet INT DEFAULT NULL, flightTime DATETIME DEFAULT NULL, cancelFlight DATETIME DEFAULT NULL, flightType VARCHAR(255) DEFAULT NULL, recycleAt DATETIME DEFAULT NULL, planete INT DEFAULT NULL, sonde BIGINT DEFAULT NULL, cargoI BIGINT DEFAULT NULL, cargoV BIGINT DEFAULT NULL, cargoX BIGINT DEFAULT NULL, colonizer INT DEFAULT NULL, recycleur INT DEFAULT NULL, barge INT DEFAULT NULL, moonMaker INT DEFAULT NULL, radarShip INT DEFAULT NULL, brouilleurShip INT DEFAULT NULL, motherShip INT DEFAULT NULL, hunter BIGINT DEFAULT NULL, hunterHeavy BIGINT DEFAULT NULL, hunterWar BIGINT DEFAULT NULL, corvet BIGINT DEFAULT NULL, corvetLaser BIGINT DEFAULT NULL, corvetWar BIGINT DEFAULT NULL, fregate BIGINT DEFAULT NULL, fregatePlasma BIGINT DEFAULT NULL, croiser BIGINT DEFAULT NULL, ironClad BIGINT DEFAULT NULL, destroyer BIGINT DEFAULT NULL, soldier INT DEFAULT NULL, worker INT DEFAULT NULL, scientist INT DEFAULT NULL, niobium INT DEFAULT NULL, water INT DEFAULT NULL, INDEX IDX_A05E1E47A76ED395 (user_id), INDEX IDX_A05E1E47A25E9820 (planet_id), UNIQUE INDEX UNIQ_A05E1E473349A583 (commander_id), INDEX IDX_A05E1E47DE95C867 (sector_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, planet_id INT DEFAULT NULL, sonde BIGINT DEFAULT NULL, cargoI BIGINT DEFAULT NULL, cargoV BIGINT DEFAULT NULL, cargoX BIGINT DEFAULT NULL, colonizer INT DEFAULT NULL, recycleur INT DEFAULT NULL, barge INT DEFAULT NULL, moonMaker INT DEFAULT NULL, radarShip INT DEFAULT NULL, brouilleurShip INT DEFAULT NULL, motherShip INT DEFAULT NULL, hunter BIGINT DEFAULT NULL, hunterHeavy BIGINT DEFAULT NULL, hunterWar BIGINT DEFAULT NULL, corvet BIGINT DEFAULT NULL, corvetLaser BIGINT DEFAULT NULL, corvetWar BIGINT DEFAULT NULL, fregate BIGINT DEFAULT NULL, fregatePlasma BIGINT DEFAULT NULL, croiser BIGINT DEFAULT NULL, ironClad BIGINT DEFAULT NULL, destroyer BIGINT DEFAULT NULL, productAt DATETIME NOT NULL, UNIQUE INDEX UNIQ_D34A04ADA25E9820 (planet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ally_pna (id INT AUTO_INCREMENT NOT NULL, ally_id INT DEFAULT NULL, allyTag VARCHAR(5) NOT NULL, signedAt DATETIME NOT NULL, accepted TINYINT(1) NOT NULL, INDEX IDX_B8D574011C6E3E76 (ally_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE fleet ADD CONSTRAINT FK_A05E1E47A76ED395 FOREIGN KEY (user_id) REFERENCES app_users (id)');
        $this->addSql('ALTER TABLE fleet ADD CONSTRAINT FK_A05E1E47A25E9820 FOREIGN KEY (planet_id) REFERENCES planet (id)');
        $this->addSql('ALTER TABLE fleet ADD CONSTRAINT FK_A05E1E473349A583 FOREIGN KEY (commander_id) REFERENCES commander (id)');
        $this->addSql('ALTER TABLE fleet ADD CONSTRAINT FK_A05E1E47DE95C867 FOREIGN KEY (sector_id) REFERENCES sector (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADA25E9820 FOREIGN KEY (planet_id) REFERENCES planet (id)');
        $this->addSql('ALTER TABLE ally_pna ADD CONSTRAINT FK_B8D574011C6E3E76 FOREIGN KEY (ally_id) REFERENCES ally (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE fleet');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE ally_pna');
    }
}
