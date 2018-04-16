<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180416140439 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ally (id INT AUTO_INCREMENT NOT NULL, bitcoin BIGINT NOT NULL, image_name VARCHAR(255) DEFAULT NULL, image_size INT DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE app_users ADD ally_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE app_users ADD CONSTRAINT FK_C25028241C6E3E76 FOREIGN KEY (ally_id) REFERENCES ally (id)');
        $this->addSql('CREATE INDEX IDX_C25028241C6E3E76 ON app_users (ally_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE app_users DROP FOREIGN KEY FK_C25028241C6E3E76');
        $this->addSql('DROP TABLE ally');
        $this->addSql('DROP INDEX IDX_C25028241C6E3E76 ON app_users');
        $this->addSql('ALTER TABLE app_users DROP ally_id');
    }
}
