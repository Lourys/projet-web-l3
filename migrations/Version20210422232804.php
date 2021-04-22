<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210422232804 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE im2021_panier (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, utilisateur_id INTEGER NOT NULL, produit_id INTEGER NOT NULL, quantite INTEGER NOT NULL)');
        $this->addSql('CREATE INDEX IDX_2129058FFB88E14F ON im2021_panier (utilisateur_id)');
        $this->addSql('CREATE INDEX IDX_2129058FF347EFB ON im2021_panier (produit_id)');
        $this->addSql('CREATE TABLE im2021_produit (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, libelle VARCHAR(30) NOT NULL, prix DOUBLE PRECISION NOT NULL, quantite_stock INTEGER NOT NULL)');
        $this->addSql('CREATE TABLE im2021_utilisateur (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, identifiant VARCHAR(30) NOT NULL, mot_de_passe VARCHAR(64) NOT NULL, nom VARCHAR(30) DEFAULT NULL, prenom VARCHAR(30) DEFAULT NULL, anniversaire DATE DEFAULT NULL, is_admin BOOLEAN NOT NULL)');
        $this->addSql('DROP TABLE panier');
        $this->addSql('DROP TABLE produit');
        $this->addSql('DROP TABLE utilisateur');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE panier (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, utilisateur_id INTEGER NOT NULL, produit_id INTEGER NOT NULL, quantite INTEGER NOT NULL)');
        $this->addSql('CREATE INDEX IDX_24CC0DF2F347EFB ON panier (produit_id)');
        $this->addSql('CREATE INDEX IDX_24CC0DF2FB88E14F ON panier (utilisateur_id)');
        $this->addSql('CREATE TABLE produit (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, libelle VARCHAR(30) NOT NULL COLLATE BINARY, prix DOUBLE PRECISION NOT NULL, quantite_stock INTEGER NOT NULL)');
        $this->addSql('CREATE TABLE utilisateur (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, identifiant VARCHAR(30) NOT NULL COLLATE BINARY, mot_de_passe VARCHAR(64) NOT NULL COLLATE BINARY, nom VARCHAR(30) DEFAULT NULL COLLATE BINARY, prenom VARCHAR(30) DEFAULT NULL COLLATE BINARY, anniversaire DATE DEFAULT NULL, is_admin BOOLEAN NOT NULL)');
        $this->addSql('DROP TABLE im2021_panier');
        $this->addSql('DROP TABLE im2021_produit');
        $this->addSql('DROP TABLE im2021_utilisateur');
    }
}
