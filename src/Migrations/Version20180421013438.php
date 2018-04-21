<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180421013438 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fleet ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE fleet ADD CONSTRAINT FK_A05E1E47A76ED395 FOREIGN KEY (user_id) REFERENCES app_users (id)');
        $this->addSql('CREATE INDEX IDX_A05E1E47A76ED395 ON fleet (user_id)');
        $this->addSql('ALTER TABLE ship ADD planet_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ship ADD CONSTRAINT FK_FA30EB24A25E9820 FOREIGN KEY (planet_id) REFERENCES planet (id)');
        $this->addSql('CREATE INDEX IDX_FA30EB24A25E9820 ON ship (planet_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fleet DROP FOREIGN KEY FK_A05E1E47A76ED395');
        $this->addSql('DROP INDEX IDX_A05E1E47A76ED395 ON fleet');
        $this->addSql('ALTER TABLE fleet DROP user_id');
        $this->addSql('ALTER TABLE ship DROP FOREIGN KEY FK_FA30EB24A25E9820');
        $this->addSql('DROP INDEX IDX_FA30EB24A25E9820 ON ship');
        $this->addSql('ALTER TABLE ship DROP planet_id');
    }
}
