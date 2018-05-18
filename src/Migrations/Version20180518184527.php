<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180518184527 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE planet ADD product_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE planet ADD CONSTRAINT FK_68136AA54584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_68136AA54584665A ON planet (product_id)');
        $this->addSql('ALTER TABLE app_users DROP FOREIGN KEY FK_C25028244584665A');
        $this->addSql('DROP INDEX UNIQ_C25028244584665A ON app_users');
        $this->addSql('ALTER TABLE app_users DROP product_id');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE app_users ADD product_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE app_users ADD CONSTRAINT FK_C25028244584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C25028244584665A ON app_users (product_id)');
        $this->addSql('ALTER TABLE planet DROP FOREIGN KEY FK_68136AA54584665A');
        $this->addSql('DROP INDEX UNIQ_68136AA54584665A ON planet');
        $this->addSql('ALTER TABLE planet DROP product_id');
    }
}
