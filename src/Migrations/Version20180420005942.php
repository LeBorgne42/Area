<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180420005942 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ally_allied (id INT AUTO_INCREMENT NOT NULL, ally_id INT DEFAULT NULL, allyTag VARCHAR(5) NOT NULL, signedAt DATETIME NOT NULL, accepted TINYINT(1) NOT NULL, INDEX IDX_161AB9BB1C6E3E76 (ally_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ally_allied ADD CONSTRAINT FK_161AB9BB1C6E3E76 FOREIGN KEY (ally_id) REFERENCES ally (id)');
        $this->addSql('DROP TABLE allied');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE allied (id INT AUTO_INCREMENT NOT NULL, ally_id INT DEFAULT NULL, allyTag VARCHAR(5) NOT NULL COLLATE utf8mb4_unicode_ci, signedAt DATETIME NOT NULL, accepted TINYINT(1) NOT NULL, INDEX IDX_4A0B65A71C6E3E76 (ally_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE allied ADD CONSTRAINT FK_4A0B65A71C6E3E76 FOREIGN KEY (ally_id) REFERENCES ally (id)');
        $this->addSql('DROP TABLE ally_allied');
    }
}
