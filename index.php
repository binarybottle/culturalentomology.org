<!-- Website by Arno Klein arno@binarybottle.com . 2016 . Apache v2.0 license -->

<?php
include_once("../db/culturalentomology_db.php");
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
      } else {
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
  }

  /* SQL query:
     ALTER TABLE `objects` ADD FULLTEXT(`title`,`category1`,`category2`,`category3`,`category4`,`creator`,`year`,`object_medium`,`time_period`,`nation`,`state`,`city`,`taxon_common_name`,`taxon_order`,`taxon_family`,`taxon_species`,`collection`,`citation`,`description`,`permission_information`)
     #1070 - Too many key parts specified; max 16 parts allowed
  */
  $sql = "SELECT *, MATCH(title,category1,category2,category3,creator,object_medium,time_period,nation,state,city,taxon_common_name,taxon_order,taxon_family,taxon_species,collection,description)
          AGAINST ('$searchstring' $bool) AS score FROM objects
          WHERE MATCH(title,category1,category2,category3,creator,object_medium,time_period,nation,state,city,taxon_common_name,taxon_order,taxon_family,taxon_species,collection,description)
          AGAINST ('$searchstring' $bool)
          AND hide='0' AND registered='1'
          ORDER BY pk_object_id ASC, score";
          //ORDER BY entry_date DESC, entry_update ASC, score";

  $result = mysql_query($sql) or die (mysql_error());

  echo '<br>';

  if (mysql_num_rows($result)==1) {
     echo '<div class="foundresults">1 result for <b>'.$searchstring.'</b>:  </div>';
  }
  else {
     echo '<div class="foundresults">' . mysql_num_rows($result) . ' results for <b>'.$searchstring.'</b>: </div>';
  }

  // This whole loop is repeated in submit_review.php
  // except for "// Show files if images" portion
  if ($result) {

    // Loop through search results
    while($row = mysql_fetch_object($result)) {
        $object_ID = $row->pk_object_id;
        $filename1 = $row->filename1;
        $filename2 = $row->filename2;
        $filename3 = $row->filename3;
        $filename4 = $row->filename4;
        $filename5 = $row->filename5;
        $filename6 = $row->filename6;
        $filename7 = $row->filename7;
        $filename8 = $row->filename8;
        $filename9 = $row->filename9;
        $filename10 = $row->filename10;
        $title = $row->title;
        $category1 = $row->category1;
        $category2 = $row->category2;
        $category3 = $row->category3;
        $category4 = $row->category4;
        $creator = $row->creator;
        $year = $row->year;
        $object_medium = $row->object_medium;
        $object_dimensions = $row->object_dimensions;
        $time_period = $row->time_period;
        $nation = $row->nation;
        $state = $row->state;
        $city = $row->city;
        $taxon_common_name = $row->taxon_common_name;
        $taxon_order = $row->taxon_order;
        $taxon_family = $row->taxon_family;
        $taxon_species = $row->taxon_species;
        $url = $row->url;
        $collection = $row->collection;
        $citation = $row->citation;
        $description = $row->description;
        $comments = $row->comments;
        $permission_information = $row->permission_information;

        // Line
        echo '<hr size="1" />';
  
        echo '<div class="collection">';

        // Text
        if (strlen(trim($title))>0) {
            echo '<b>'.$title.'</b><br>';
        }

        if (strlen(trim($description))>0) {
            $pattern = '/[\r\n]+/';
            $replacement = '<br><br>';
            $description = preg_replace($pattern, $replacement, $description);
            if (strlen(trim($description))>0) {
                echo '<div class="notes">';
                echo $description;
                echo '</div><br>';
            }
        }

        if (strlen(trim($creator))>0) {
            echo $creator;
        }
        if ((strlen(trim($creator))>0) && ((strlen(trim($year))>0) || (strlen(trim($object_medium))>0) || (strlen(trim($object_dimensions))>0))) {
            echo ', ';
        }
        if (strlen(trim($year))>0) {
            echo $year;
        }
        if (((strlen(trim($creator))>0) || (strlen(trim($year))>0)) && ((strlen(trim($object_medium))>0) || (strlen(trim($object_dimensions))>0))) {
            echo ', ';
        }
        if (strlen(trim($object_medium))>0) {
            echo $object_medium. ' ';
        }
        if (strlen(trim($object_dimensions))>0) {
            echo '('.$object_dimensions.')';
        }
        if ((strlen(trim($creator))>0) || (strlen(trim($year))>0) || (strlen(trim($object_medium))>0) || (strlen(trim($object_dimensions))>0)) {
            echo '<br>';
        }

        if (strlen(trim($url))>0) {
           echo '<span class="tip">Website: </span><a href="'.$url.'" target=“_blank”>'.$url.'</a><br>';
        }

        if ((strlen(trim($city))>0) || (strlen(trim($state))>0) || (strlen(trim($nation))>0)) {
            echo '<span class="tip">Location: </span>';
        }

        if (strlen(trim($city))>0) {
            echo $city;
            if ((strlen(trim($state))>0) || (strlen(trim($nation))>0)) {
                echo ', ';
            } else {
                echo '<br>';
            }
        }
        if (strlen(trim($state))>0) {
            echo $state;
            if (strlen(trim($nation))>0) {
                echo ', '.$nation.'<br>';
            } else {
                echo '<br>';
            }
        } else {
            if (strlen(trim($nation))>0) {
                echo $nation.'<br>';
            }
        }

        if (strlen(trim($collection))>0) {
          echo '<span class="tip">Collection: </span>'.$collection.'';
          echo '<br>';
        }
        
        if (strlen(trim($citation))>0) {
          echo '<span class="tip">Citation: </span>'.$citation.'';
          echo '<br>';
        }
        
        if (strlen(trim($category1))>0) {
            if ((strlen(trim($category2))==0) && (strlen(trim($category3))==0) && (strlen(trim($category3))==0)) { 
                if (strlen(trim($category1))>0) {
                  echo '<span class="tip">Category: </span>'.$category1.'';
                }
            } else {
                echo '<span class="tip">Categories: </span>'.$category1;
                if (strlen(trim($category2))>0) {
                  echo ', '.$category2;
                }
                if (strlen(trim($category3))>0) {
                  echo ', '.$category3;
                }
                if (strlen(trim($category4))>0) {
                  echo ', '.$category4;
                }
            }
            echo '<br>';
        }

        if (strlen(trim($time_period))>0) {
          echo '<span class="tip">Time period: </span>'.$time_period.'';
          echo '<br>';
        }
        
        if (strlen(trim($taxon_common_name))>0) {
          echo '<span class="tip">Taxon common name: </span>'.$taxon_common_name.'';
          echo '<br>';
        }
        if (strlen(trim($taxon_order))>0) {
          echo '<span class="tip">Taxon order: </span>'.$taxon_order.'';
          echo '<br>';
        }
        if (strlen(trim($taxon_family))>0) {
          echo '<span class="tip">Taxon family: </span>'.$taxon_family.'';
          echo '<br>';
        }
        if (strlen(trim($taxon_species))>0) {
          echo '<span class="tip">Taxon species: </span>'.$taxon_species.'';
          echo '<br>';
        }
        
        //if (strlen(trim($permission_information))>0) {
        //  echo '<span class="tip">File permission: </span>'.$permission_information.'';
        //}
        
        echo '</div>';

        // Show files if images
        $filenames = array($filename1, $filename2, $filename3, $filename4, $filename5, $filename6, $filename7, $filename8, $filename9, $filename10);
        for ( $i = 0; $i <=9; $i += 1) {
            $filename = $filenames[$i];
            if (strlen($filename) > 0) {
                $path_parts = pathinfo($filename);
                $extension = $path_parts['extension'];

                // If the file has an image extension
                if ( in_array($extension, $all_image_extensions) ) {
                    $filestem = $path_parts['filename'];
                    $new_file = $filestem.'.'.$converted_image_extension;

                    // Show converted image if it exists
                    if (file_exists($converted_images_path.'/'.$new_file)) {
                        echo '<a href="'.$converted_images_path.'/'.$new_file.'" target="_blank"><img src="'.$converted_images_path.'/'.$new_file.'" width="480" border="0"><br></a><span class="font80">'.$converted_images_path.'/'.$new_file.'</span><br><br>';

                    // Show original image if it wasn't converted
                    } else {
                        echo '<a href="'.$raw_files_path.'/'.$filename.'" target="_blank"><img src="'.$raw_files_path.'/'.$filename.'" width="480" border="0"><br></a><span class="font80">'.$raw_files_path.'/'.$filename.'</span><br><br>';
                    }

                // If not an image, show a link to the file
                } else {
                    echo '<br><span class="tip">File: </span><a href="'.$raw_files_path.'/'.$filename.'" target="_blank">'.$raw_files_path.'/'.$filename.'</a><br><br>';
                }
            }
        }
        echo '<span class="idfont">#'.$object_ID.'</span><br>';
    }
  }


  echo '</div>';
  
  // Footer
  include_once("./shared/footer.php"); 

?>
