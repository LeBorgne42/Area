<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181206170942 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE fleet_list (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, name VARCHAR(20) NOT NULL, priority INT NOT NULL, INDEX IDX_8BDD93A5A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE fleet_list ADD CONSTRAINT FK_8BDD93A5A76ED395 FOREIGN KEY (user_id) REFERENCES app_users (id)');
        $this->addSql('ALTER TABLE fleet ADD fleet_list_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE fleet ADD CONSTRAINT FK_A05E1E471BFC2D80 FOREIGN KEY (fleet_list_id) REFERENCES fleet_list (id)');
        $this->addSql('CREATE INDEX IDX_A05E1E471BFC2D80 ON fleet (fleet_list_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fleet DROP FOREIGN KEY FK_A05E1E471BFC2D80');
        $this->addSql('DROP TABLE fleet_list');
        $this->addSql('DROP INDEX IDX_A05E1E471BFC2D80 ON fleet');
        $this->addSql('ALTER TABLE fleet DROP fleet_list_id');
    }
}
