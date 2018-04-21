<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180421004237 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE z_cargo (id INT AUTO_INCREMENT NOT NULL, bitcoin INT NOT NULL, level INT NOT NULL, finishAt DATETIME DEFAULT NULL, constructTime BIGINT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE z_plasma (id INT AUTO_INCREMENT NOT NULL, bitcoin INT NOT NULL, level INT NOT NULL, finishAt DATETIME DEFAULT NULL, constructTime BIGINT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE z_missile (id INT AUTO_INCREMENT NOT NULL, bitcoin INT NOT NULL, level INT NOT NULL, finishAt DATETIME DEFAULT NULL, constructTime BIGINT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE z_laser (id INT AUTO_INCREMENT NOT NULL, bitcoin INT NOT NULL, level INT NOT NULL, finishAt DATETIME DEFAULT NULL, constructTime BIGINT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE z_armement (id INT AUTO_INCREMENT NOT NULL, bitcoin INT NOT NULL, level INT NOT NULL, finishAt DATETIME DEFAULT NULL, constructTime BIGINT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE z_cargo');
        $this->addSql('DROP TABLE z_plasma');
        $this->addSql('DROP TABLE z_missile');
        $this->addSql('DROP TABLE z_laser');
        $this->addSql('DROP TABLE z_armement');
    }
}
