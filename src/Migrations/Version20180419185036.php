<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180419185036 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE war (id INT AUTO_INCREMENT NOT NULL, ally_id INT DEFAULT NULL, allyTag VARCHAR(5) NOT NULL, signedAt DATETIME NOT NULL, accepted TINYINT(1) NOT NULL, INDEX IDX_6C12ED311C6E3E76 (ally_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE orbite (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fleet (id INT AUTO_INCREMENT NOT NULL, soldier_id INT DEFAULT NULL, worker_id INT DEFAULT NULL, scientist_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_A05E1E47A38C1700 (soldier_id), UNIQUE INDEX UNIQ_A05E1E476B20BA36 (worker_id), UNIQUE INDEX UNIQ_A05E1E47EBA327D6 (scientist_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE allied (id INT AUTO_INCREMENT NOT NULL, ally_id INT DEFAULT NULL, allyTag VARCHAR(5) NOT NULL, signedAt DATETIME NOT NULL, accepted TINYINT(1) NOT NULL, INDEX IDX_4A0B65A71C6E3E76 (ally_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE buildSearch (id INT AUTO_INCREMENT NOT NULL, niobium INT NOT NULL, water INT NOT NULL, production NUMERIC(9, 5) NOT NULL, level INT NOT NULL, finishAt DATETIME DEFAULT NULL, constructTime BIGINT NOT NULL, ground INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE soldier (id INT AUTO_INCREMENT NOT NULL, amount BIGINT NOT NULL, life INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lightUsine (id INT AUTO_INCREMENT NOT NULL, niobium INT NOT NULL, water INT NOT NULL, production NUMERIC(9, 5) NOT NULL, level INT NOT NULL, finishAt DATETIME DEFAULT NULL, constructTime BIGINT NOT NULL, ground INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE planet (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, building_id INT DEFAULT NULL, orbite_id INT DEFAULT NULL, soldier_id INT DEFAULT NULL, worker_id INT DEFAULT NULL, scientist_id INT DEFAULT NULL, sector_id INT DEFAULT NULL, name VARCHAR(15) DEFAULT NULL, niobium NUMERIC(28, 5) NOT NULL, water NUMERIC(28, 5) NOT NULL, position INT NOT NULL, ground INT DEFAULT NULL, groundPlace INT DEFAULT NULL, sky INT DEFAULT NULL, skyPlace INT DEFAULT NULL, empty TINYINT(1) NOT NULL, imageName VARCHAR(20) DEFAULT NULL, INDEX IDX_68136AA5A76ED395 (user_id), UNIQUE INDEX UNIQ_68136AA54D2A7E12 (building_id), UNIQUE INDEX UNIQ_68136AA521DE9274 (orbite_id), UNIQUE INDEX UNIQ_68136AA5A38C1700 (soldier_id), UNIQUE INDEX UNIQ_68136AA56B20BA36 (worker_id), UNIQUE INDEX UNIQ_68136AA5EBA327D6 (scientist_id), INDEX IDX_68136AA5DE95C867 (sector_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE galaxy (id INT AUTO_INCREMENT NOT NULL, position INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ally (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(15) NOT NULL, sigle VARCHAR(5) NOT NULL, slogan VARCHAR(30) NOT NULL, bitcoin BIGINT NOT NULL, taxe INT NOT NULL, image_name VARCHAR(255) DEFAULT NULL, image_size INT DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_382900D5E237E06 (name), UNIQUE INDEX UNIQ_382900D8776B952 (sigle), UNIQUE INDEX UNIQ_382900D988768C9 (slogan), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE grade (id INT AUTO_INCREMENT NOT NULL, ally_id INT DEFAULT NULL, placement INT NOT NULL, name VARCHAR(20) NOT NULL, canRecruit TINYINT(1) NOT NULL, canKick TINYINT(1) NOT NULL, canWar TINYINT(1) NOT NULL, canPeace TINYINT(1) NOT NULL, INDEX IDX_595AAE341C6E3E76 (ally_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE x_extractor (id INT AUTO_INCREMENT NOT NULL, niobium INT NOT NULL, water INT NOT NULL, production NUMERIC(9, 5) NOT NULL, level INT NOT NULL, finishAt DATETIME DEFAULT NULL, constructTime BIGINT NOT NULL, ground INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE heavyUsine (id INT AUTO_INCREMENT NOT NULL, niobium INT NOT NULL, water INT NOT NULL, production NUMERIC(9, 5) NOT NULL, level INT NOT NULL, finishAt DATETIME DEFAULT NULL, constructTime BIGINT NOT NULL, ground INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app_users (id INT AUTO_INCREMENT NOT NULL, ally_id INT DEFAULT NULL, grade_id INT DEFAULT NULL, rank_id INT DEFAULT NULL, username VARCHAR(20) NOT NULL, email VARCHAR(40) NOT NULL, joinAllyAt DATETIME DEFAULT NULL, password VARCHAR(64) NOT NULL, bitcoin NUMERIC(28, 5) NOT NULL, created_at DATETIME NOT NULL, connected TINYINT(1) NOT NULL, image_name VARCHAR(255) DEFAULT NULL, image_size INT DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_C2502824F85E0677 (username), UNIQUE INDEX UNIQ_C2502824E7927C74 (email), INDEX IDX_C25028241C6E3E76 (ally_id), INDEX IDX_C2502824FE19A1A8 (grade_id), UNIQUE INDEX UNIQ_C25028247616678F (rank_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sector (id INT AUTO_INCREMENT NOT NULL, galaxy_id INT DEFAULT NULL, position INT NOT NULL, INDEX IDX_4BA3D9E8B61FAB2 (galaxy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE proposal (id INT AUTO_INCREMENT NOT NULL, ally_id INT DEFAULT NULL, user_id INT DEFAULT NULL, proposalAt DATETIME NOT NULL, INDEX IDX_BFE594721C6E3E76 (ally_id), INDEX IDX_BFE59472A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE scientist (id INT AUTO_INCREMENT NOT NULL, amount BIGINT NOT NULL, life INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE building (id INT AUTO_INCREMENT NOT NULL, miner_id INT DEFAULT NULL, extractor_id INT DEFAULT NULL, caserne_id INT DEFAULT NULL, radar_id INT DEFAULT NULL, spaceShip_id INT DEFAULT NULL, buildSearch_id INT DEFAULT NULL, skyRadar_id INT DEFAULT NULL, skyBrouilleur_id INT DEFAULT NULL, lightUsine_id INT DEFAULT NULL, heavyUsine_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_E16F61D4BF88117 (miner_id), UNIQUE INDEX UNIQ_E16F61D4DBCA8D53 (extractor_id), UNIQUE INDEX UNIQ_E16F61D485646CF7 (spaceShip_id), UNIQUE INDEX UNIQ_E16F61D42D15FD1C (buildSearch_id), UNIQUE INDEX UNIQ_E16F61D49C03C926 (caserne_id), UNIQUE INDEX UNIQ_E16F61D4C9976951 (radar_id), UNIQUE INDEX UNIQ_E16F61D42C83DB79 (skyRadar_id), UNIQUE INDEX UNIQ_E16F61D4EA5D5F6 (skyBrouilleur_id), UNIQUE INDEX UNIQ_E16F61D464119DE1 (lightUsine_id), UNIQUE INDEX UNIQ_E16F61D4813F26B9 (heavyUsine_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE worker (id INT AUTO_INCREMENT NOT NULL, amount NUMERIC(28, 5) NOT NULL, life INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE skyBrouilleur (id INT AUTO_INCREMENT NOT NULL, niobium INT NOT NULL, water INT NOT NULL, level INT NOT NULL, finishAt DATETIME DEFAULT NULL, constructTime BIGINT NOT NULL, sky INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE radar (id INT AUTO_INCREMENT NOT NULL, niobium INT NOT NULL, water INT NOT NULL, level INT NOT NULL, finishAt DATETIME DEFAULT NULL, constructTime BIGINT NOT NULL, ground INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rank (id INT AUTO_INCREMENT NOT NULL, point BIGINT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE caserne (id INT AUTO_INCREMENT NOT NULL, niobium INT NOT NULL, water INT NOT NULL, production NUMERIC(9, 5) NOT NULL, level INT NOT NULL, finishAt DATETIME DEFAULT NULL, constructTime BIGINT NOT NULL, ground INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pna (id INT AUTO_INCREMENT NOT NULL, ally_id INT DEFAULT NULL, allyTag VARCHAR(5) NOT NULL, signedAt DATETIME NOT NULL, accepted TINYINT(1) NOT NULL, INDEX IDX_6A7BA6A51C6E3E76 (ally_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE spaceShip (id INT AUTO_INCREMENT NOT NULL, niobium INT NOT NULL, water INT NOT NULL, production NUMERIC(9, 5) NOT NULL, level INT NOT NULL, finishAt DATETIME DEFAULT NULL, constructTime BIGINT NOT NULL, ground INT NOT NULL, sky INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app_admins (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(20) NOT NULL, email VARCHAR(40) NOT NULL, password VARCHAR(64) NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_47E3788FF85E0677 (username), UNIQUE INDEX UNIQ_47E3788FE7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE x_miner (id INT AUTO_INCREMENT NOT NULL, niobium INT NOT NULL, water INT NOT NULL, production NUMERIC(9, 5) NOT NULL, level INT NOT NULL, finishAt DATETIME DEFAULT NULL, constructTime BIGINT NOT NULL, ground INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE skyRadar (id INT AUTO_INCREMENT NOT NULL, niobium INT NOT NULL, water INT NOT NULL, level INT NOT NULL, finishAt DATETIME DEFAULT NULL, constructTime BIGINT NOT NULL, sky INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE war ADD CONSTRAINT FK_6C12ED311C6E3E76 FOREIGN KEY (ally_id) REFERENCES ally (id)');
        $this->addSql('ALTER TABLE fleet ADD CONSTRAINT FK_A05E1E47A38C1700 FOREIGN KEY (soldier_id) REFERENCES soldier (id)');
        $this->addSql('ALTER TABLE fleet ADD CONSTRAINT FK_A05E1E476B20BA36 FOREIGN KEY (worker_id) REFERENCES worker (id)');
        $this->addSql('ALTER TABLE fleet ADD CONSTRAINT FK_A05E1E47EBA327D6 FOREIGN KEY (scientist_id) REFERENCES scientist (id)');
        $this->addSql('ALTER TABLE allied ADD CONSTRAINT FK_4A0B65A71C6E3E76 FOREIGN KEY (ally_id) REFERENCES ally (id)');
        $this->addSql('ALTER TABLE planet ADD CONSTRAINT FK_68136AA5A76ED395 FOREIGN KEY (user_id) REFERENCES app_users (id)');
        $this->addSql('ALTER TABLE planet ADD CONSTRAINT FK_68136AA54D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id)');
        $this->addSql('ALTER TABLE planet ADD CONSTRAINT FK_68136AA521DE9274 FOREIGN KEY (orbite_id) REFERENCES orbite (id)');
        $this->addSql('ALTER TABLE planet ADD CONSTRAINT FK_68136AA5A38C1700 FOREIGN KEY (soldier_id) REFERENCES soldier (id)');
        $this->addSql('ALTER TABLE planet ADD CONSTRAINT FK_68136AA56B20BA36 FOREIGN KEY (worker_id) REFERENCES worker (id)');
        $this->addSql('ALTER TABLE planet ADD CONSTRAINT FK_68136AA5EBA327D6 FOREIGN KEY (scientist_id) REFERENCES scientist (id)');
        $this->addSql('ALTER TABLE planet ADD CONSTRAINT FK_68136AA5DE95C867 FOREIGN KEY (sector_id) REFERENCES sector (id)');
        $this->addSql('ALTER TABLE grade ADD CONSTRAINT FK_595AAE341C6E3E76 FOREIGN KEY (ally_id) REFERENCES ally (id)');
        $this->addSql('ALTER TABLE app_users ADD CONSTRAINT FK_C25028241C6E3E76 FOREIGN KEY (ally_id) REFERENCES ally (id)');
        $this->addSql('ALTER TABLE app_users ADD CONSTRAINT FK_C2502824FE19A1A8 FOREIGN KEY (grade_id) REFERENCES grade (id)');
        $this->addSql('ALTER TABLE app_users ADD CONSTRAINT FK_C25028247616678F FOREIGN KEY (rank_id) REFERENCES rank (id)');
        $this->addSql('ALTER TABLE sector ADD CONSTRAINT FK_4BA3D9E8B61FAB2 FOREIGN KEY (galaxy_id) REFERENCES galaxy (id)');
        $this->addSql('ALTER TABLE proposal ADD CONSTRAINT FK_BFE594721C6E3E76 FOREIGN KEY (ally_id) REFERENCES ally (id)');
        $this->addSql('ALTER TABLE proposal ADD CONSTRAINT FK_BFE59472A76ED395 FOREIGN KEY (user_id) REFERENCES app_users (id)');
        $this->addSql('ALTER TABLE building ADD CONSTRAINT FK_E16F61D4BF88117 FOREIGN KEY (miner_id) REFERENCES x_miner (id)');
        $this->addSql('ALTER TABLE building ADD CONSTRAINT FK_E16F61D4DBCA8D53 FOREIGN KEY (extractor_id) REFERENCES x_extractor (id)');
        $this->addSql('ALTER TABLE building ADD CONSTRAINT FK_E16F61D485646CF7 FOREIGN KEY (spaceShip_id) REFERENCES spaceShip (id)');
        $this->addSql('ALTER TABLE building ADD CONSTRAINT FK_E16F61D42D15FD1C FOREIGN KEY (buildSearch_id) REFERENCES buildSearch (id)');
        $this->addSql('ALTER TABLE building ADD CONSTRAINT FK_E16F61D49C03C926 FOREIGN KEY (caserne_id) REFERENCES caserne (id)');
        $this->addSql('ALTER TABLE building ADD CONSTRAINT FK_E16F61D4C9976951 FOREIGN KEY (radar_id) REFERENCES radar (id)');
        $this->addSql('ALTER TABLE building ADD CONSTRAINT FK_E16F61D42C83DB79 FOREIGN KEY (skyRadar_id) REFERENCES skyRadar (id)');
        $this->addSql('ALTER TABLE building ADD CONSTRAINT FK_E16F61D4EA5D5F6 FOREIGN KEY (skyBrouilleur_id) REFERENCES skyBrouilleur (id)');
        $this->addSql('ALTER TABLE building ADD CONSTRAINT FK_E16F61D464119DE1 FOREIGN KEY (lightUsine_id) REFERENCES lightUsine (id)');
        $this->addSql('ALTER TABLE building ADD CONSTRAINT FK_E16F61D4813F26B9 FOREIGN KEY (heavyUsine_id) REFERENCES heavyUsine (id)');
        $this->addSql('ALTER TABLE pna ADD CONSTRAINT FK_6A7BA6A51C6E3E76 FOREIGN KEY (ally_id) REFERENCES ally (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE planet DROP FOREIGN KEY FK_68136AA521DE9274');
        $this->addSql('ALTER TABLE building DROP FOREIGN KEY FK_E16F61D42D15FD1C');
        $this->addSql('ALTER TABLE fleet DROP FOREIGN KEY FK_A05E1E47A38C1700');
        $this->addSql('ALTER TABLE planet DROP FOREIGN KEY FK_68136AA5A38C1700');
        $this->addSql('ALTER TABLE building DROP FOREIGN KEY FK_E16F61D464119DE1');
        $this->addSql('ALTER TABLE sector DROP FOREIGN KEY FK_4BA3D9E8B61FAB2');
        $this->addSql('ALTER TABLE war DROP FOREIGN KEY FK_6C12ED311C6E3E76');
        $this->addSql('ALTER TABLE allied DROP FOREIGN KEY FK_4A0B65A71C6E3E76');
        $this->addSql('ALTER TABLE grade DROP FOREIGN KEY FK_595AAE341C6E3E76');
        $this->addSql('ALTER TABLE app_users DROP FOREIGN KEY FK_C25028241C6E3E76');
        $this->addSql('ALTER TABLE proposal DROP FOREIGN KEY FK_BFE594721C6E3E76');
        $this->addSql('ALTER TABLE pna DROP FOREIGN KEY FK_6A7BA6A51C6E3E76');
        $this->addSql('ALTER TABLE app_users DROP FOREIGN KEY FK_C2502824FE19A1A8');
        $this->addSql('ALTER TABLE building DROP FOREIGN KEY FK_E16F61D4DBCA8D53');
        $this->addSql('ALTER TABLE building DROP FOREIGN KEY FK_E16F61D4813F26B9');
        $this->addSql('ALTER TABLE planet DROP FOREIGN KEY FK_68136AA5A76ED395');
        $this->addSql('ALTER TABLE proposal DROP FOREIGN KEY FK_BFE59472A76ED395');
        $this->addSql('ALTER TABLE planet DROP FOREIGN KEY FK_68136AA5DE95C867');
        $this->addSql('ALTER TABLE fleet DROP FOREIGN KEY FK_A05E1E47EBA327D6');
        $this->addSql('ALTER TABLE planet DROP FOREIGN KEY FK_68136AA5EBA327D6');
        $this->addSql('ALTER TABLE planet DROP FOREIGN KEY FK_68136AA54D2A7E12');
        $this->addSql('ALTER TABLE fleet DROP FOREIGN KEY FK_A05E1E476B20BA36');
        $this->addSql('ALTER TABLE planet DROP FOREIGN KEY FK_68136AA56B20BA36');
        $this->addSql('ALTER TABLE building DROP FOREIGN KEY FK_E16F61D4EA5D5F6');
        $this->addSql('ALTER TABLE building DROP FOREIGN KEY FK_E16F61D4C9976951');
        $this->addSql('ALTER TABLE app_users DROP FOREIGN KEY FK_C25028247616678F');
        $this->addSql('ALTER TABLE building DROP FOREIGN KEY FK_E16F61D49C03C926');
        $this->addSql('ALTER TABLE building DROP FOREIGN KEY FK_E16F61D485646CF7');
        $this->addSql('ALTER TABLE building DROP FOREIGN KEY FK_E16F61D4BF88117');
        $this->addSql('ALTER TABLE building DROP FOREIGN KEY FK_E16F61D42C83DB79');
        $this->addSql('DROP TABLE war');
        $this->addSql('DROP TABLE orbite');
        $this->addSql('DROP TABLE fleet');
        $this->addSql('DROP TABLE allied');
        $this->addSql('DROP TABLE buildSearch');
        $this->addSql('DROP TABLE soldier');
        $this->addSql('DROP TABLE lightUsine');
        $this->addSql('DROP TABLE planet');
        $this->addSql('DROP TABLE galaxy');
        $this->addSql('DROP TABLE ally');
        $this->addSql('DROP TABLE grade');
        $this->addSql('DROP TABLE x_extractor');
        $this->addSql('DROP TABLE heavyUsine');
        $this->addSql('DROP TABLE app_users');
        $this->addSql('DROP TABLE sector');
        $this->addSql('DROP TABLE proposal');
        $this->addSql('DROP TABLE scientist');
        $this->addSql('DROP TABLE building');
        $this->addSql('DROP TABLE worker');
        $this->addSql('DROP TABLE skyBrouilleur');
        $this->addSql('DROP TABLE radar');
        $this->addSql('DROP TABLE rank');
        $this->addSql('DROP TABLE caserne');
        $this->addSql('DROP TABLE pna');
        $this->addSql('DROP TABLE spaceShip');
        $this->addSql('DROP TABLE app_admins');
        $this->addSql('DROP TABLE x_miner');
        $this->addSql('DROP TABLE skyRadar');
    }
}
