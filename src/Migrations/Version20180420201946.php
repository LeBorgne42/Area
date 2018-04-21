<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180420201946 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE y_colonizer (id INT AUTO_INCREMENT NOT NULL, niobium INT NOT NULL, water INT NOT NULL, bitcoin INT NOT NULL, amount BIGINT NOT NULL, finishAt DATETIME DEFAULT NULL, constructTime BIGINT NOT NULL, armor INT NOT NULL, speed INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ship (id INT AUTO_INCREMENT NOT NULL, fleet_id INT DEFAULT NULL, orbite_id INT DEFAULT NULL, colonizer_id INT DEFAULT NULL, INDEX IDX_FA30EB244B061DF9 (fleet_id), INDEX IDX_FA30EB2421DE9274 (orbite_id), UNIQUE INDEX UNIQ_FA30EB24D726A9F2 (colonizer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ship ADD CONSTRAINT FK_FA30EB244B061DF9 FOREIGN KEY (fleet_id) REFERENCES fleet (id)');
        $this->addSql('ALTER TABLE ship ADD CONSTRAINT FK_FA30EB2421DE9274 FOREIGN KEY (orbite_id) REFERENCES orbite (id)');
        $this->addSql('ALTER TABLE ship ADD CONSTRAINT FK_FA30EB24D726A9F2 FOREIGN KEY (colonizer_id) REFERENCES y_colonizer (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ship DROP FOREIGN KEY FK_FA30EB24D726A9F2');
        $this->addSql('DROP TABLE y_colonizer');
        $this->addSql('DROP TABLE ship');
    }
}
