-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Lun 11 Janvier 2016 à 15:20
-- Version du serveur :  5.6.17
-- Version de PHP :  5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `valocal`
--

-- --------------------------------------------------------

--
-- Structure de la table `achats`
--

CREATE TABLE IF NOT EXISTS `achats` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `fournisseur` mediumint(9) NOT NULL COMMENT 'join:clients,id,nom',
  `categorie_params` varchar(100) NOT NULL,
  `poids` float(8,2) NOT NULL DEFAULT '100.00',
  `date` date NOT NULL COMMENT 'aujourdhui',
  `heure` time NOT NULL COMMENT 'maintenant',
  `cours` float(8,2) NOT NULL,
  `tva` float(8,2) NOT NULL,
  `prix_ht` float(8,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Structure de la table `clients`
--

CREATE TABLE IF NOT EXISTS `clients` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `nom` varchar(200) NOT NULL,
  `prenom` varchar(200) NOT NULL,
  `categorie` varchar(200) NOT NULL COMMENT 'dero',
  `email` varchar(200) NOT NULL,
  `telephone` varchar(200) NOT NULL,
  `portable` varchar(100) NOT NULL,
  `adresse` varchar(200) NOT NULL,
  `cp` varchar(200) NOT NULL,
  `ville` varchar(100) NOT NULL,
  `SIRET` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Structure de la table `cours`
--

CREATE TABLE IF NOT EXISTS `cours` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `valeur_brute` double NOT NULL,
  `valeur_euro_tonne` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Structure de la table `parametres`
--

CREATE TABLE IF NOT EXISTS `parametres` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `titre` varchar(200) NOT NULL,
  `pourcentage_interne` varchar(200) NOT NULL,
  `pourcentage_reel` varchar(200) NOT NULL,
  `categorie` varchar(100) NOT NULL COMMENT 'dero',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Structure de la table `ventes`
--

CREATE TABLE IF NOT EXISTS `ventes` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `client` mediumint(9) NOT NULL COMMENT 'join:clients,id,nom',
  `categorie_params` varchar(100) NOT NULL,
  `poids` float(8,2) NOT NULL,
  `date` date NOT NULL COMMENT 'aujourdhui',
  `heure` time NOT NULL COMMENT 'maintenant',
  `cours` float(8,2) NOT NULL,
  `tva` float(8,2) NOT NULL,
  `prix_ht` float(8,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
