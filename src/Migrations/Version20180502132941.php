<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180502132941 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, sender INT NOT NULL, title VARCHAR(20) NOT NULL, content VARCHAR(500) NOT NULL, bitcoin BIGINT NOT NULL, sendAt DATETIME NOT NULL, INDEX IDX_B6BD307FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FA76ED395 FOREIGN KEY (user_id) REFERENCES app_users (id)');
        $this->addSql('DROP TABLE u_message');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE u_message (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, sender INT NOT NULL, title VARCHAR(20) NOT NULL COLLATE utf8mb4_unicode_ci, content VARCHAR(500) NOT NULL COLLATE utf8mb4_unicode_ci, bitcoin BIGINT NOT NULL, sendAt DATETIME NOT NULL, INDEX IDX_FBEA9C5FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE u_message ADD CONSTRAINT FK_FBEA9C5FA76ED395 FOREIGN KEY (user_id) REFERENCES app_users (id)');
        $this->addSql('DROP TABLE message');
    }
}
