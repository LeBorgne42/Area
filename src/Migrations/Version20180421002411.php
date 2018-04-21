<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180421002411 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE y_fregate (id INT AUTO_INCREMENT NOT NULL, niobium INT NOT NULL, water INT NOT NULL, cost INT NOT NULL, amount BIGINT NOT NULL, finishAt DATETIME DEFAULT NULL, constructTime BIGINT NOT NULL, signature INT NOT NULL, armor INT NOT NULL, shield INT NOT NULL, missile INT NOT NULL, laser INT NOT NULL, plasma INT NOT NULL, cargo INT NOT NULL, speed INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE y_hunter (id INT AUTO_INCREMENT NOT NULL, niobium INT NOT NULL, water INT NOT NULL, cost INT NOT NULL, amount BIGINT NOT NULL, finishAt DATETIME DEFAULT NULL, constructTime BIGINT NOT NULL, signature INT NOT NULL, armor INT NOT NULL, shield INT NOT NULL, missile INT NOT NULL, laser INT NOT NULL, plasma INT NOT NULL, cargo INT NOT NULL, speed INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE y_colonizer ADD signature INT NOT NULL, ADD shield INT NOT NULL, ADD missile INT NOT NULL, ADD laser INT NOT NULL, ADD plasma INT NOT NULL, ADD cargo INT NOT NULL');
        $this->addSql('ALTER TABLE ship ADD hunter_id INT DEFAULT NULL, ADD fregate_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ship ADD CONSTRAINT FK_FA30EB24A7DC5C81 FOREIGN KEY (hunter_id) REFERENCES y_hunter (id)');
        $this->addSql('ALTER TABLE ship ADD CONSTRAINT FK_FA30EB246D3EAA45 FOREIGN KEY (fregate_id) REFERENCES y_fregate (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FA30EB24A7DC5C81 ON ship (hunter_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FA30EB246D3EAA45 ON ship (fregate_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ship DROP FOREIGN KEY FK_FA30EB246D3EAA45');
        $this->addSql('ALTER TABLE ship DROP FOREIGN KEY FK_FA30EB24A7DC5C81');
        $this->addSql('DROP TABLE y_fregate');
        $this->addSql('DROP TABLE y_hunter');
        $this->addSql('DROP INDEX UNIQ_FA30EB24A7DC5C81 ON ship');
        $this->addSql('DROP INDEX UNIQ_FA30EB246D3EAA45 ON ship');
        $this->addSql('ALTER TABLE ship DROP hunter_id, DROP fregate_id');
        $this->addSql('ALTER TABLE y_colonizer DROP signature, DROP shield, DROP missile, DROP laser, DROP plasma, DROP cargo');
    }
}
