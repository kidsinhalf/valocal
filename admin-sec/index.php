<?php
session_start();
include '../inc/baseconnect.php';
include '../inc/fonctions.php';
$tab_alerte=array();
include 'inc/recuperation-get.php';
?>
<?php include 'blocs/header-admin.php';?>  
    <div class="row">
        <div class="large-12 columns">
		<?php include 'blocs/menu-admin.php';?>  
        </div>
    </div>

    <div class="row">
        <div class="large-12 columns">
			<h1>Accueil</h1>
			<?php
			include 'blocs/cours-cuivre.php';
			?>
            <?php if (!empty($tab_alerte)) foreach ($tab_alerte as $alerte) echo $alerte; //Le tableau d'alertes?>
		
			
			<div class="row">
				<div class="small-12 text-center large-4 columns">
				<a class="button success" href="edit-table.php?table=clients&action=nouveau" title="Nouveau client">Ajouter Client/Fournisseur</a>
				</div>
				
				<div class="small-12 text-center large-4 columns">
				<a class="button success" href="edit-table.php?table=achats&action=nouveau" title="Nouvel achat">Nouvel Achat</a>
				</div>
				
				<div class="small-12 text-center large-4 columns">
				<a class="button success" href="edit-table.php?table=ventes&action=nouveau" title="Nouvelle vente">Nouvelle Vente</a>
				</div>
			</div>
			
			<div class="row">
				<div class="small-12 text-center large-4 columns">
				<a class="button" href="view-table.php?table=clients" title="Nouveau client">Clients&Fournisseurs</a>
				</div>
				
				<div class="small-12 text-center large-4 columns">
				<a class="button" href="view-table.php?table=achats" title="Nouvel achat">Achats</a>
				</div>
				
				<div class="small-12 text-center large-4 columns">
				<a class="button" href="view-table.php?table=ventes" title="Nouvelle vente">Ventes</a>
				</div>
			</div>
			
			<div class="row">
				<div class="small-12 text-center large-4 columns">
				<a class="button success" href="view-table.php?table=tva" title="Nouveau client">TVA</a>
				</div>
				
				<div class="small-12 text-center large-4 columns">
				<a class="button success" href="edit-table.php?table=depenses&action=nouveau" title="Nouvelle facture">Nouvelle dépense</a>
				</div>
				
				<div class="small-12 text-center large-4 columns">
				<a class="button success" href="edit-table.php?table=recettes&action=nouveau" title="Nouvelle recette">Nouvelle recette</a>
				</div>
			</div>
			
			<div class="row">
				<div class="small-12 text-center large-4 columns">
				<a class="button" href="calcul-tva.php" title="#">#</a>
				</div>
				
				<div class="small-12 text-center large-4 columns">
				<a class="button" href="view-table.php?table=depenses" title="Factures">Dépenses</a>
				</div>
				
				<div class="small-12 text-center large-4 columns">
				<a class="button" href="view-table.php?table=recettes" title="Recettes">Recettes</a>
				</div>
			</div>
       
<!-- Code à modifier-->
<?php
?>
 
            </div>
          </div>
<?php include 'blocs/footer-admin.php';?>	