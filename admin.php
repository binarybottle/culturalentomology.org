<?php
include_once("../db/culturalentomology_db.php"); // includes $admin_email
include_once("shared/header.html");
include_once("shared/banner.html");
?>

<title>Edit the Cultural Entomology Database</title>

<?php include_once("shared/banner.html"); ?>

<div class="main">
<br />
<br />


   <h1>Edit the cultural entomology database</h1>
   Alter text, click "Submit" at the bottom, and refresh browser to view changes.
   <br /><br /><br />

<?php
 
// Search form (words & range)
   include_once("./shared/searchForm.php");

// Create the navigation switch
   $cmd = (isset($_GET['cmd']) ? $_GET['cmd'] : '');

   switch($cmd)
   {
      default:
      searchForm();
  
      break;
    
      $mode = 'normal';
      case "search":
        searchForm();
    
        $searchstring = mysql_real_escape_string($_GET['words']);
        $searchstart  = mysql_real_escape_string($_GET['start']);
        $searchstop   = mysql_real_escape_string($_GET['stop']);

        if (strlen(trim($searchstart))==0) {
           $searchstart = 1;
        }
        if (strlen(trim($searchstop))==0) {
           $searchstop = 9999999;
        }

        if (strlen(trim($searchstring))==0) {
           $sql = "SELECT * FROM objects
                   WHERE pk_object_id >= " . (int)$searchstart . 
                   " AND pk_object_id <= " . (int)$searchstop;
        }
        else {
    
           switch($_GET['mode'])
           {
             case "normal":
               $mode = 'normal';
               $bool = '';
               break;
             case "boolean":
               $mode = 'boolean';
               $bool = ' IN BOOLEAN MODE ';
               break;
           }

           $sql_submissions    = "SELECT * FROM objects
                   MATCH(notes)
                   AGAINST ('$searchstring' $bool) AS score FROM objects
                   WHERE MATCH(notes)
                   AGAINST ('$searchstring' $bool)
                     AND pk_object_id >= " . (int)$searchstart . 
                   " AND pk_object_id <= " . (int)$searchstop  .
                   " ORDER BY score DESC";
        }

        $result = mysql_query($sql) or die (mysql_error());

        $num_rows = mysql_num_rows($result);

        if ($num_rows==1) {
           echo '<span class="font80"><i>Found ' . $num_rows . ' result:  </i></span><br />';
        }
        else {
           echo '<span class="font80"><i>Found ' . $num_rows . ' results: </i></span><br />';
        }

      break;
   }  // switch

   if ($result) {

      echo '<form method="post" action="admin.php?cmd=search&words='.
                                       $searchstring.'&mode='.$mode.'&start='.$searchstart.'&stop='.$searchstop.'">';

   // Loop through search results      
      $i=1;
      while($row = mysql_fetch_object($result))
      {
         $image_ID               = $row->pk_object_id;
         $image_title            = $row->title;
         $image_file             = $row->filename1;
         $image_collection       = $row->collection;
         $image_creator          = $row->creator;
         $image_medium           = $row->object_medium;
         $image_notes            = $row->description;
         $image_date             = $row->year;
         $image_date_circa       = $row->time_period;
         $image_indate           = $row->entry_date;
         $image_update           = $row->entry_update;
         $image_registered       = $row->registered;
         $image_hide             = $row->hide;

      // Image repository
         $filename_expl    = explode("/",$image_file);
         $filename_clip    = '';
         for($icount = 0; $icount < count($filename_expl)-1; $icount++){
            if (count($filename_expl)>1) {
               $filename_clip = $filename_clip . $filename_expl[$icount] . '/';
            }
         }
         $image_file_view = $filename_clip . $image_prepend . $filename_expl[count($filename_expl)-1];
         $image_file_full = $filename_clip . $filename_expl[count($filename_expl)-1];
         //echo $image_repository.$image_file;

      // Line
         echo '<hr size="1" />';

      // Anchor
         echo '<a name="'.$image_ID.'"></a>';

      // Image
         echo '<table width="800" border="0" cellspacing="0" cellpadding="10">';
         echo ' <tr>';
         echo '  <td width="240">';

         echo '   <img src="' . $image_repository . $image_file_view . '" border="0">';
         if (strlen(trim($image_url))>0) {
            echo '</a>';
         }

//         echo '  <img src="' . $image_repository . $image_file_view . '" height="240">';

         echo '   <span class="font80">'.$image_ID.'</span>';
         echo '  </td>';
         echo '  <td width="560">';

         echo '<input type="hidden" name="update_ID'.$i.'" value="'.$image_ID.'"><br />';

         echo '<div class="font80" color="#996663"><i>';

         echo 'Title:      <br /><input type="text" size="65" name="update_title'.$i.'"   value="'.$image_title     .'"><br />';

         echo 'File:           <br /><textarea cols="75" rows="1" name="update_file'.$i.'">'   
                                                                 .$image_file           .'</textarea><br />';
         echo 'Start date:       <input type="text" size="5"  name="update_date'.$i.'"    value="'.$image_date      .'">';
         echo '&nbsp;&nbsp;&nbsp;&nbsp;';
         if ($image_date_circa==1) {
                  $circa = 'checked'; $accurate = '';
         } else { $circa = '';        $accurate = 'checked';
         }
         echo 'Circa: Y              <input type="radio"          name="update_circa'.$i.'"   value="1" '.$circa        .'>';
         echo 'N                     <input type="radio"          name="update_circa'.$i.'"   value="0" '.$accurate     .'><br />';

         echo 'Medium:         <br /><input type="text" size="65" name="update_medium'.$i.'"  value="'.$image_medium    .'"><br />';
         echo 'Creator:        <br /><input type="text" size="65" name="update_creator'.$i.'" value="'.$image_creator   .'"><br />';
         echo 'Image URL:      <br /><input type="text" size="65" name="update_url'.$i.'"     value="'.$image_url       .'"><br />';

         echo 'Notes:          <br /><textarea cols="75" rows="5" name="update_notes'.$i.'">'
                                                                 .$image_notes          .'</textarea><br />';
         echo 'Collection:     <br /><textarea cols="75" rows="1" name="update_collection'.$i.'">'
                                                                 .$image_collection     .'</textarea><br />';
         echo 'Input date:           <input type="text" size="8"  name="update_indate'.$i.'" value="'.$image_indate     .'">';
         echo '&nbsp;&nbsp;&nbsp;&nbsp;';
         echo 'Latest update:        <input type="text" size="8"  name="update_update'.$i.'" value="'.$image_update     .'">';
         echo '&nbsp;&nbsp;&nbsp;&nbsp;';
         if ($image_registered==1) {
                  $registered = 'checked'; $unregd = '';
         } else { $registered = '';        $unregd = 'checked';
         }
         echo 'Registered: Y         <input type="radio"          name="update_registered'.$i.'"   value="1" '.$registered.'>';
         echo 'N                     <input type="radio"          name="update_registered'.$i.'"   value="0" '.$unregd.'><br />';
         if ($image_hide==1) {
                  $hide = 'checked'; $show = '';
         } else { $hide = '';        $show = 'checked';
         }
         echo 'Hide: Y               <input type="radio"          name="update_hide'.$i.'"   value="1" '.$hide          .'>';
         echo 'N                     <input type="radio"          name="update_hide'.$i.'"   value="0" '.$show          .'><br />';
         echo '<br />';
         echo '</i></div>';
         echo '   </td>';
         echo '  </tr>';
         echo ' </table>';

         $i=$i+1;

      } // while

      if ($num_rows>0) {
   
         if(isset($update_file1)) {
                  $update_entry = 1;
         } else { $update_entry = 0;
         } 

         echo '<br /><input type="submit" value="Update" />';
         echo '&nbsp;&nbsp;&nbsp;&nbsp;';
         echo '<input type="reset"  value="Reset"  />';
         echo '</form> <br />';

         if ($update_entry==1) {

            $i2=0;
            while($i2 < $num_rows) {
              $i2=$i2+1;

              $image_title          = trim(mysql_real_escape_string(stripslashes(${'update_title'.$i2})));
              $image_file           = trim(mysql_real_escape_string(stripslashes(${'update_file'.$i2})));
              $image_date_circa     = trim(mysql_real_escape_string(stripslashes(${'update_circa'.$i2})));
              $image_date           = trim(mysql_real_escape_string(stripslashes(${'update_date'.$i2})));
              $image_medium         = trim(mysql_real_escape_string(stripslashes(${'update_medium'.$i2})));
              $image_creator        = trim(mysql_real_escape_string(stripslashes(${'update_creator'.$i2})));
              $image_notes          = trim(mysql_real_escape_string(stripslashes(${'update_notes'.$i2})));
              $image_collection     = trim(mysql_real_escape_string(stripslashes(${'update_collection'.$i2})));
              $image_indate         = trim(mysql_real_escape_string(stripslashes(${'update_indate'.$i2})));
              $image_update         = trim(mysql_real_escape_string(stripslashes(${'update_update'.$i2})));
              $image_registered     = trim(mysql_real_escape_string(stripslashes(${'update_registered'.$i2})));
              $image_hide           = trim(mysql_real_escape_string(stripslashes(${'update_hide'.$i2})));;
              $image_ID             = trim(mysql_real_escape_string(stripslashes(${'update_ID'.$i2})));

              $sql2  = 'UPDATE images SET ';

              $sql2 .= 'image_title          = "'.$image_title.'", ';
              $sql2 .= 'image_file           = "'.$image_file.'", ';
              $sql2 .= 'image_date_circa     = "'.$image_date_circa.'", ';
              $sql2 .= 'image_date           = "'.$image_date.'", ';
              $sql2 .= 'image_medium         = "'.$image_medium.'", ';
              $sql2 .= 'image_creator        = "'.$image_creator.'", ';
              $sql2 .= 'image_notes          = "'.$image_notes.'", ';
              $sql2 .= 'image_collection     = "'.$image_collection.'", ';
              $sql2 .= 'image_indate         = "'.$image_indate.'", ';
              $sql2 .= 'image_update         = "'.$image_update.'", ';
              $sql2 .= 'image_registered     = "'.$image_registered.'", ';
              $sql2 .= 'image_hide           = "'.$image_hide.'" ';
              $sql2 .= ' WHERE pk_image_id   = "'.$image_ID.'" ';

              //echo '<br>'.$sql2.'<br>';

              $result2 = mysql_query($sql2) or die (mysql_error());           

           }  //while($i2 < $num_rows) {
         }    //if ($update_entry==1) {
      }       //if ($num_rows>0) {
   }          //switch($cmd)

?>

</div>

<? include_once("shared/footer.php"); ?>
