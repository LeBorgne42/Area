<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190227152350 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE construct (id INT AUTO_INCREMENT NOT NULL, planet_id INT DEFAULT NULL, construct VARCHAR(255) NOT NULL, constructTime INT NOT NULL, niobium NUMERIC(28, 5) NOT NULL, water NUMERIC(28, 5) NOT NULL, ground INT NOT NULL, sky INT NOT NULL, INDEX IDX_5AF55AEDA25E9820 (planet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE construct ADD CONSTRAINT FK_5AF55AEDA25E9820 FOREIGN KEY (planet_id) REFERENCES planet (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE construct');
    }
}
