<?php

 // Log into MySQL server
    require_once('../../db/infovis_db.php');

    $query1  = "SELECT * FROM images ORDER BY image_ID";
    $result1 = mysql_query($query1,$dbh);
    $pk      = 1;

    if ($result1) {
   
       while ($row = mysql_fetch_array($result1, MYSQL_ASSOC)) {
                $query2  = "UPDATE images SET ";


                $string1 = $row['image_file_orig'];
                $string2 = str_replace("visualcomplexity", "visualcomplexity/", $string1); 
                $query2 .= "image_file_orig = '"       . $string2 . "' ";


                $query2 .= " WHERE image_ID = '"  . $pk       . "' ";
                echo $query2 . '<br>';
                $result2 = mysql_query( $query2, $dbh );
                echo "<result>" . ( $result2 ? "success" : "failure" ) . "</result>";

                $pk = $pk + 1;
       }
    }

    mysql_close($dbh) or die ("Could not close connection to database!");

?>