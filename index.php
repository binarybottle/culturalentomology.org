<!-- Website by Arno Klein arno@binarybottle.com . 2016 . Apache v2.0 license -->

<?php
include_once("../../db/culturalentomology_db.php");
include_once("shared/img_specs.php");
include_once("shared/header.html");
include_once("shared/banner.html"); 
?>

<title>Search Database of Cultural Entomology</title>

<?php 

$default_searchstring = "queen";

// Search form
   include_once("shared/searchForm.php");

echo '<div class="main">';

// Create the navigation switch
   $cmd = (isset($_GET['cmd']) ? $_GET['cmd'] : '');

   switch($cmd)
   {
      default:
      //searchForm();
      //break;
    
      case "search":
        echo '<div class="searchform">';
        searchForm();
        echo '</div>';

        $words = $_GET['words'];
        if (strlen($words) > 0) {  
           $searchstring = mysql_real_escape_string($words);
        } else 
           $searchstring = mysql_real_escape_string($default_searchstring);
        }
        switch($_GET['mode'])
        {
          case "normal":
            $bool = '';
            break;
          case "boolean":
            $bool = ' IN BOOLEAN MODE ';
            break;
        }

// description, creator, collection, object_medium, nation, city, taxon_common_name, taxon_order, taxon_family, taxon_species
        $sql = "SELECT *,
                MATCH(description)
                AGAINST ('$searchstring' $bool) AS score FROM images
                WHERE MATCH(description)
                AGAINST ('$searchstring' $bool)
                AND hide='0' AND registered='1'
                ORDER BY pk_image_id ASC, score";
                //ORDER BY entry_date DESC, entry_update ASC, score";

        $result = mysql_query($sql) or die (mysql_error());

        echo '<br />';

        if (mysql_num_rows($result)==1) {
           echo '<div class="foundresults"><i>Found ' . mysql_num_rows($result) . ' result for "'.$searchstring.'":  </i></div>';
        }
        else {
           echo '<div class="foundresults"><i>Found ' . mysql_num_rows($result) . ' results for "'.$searchstring.'": </i></div>';
        }

//      break;
//   }

   if ($result) {

   // Loop through search results
      while($row = mysql_fetch_object($result))
      {
         $image_ID =                           $row->pk_image_id;
         $year =                               $row->year;
         $title =                 stripslashes($row->title);
         $image_filename =        stripslashes($row->image_filename);
         $collection =            stripslashes($row->collection);
         $author =                stripslashes($row->author);
         $object_medium =         stripslashes($row->object_medium);
         $description =           stripslashes($row->description);
         $url =                   stripslashes($row->url);


      // Image repository
/*
         $filename_expl    = explode("/",$image_filename);
         $filename_clip    = '';
         for($icount = 0; $icount < count($filename_expl)-1; $icount++){
            if (count($filename_expl)>1) {
               $filename_clip = $filename_clip . $filename_expl[$icount] . '/';
            }
         }
         $image_filename = $filename_clip . $image_prepend . $filename_expl[count($filename_expl)-1];
*/
         //$image_file_full = $filename_clip . $filename_expl[count($filename_expl)-1];
         //echo $image_repository.$image_filename;

      // Line
         echo '<hr size="1" />';

      // Image
         echo '<table width="800" border="0" cellspacing="0" cellpadding="10">';
         echo ' <tr>';
         echo '  <td width="240">';

         //if (strlen(trim($url))>0) {
         //   echo '<A HREF="images/'.$image_file_full.'" target=“_blank”>';
         //   //echo '<A HREF="javascript:popUp(\'images/'.$image_file_full.'\')">';
         //}
         echo '   <img src="' . $image_repository . $image_filename . '" border="0" width="400px">';
         //if (strlen(trim($image_url))>0) {
         //   echo '</a>';
         //}
         echo '   <span class="ID">'.$image_ID.'</span>';
         echo '  </td>';
         echo '  <td width="560">';

      // Image text
/*
         if (strlen(trim($title))>0) {
            echo '<div class="title">';

            if (strlen(trim($url))>0) {

               echo '<h3><b><A HREF="'.$url.'" target=“_blank”>'
                    .$title
                    .'</a></b></h3>';
            } else {
               echo '<h3><b>'.$title.'</b></h3>';
            }
            //if (strlen(trim($url))>0) {
            echo '</div>';
         }   
*/

         echo '<div class="credits">';
         if (strlen(trim($year))>0 && trim($year)>0) {
            echo $year . ' ';
         }
         if (strlen(trim($object_medium))>0) {
            echo $object_medium . ' ';
         }   
         if (strlen(trim($creator))>0) {
            if ((strlen(trim($year))>0 && trim($year)>0) || (strlen(trim($object_medium))>0)) {
               echo 'by '; 
            }
            echo $creator;
         }   
         echo '</div>';

         if (strlen(trim($description))>0) {
            echo '<div class="notes">';
            $pattern = '/[\r\n]+/';
            $replacement = '<br><br>';
            $description = preg_replace($pattern, $replacement, $description);
            echo '<br>'.$description.'<br><br>';
            echo '</div>';
         }   

         if (strlen(trim($collection))>0) {
            echo '<div class="collection">';
            echo '<i>'.$collection.'</i>';
            echo '</div>';
         }

         echo '   </td>';
         echo '  </tr>';
         echo ' </table>';

      }
   }

?>

</div>

<? include_once("shared/footer.php"); ?>
