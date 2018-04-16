<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180416173105 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE grade DROP FOREIGN KEY FK_595AAE34A76ED395');
        $this->addSql('DROP INDEX UNIQ_595AAE34A76ED395 ON grade');
        $this->addSql('ALTER TABLE grade DROP user_id');
        $this->addSql('ALTER TABLE app_users ADD grade_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE app_users ADD CONSTRAINT FK_C2502824FE19A1A8 FOREIGN KEY (grade_id) REFERENCES grade (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C2502824FE19A1A8 ON app_users (grade_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE app_users DROP FOREIGN KEY FK_C2502824FE19A1A8');
        $this->addSql('DROP INDEX UNIQ_C2502824FE19A1A8 ON app_users');
        $this->addSql('ALTER TABLE app_users DROP grade_id');
        $this->addSql('ALTER TABLE grade ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE grade ADD CONSTRAINT FK_595AAE34A76ED395 FOREIGN KEY (user_id) REFERENCES app_users (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_595AAE34A76ED395 ON grade (user_id)');
    }
}
