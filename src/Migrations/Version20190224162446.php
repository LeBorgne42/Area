<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190224162446 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE quest_user (quest_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_83997ABB209E9EF4 (quest_id), INDEX IDX_83997ABBA76ED395 (user_id), PRIMARY KEY(quest_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE quest_user ADD CONSTRAINT FK_83997ABB209E9EF4 FOREIGN KEY (quest_id) REFERENCES quest (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE quest_user ADD CONSTRAINT FK_83997ABBA76ED395 FOREIGN KEY (user_id) REFERENCES app_users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE app_users DROP FOREIGN KEY FK_C2502824209E9EF4');
        $this->addSql('DROP INDEX IDX_C2502824209E9EF4 ON app_users');
        $this->addSql('ALTER TABLE app_users DROP quest_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE quest_user');
        $this->addSql('ALTER TABLE app_users ADD quest_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE app_users ADD CONSTRAINT FK_C2502824209E9EF4 FOREIGN KEY (quest_id) REFERENCES quest (id)');
        $this->addSql('CREATE INDEX IDX_C2502824209E9EF4 ON app_users (quest_id)');
    }
}
