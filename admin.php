<?php
include_once("../db/culturalentomology_db.php");
include_once("shared/header.html");
include_once("shared/banner.html");
?>

<title>Edit the Cultural Entomology Database</title>

<?php include_once("shared/banner.html"); ?>

<div class="main">

   <h1>Edit the cultural entomology database</h1>
   Alter text, click "Update" at the bottom, and refresh browser to view changes.
   <br><br><br>

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

           $sql = "SELECT * FROM objects
                   WHERE MATCH(title,category1,category2,category3,creator,object_medium,time_period,nation,state,city,taxon_common_name,taxon_order,taxon_family,taxon_species,collection,description)
                   AGAINST ('$searchstring' $bool)
                     AND pk_object_id >= " . (int)$searchstart . 
                   " AND pk_object_id <= " . (int)$searchstop;
                   " ORDER BY pk_object_id"; 

           //WHERE MATCH(title, category1, category2, category3, category4, creator, object_medium, time_period, nation, state, city, taxon_common_name, taxon_order, taxon_family, taxon_species, taxon_common_name2, taxon_common_name3, taxon_common_name4, taxon_order2, taxon_order3, taxon_order4, taxon_family2, taxon_family3, taxon_family4, taxon_species2, taxon_species3, taxon_species4, collection, citation, description, comments, curator)
        }

        $result = mysql_query($sql) or die (mysql_error());

        $num_rows = mysql_num_rows($result);

        if ($num_rows==1) {
           echo '<span class="font80"><i>Found ' . $num_rows . ' result:  </i></span><br>';
        }
        else {
           echo '<span class="font80"><i>Found ' . $num_rows . ' results: </i></span><br>';
        }

      break;
   }  // switch

   if ($result && $num_rows>0) {

//      echo '<form name="myform" method="post" action="admin2.php?cmd=search&words='.
//                                       $searchstring.'&mode='.$mode.'&start='.$searchstart.'&stop='.$searchstop.'">';
      echo '<form name="myform" method="post" action="admin2.php" enctype="multipart/form-data">';
      echo '<input type="hidden" name="num_rows" value="'.$num_rows.'">';

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

      // Image
         $converted_filename = '';
         if (strlen($image_file) > 0) {
             $extension = end(explode(".", $image_file));
             if ( in_array($extension, $image_extensions_for_viewing ) ) {
                 $converted_filename = str_replace($extension, $converted_image_extension, $image_file);
             }
         }

      // Line
         echo '<hr size="1" />';

      // Anchor
         echo '<a name="'.$image_ID.'"></a>';

      // Image
         echo '<table width="800" border="0" cellspacing="0" cellpadding="10">';
         echo ' <tr>';
         echo '  <td width="240">';
         if (strlen($converted_filename) > 0) {
             echo '   <img src="' . $converted_images_path . '/' . $converted_filename . '" border="0" width="120">';
         }
         echo '   <span class="font80">'.$image_ID.'</span>';
         echo '  </td>';
         echo '  <td width="560">';

         echo '<input type="hidden" name="update_ID'.$i.'" value="'.$image_ID.'"><br>';

         echo '<div class="font80" color="#996663"><i>';
         echo 'File: '.$image_file.'<br>';
         echo '<input type="hidden" name="myform[file'.$i.']" value="'.$image_file.'">';
         echo 'Title:      <br><input type="text" size="65" name="myform[update_title'.$i.']"   value="'.$image_title     .'"><br>';

         echo 'Start date:       <input type="text" size="5"  name="myform[update_date'.$i.']"    value="'.$image_date      .'">';https://panel.dreamhost.com/ 
         echo '&nbsp;&nbsp;&nbsp;&nbsp;';
         if ($image_date_circa==1) {
                  $circa = 'checked'; $accurate = '';
         } else { $circa = '';        $accurate = 'checked';
         }
         echo 'Circa: Y              <input type="radio"          name="myform[update_circa'.$i.']"   value="1" '.$circa        .'>';
         echo 'N                     <input type="radio"          name="myform[update_circa'.$i.']"   value="0" '.$accurate     .'><br>';
         echo 'Medium:         <br><input type="text" size="65" name="myform[update_medium'.$i.']"  value="'.$image_medium    .'"><br>';
         echo 'Creator:        <br><input type="text" size="65" name="myform[update_creator'.$i.']" value="'.$image_creator   .'"><br>';
         echo 'Notes:          <br><textarea cols="75" rows="5" name="myform[update_notes'.$i.']">'
                                                                 .$image_notes          .'</textarea><br>';
         echo 'Collection:     <br><textarea cols="75" rows="1" name="myform[update_collection'.$i.']">'
                                                                 .$image_collection     .'</textarea><br>';
         echo 'Input date:           <input type="text" size="8"  name="myform[update_indate'.$i.']" value="'.$image_indate     .'">';
         echo '&nbsp;&nbsp;&nbsp;&nbsp;';
         echo 'Latest update:        <input type="text" size="8"  name="myform[update_update'.$i.']" value="'.$image_update     .'">';
         echo '&nbsp;&nbsp;&nbsp;&nbsp;';
         if ($image_registered==1) {
                  $registered = 'checked'; $unregd = '';
         } else { $registered = '';        $unregd = 'checked';
         }
         echo 'Registered: Y         <input type="radio"          name="myform[update_registered'.$i.']"   value="1" '.$registered.'>';
         echo 'N                     <input type="radio"          name="myform[update_registered'.$i.']"   value="0" '.$unregd.'><br>';
         if ($image_hide==1) {
                  $hide = 'checked'; $show = '';
         } else { $hide = '';        $show = 'checked';
         }
         echo 'Hide: Y               <input type="radio"          name="myform[update_hide'.$i.']"   value="1" '.$hide          .'>';
         echo 'N                     <input type="radio"          name="myform[update_hide'.$i.']"   value="0" '.$show          .'><br>';
         echo '<br>';
         echo '</i></div>';
         echo '   </td>';
         echo '  </tr>';
         echo ' </table>';

         $i=$i+1;

      } // while

      echo '<br><input type="submit" value="Update" />';
      echo '&nbsp;&nbsp;&nbsp;&nbsp;';
      echo '<input type="reset"  value="Reset"  />';
      echo '</form>';

  }

?>

</div>

<? include_once("shared/footer.php"); ?>