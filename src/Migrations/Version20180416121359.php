<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180416121359 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE soldier (id INT AUTO_INCREMENT NOT NULL, planet_id INT DEFAULT NULL, amount BIGINT NOT NULL, life INT NOT NULL, INDEX IDX_B04F2D02A25E9820 (planet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE scientist (id INT AUTO_INCREMENT NOT NULL, planet_id INT DEFAULT NULL, amount BIGINT NOT NULL, life INT NOT NULL, INDEX IDX_E1774A61A25E9820 (planet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE worker (id INT AUTO_INCREMENT NOT NULL, planet_id INT DEFAULT NULL, amount BIGINT NOT NULL, life INT NOT NULL, INDEX IDX_9FB2BF62A25E9820 (planet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE soldier ADD CONSTRAINT FK_B04F2D02A25E9820 FOREIGN KEY (planet_id) REFERENCES planet (id)');
        $this->addSql('ALTER TABLE scientist ADD CONSTRAINT FK_E1774A61A25E9820 FOREIGN KEY (planet_id) REFERENCES planet (id)');
        $this->addSql('ALTER TABLE worker ADD CONSTRAINT FK_9FB2BF62A25E9820 FOREIGN KEY (planet_id) REFERENCES planet (id)');
        $this->addSql('DROP TABLE human');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE human (id INT AUTO_INCREMENT NOT NULL, planet_id INT DEFAULT NULL, INDEX IDX_A562D5F5A25E9820 (planet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE human ADD CONSTRAINT FK_A562D5F5A25E9820 FOREIGN KEY (planet_id) REFERENCES planet (id)');
        $this->addSql('DROP TABLE soldier');
        $this->addSql('DROP TABLE scientist');
        $this->addSql('DROP TABLE worker');
    }
}
