<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180425042541 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fleet DROP FOREIGN KEY FK_A05E1E47C256317D');
        $this->addSql('ALTER TABLE planet DROP FOREIGN KEY FK_68136AA5C256317D');
        $this->addSql('ALTER TABLE ship DROP FOREIGN KEY FK_FA30EB24222D237A');
        $this->addSql('ALTER TABLE ship DROP FOREIGN KEY FK_FA30EB24D726A9F2');
        $this->addSql('ALTER TABLE ship DROP FOREIGN KEY FK_FA30EB246D3EAA45');
        $this->addSql('ALTER TABLE ship DROP FOREIGN KEY FK_FA30EB24A7DC5C81');
        $this->addSql('ALTER TABLE ship DROP FOREIGN KEY FK_FA30EB24D7B82D29');
        $this->addSql('ALTER TABLE ship DROP FOREIGN KEY FK_FA30EB24F1B7D1A2');
        $this->addSql('DROP TABLE ship');
        $this->addSql('DROP TABLE y_barge');
        $this->addSql('DROP TABLE y_colonizer');
        $this->addSql('DROP TABLE y_fregate');
        $this->addSql('DROP TABLE y_hunter');
        $this->addSql('DROP TABLE y_recycleur');
        $this->addSql('DROP TABLE y_sonde');
        $this->addSql('DROP INDEX UNIQ_A05E1E47C256317D ON fleet');
        $this->addSql('ALTER TABLE fleet ADD attack TINYINT(1) NOT NULL, ADD sonde BIGINT DEFAULT NULL, ADD recycleur INT DEFAULT NULL, ADD barge INT DEFAULT NULL, ADD hunter BIGINT DEFAULT NULL, ADD fregate BIGINT DEFAULT NULL, CHANGE ship_id colonizer INT DEFAULT NULL');
        $this->addSql('DROP INDEX UNIQ_68136AA5C256317D ON planet');
        $this->addSql('ALTER TABLE planet ADD sonde BIGINT DEFAULT NULL, ADD recycleur INT DEFAULT NULL, ADD barge INT DEFAULT NULL, ADD hunter BIGINT DEFAULT NULL, ADD fregate BIGINT DEFAULT NULL, CHANGE ship_id colonizer INT DEFAULT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ship (id INT AUTO_INCREMENT NOT NULL, fleet_id INT DEFAULT NULL, planet_id INT DEFAULT NULL, sonde_id INT DEFAULT NULL, colonizer_id INT DEFAULT NULL, recycleur_id INT DEFAULT NULL, barge_id INT DEFAULT NULL, hunter_id INT DEFAULT NULL, fregate_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_FA30EB244B061DF9 (fleet_id), UNIQUE INDEX UNIQ_FA30EB24A25E9820 (planet_id), UNIQUE INDEX UNIQ_FA30EB24F1B7D1A2 (sonde_id), UNIQUE INDEX UNIQ_FA30EB24D726A9F2 (colonizer_id), UNIQUE INDEX UNIQ_FA30EB24D7B82D29 (recycleur_id), UNIQUE INDEX UNIQ_FA30EB24222D237A (barge_id), UNIQUE INDEX UNIQ_FA30EB24A7DC5C81 (hunter_id), UNIQUE INDEX UNIQ_FA30EB246D3EAA45 (fregate_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE y_barge (id INT AUTO_INCREMENT NOT NULL, niobium INT NOT NULL, water INT NOT NULL, bitcoin INT NOT NULL, amount BIGINT NOT NULL, finishAt DATETIME DEFAULT NULL, constructTime BIGINT NOT NULL, signature INT NOT NULL, armor INT NOT NULL, shield INT NOT NULL, missile INT NOT NULL, laser INT NOT NULL, plasma INT NOT NULL, cargo INT NOT NULL, speed INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE y_colonizer (id INT AUTO_INCREMENT NOT NULL, niobium INT NOT NULL, water INT NOT NULL, bitcoin INT NOT NULL, amount BIGINT NOT NULL, finishAt DATETIME DEFAULT NULL, constructTime BIGINT NOT NULL, signature INT NOT NULL, armor INT NOT NULL, shield INT NOT NULL, missile INT NOT NULL, laser INT NOT NULL, plasma INT NOT NULL, cargo INT NOT NULL, speed INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE y_fregate (id INT AUTO_INCREMENT NOT NULL, niobium INT NOT NULL, water INT NOT NULL, amount BIGINT NOT NULL, finishAt DATETIME DEFAULT NULL, constructTime BIGINT NOT NULL, signature INT NOT NULL, armor INT NOT NULL, shield INT NOT NULL, missile INT NOT NULL, laser INT NOT NULL, plasma INT NOT NULL, cargo INT NOT NULL, speed INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE y_hunter (id INT AUTO_INCREMENT NOT NULL, niobium INT NOT NULL, water INT NOT NULL, amount BIGINT NOT NULL, finishAt DATETIME DEFAULT NULL, constructTime BIGINT NOT NULL, signature INT NOT NULL, armor INT NOT NULL, shield INT NOT NULL, missile INT NOT NULL, laser INT NOT NULL, plasma INT NOT NULL, cargo INT NOT NULL, speed INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE y_recycleur (id INT AUTO_INCREMENT NOT NULL, niobium INT NOT NULL, water INT NOT NULL, bitcoin INT NOT NULL, amount BIGINT NOT NULL, finishAt DATETIME DEFAULT NULL, constructTime BIGINT NOT NULL, signature INT NOT NULL, armor INT NOT NULL, shield INT NOT NULL, missile INT NOT NULL, laser INT NOT NULL, plasma INT NOT NULL, cargo INT NOT NULL, speed INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE y_sonde (id INT AUTO_INCREMENT NOT NULL, niobium INT NOT NULL, water INT NOT NULL, amount BIGINT NOT NULL, finishAt DATETIME DEFAULT NULL, constructTime BIGINT NOT NULL, signature INT NOT NULL, armor INT NOT NULL, shield INT NOT NULL, missile INT NOT NULL, laser INT NOT NULL, plasma INT NOT NULL, cargo INT NOT NULL, speed INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ship ADD CONSTRAINT FK_FA30EB24222D237A FOREIGN KEY (barge_id) REFERENCES y_barge (id)');
        $this->addSql('ALTER TABLE ship ADD CONSTRAINT FK_FA30EB244B061DF9 FOREIGN KEY (fleet_id) REFERENCES fleet (id)');
        $this->addSql('ALTER TABLE ship ADD CONSTRAINT FK_FA30EB246D3EAA45 FOREIGN KEY (fregate_id) REFERENCES y_fregate (id)');
        $this->addSql('ALTER TABLE ship ADD CONSTRAINT FK_FA30EB24A25E9820 FOREIGN KEY (planet_id) REFERENCES planet (id)');
        $this->addSql('ALTER TABLE ship ADD CONSTRAINT FK_FA30EB24A7DC5C81 FOREIGN KEY (hunter_id) REFERENCES y_hunter (id)');
        $this->addSql('ALTER TABLE ship ADD CONSTRAINT FK_FA30EB24D726A9F2 FOREIGN KEY (colonizer_id) REFERENCES y_colonizer (id)');
        $this->addSql('ALTER TABLE ship ADD CONSTRAINT FK_FA30EB24D7B82D29 FOREIGN KEY (recycleur_id) REFERENCES y_recycleur (id)');
        $this->addSql('ALTER TABLE ship ADD CONSTRAINT FK_FA30EB24F1B7D1A2 FOREIGN KEY (sonde_id) REFERENCES y_sonde (id)');
        $this->addSql('ALTER TABLE fleet ADD ship_id INT DEFAULT NULL, DROP attack, DROP sonde, DROP colonizer, DROP recycleur, DROP barge, DROP hunter, DROP fregate');
        $this->addSql('ALTER TABLE fleet ADD CONSTRAINT FK_A05E1E47C256317D FOREIGN KEY (ship_id) REFERENCES ship (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A05E1E47C256317D ON fleet (ship_id)');
        $this->addSql('ALTER TABLE planet ADD ship_id INT DEFAULT NULL, DROP sonde, DROP colonizer, DROP recycleur, DROP barge, DROP hunter, DROP fregate');
        $this->addSql('ALTER TABLE planet ADD CONSTRAINT FK_68136AA5C256317D FOREIGN KEY (ship_id) REFERENCES ship (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_68136AA5C256317D ON planet (ship_id)');
    }
}
