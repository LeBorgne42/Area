<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180606110504 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ally_peace (id INT AUTO_INCREMENT NOT NULL, ally_id INT DEFAULT NULL, allyTag VARCHAR(5) NOT NULL, signedAt DATETIME NOT NULL, accepted TINYINT(1) NOT NULL, type TINYINT(1) NOT NULL, planet INT DEFAULT NULL, taxe INT DEFAULT NULL, pdg INT DEFAULT NULL, INDEX IDX_ABA304FC1C6E3E76 (ally_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ally_peace ADD CONSTRAINT FK_ABA304FC1C6E3E76 FOREIGN KEY (ally_id) REFERENCES ally (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE ally_peace');
    }
}
