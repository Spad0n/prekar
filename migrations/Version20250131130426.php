<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250131130426 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE admin (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_banned (admin_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_FE1B168B642B8210 (admin_id), INDEX IDX_FE1B168BA76ED395 (user_id), PRIMARY KEY(admin_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE car (id INT AUTO_INCREMENT NOT NULL, user_owner_id INT DEFAULT NULL, brand VARCHAR(50) NOT NULL, model VARCHAR(50) NOT NULL, registration VARCHAR(20) NOT NULL, nb_seat INT DEFAULT NULL, boot_capacity BIGINT DEFAULT NULL, fuel_type VARCHAR(20) NOT NULL, INDEX IDX_773DE69D9EB185F9 (user_owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dispute (id INT AUTO_INCREMENT NOT NULL, jurist_id INT DEFAULT NULL, description LONGTEXT DEFAULT NULL, status VARCHAR(50) NOT NULL, reporting_date DATE NOT NULL, INDEX IDX_3C9250075CD7AE06 (jurist_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE jurist (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, sender_id INT NOT NULL, receiver_id INT NOT NULL, text LONGTEXT DEFAULT NULL, date_message DATE NOT NULL, INDEX IDX_B6BD307FF624B39D (sender_id), INDEX IDX_B6BD307FCD53EDB6 (receiver_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE offer (id INT AUTO_INCREMENT NOT NULL, car_id INT NOT NULL, user_owner_id INT DEFAULT NULL, start_date DATE NOT NULL, end_date DATE DEFAULT NULL, localisation_garage LONGTEXT DEFAULT NULL, price BIGINT NOT NULL, delivery VARCHAR(50) DEFAULT NULL, available VARCHAR(50) DEFAULT NULL, INDEX IDX_29D6873EC3C6F69F (car_id), INDEX IDX_29D6873E9EB185F9 (user_owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, apply_id INT DEFAULT NULL, admin_id INT DEFAULT NULL, user_owner_id INT DEFAULT NULL, total DOUBLE PRECISION NOT NULL, status VARCHAR(50) NOT NULL, pay_date DATE NOT NULL, UNIQUE INDEX UNIQ_6D28840D4DDCCBDE (apply_id), INDEX IDX_6D28840D642B8210 (admin_id), INDEX IDX_6D28840D9EB185F9 (user_owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE renting (id INT AUTO_INCREMENT NOT NULL, offer_id INT DEFAULT NULL, user_borrower_id INT DEFAULT NULL, nb_km BIGINT NOT NULL, commentary LONGTEXT NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, UNIQUE INDEX UNIQ_13533C0F53C674EE (offer_id), INDEX IDX_13533C0FB8D5F8BE (user_borrower_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE report (id INT AUTO_INCREMENT NOT NULL, dispute_id INT NOT NULL, user_borrower_id INT DEFAULT NULL, user_owner_id INT DEFAULT NULL, INDEX IDX_C42F7784C7B47CB5 (dispute_id), INDEX IDX_C42F7784B8D5F8BE (user_borrower_id), INDEX IDX_C42F77849EB185F9 (user_owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service (id INT AUTO_INCREMENT NOT NULL, admin_id INT DEFAULT NULL, service_fee BIGINT DEFAULT NULL, UNIQUE INDEX UNIQ_E19D9AD2642B8210 (admin_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, profile_image VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_name VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, start_sub DATE DEFAULT NULL, end_sub DATE DEFAULT NULL, nb_offers INT DEFAULT NULL, balance BIGINT DEFAULT NULL, discr VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE admin ADD CONSTRAINT FK_880E0D76BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_banned ADD CONSTRAINT FK_FE1B168B642B8210 FOREIGN KEY (admin_id) REFERENCES admin (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_banned ADD CONSTRAINT FK_FE1B168BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE car ADD CONSTRAINT FK_773DE69D9EB185F9 FOREIGN KEY (user_owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE dispute ADD CONSTRAINT FK_3C9250075CD7AE06 FOREIGN KEY (jurist_id) REFERENCES jurist (id)');
        $this->addSql('ALTER TABLE jurist ADD CONSTRAINT FK_FCD5E588BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF624B39D FOREIGN KEY (sender_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FCD53EDB6 FOREIGN KEY (receiver_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE offer ADD CONSTRAINT FK_29D6873EC3C6F69F FOREIGN KEY (car_id) REFERENCES car (id)');
        $this->addSql('ALTER TABLE offer ADD CONSTRAINT FK_29D6873E9EB185F9 FOREIGN KEY (user_owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D4DDCCBDE FOREIGN KEY (apply_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D642B8210 FOREIGN KEY (admin_id) REFERENCES admin (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D9EB185F9 FOREIGN KEY (user_owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE renting ADD CONSTRAINT FK_13533C0F53C674EE FOREIGN KEY (offer_id) REFERENCES offer (id)');
        $this->addSql('ALTER TABLE renting ADD CONSTRAINT FK_13533C0FB8D5F8BE FOREIGN KEY (user_borrower_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F7784C7B47CB5 FOREIGN KEY (dispute_id) REFERENCES dispute (id)');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F7784B8D5F8BE FOREIGN KEY (user_borrower_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F77849EB185F9 FOREIGN KEY (user_owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD2642B8210 FOREIGN KEY (admin_id) REFERENCES admin (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE admin DROP FOREIGN KEY FK_880E0D76BF396750');
        $this->addSql('ALTER TABLE user_banned DROP FOREIGN KEY FK_FE1B168B642B8210');
        $this->addSql('ALTER TABLE user_banned DROP FOREIGN KEY FK_FE1B168BA76ED395');
        $this->addSql('ALTER TABLE car DROP FOREIGN KEY FK_773DE69D9EB185F9');
        $this->addSql('ALTER TABLE dispute DROP FOREIGN KEY FK_3C9250075CD7AE06');
        $this->addSql('ALTER TABLE jurist DROP FOREIGN KEY FK_FCD5E588BF396750');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FF624B39D');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FCD53EDB6');
        $this->addSql('ALTER TABLE offer DROP FOREIGN KEY FK_29D6873EC3C6F69F');
        $this->addSql('ALTER TABLE offer DROP FOREIGN KEY FK_29D6873E9EB185F9');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D4DDCCBDE');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D642B8210');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D9EB185F9');
        $this->addSql('ALTER TABLE renting DROP FOREIGN KEY FK_13533C0F53C674EE');
        $this->addSql('ALTER TABLE renting DROP FOREIGN KEY FK_13533C0FB8D5F8BE');
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F7784C7B47CB5');
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F7784B8D5F8BE');
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F77849EB185F9');
        $this->addSql('ALTER TABLE service DROP FOREIGN KEY FK_E19D9AD2642B8210');
        $this->addSql('DROP TABLE admin');
        $this->addSql('DROP TABLE user_banned');
        $this->addSql('DROP TABLE car');
        $this->addSql('DROP TABLE dispute');
        $this->addSql('DROP TABLE jurist');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE offer');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE renting');
        $this->addSql('DROP TABLE report');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
