<?php

include "../../db/infovis_db.php";
include "shared/img_specs.php";

   // Search
      $sql = 'SELECT * FROM images';
      $result = mysql_query($sql) or die (mysql_error());

   // Loop
      $irow=1;
      while($row = mysql_fetch_object($result))
      {
         $ID = $row->pk_image_id;
         $image_file = $row->image_file;

         $filename_expl = explode("/",$image_file);
         $filename_clip = '';
         for($icount = 0; $icount < count($filename_expl)-1; $icount++){
            if (count($filename_expl)>1) {
               $filename_clip = $filename_clip . $filename_expl[$icount] . '/';
            }
         }
         $image_file_last = $filename_expl[count($filename_expl)-1];
         $image_file_last = preg_replace('/^(sm_)+/','',$image_file_last);       
         $image_file_new  = $filename_clip . $image_file_last;

         $sql2  = 'UPDATE images SET ';
         $sql2 .= 'image_file = "'.$image_file_new.'" WHERE pk_image_id = '.$ID;
         $result2 = mysql_query($sql2) or die (mysql_error());           

      }

?>