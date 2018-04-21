<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180421011004 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE z_recycleur (id INT AUTO_INCREMENT NOT NULL, bitcoin INT NOT NULL, level TINYINT(1) NOT NULL, finishAt DATETIME DEFAULT NULL, constructTime BIGINT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE research ADD cargo_id INT DEFAULT NULL, ADD recycleur_id INT DEFAULT NULL, ADD armement_id INT DEFAULT NULL, ADD missile_id INT DEFAULT NULL, ADD laser_id INT DEFAULT NULL, ADD plasma_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE research ADD CONSTRAINT FK_57EB50C2813AC380 FOREIGN KEY (cargo_id) REFERENCES z_cargo (id)');
        $this->addSql('ALTER TABLE research ADD CONSTRAINT FK_57EB50C2D7B82D29 FOREIGN KEY (recycleur_id) REFERENCES z_recycleur (id)');
        $this->addSql('ALTER TABLE research ADD CONSTRAINT FK_57EB50C2D4413494 FOREIGN KEY (armement_id) REFERENCES z_armement (id)');
        $this->addSql('ALTER TABLE research ADD CONSTRAINT FK_57EB50C22ECD5341 FOREIGN KEY (missile_id) REFERENCES z_missile (id)');
        $this->addSql('ALTER TABLE research ADD CONSTRAINT FK_57EB50C24BF5DEAB FOREIGN KEY (laser_id) REFERENCES z_laser (id)');
        $this->addSql('ALTER TABLE research ADD CONSTRAINT FK_57EB50C24AA16509 FOREIGN KEY (plasma_id) REFERENCES z_plasma (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_57EB50C2813AC380 ON research (cargo_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_57EB50C2D7B82D29 ON research (recycleur_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_57EB50C2D4413494 ON research (armement_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_57EB50C22ECD5341 ON research (missile_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_57EB50C24BF5DEAB ON research (laser_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_57EB50C24AA16509 ON research (plasma_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE research DROP FOREIGN KEY FK_57EB50C2D7B82D29');
        $this->addSql('DROP TABLE z_recycleur');
        $this->addSql('ALTER TABLE research DROP FOREIGN KEY FK_57EB50C2813AC380');
        $this->addSql('ALTER TABLE research DROP FOREIGN KEY FK_57EB50C2D4413494');
        $this->addSql('ALTER TABLE research DROP FOREIGN KEY FK_57EB50C22ECD5341');
        $this->addSql('ALTER TABLE research DROP FOREIGN KEY FK_57EB50C24BF5DEAB');
        $this->addSql('ALTER TABLE research DROP FOREIGN KEY FK_57EB50C24AA16509');
        $this->addSql('DROP INDEX UNIQ_57EB50C2813AC380 ON research');
        $this->addSql('DROP INDEX UNIQ_57EB50C2D7B82D29 ON research');
        $this->addSql('DROP INDEX UNIQ_57EB50C2D4413494 ON research');
        $this->addSql('DROP INDEX UNIQ_57EB50C22ECD5341 ON research');
        $this->addSql('DROP INDEX UNIQ_57EB50C24BF5DEAB ON research');
        $this->addSql('DROP INDEX UNIQ_57EB50C24AA16509 ON research');
        $this->addSql('ALTER TABLE research DROP cargo_id, DROP recycleur_id, DROP armement_id, DROP missile_id, DROP laser_id, DROP plasma_id');
    }
}
