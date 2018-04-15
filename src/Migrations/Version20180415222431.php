<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180415222431 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE orbite (id INT AUTO_INCREMENT NOT NULL, planet_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_6BD04981A25E9820 (planet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bitcoin (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, amount INT NOT NULL, UNIQUE INDEX UNIQ_D6C1D26EA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE planet (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, sector_id INT DEFAULT NULL, name VARCHAR(15) NOT NULL, position INT NOT NULL, land INT NOT NULL, sky INT NOT NULL, imageName VARCHAR(20) NOT NULL, INDEX IDX_68136AA5A76ED395 (user_id), INDEX IDX_68136AA5DE95C867 (sector_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE galaxy (id INT AUTO_INCREMENT NOT NULL, position INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app_users (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(20) NOT NULL, email VARCHAR(40) NOT NULL, password VARCHAR(64) NOT NULL, created_at DATETIME NOT NULL, image_name VARCHAR(255) DEFAULT NULL, image_size INT DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_C2502824F85E0677 (username), UNIQUE INDEX UNIQ_C2502824E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sector (id INT AUTO_INCREMENT NOT NULL, galaxy_id INT DEFAULT NULL, position INT NOT NULL, INDEX IDX_4BA3D9E8B61FAB2 (galaxy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE building (id INT AUTO_INCREMENT NOT NULL, planet_id INT DEFAULT NULL, INDEX IDX_E16F61D4A25E9820 (planet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE water (id INT AUTO_INCREMENT NOT NULL, planet_id INT DEFAULT NULL, amount INT NOT NULL, UNIQUE INDEX UNIQ_FB3314DAA25E9820 (planet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE human (id INT AUTO_INCREMENT NOT NULL, planet_id INT DEFAULT NULL, INDEX IDX_A562D5F5A25E9820 (planet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE niobium (id INT AUTO_INCREMENT NOT NULL, planet_id INT DEFAULT NULL, amount INT NOT NULL, UNIQUE INDEX UNIQ_C51E09B9A25E9820 (planet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app_admins (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(20) NOT NULL, email VARCHAR(40) NOT NULL, password VARCHAR(64) NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_47E3788FF85E0677 (username), UNIQUE INDEX UNIQ_47E3788FE7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE orbite ADD CONSTRAINT FK_6BD04981A25E9820 FOREIGN KEY (planet_id) REFERENCES planet (id)');
        $this->addSql('ALTER TABLE bitcoin ADD CONSTRAINT FK_D6C1D26EA76ED395 FOREIGN KEY (user_id) REFERENCES app_users (id)');
        $this->addSql('ALTER TABLE planet ADD CONSTRAINT FK_68136AA5A76ED395 FOREIGN KEY (user_id) REFERENCES app_users (id)');
        $this->addSql('ALTER TABLE planet ADD CONSTRAINT FK_68136AA5DE95C867 FOREIGN KEY (sector_id) REFERENCES sector (id)');
        $this->addSql('ALTER TABLE sector ADD CONSTRAINT FK_4BA3D9E8B61FAB2 FOREIGN KEY (galaxy_id) REFERENCES galaxy (id)');
        $this->addSql('ALTER TABLE building ADD CONSTRAINT FK_E16F61D4A25E9820 FOREIGN KEY (planet_id) REFERENCES planet (id)');
        $this->addSql('ALTER TABLE water ADD CONSTRAINT FK_FB3314DAA25E9820 FOREIGN KEY (planet_id) REFERENCES planet (id)');
        $this->addSql('ALTER TABLE human ADD CONSTRAINT FK_A562D5F5A25E9820 FOREIGN KEY (planet_id) REFERENCES planet (id)');
        $this->addSql('ALTER TABLE niobium ADD CONSTRAINT FK_C51E09B9A25E9820 FOREIGN KEY (planet_id) REFERENCES planet (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE orbite DROP FOREIGN KEY FK_6BD04981A25E9820');
        $this->addSql('ALTER TABLE building DROP FOREIGN KEY FK_E16F61D4A25E9820');
        $this->addSql('ALTER TABLE water DROP FOREIGN KEY FK_FB3314DAA25E9820');
        $this->addSql('ALTER TABLE human DROP FOREIGN KEY FK_A562D5F5A25E9820');
        $this->addSql('ALTER TABLE niobium DROP FOREIGN KEY FK_C51E09B9A25E9820');
        $this->addSql('ALTER TABLE sector DROP FOREIGN KEY FK_4BA3D9E8B61FAB2');
        $this->addSql('ALTER TABLE bitcoin DROP FOREIGN KEY FK_D6C1D26EA76ED395');
        $this->addSql('ALTER TABLE planet DROP FOREIGN KEY FK_68136AA5A76ED395');
        $this->addSql('ALTER TABLE planet DROP FOREIGN KEY FK_68136AA5DE95C867');
        $this->addSql('DROP TABLE orbite');
        $this->addSql('DROP TABLE bitcoin');
        $this->addSql('DROP TABLE planet');
        $this->addSql('DROP TABLE galaxy');
        $this->addSql('DROP TABLE app_users');
        $this->addSql('DROP TABLE sector');
        $this->addSql('DROP TABLE building');
        $this->addSql('DROP TABLE water');
        $this->addSql('DROP TABLE human');
        $this->addSql('DROP TABLE niobium');
        $this->addSql('DROP TABLE app_admins');
    }
}
