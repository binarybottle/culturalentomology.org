<!-- Website by Arno Klein arno@binarybottle.com . 2016 . Apache v2.0 license -->

<?php
include_once("../db/culturalentomology_db.php");
include_once("shared/img_specs.php");
include_once("shared/header.html");
include_once("shared/banner.html"); 

$allowedExts = array(
  "bmp",
  "gif",
  "jpg",
  "jpeg",
  "pjpeg",
  "png"
); 
?>

<title>Insects Incorporated: Database of Cultural Entomology</title>
<div class="main">

<?php 

  // Search form
  $default_searchstring = '+silk -"silk-screen"';
  include_once("shared/searchForm.php");

  // Create the navigation switch
  $cmd = (isset($_GET['cmd']) ? $_GET['cmd'] : '');

   switch($cmd)
   {
      default:
        $bool = ' IN BOOLEAN MODE ';
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
           $searchstring = $default_searchstring;
        }
        switch($_GET['mode'])
        {
          case "boolean":
            $bool = ' IN BOOLEAN MODE ';
            break;
          case "normal":
            $bool = '';
            break;
        }

/*
        $sql = "SELECT *, MATCH(title,creator,object_medium,nation,city,taxon_common_name,taxon_order,taxon_family,taxon_species,collection,citation,description,permission_information)
                AGAINST ('$searchstring' $bool) AS score FROM objects
                WHERE MATCH(title,creator,object_medium,nation,city,taxon_common_name,taxon_order,taxon_family,taxon_species,collection,citation,description,permission_information)
                AGAINST ('$searchstring' $bool)
                AND hide='0' AND registered='1'
                ORDER BY pk_object_id ASC, score";
                //ORDER BY entry_date DESC, entry_update ASC, score";
*/
        $sql = "SELECT *, MATCH(title,creator,object_medium,nation,city,taxon_common_name,taxon_order,taxon_family,taxon_species,collection,citation,description,permission_information)
                AGAINST ('$searchstring' $bool) AS score FROM objects
                WHERE MATCH(title,creator,object_medium,nation,city,taxon_common_name,taxon_order,taxon_family,taxon_species,collection,citation,description,permission_information)
                AGAINST ('$searchstring' $bool)
                AND hide='0' AND registered='1'
                ORDER BY pk_object_id ASC, score";
                //ORDER BY entry_date DESC, entry_update ASC, score";

        $result = mysql_query($sql) or die (mysql_error());

        echo '<br>';

        if (mysql_num_rows($result)==1) {
           echo '<div class="foundresults">1 result for <b>'.$searchstring.'</b>:  </i></div>';
        }
        else {
           echo '<div class="foundresults"><i>' . mysql_num_rows($result) . ' results for <b>'.$searchstring.'</b>: </i></div>';
        }

//      break;
//   }

   if ($result) {

   // Loop through search results
      while($row = mysql_fetch_object($result))
      {
         $object_ID = $row->pk_object_id;
         $filename1 = stripslashes($row->filename1);
         $title = stripslashes($row->title);
         $primary_category = $row->primary_category;
         $category2 = $row->category2;
         $category3 = $row->category3;
         $category4 = $row->category4;
         $creator = stripslashes($row->creator);
         $year = stripslashes($row->year);
         $object_medium = stripslashes($row->object_medium);
         $object_dimensions = stripslashes($row->object_dimensions);
         $time_period = stripslashes($row->time_period);
         $nation = stripslashes($row->nation);
         $city = stripslashes($row->city);
         $taxon_common_name = stripslashes($row->taxon_common_name);
         $taxon_order = stripslashes($row->taxon_order);
         $taxon_family = stripslashes($row->taxon_family);
         $taxon_species = stripslashes($row->taxon_species);
         $url = stripslashes($row->url);
         $collection = stripslashes($row->collection);
         $citation = stripslashes($row->citation);
         $description = stripslashes($row->description);
         $permission_information = stripslashes($row->permission_information);

      // Line
         echo '<hr size="1" />';

      // Image
      if (strlen($filename1) > 0) {
         $extension = end(explode(".", $filename1));
         if ( in_array($extension, $allowedExts ) ) {
             echo '<table width="100%" border="0" cellspacing="0" cellpadding="10">';
             echo ' <tr>';
             echo '  <td width="40%">';
             echo '   <img src="files/'.$filename1.'" height="240">';
             echo '   <a href="files/'.$filename1.'" target="_blank"><img src="files/'.$filename1.'" border="0"></a>';
             echo '   <span class="font80">'.$filename1.' ('.$object_ID.')</span>';
             echo '  </td>';
             echo '  <td width="60%">';
         } else {
             echo '<i>File:</i> '.stripslashes($filename1).' ('.$object_ID.')<br />';
         }
      }

      // Image text
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

         echo '<div class="credits">';
         //if (strlen(trim($time_period))>0 && trim($time_period)>0) {
         //   echo $time_period . ' ';
         //}
         if (strlen(trim($year))>0) {
            echo $year . '<br>';
         }
         if (strlen(trim($object_medium))>0) {
            echo $object_medium . ' ';
         }   
         if (strlen(trim($object_dimensions))>0) {
            echo '('.$object_dimensions.') ';
         }   

         if (strlen(trim($creator))>0) {
            if ((strlen(trim($year))>0 && trim($year)>0) || (strlen(trim($object_medium))>0) || (strlen(trim($object_dimensions))>0)) {
               echo 'by '; 
            }
            echo $creator;
         }   
         if (strlen(trim($city))>0) {
            echo '<br>'.$city;
            if (strlen(trim($nation))>0) {
               echo ', ';
            }
         } else {
            echo '<br>';
         }   
         if (strlen(trim($nation))>0) {
            echo $nation;
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
            echo '<i>Collection:</i> '.$collection.'</i>';
            echo '</div><br>';
         }

         if (strlen(trim($citation))>0) {
            echo '<div class="collection">';
            echo '<i>Citation:</i> '.$citation.'</i>';
            echo '</div>';
         }

/*
         // Missing from above:
         echo '<i>primary_category:</i> '.stripslashes($primary_category).'<br />';
         echo '<i>category2:</i> '.stripslashes($category2).'<br />';
         echo '<i>category3:</i> '.stripslashes($category3).'<br />';
         echo '<i>category4:</i> '.stripslashes($category4).'<br />';
         echo '<i>time_period:</i> '.stripslashes($time_period).'<br />';
         echo '<i>taxon_common_name:</i> '.stripslashes($taxon_common_name).'<br />';
         echo '<i>taxon_order:</i> '.stripslashes($taxon_order).'<br />';
         echo '<i>taxon_family:</i> '.stripslashes($taxon_family).'<br />';
         echo '<i>taxon_species:</i> '.stripslashes($taxon_species).'<br />';
         echo '<i>permission_information:</i> '.stripslashes($permission_information).'<br />';
*/

         echo '   </td>';
         echo '  </tr>';
         echo ' </table>';

      }
   }

?>

</div>

<? include_once("shared/footer.php"); ?>
