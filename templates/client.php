<?php 
$table='clients';
if (is_int($_GET['id'])) $id=$_GET['id']; else $id=0;
$donnees=db_select('SELECT * FROM '.$table.' WHERE id='.$id);
?>
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
                  <h1><?php echo $titre;?></h1>
                  <p class="subheader">Vue Client</p>
				  
				  <?php
					
					
					?>
       
              </div>
			  
			  
              <div class="large-4 columns">
				<h2>Col Droite</h2>
			  </div>
			  
            </div>
          </div>
        </div>
<?php include 'blocs/footer.php';?>