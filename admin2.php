<?php
include_once("../db/culturalentomology_db.php");
include_once("shared/header.html");
include_once("shared/banner.html");
?>

<title>Edit the Cultural Entomology Database</title>

<?php include_once("shared/banner.html"); ?>

<div class="main">

Return to <a href="admin.php">editing the database</a>.<br>

<?php

      $num_rows = $_POST[num_rows];
      if ($num_rows>0) {

         if (isset($_POST['myform'])) {
             $values = $_POST['myform'];
             //echo $values['file1'];
         }

         if(isset($values['file1'])) {
                  $update_entry = 1;
         } else { $update_entry = 0;
         } 

         if ($update_entry==1) {

            //$i2 = 1; 
            //echo $values['update_title'.$i2];

            $i2=0;
            while($i2 < $num_rows) {
              $i2=$i2+1;

              $image_title          = trim(mysql_real_escape_string(stripslashes($values['update_title'.$i2])));
              $image_date_circa     = trim(mysql_real_escape_string(stripslashes($values['update_circa'.$i2])));
              $image_date           = trim(mysql_real_escape_string(stripslashes($values['update_date'.$i2])));
              $image_medium         = trim(mysql_real_escape_string(stripslashes($values['update_medium'.$i2])));
              $image_creator        = trim(mysql_real_escape_string(stripslashes($values['update_creator'.$i2])));
              $image_notes          = trim(mysql_real_escape_string(stripslashes($values['update_notes'.$i2])));
              $image_collection     = trim(mysql_real_escape_string(stripslashes($values['update_collection'.$i2])));
              $image_indate         = trim(mysql_real_escape_string(stripslashes($values['update_indate'.$i2])));
              $image_update         = trim(mysql_real_escape_string(stripslashes($values['update_update'.$i2])));
              $image_registered     = trim(mysql_real_escape_string(stripslashes($values['update_registered'.$i2])));
              $image_hide           = trim(mysql_real_escape_string(stripslashes($values['update_hide'.$i2])));;
              $image_ID             = trim(mysql_real_escape_string(stripslashes($values['update_ID'.$i2])));

              $sql2  = 'UPDATE objects SET ';

              $sql2 .= 'title          = "'.$image_title.'", ';
              $sql2 .= 'time_period    = "'.$image_date_circa.'", ';
              $sql2 .= 'year           = "'.$image_date.'", ';
              $sql2 .= 'object_medium  = "'.$image_medium.'", ';
              $sql2 .= 'creator        = "'.$image_creator.'", ';
              $sql2 .= 'description    = "'.$image_notes.'", ';
              $sql2 .= 'collection     = "'.$image_collection.'", ';
              $sql2 .= 'entry_date     = "'.$image_indate.'", ';
              $sql2 .= 'entry_update   = "'.$image_update.'", ';
              $sql2 .= 'registered     = "'.$image_registered.'", ';
              $sql2 .= 'hide           = "'.$image_hide.'" ';
              $sql2 .= ' WHERE pk_object_id  = "'.$image_ID.'" ';

              //echo '<br>'.$sql2.'<br>';

              $result2 = mysql_query($sql2) or die (mysql_error());           

           }  //while($i2 < $num_rows) {
         }    //if ($update_entry==1) {
      }       //if ($num_rows>0) {

?>

</div>

<? include_once("shared/footer.php"); ?>
