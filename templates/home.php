<?php include 'blocs/header.php';?>  
      <div class="row">
        <div class="large-12 columns">
		<?php include 'blocs/nav-top.php';?>  
          </div>
        </div>

        <div class="row">
          <div class="large-12 columns">
            <div class="row">

              <div class="large-8 columns">
                  <h1>Accueil</h1>
                  <p class="subheader">Valorisation de pneus et câbles électriques.</p>
				  
				  <?php
$debug=true;
include 'inc/fonctions.php';
$clients=db_select('SELECT * FROM clients');
//SHOW
db_show_on_table($clients, array('id'=>'id', 'Nom'=>'nom', 'Prénom'=>'prenom'));
?>
       
              </div>
			  
			  
              <div class="large-4 columns">
				<h2>Col Droite</h2>
			  </div>
			  
            </div>
          </div>
        </div>
<?php include 'blocs/footer.php';?>