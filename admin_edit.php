<?php
include_once("../db/culturalentomology_db.php");
include_once("login.php");
include_once("shared/header.html");
include_once("shared/banner.html");

$all_image_extensions = array(
  "bmp",
  "gif",
  "jpg",
  "jpeg",
  "pjpeg",
  "png",
  "tif",
  "tiff",
  "BMP",
  "GIF",
  "JPG",
  "JPEG",
  "PJPEG",
  "PNG",
  "TIF",
  "TIFF"
);
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
        $range_start  = mysql_real_escape_string($_GET['start']);
        $range_stop   = mysql_real_escape_string($_GET['stop']);

        if (strlen(trim($range_start))==0) {
           $range_start = 1;
        }
        if (strlen(trim($range_stop))==0) {
           $range_stop = 9999999;
        }

        if (strlen(trim($searchstring))==0) {
           $sql = "SELECT * FROM objects
                   WHERE pk_object_id >= " . (int)$range_start . 
                   " AND pk_object_id <= " . (int)$range_stop .
                   " ORDER BY pk_object_id ASC";
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
                     AND pk_object_id >= " . (int)$range_start . 
                   " AND pk_object_id <= " . (int)$range_stop .
                   " ORDER BY pk_object_id ASC";

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

      echo '<form name="myform" method="post" action="admin_edit.php?cmd=search&words='.$searchstring.'&mode='.$mode.'&start='.$range_start.'&stop='.$range_stop.'">';

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
         $image_dimensions       = $row->object_dimensions;
         $image_nation           = $row->nation;
         $image_state            = $row->state;
         $image_city             = $row->city;
         $image_taxon_common_name = $row->taxon_common_name;
         $image_taxon_order      = $row->taxon_order;
         $image_taxon_family     = $row->taxon_family;
         $image_taxon_species    = $row->taxon_species;
         $image_taxon_common_name2 = $row->taxon_common_name2;
         $image_taxon_order2      = $row->taxon_order2;
         $image_taxon_family2     = $row->taxon_family2;
         $image_taxon_species2    = $row->taxon_species2;
         $image_taxon_common_name3 = $row->taxon_common_name3;
         $image_taxon_order3      = $row->taxon_order3;
         $image_taxon_family3     = $row->taxon_family3;
         $image_taxon_species3    = $row->taxon_species3;
         $image_taxon_common_name4 = $row->taxon_common_name4;
         $image_taxon_order4      = $row->taxon_order4;
         $image_taxon_family4     = $row->taxon_family4;
         $image_taxon_species4    = $row->taxon_species4;
         $image_url               = $row->url;
         $image_permission        = $row->permission_information;
         $image_citation          = $row->citation;
         $image_comments          = $row->comments;

      // Image
         $converted_filename = '';
         if (strlen($image_file) > 0) {
            $path_parts = pathinfo($image_file);
            $extension = $path_parts['extension'];

            // If the file has an image extension
            if ( in_array($extension, $all_image_extensions) ) {
                $filestem = $path_parts['filename'];
                $converted_filename = $filestem.'.'.$converted_image_extension;
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
             echo '   <img src="' . $converted_images_path . '/' . $converted_filename . '" border="0" width="480">';
         }
         echo '   <span class="font80">'.$image_ID.'</span>';
         echo '  </td>';
         echo '  <td width="560">';

         echo '<input type="hidden" name="myform[update_ID'.$i.']" value="'.$image_ID.'"><br>';

         echo '<div class="font80" color="#996663"><i>';
         echo 'File: '.$image_file.'<br>';
         echo '<input type="hidden" name="myform[file'.$i.']" value="'.$image_file.'">';
         echo 'Title:      <br><input type="text" size="65" name="myform[update_title'.$i.']"   value="'.htmlentities($image_title)     .'"><br>';

         echo 'Start date:       <input type="text" size="5"  name="myform[update_date'.$i.']"    value="'.htmlentities($image_date)      .'">';https://panel.dreamhost.com/ 
         echo '&nbsp;&nbsp;&nbsp;&nbsp;';
         if ($image_date_circa==1) {
                  $circa = 'checked'; $accurate = '';
         } else { $circa = '';        $accurate = 'checked';
         }
         echo 'Circa: Y              <input type="radio"          name="myform[update_circa'.$i.']"   value="1" '.$circa        .'>';
         echo 'N                     <input type="radio"          name="myform[update_circa'.$i.']"   value="0" '.$accurate     .'><br>';
         echo 'Medium:         <br><input type="text" size="65" name="myform[update_medium'.$i.']"  value="'.htmlentities($image_medium)    .'"><br>';
         echo 'Creator:        <br><input type="text" size="65" name="myform[update_creator'.$i.']" value="'.htmlentities($image_creator)   .'"><br>';
         echo 'Notes:          <br><textarea cols="75" rows="5" name="myform[update_notes'.$i.']">'
                                                                 .htmlentities($image_notes)          .'</textarea><br>';
         echo 'Collection:     <br><textarea cols="75" rows="1" name="myform[update_collection'.$i.']">'
                                                                 .htmlentities($image_collection)     .'</textarea><br>';

         echo 'Dimensions:     <br><textarea cols="75" rows="1" name="myform[update_dimensions'.$i.']">'
                                                                 .htmlentities($image_dimensions)     .'</textarea><br>';
         echo 'Nation:     <br><textarea cols="75" rows="1" name="myform[update_nation'.$i.']">'
                                                                 .htmlentities($image_nation)     .'</textarea><br>';
         echo 'State:     <br><textarea cols="75" rows="1" name="myform[update_state'.$i.']">'
                                                                 .htmlentities($image_state)     .'</textarea><br>';
         echo 'City:     <br><textarea cols="75" rows="1" name="myform[update_city'.$i.']">'
                                                                 .htmlentities($image_city)     .'</textarea><br>';
         echo 'Taxon_Common_Name:     <br><textarea cols="75" rows="1" name="myform[update_taxon_common_name'.$i.']">'
                                                                 .htmlentities($image_taxon_common_name)     .'</textarea><br>';
         echo 'Taxon_Order:     <br><textarea cols="75" rows="1" name="myform[update_taxon_order'.$i.']">'
                                                                 .htmlentities($image_taxon_order)     .'</textarea><br>';
         echo 'Taxon_Family:     <br><textarea cols="75" rows="1" name="myform[update_taxon_family'.$i.']">'
                                                                 .htmlentities($image_taxon_family)     .'</textarea><br>';
         echo 'Taxon_Species:     <br><textarea cols="75" rows="1" name="myform[update_taxon_species'.$i.']">'
                                                                 .htmlentities($image_taxon_species)     .'</textarea><br>';
         echo 'Taxon_Common_Name2:     <br><textarea cols="75" rows="1" name="myform[update_taxon_common_name2'.$i.']">'
                                                                 .htmlentities($image_taxon_common_name2)     .'</textarea><br>';
         echo 'Taxon_Order2:     <br><textarea cols="75" rows="1" name="myform[update_taxon_order2'.$i.']">'
                                                                 .htmlentities($image_taxon_order2)     .'</textarea><br>';
         echo 'Taxon_Family2:     <br><textarea cols="75" rows="1" name="myform[update_taxon_family2'.$i.']">'
                                                                 .htmlentities($image_taxon_family2)     .'</textarea><br>';
         echo 'Taxon_Species2:     <br><textarea cols="75" rows="1" name="myform[update_taxon_species2'.$i.']">'
                                                                 .htmlentities($image_taxon_species2)     .'</textarea><br>';
         echo 'Taxon_Common_Name3:     <br><textarea cols="75" rows="1" name="myform[update_taxon_common_name3'.$i.']">'
                                                                 .htmlentities($image_taxon_common_name3)     .'</textarea><br>';
         echo 'Taxon_Order3:     <br><textarea cols="75" rows="1" name="myform[update_taxon_order3'.$i.']">'
                                                                 .htmlentities($image_taxon_order3)     .'</textarea><br>';
         echo 'Taxon_Family3:     <br><textarea cols="75" rows="1" name="myform[update_taxon_family3'.$i.']">'
                                                                 .htmlentities($image_taxon_family3)     .'</textarea><br>';
         echo 'Taxon_Species3:     <br><textarea cols="75" rows="1" name="myform[update_taxon_species3'.$i.']">'
                                                                 .htmlentities($image_taxon_species3)     .'</textarea><br>';
         echo 'Taxon_Common_Name4:     <br><textarea cols="75" rows="1" name="myform[update_taxon_common_name4'.$i.']">'
                                                                 .htmlentities($image_taxon_common_name4)     .'</textarea><br>';
         echo 'Taxon_Order4:     <br><textarea cols="75" rows="1" name="myform[update_taxon_order4'.$i.']">'
                                                                 .htmlentities($image_taxon_order4)     .'</textarea><br>';
         echo 'Taxon_Family4:     <br><textarea cols="75" rows="1" name="myform[update_taxon_family4'.$i.']">'
                                                                 .htmlentities($image_taxon_family4)     .'</textarea><br>';
         echo 'Taxon_Species4:     <br><textarea cols="75" rows="1" name="myform[update_taxon_species4'.$i.']">'
                                                                 .htmlentities($image_taxon_species4)     .'</textarea><br>';

         echo 'Url:     <br><textarea cols="75" rows="1" name="myform[update_url'.$i.']">'
                                                                 .htmlentities($image_url)     .'</textarea><br>';
         echo 'Permission:     <br><textarea cols="75" rows="1" name="myform[update_permission'.$i.']">'
                                                                 .htmlentities($image_permission)     .'</textarea><br>';
         echo 'Citation:     <br><textarea cols="75" rows="1" name="myform[update_citation'.$i.']">'
                                                                 .htmlentities($image_citation)     .'</textarea><br>';
         echo 'Comments:     <br><textarea cols="75" rows="1" name="myform[update_comments'.$i.']">'
                                                                 .htmlentities($image_comments)     .'</textarea><br>';

         echo 'Input date:           <input type="text" size="8"  name="myform[update_indate'.$i.']" value="'.$image_indate     .'">';
         echo '&nbsp;&nbsp;&nbsp;&nbsp;';
         echo 'Latest update:        <input type="text" size="8"  name="myform[update_update'.$i.']" value="'. $image_update .'">';
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

      // Update database:
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
              $image_dimensions     = trim(mysql_real_escape_string(stripslashes($values['update_dimensions'.$i2])));
              $image_nation     = trim(mysql_real_escape_string(stripslashes($values['update_nation'.$i2])));
              $image_state     = trim(mysql_real_escape_string(stripslashes($values['update_state'.$i2])));
              $image_city     = trim(mysql_real_escape_string(stripslashes($values['update_city'.$i2])));
              $image_taxon_common_name     = trim(mysql_real_escape_string(stripslashes($values['update_taxon_common_name'.$i2])));
              $image_taxon_order     = trim(mysql_real_escape_string(stripslashes($values['update_taxon_order'.$i2])));
              $image_taxon_family     = trim(mysql_real_escape_string(stripslashes($values['update_taxon_family'.$i2])));
              $image_taxon_species     = trim(mysql_real_escape_string(stripslashes($values['update_taxon_species'.$i2])));
              $image_taxon_common_name2     = trim(mysql_real_escape_string(stripslashes($values['update_taxon_common_name2'.$i2])));
              $image_taxon_order2     = trim(mysql_real_escape_string(stripslashes($values['update_taxon_order2'.$i2])));
              $image_taxon_family2     = trim(mysql_real_escape_string(stripslashes($values['update_taxon_family2'.$i2])));
              $image_taxon_species2     = trim(mysql_real_escape_string(stripslashes($values['update_taxon_species2'.$i2])));
              $image_taxon_common_name3     = trim(mysql_real_escape_string(stripslashes($values['update_taxon_common_name3'.$i2])));
              $image_taxon_order3     = trim(mysql_real_escape_string(stripslashes($values['update_taxon_order3'.$i2])));
              $image_taxon_family3     = trim(mysql_real_escape_string(stripslashes($values['update_taxon_family3'.$i2])));
              $image_taxon_species3     = trim(mysql_real_escape_string(stripslashes($values['update_taxon_species3'.$i2])));
              $image_taxon_common_name4     = trim(mysql_real_escape_string(stripslashes($values['update_taxon_common_name4'.$i2])));
              $image_taxon_order4     = trim(mysql_real_escape_string(stripslashes($values['update_taxon_order4'.$i2])));
              $image_taxon_family4     = trim(mysql_real_escape_string(stripslashes($values['update_taxon_family4'.$i2])));
              $image_taxon_species4     = trim(mysql_real_escape_string(stripslashes($values['update_taxon_species4'.$i2])));
              $image_url     = trim(mysql_real_escape_string(stripslashes($values['update_url'.$i2])));
              $image_permission     = trim(mysql_real_escape_string(stripslashes($values['update_permission'.$i2])));
              $image_citation     = trim(mysql_real_escape_string(stripslashes($values['update_citation'.$i2])));
              $image_comments     = trim(mysql_real_escape_string(stripslashes($values['update_comments'.$i2])));

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
              $sql2 .= 'object_dimensions     = "'.$image_dimensions.'", ';
              $sql2 .= 'nation     = "'.$image_nation.'", ';
              $sql2 .= 'state     = "'.$image_state.'", ';
              $sql2 .= 'city     = "'.$image_city.'", ';
              $sql2 .= 'taxon_common_name     = "'.$image_taxon_common_name.'", ';
              $sql2 .= 'taxon_order     = "'.$image_taxon_order.'", ';
              $sql2 .= 'taxon_family     = "'.$image_taxon_family.'", ';
              $sql2 .= 'taxon_species     = "'.$image_taxon_species.'", ';
              $sql2 .= 'taxon_common_name2     = "'.$image_taxon_common_name2.'", ';
              $sql2 .= 'taxon_order2     = "'.$image_taxon_order2.'", ';
              $sql2 .= 'taxon_family2     = "'.$image_taxon_family2.'", ';
              $sql2 .= 'taxon_species2     = "'.$image_taxon_species2.'", ';
              $sql2 .= 'taxon_common_name3     = "'.$image_taxon_common_name3.'", ';
              $sql2 .= 'taxon_order3     = "'.$image_taxon_order3.'", ';
              $sql2 .= 'taxon_family3     = "'.$image_taxon_family3.'", ';
              $sql2 .= 'taxon_species3     = "'.$image_taxon_species3.'", ';
              $sql2 .= 'taxon_common_name4     = "'.$image_taxon_common_name4.'", ';
              $sql2 .= 'taxon_order4     = "'.$image_taxon_order4.'", ';
              $sql2 .= 'taxon_family4     = "'.$image_taxon_family4.'", ';
              $sql2 .= 'taxon_species4     = "'.$image_taxon_species4.'", ';
              $sql2 .= 'url     = "'.$image_url.'", ';
              $sql2 .= 'permission_information     = "'.$image_permission.'", ';
              $sql2 .= 'citation     = "'.$image_citation.'", ';
              $sql2 .= 'comments     = "'.$image_comments.'", ';
              $sql2 .= 'entry_date     = "'.$image_indate.'", ';
              $sql2 .= 'entry_update   = "'.$image_update.'", ';
              $sql2 .= 'registered     = "'.$image_registered.'", ';
              $sql2 .= 'hide           = "'.$image_hide.'" ';
              $sql2 .= ' WHERE pk_object_id  = "'.$image_ID.'" ';

	      // Print query:
              echo '<br>'.$sql2.'<br>';

              $result2 = mysql_query($sql2) or die (mysql_error());           

           }  //while($i2 < $num_rows) {
         }    //if ($update_entry==1) {
      }       //if ($num_rows>0) {
  }

?>

</div>

<? include_once("shared/footer.php"); ?>
