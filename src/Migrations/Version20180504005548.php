<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180504005548 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE s_content ADD salon_id INT DEFAULT NULL, DROP bitcoin');
        $this->addSql('ALTER TABLE s_content ADD CONSTRAINT FK_C088E5034C91BDE4 FOREIGN KEY (salon_id) REFERENCES salon (id)');
        $this->addSql('CREATE INDEX IDX_C088E5034C91BDE4 ON s_content (salon_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE s_content DROP FOREIGN KEY FK_C088E5034C91BDE4');
        $this->addSql('DROP INDEX IDX_C088E5034C91BDE4 ON s_content');
        $this->addSql('ALTER TABLE s_content ADD bitcoin BIGINT NOT NULL, DROP salon_id');
    }
}
