<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190706173144 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE galaxy ADD server_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE galaxy ADD CONSTRAINT FK_F6BB13761844E6B7 FOREIGN KEY (server_id) REFERENCES server (id)');
        $this->addSql('CREATE INDEX IDX_F6BB13761844E6B7 ON galaxy (server_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE galaxy DROP FOREIGN KEY FK_F6BB13761844E6B7');
        $this->addSql('DROP INDEX IDX_F6BB13761844E6B7 ON galaxy');
        $this->addSql('ALTER TABLE galaxy DROP server_id');
    }
}
