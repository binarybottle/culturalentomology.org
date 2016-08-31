<?php

 // Log into MySQL server
    require_once('../../db/infovis_db.php');

    for ( $i = 161; $i <=285; $i += 1) {

       $query_from  = "SELECT * FROM images WHERE pk_image_id=$i";
       $result_from = mysql_query($query_from,$dbh);
//       $query_to    = "SELECT * FROM images WHERE pk_image_id=$i-1";
//       $result_to   = mysql_query($query_to,$dbh);
//       if ($result_from && $result_to) {

       if ($result_from) {
   
          $row_from = mysql_fetch_row($result_from);
//          $row_to   = mysql_fetch_row($result_to);

          $i2 = $i-1;
          $query_cnvrt1 = 'UPDATE images SET image_file = "'.$row_from[10].'" WHERE pk_image_id='.$i2;

          echo $query_cnvrt1.'<br />';

          $result1 = mysql_query( $query_cnvrt1 );
          echo "<result>" . ( $result1 ? "success" : "failure" ) . "</result><br />";
       }
    }

    mysql_close($dbh) or die ("Could not close connection to database!");

?>