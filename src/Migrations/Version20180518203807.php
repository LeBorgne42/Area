<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180518203807 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE planet DROP FOREIGN KEY FK_68136AA54584665A');
        $this->addSql('DROP INDEX UNIQ_68136AA54584665A ON planet');
        $this->addSql('ALTER TABLE planet DROP product_id');
        $this->addSql('ALTER TABLE product ADD planet_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADA25E9820 FOREIGN KEY (planet_id) REFERENCES planet (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D34A04ADA25E9820 ON product (planet_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE planet ADD product_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE planet ADD CONSTRAINT FK_68136AA54584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_68136AA54584665A ON planet (product_id)');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADA25E9820');
        $this->addSql('DROP INDEX UNIQ_D34A04ADA25E9820 ON product');
        $this->addSql('ALTER TABLE product DROP planet_id');
    }
}
