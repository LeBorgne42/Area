<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180504145313 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ally_war (id INT AUTO_INCREMENT NOT NULL, ally_id INT DEFAULT NULL, allyTag VARCHAR(5) NOT NULL, signedAt DATETIME NOT NULL, accepted TINYINT(1) NOT NULL, INDEX IDX_BEBC3F951C6E3E76 (ally_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fleet (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, planet_id INT DEFAULT NULL, sector_id INT DEFAULT NULL, name VARCHAR(15) NOT NULL, attack TINYINT(1) NOT NULL, fightAt DATETIME DEFAULT NULL, newPlanet INT DEFAULT NULL, flightTime DATETIME DEFAULT NULL, planete INT DEFAULT NULL, sonde BIGINT DEFAULT NULL, colonizer INT DEFAULT NULL, recycleur INT DEFAULT NULL, barge INT DEFAULT NULL, hunter BIGINT DEFAULT NULL, fregate BIGINT DEFAULT NULL, soldier INT DEFAULT NULL, worker INT DEFAULT NULL, scientist INT DEFAULT NULL, niobium INT DEFAULT NULL, water INT DEFAULT NULL, INDEX IDX_A05E1E47A76ED395 (user_id), INDEX IDX_A05E1E47A25E9820 (planet_id), INDEX IDX_A05E1E47DE95C867 (sector_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ally_allied (id INT AUTO_INCREMENT NOT NULL, ally_id INT DEFAULT NULL, allyTag VARCHAR(5) NOT NULL, signedAt DATETIME NOT NULL, accepted TINYINT(1) NOT NULL, INDEX IDX_161AB9BB1C6E3E76 (ally_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE planet (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, sector_id INT DEFAULT NULL, name VARCHAR(15) DEFAULT NULL, niobium NUMERIC(28, 5) NOT NULL, water NUMERIC(28, 5) NOT NULL, nbCdr BIGINT DEFAULT NULL, wtCdr BIGINT DEFAULT NULL, shipProduction NUMERIC(28, 5) NOT NULL, workerProduction NUMERIC(28, 5) NOT NULL, scientistProduction NUMERIC(28, 5) NOT NULL, soldierMax INT NOT NULL, nbProduction NUMERIC(28, 5) NOT NULL, wtProduction NUMERIC(28, 5) NOT NULL, construct VARCHAR(255) DEFAULT NULL, constructAt DATETIME DEFAULT NULL, miner INT DEFAULT NULL, extractor INT DEFAULT NULL, spaceShip INT DEFAULT NULL, centerSearch INT DEFAULT NULL, metropole INT DEFAULT NULL, city INT DEFAULT NULL, caserne INT DEFAULT NULL, radar INT DEFAULT NULL, skyRadar INT DEFAULT NULL, skyBrouilleur INT DEFAULT NULL, lightUsine INT DEFAULT NULL, heavyUsine INT DEFAULT NULL, sonde BIGINT DEFAULT NULL, colonizer INT DEFAULT NULL, recycleur INT DEFAULT NULL, barge INT DEFAULT NULL, hunter BIGINT DEFAULT NULL, fregate BIGINT DEFAULT NULL, soldier INT DEFAULT NULL, worker INT NOT NULL, scientist INT DEFAULT NULL, position INT NOT NULL, ground INT DEFAULT NULL, groundPlace INT DEFAULT NULL, sky INT DEFAULT NULL, skyPlace INT DEFAULT NULL, empty TINYINT(1) NOT NULL, cdr TINYINT(1) NOT NULL, merchant TINYINT(1) NOT NULL, imageName VARCHAR(20) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_68136AA5A76ED395 (user_id), INDEX IDX_68136AA5DE95C867 (sector_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE galaxy (id INT AUTO_INCREMENT NOT NULL, position INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ally (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(15) NOT NULL, sigle VARCHAR(5) NOT NULL, slogan VARCHAR(30) NOT NULL, bitcoin BIGINT NOT NULL, taxe INT NOT NULL, image_name VARCHAR(255) DEFAULT NULL, image_size INT DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_382900D5E237E06 (name), UNIQUE INDEX UNIQ_382900D8776B952 (sigle), UNIQUE INDEX UNIQ_382900D988768C9 (slogan), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE grade (id INT AUTO_INCREMENT NOT NULL, ally_id INT DEFAULT NULL, placement INT NOT NULL, name VARCHAR(20) NOT NULL, canRecruit TINYINT(1) NOT NULL, canKick TINYINT(1) NOT NULL, canWar TINYINT(1) NOT NULL, canPeace TINYINT(1) NOT NULL, INDEX IDX_595AAE341C6E3E76 (ally_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app_users (id INT AUTO_INCREMENT NOT NULL, ally_id INT DEFAULT NULL, grade_id INT DEFAULT NULL, rank_id INT DEFAULT NULL, username VARCHAR(20) NOT NULL, email VARCHAR(40) NOT NULL, joinAllyAt DATETIME DEFAULT NULL, password VARCHAR(64) NOT NULL, salonBan DATETIME DEFAULT NULL, viewMessage TINYINT(1) NOT NULL, viewReport TINYINT(1) NOT NULL, bitcoin NUMERIC(28, 5) NOT NULL, scientistProduction NUMERIC(28, 5) NOT NULL, onde INT DEFAULT NULL, industry INT DEFAULT NULL, lightShip INT DEFAULT NULL, heavyShip INT DEFAULT NULL, discipline INT DEFAULT NULL, hyperespace INT DEFAULT NULL, barge INT DEFAULT NULL, utility INT DEFAULT NULL, demography INT DEFAULT NULL, terraformation INT DEFAULT NULL, cargo INT DEFAULT NULL, recycleur INT DEFAULT NULL, armement INT DEFAULT NULL, missile INT DEFAULT NULL, laser INT DEFAULT NULL, plasma INT DEFAULT NULL, searchAt DATETIME DEFAULT NULL, search VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, connected TINYINT(1) NOT NULL, gameOver VARCHAR(255) DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, image_size INT DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_C2502824F85E0677 (username), UNIQUE INDEX UNIQ_C2502824E7927C74 (email), INDEX IDX_C25028241C6E3E76 (ally_id), INDEX IDX_C2502824FE19A1A8 (grade_id), UNIQUE INDEX UNIQ_C25028247616678F (rank_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sector (id INT AUTO_INCREMENT NOT NULL, galaxy_id INT DEFAULT NULL, position INT NOT NULL, INDEX IDX_4BA3D9E8B61FAB2 (galaxy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ally_proposal (id INT AUTO_INCREMENT NOT NULL, ally_id INT DEFAULT NULL, user_id INT DEFAULT NULL, proposalAt DATETIME NOT NULL, INDEX IDX_D6F70BC11C6E3E76 (ally_id), INDEX IDX_D6F70BC1A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE s_content (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, salon_id INT DEFAULT NULL, message VARCHAR(200) NOT NULL, sendAt DATETIME NOT NULL, INDEX IDX_C088E503A76ED395 (user_id), INDEX IDX_C088E5034C91BDE4 (salon_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rank (id INT AUTO_INCREMENT NOT NULL, point BIGINT NOT NULL, oldPoint BIGINT NOT NULL, position INT NOT NULL, oldPosition INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ally_pna (id INT AUTO_INCREMENT NOT NULL, ally_id INT DEFAULT NULL, allyTag VARCHAR(5) NOT NULL, signedAt DATETIME NOT NULL, accepted TINYINT(1) NOT NULL, INDEX IDX_B8D574011C6E3E76 (ally_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE salon (id INT AUTO_INCREMENT NOT NULL, ally_id INT DEFAULT NULL, name VARCHAR(20) NOT NULL, UNIQUE INDEX UNIQ_F268F4171C6E3E76 (ally_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE salon_user (salon_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_6B3C75974C91BDE4 (salon_id), INDEX IDX_6B3C7597A76ED395 (user_id), PRIMARY KEY(salon_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app_admins (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(20) NOT NULL, email VARCHAR(40) NOT NULL, password VARCHAR(64) NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_47E3788FF85E0677 (username), UNIQUE INDEX UNIQ_47E3788FE7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, sender VARCHAR(255) DEFAULT NULL, idSender INT NOT NULL, title VARCHAR(20) NOT NULL, content VARCHAR(500) NOT NULL, bitcoin BIGINT NOT NULL, sendAt DATETIME NOT NULL, INDEX IDX_B6BD307FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE report (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, title VARCHAR(20) NOT NULL, content VARCHAR(500) NOT NULL, sendAt DATETIME NOT NULL, INDEX IDX_C42F7784A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ally_war ADD CONSTRAINT FK_BEBC3F951C6E3E76 FOREIGN KEY (ally_id) REFERENCES ally (id)');
        $this->addSql('ALTER TABLE fleet ADD CONSTRAINT FK_A05E1E47A76ED395 FOREIGN KEY (user_id) REFERENCES app_users (id)');
        $this->addSql('ALTER TABLE fleet ADD CONSTRAINT FK_A05E1E47A25E9820 FOREIGN KEY (planet_id) REFERENCES planet (id)');
        $this->addSql('ALTER TABLE fleet ADD CONSTRAINT FK_A05E1E47DE95C867 FOREIGN KEY (sector_id) REFERENCES sector (id)');
        $this->addSql('ALTER TABLE ally_allied ADD CONSTRAINT FK_161AB9BB1C6E3E76 FOREIGN KEY (ally_id) REFERENCES ally (id)');
        $this->addSql('ALTER TABLE planet ADD CONSTRAINT FK_68136AA5A76ED395 FOREIGN KEY (user_id) REFERENCES app_users (id)');
        $this->addSql('ALTER TABLE planet ADD CONSTRAINT FK_68136AA5DE95C867 FOREIGN KEY (sector_id) REFERENCES sector (id)');
        $this->addSql('ALTER TABLE grade ADD CONSTRAINT FK_595AAE341C6E3E76 FOREIGN KEY (ally_id) REFERENCES ally (id)');
        $this->addSql('ALTER TABLE app_users ADD CONSTRAINT FK_C25028241C6E3E76 FOREIGN KEY (ally_id) REFERENCES ally (id)');
        $this->addSql('ALTER TABLE app_users ADD CONSTRAINT FK_C2502824FE19A1A8 FOREIGN KEY (grade_id) REFERENCES grade (id)');
        $this->addSql('ALTER TABLE app_users ADD CONSTRAINT FK_C25028247616678F FOREIGN KEY (rank_id) REFERENCES rank (id)');
        $this->addSql('ALTER TABLE sector ADD CONSTRAINT FK_4BA3D9E8B61FAB2 FOREIGN KEY (galaxy_id) REFERENCES galaxy (id)');
        $this->addSql('ALTER TABLE ally_proposal ADD CONSTRAINT FK_D6F70BC11C6E3E76 FOREIGN KEY (ally_id) REFERENCES ally (id)');
        $this->addSql('ALTER TABLE ally_proposal ADD CONSTRAINT FK_D6F70BC1A76ED395 FOREIGN KEY (user_id) REFERENCES app_users (id)');
        $this->addSql('ALTER TABLE s_content ADD CONSTRAINT FK_C088E503A76ED395 FOREIGN KEY (user_id) REFERENCES app_users (id)');
        $this->addSql('ALTER TABLE s_content ADD CONSTRAINT FK_C088E5034C91BDE4 FOREIGN KEY (salon_id) REFERENCES salon (id)');
        $this->addSql('ALTER TABLE ally_pna ADD CONSTRAINT FK_B8D574011C6E3E76 FOREIGN KEY (ally_id) REFERENCES ally (id)');
        $this->addSql('ALTER TABLE salon ADD CONSTRAINT FK_F268F4171C6E3E76 FOREIGN KEY (ally_id) REFERENCES ally (id)');
        $this->addSql('ALTER TABLE salon_user ADD CONSTRAINT FK_6B3C75974C91BDE4 FOREIGN KEY (salon_id) REFERENCES salon (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE salon_user ADD CONSTRAINT FK_6B3C7597A76ED395 FOREIGN KEY (user_id) REFERENCES app_users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FA76ED395 FOREIGN KEY (user_id) REFERENCES app_users (id)');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F7784A76ED395 FOREIGN KEY (user_id) REFERENCES app_users (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fleet DROP FOREIGN KEY FK_A05E1E47A25E9820');
        $this->addSql('ALTER TABLE sector DROP FOREIGN KEY FK_4BA3D9E8B61FAB2');
        $this->addSql('ALTER TABLE ally_war DROP FOREIGN KEY FK_BEBC3F951C6E3E76');
        $this->addSql('ALTER TABLE ally_allied DROP FOREIGN KEY FK_161AB9BB1C6E3E76');
        $this->addSql('ALTER TABLE grade DROP FOREIGN KEY FK_595AAE341C6E3E76');
        $this->addSql('ALTER TABLE app_users DROP FOREIGN KEY FK_C25028241C6E3E76');
        $this->addSql('ALTER TABLE ally_proposal DROP FOREIGN KEY FK_D6F70BC11C6E3E76');
        $this->addSql('ALTER TABLE ally_pna DROP FOREIGN KEY FK_B8D574011C6E3E76');
        $this->addSql('ALTER TABLE salon DROP FOREIGN KEY FK_F268F4171C6E3E76');
        $this->addSql('ALTER TABLE app_users DROP FOREIGN KEY FK_C2502824FE19A1A8');
        $this->addSql('ALTER TABLE fleet DROP FOREIGN KEY FK_A05E1E47A76ED395');
        $this->addSql('ALTER TABLE planet DROP FOREIGN KEY FK_68136AA5A76ED395');
        $this->addSql('ALTER TABLE ally_proposal DROP FOREIGN KEY FK_D6F70BC1A76ED395');
        $this->addSql('ALTER TABLE s_content DROP FOREIGN KEY FK_C088E503A76ED395');
        $this->addSql('ALTER TABLE salon_user DROP FOREIGN KEY FK_6B3C7597A76ED395');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FA76ED395');
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F7784A76ED395');
        $this->addSql('ALTER TABLE fleet DROP FOREIGN KEY FK_A05E1E47DE95C867');
        $this->addSql('ALTER TABLE planet DROP FOREIGN KEY FK_68136AA5DE95C867');
        $this->addSql('ALTER TABLE app_users DROP FOREIGN KEY FK_C25028247616678F');
        $this->addSql('ALTER TABLE s_content DROP FOREIGN KEY FK_C088E5034C91BDE4');
        $this->addSql('ALTER TABLE salon_user DROP FOREIGN KEY FK_6B3C75974C91BDE4');
        $this->addSql('DROP TABLE ally_war');
        $this->addSql('DROP TABLE fleet');
        $this->addSql('DROP TABLE ally_allied');
        $this->addSql('DROP TABLE planet');
        $this->addSql('DROP TABLE galaxy');
        $this->addSql('DROP TABLE ally');
        $this->addSql('DROP TABLE grade');
        $this->addSql('DROP TABLE app_users');
        $this->addSql('DROP TABLE sector');
        $this->addSql('DROP TABLE ally_proposal');
        $this->addSql('DROP TABLE s_content');
        $this->addSql('DROP TABLE rank');
        $this->addSql('DROP TABLE ally_pna');
        $this->addSql('DROP TABLE salon');
        $this->addSql('DROP TABLE salon_user');
        $this->addSql('DROP TABLE app_admins');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE report');
    }
}
