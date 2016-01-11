ALTER TABLE achats MODIFY COLUMN poids float(8,2) AFTER heure;
ALTER TABLE achats MODIFY COLUMN categorie_params varchar(100) AFTER heure;
ALTER TABLE achats MODIFY COLUMN fournisseur mediumint(9) AFTER heure;


ALTER TABLE ventes MODIFY COLUMN poids float(8,2) AFTER heure;
ALTER TABLE ventes MODIFY COLUMN categorie_params varchar(100) AFTER heure;
ALTER TABLE ventes MODIFY COLUMN client mediumint(9) AFTER heure;

