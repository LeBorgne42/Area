<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180531143810 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE commander (id INT AUTO_INCREMENT NOT NULL, capture TINYINT(1) NOT NULL, name VARCHAR(25) NOT NULL, level INT NOT NULL, speed INT NOT NULL, shield INT NOT NULL, armor INT NOT NULL, laser INT NOT NULL, missile INT NOT NULL, plasma INT NOT NULL, niobium INT NOT NULL, water INT NOT NULL, bitcoin INT NOT NULL, worker INT NOT NULL, soldier INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE fleet ADD commander_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE fleet ADD CONSTRAINT FK_A05E1E473349A583 FOREIGN KEY (commander_id) REFERENCES commander (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A05E1E473349A583 ON fleet (commander_id)');
        $this->addSql('ALTER TABLE planet ADD commander_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE planet ADD CONSTRAINT FK_68136AA53349A583 FOREIGN KEY (commander_id) REFERENCES commander (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_68136AA53349A583 ON planet (commander_id)');
        $this->addSql('ALTER TABLE ally ADD politic VARCHAR(25) NOT NULL');
        $this->addSql('ALTER TABLE app_users ADD commander_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE app_users ADD CONSTRAINT FK_C25028243349A583 FOREIGN KEY (commander_id) REFERENCES commander (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C25028243349A583 ON app_users (commander_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fleet DROP FOREIGN KEY FK_A05E1E473349A583');
        $this->addSql('ALTER TABLE planet DROP FOREIGN KEY FK_68136AA53349A583');
        $this->addSql('ALTER TABLE app_users DROP FOREIGN KEY FK_C25028243349A583');
        $this->addSql('DROP TABLE commander');
        $this->addSql('ALTER TABLE ally DROP politic');
        $this->addSql('DROP INDEX UNIQ_C25028243349A583 ON app_users');
        $this->addSql('ALTER TABLE app_users DROP commander_id');
        $this->addSql('DROP INDEX UNIQ_A05E1E473349A583 ON fleet');
        $this->addSql('ALTER TABLE fleet DROP commander_id');
        $this->addSql('DROP INDEX UNIQ_68136AA53349A583 ON planet');
        $this->addSql('ALTER TABLE planet DROP commander_id');
    }
}
