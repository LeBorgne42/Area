<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180506150412 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ally ADD salon_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ally ADD CONSTRAINT FK_382900D4C91BDE4 FOREIGN KEY (salon_id) REFERENCES salon (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_382900D4C91BDE4 ON ally (salon_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ally DROP FOREIGN KEY FK_382900D4C91BDE4');
        $this->addSql('DROP INDEX UNIQ_382900D4C91BDE4 ON ally');
        $this->addSql('ALTER TABLE ally DROP salon_id');
    }
}
