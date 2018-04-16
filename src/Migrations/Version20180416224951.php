<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180416224951 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE orbite (id INT AUTO_INCREMENT NOT NULL, planet_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_6BD04981A25E9820 (planet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fleet (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE soldier (id INT AUTO_INCREMENT NOT NULL, planet_id INT DEFAULT NULL, fleet_id INT DEFAULT NULL, amount BIGINT NOT NULL, life INT NOT NULL, UNIQUE INDEX UNIQ_B04F2D02A25E9820 (planet_id), UNIQUE INDEX UNIQ_B04F2D024B061DF9 (fleet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE planet (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, sector_id INT DEFAULT NULL, name VARCHAR(15) DEFAULT NULL, niobium BIGINT NOT NULL, water BIGINT NOT NULL, position INT NOT NULL, land INT DEFAULT NULL, sky INT DEFAULT NULL, empty TINYINT(1) NOT NULL, imageName VARCHAR(20) DEFAULT NULL, INDEX IDX_68136AA5A76ED395 (user_id), INDEX IDX_68136AA5DE95C867 (sector_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE galaxy (id INT AUTO_INCREMENT NOT NULL, position INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ally (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(15) NOT NULL, sigle VARCHAR(5) NOT NULL, slogan VARCHAR(30) NOT NULL, bitcoin BIGINT NOT NULL, taxe INT NOT NULL, pna LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', allied LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', war LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', image_name VARCHAR(255) DEFAULT NULL, image_size INT DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_382900D5E237E06 (name), UNIQUE INDEX UNIQ_382900D8776B952 (sigle), UNIQUE INDEX UNIQ_382900D988768C9 (slogan), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE grade (id INT AUTO_INCREMENT NOT NULL, ally_id INT DEFAULT NULL, placement INT NOT NULL, name VARCHAR(20) NOT NULL, canRecruit TINYINT(1) NOT NULL, canKick TINYINT(1) NOT NULL, canWar TINYINT(1) NOT NULL, canPeace TINYINT(1) NOT NULL, INDEX IDX_595AAE341C6E3E76 (ally_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app_users (id INT AUTO_INCREMENT NOT NULL, ally_id INT DEFAULT NULL, grade_id INT DEFAULT NULL, rank_id INT DEFAULT NULL, username VARCHAR(20) NOT NULL, email VARCHAR(40) NOT NULL, joinAllyAt DATETIME NOT NULL, password VARCHAR(64) NOT NULL, bitcoin BIGINT NOT NULL, created_at DATETIME NOT NULL, connected TINYINT(1) NOT NULL, image_name VARCHAR(255) DEFAULT NULL, image_size INT DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_C2502824F85E0677 (username), UNIQUE INDEX UNIQ_C2502824E7927C74 (email), INDEX IDX_C25028241C6E3E76 (ally_id), UNIQUE INDEX UNIQ_C2502824FE19A1A8 (grade_id), UNIQUE INDEX UNIQ_C25028247616678F (rank_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sector (id INT AUTO_INCREMENT NOT NULL, galaxy_id INT DEFAULT NULL, position INT NOT NULL, INDEX IDX_4BA3D9E8B61FAB2 (galaxy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE scientist (id INT AUTO_INCREMENT NOT NULL, planet_id INT DEFAULT NULL, fleet_id INT DEFAULT NULL, amount BIGINT NOT NULL, life INT NOT NULL, UNIQUE INDEX UNIQ_E1774A61A25E9820 (planet_id), UNIQUE INDEX UNIQ_E1774A614B061DF9 (fleet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE building (id INT AUTO_INCREMENT NOT NULL, planet_id INT DEFAULT NULL, INDEX IDX_E16F61D4A25E9820 (planet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE worker (id INT AUTO_INCREMENT NOT NULL, planet_id INT DEFAULT NULL, fleet_id INT DEFAULT NULL, amount BIGINT NOT NULL, life INT NOT NULL, UNIQUE INDEX UNIQ_9FB2BF62A25E9820 (planet_id), UNIQUE INDEX UNIQ_9FB2BF624B061DF9 (fleet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rank (id INT AUTO_INCREMENT NOT NULL, point BIGINT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app_admins (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(20) NOT NULL, email VARCHAR(40) NOT NULL, password VARCHAR(64) NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_47E3788FF85E0677 (username), UNIQUE INDEX UNIQ_47E3788FE7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE orbite ADD CONSTRAINT FK_6BD04981A25E9820 FOREIGN KEY (planet_id) REFERENCES planet (id)');
        $this->addSql('ALTER TABLE soldier ADD CONSTRAINT FK_B04F2D02A25E9820 FOREIGN KEY (planet_id) REFERENCES planet (id)');
        $this->addSql('ALTER TABLE soldier ADD CONSTRAINT FK_B04F2D024B061DF9 FOREIGN KEY (fleet_id) REFERENCES fleet (id)');
        $this->addSql('ALTER TABLE planet ADD CONSTRAINT FK_68136AA5A76ED395 FOREIGN KEY (user_id) REFERENCES app_users (id)');
        $this->addSql('ALTER TABLE planet ADD CONSTRAINT FK_68136AA5DE95C867 FOREIGN KEY (sector_id) REFERENCES sector (id)');
        $this->addSql('ALTER TABLE grade ADD CONSTRAINT FK_595AAE341C6E3E76 FOREIGN KEY (ally_id) REFERENCES ally (id)');
        $this->addSql('ALTER TABLE app_users ADD CONSTRAINT FK_C25028241C6E3E76 FOREIGN KEY (ally_id) REFERENCES ally (id)');
        $this->addSql('ALTER TABLE app_users ADD CONSTRAINT FK_C2502824FE19A1A8 FOREIGN KEY (grade_id) REFERENCES grade (id)');
        $this->addSql('ALTER TABLE app_users ADD CONSTRAINT FK_C25028247616678F FOREIGN KEY (rank_id) REFERENCES rank (id)');
        $this->addSql('ALTER TABLE sector ADD CONSTRAINT FK_4BA3D9E8B61FAB2 FOREIGN KEY (galaxy_id) REFERENCES galaxy (id)');
        $this->addSql('ALTER TABLE scientist ADD CONSTRAINT FK_E1774A61A25E9820 FOREIGN KEY (planet_id) REFERENCES planet (id)');
        $this->addSql('ALTER TABLE scientist ADD CONSTRAINT FK_E1774A614B061DF9 FOREIGN KEY (fleet_id) REFERENCES fleet (id)');
        $this->addSql('ALTER TABLE building ADD CONSTRAINT FK_E16F61D4A25E9820 FOREIGN KEY (planet_id) REFERENCES planet (id)');
        $this->addSql('ALTER TABLE worker ADD CONSTRAINT FK_9FB2BF62A25E9820 FOREIGN KEY (planet_id) REFERENCES planet (id)');
        $this->addSql('ALTER TABLE worker ADD CONSTRAINT FK_9FB2BF624B061DF9 FOREIGN KEY (fleet_id) REFERENCES fleet (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE soldier DROP FOREIGN KEY FK_B04F2D024B061DF9');
        $this->addSql('ALTER TABLE scientist DROP FOREIGN KEY FK_E1774A614B061DF9');
        $this->addSql('ALTER TABLE worker DROP FOREIGN KEY FK_9FB2BF624B061DF9');
        $this->addSql('ALTER TABLE orbite DROP FOREIGN KEY FK_6BD04981A25E9820');
        $this->addSql('ALTER TABLE soldier DROP FOREIGN KEY FK_B04F2D02A25E9820');
        $this->addSql('ALTER TABLE scientist DROP FOREIGN KEY FK_E1774A61A25E9820');
        $this->addSql('ALTER TABLE building DROP FOREIGN KEY FK_E16F61D4A25E9820');
        $this->addSql('ALTER TABLE worker DROP FOREIGN KEY FK_9FB2BF62A25E9820');
        $this->addSql('ALTER TABLE sector DROP FOREIGN KEY FK_4BA3D9E8B61FAB2');
        $this->addSql('ALTER TABLE grade DROP FOREIGN KEY FK_595AAE341C6E3E76');
        $this->addSql('ALTER TABLE app_users DROP FOREIGN KEY FK_C25028241C6E3E76');
        $this->addSql('ALTER TABLE app_users DROP FOREIGN KEY FK_C2502824FE19A1A8');
        $this->addSql('ALTER TABLE planet DROP FOREIGN KEY FK_68136AA5A76ED395');
        $this->addSql('ALTER TABLE planet DROP FOREIGN KEY FK_68136AA5DE95C867');
        $this->addSql('ALTER TABLE app_users DROP FOREIGN KEY FK_C25028247616678F');
        $this->addSql('DROP TABLE orbite');
        $this->addSql('DROP TABLE fleet');
        $this->addSql('DROP TABLE soldier');
        $this->addSql('DROP TABLE planet');
        $this->addSql('DROP TABLE galaxy');
        $this->addSql('DROP TABLE ally');
        $this->addSql('DROP TABLE grade');
        $this->addSql('DROP TABLE app_users');
        $this->addSql('DROP TABLE sector');
        $this->addSql('DROP TABLE scientist');
        $this->addSql('DROP TABLE building');
        $this->addSql('DROP TABLE worker');
        $this->addSql('DROP TABLE rank');
        $this->addSql('DROP TABLE app_admins');
    }
}
