<?php

 // Log into MySQL server
    require_once('../../db/infovis_db.php');

    $query1  = "SELECT * FROM images ORDER BY pk_image_id";
    $result1 = mysql_query($query1,$dbh);
    $pk      = 1;

    if ($result1) {

       while ($row = mysql_fetch_array($result1, MYSQL_ASSOC)) {

//                $image_program = $row['image_program'];
//                $image_medium  = $row['image_medium'];
                $image_ID  = $row['pk_image_id'];
                if ($image_ID!=$pk) {
                  echo 'Should be '.$pk.' and is: '.$image_ID.'<br>';
                }
//                echo 'PRO: ' . $image_program . 'MED: ' . $image_medium . '<br>';

                $pk = $pk + 1;

       }
    }

    mysql_close($dbh) or die ("Could not close connection to database!");

?>