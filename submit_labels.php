<?php
// submit_labels.php is a form for a potential user to label uploaded images 
// and sends this information to submit_pending.php (called by submit_images.php).
//
// Website by Arno Klein arno@binarybottle.com . 2016 . Apache v2.0 license
//
include_once("../db/culturalentomology_db.php");
include_once("shared/header.html");
include_once("shared/banner.html");

?>

<div class="main">

<h1>Submissions: Label images</h1>

<div class="textblocks">

<p>Please annotate the images you uploaded with detailed information.<br>
When finished, click the "Submit" button.
</p>
</div>
<br>
<?php

   // Search for all unregistered submissions by user
      $sql_submissions    = 'SELECT * FROM images
                             WHERE fk_user_id="'.$userID.
                             '" AND registered="0" AND hide="0"';
      $result_submissions = mysql_query($sql_submissions) or die (mysql_error());
   // Number of submissions
      $num_submissions = mysql_num_rows($result_submissions);
      if ($num_submissions==1) {
         echo '<span class="font80"><i>1 image submitted for review: </i></span><br>';
      }
      else {
         echo '<span class="font80"><i>'.$num_submissions . ' images submitted for review: </i></span><br>';
      }

   //------------------------
   // Populate dropdown lists
   //------------------------
   // Find all categories for a dropdown menu
      $sql_category_menu    = "SELECT pk_category_id, category FROM categories ORDER BY pk_category_id";
      $result_category_menu = mysql_query($sql_category_menu) or die (mysql_error());
      $num_categories       = mysql_num_rows($result_category_menu);
      $inum_categories      = 0;
      $categories           = array();
      while($row_category_menu = mysql_fetch_object($result_category_menu))
      {
            $categories[$inum_categories][0] = $row_category_menu->pk_category_id;
            $categories[$inum_categories][1] = $row_category_menu->category;
        $inum_categories = $inum_categories + 1;
      }

   // Find all time periods for a dropdown menu
      $sql_period_menu    = "SELECT pk_time_period_id, time_period FROM time_periods ORDER BY pk_time_period_id";
      $result_period_menu = mysql_query($sql_period_menu) or die (mysql_error());
      $num_periods        = mysql_num_rows($result_period_menu);
      $inum_periods       = 0;
      $time_periods       = array();
      while($row_period_menu = mysql_fetch_object($result_period_menu))
      {
            $time_periods[$inum_periods][0] = $row_period_menu->pk_time_period_id;
            $time_periods[$inum_periods][1] = $row_period_menu->time_period;
        $inum_periods = $inum_periods + 1;
      }

   // Find all taxon orders for a dropdown menu
      $sql_order_menu    = "SELECT pk_order_id, taxon_order FROM taxon_orders ORDER BY pk_order_id";
      $result_order_menu = mysql_query($sql_order_menu) or die (mysql_error());
      $num_orders        = mysql_num_rows($result_order_menu);
      $inum_orders       = 0;
      $taxon_orders      = array();
      while($row_order_menu = mysql_fetch_object($result_order_menu))
      {
            $taxon_orders[$inum_orders][0] = $row_order_menu->pk_order_id;
            $taxon_orders[$inum_orders][1] = $row_order_menu->taxon_order;
        $inum_orders = $inum_orders + 1;
      }

   //--------------------------------------------
   // Construct form with each submission's entry
   //--------------------------------------------
      echo '<form method="post" action="submit_pending.php" enctype="multipart/form-data">';

   // Line & buttons
      echo '<hr size="1" />';
      $spc1 = '&nbsp;&nbsp;';
      $spc2 = '&nbsp;&nbsp;&nbsp;&nbsp;';
      echo $spc1.'<input type="submit" value="Submit" />';
      echo $spc2.'<input type="reset"  value="Reset"  />';

   // Loop through submissions
      $irow=1;
      while($row = mysql_fetch_object($result_submissions))
      {
         $ID   = $row->pk_image_id;
         $image_filename = $row->image_filename;

      // Line
         echo '<hr size="1" />';

      // Image
         echo '<table width="900" border="0" cellspacing="0" cellpadding="10">';
         echo ' <tr>';
         echo '  <td width="240">';
         echo '   <img src="images/'.$image_filename. '" height="240">';
         echo '   <span class="font80">images/'.$image_filename.' ('.$ID.')</span>';        
         echo '  </td>';
         echo '  <td width="560">';

      // Input text
         echo '<div class="font80"><i>';
         echo '<input type="hidden" name="update_id'  .$irow.'" value="'.$ID.'"><br>';
         echo '<input type="hidden" name="update_image_filename'.$irow.'" value="'.$image_filename.'"><br>';

         echo 'Title: <br><input type="text" size="65" name="update_title'.$irow.'"><br><br>';


      // Primary category
         echo 'Primary category:';
         echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
         echo '<select name="update_primary_category'.$irow.'">';
         echo '<option disabled selected value> -- select category -- </option>';
         for ($inum_categories=0; $inum_categories<$num_categories; $inum_categories++) {
                    if ($categories[$inum_categories][0]==$icategory_selected) {
                     $selected = 'selected';
            } else { $selected = '';
            }
                    $category_id_category_menu = $categories[$inum_categories][0];
                    $category = $categories[$inum_categories][1];
            echo '<option value="'.$category_id_category_menu.'" '.$selected.'>'.
                                   $category.'</option>';
                 }
                 echo '</select>';
         echo '<br><br>';

      // Additional categories

         echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

           echo 'Another category?:';
           echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
           echo '<select name="update_category2'.$irow.'">';
           echo '<option disabled selected value> -- select category -- </option>';
           for ($inum_categories=0; $inum_categories<$num_categories; $inum_categories++) {
                      if ($categories[$inum_categories][0]==$icategory_selected) {
                       $selected = 'selected';
              } else { $selected = '';
              }
                      $category_id_category_menu = $categories[$inum_categories][0];
                      $category = $categories[$inum_categories][1];
              echo '<option value="'.$category_id_category_menu.'" '.$selected.'>'.
                                     $category.'</option>';
                   }
                   echo '</select>';
         echo '<br>';

         echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

           echo 'Another category?:';
           echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
           echo '<select name="update_category3'.$irow.'">';
           echo '<option disabled selected value> -- select category -- </option>';
           for ($inum_categories=0; $inum_categories<$num_categories; $inum_categories++) {
                      if ($categories[$inum_categories][0]==$icategory_selected) {
                       $selected = 'selected';
              } else { $selected = '';
              }
                      $category_id_category_menu = $categories[$inum_categories][0];
                      $category = $categories[$inum_categories][1];
              echo '<option value="'.$category_id_category_menu.'" '.$selected.'>'.
                                     $category.'</option>';
                   }
                   echo '</select>';
         echo '<br>';

         echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

           echo 'Another category?:';
           echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
           echo '<select name="update_category4'.$irow.'">';
           echo '<option disabled selected value> -- select category -- </option>';
           for ($inum_categories=0; $inum_categories<$num_categories; $inum_categories++) {
                      if ($categories[$inum_categories][0]==$icategory_selected) {
                       $selected = 'selected';
              } else { $selected = '';
              }
                      $category_id_category_menu = $categories[$inum_categories][0];
                      $category = $categories[$inum_categories][1];
              echo '<option value="'.$category_id_category_menu.'" '.$selected.'>'.
                                     $category.'</option>';
                   }
                   echo '</select>';
         echo '<br><br>';


      // More meta-data boxes
         echo 'Author / artist / musician (creator):';
         echo '&nbsp;&nbsp;';
         echo '<input type="text" size="66" name="update_creator'.$irow.'">';
         echo '<br><br>';

         echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

         echo 'Year (YYYY):';
         echo '&nbsp;&nbsp;';
         echo '<input type="text" size="4" name="update_year'.$irow.'">';
         echo '&nbsp;&nbsp;';

         echo 'Medium:';
         echo '&nbsp;&nbsp;';
         echo '<input type="text" size="12" name="update_object_medium'.$irow.'">';
         echo '&nbsp;&nbsp;';

         echo 'Dimensions:';
         echo '&nbsp;&nbsp;';
         echo '<input type="text" size="12" name="update_object_dimensions'.$irow.'">';
         echo '<br><br>';


      // Time periods
         echo 'Subject time period:';
         echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
         echo '<select name="update_time_period'.$irow.'">';
         echo '<option disabled selected value> ------- select time period ------- </option>';
         for ($inum_periods=0; $inum_periods<$num_periods; $inum_periods++) {
                    if ($time_periods[$inum_periods][0]==$iperiod_selected) {
                     $selected = 'selected';
            } else { $selected = '';
            }
            $period_id_period_menu = $time_periods[$inum_periods][0];
            $time_period = $time_periods[$inum_periods][1];
            echo '<option value="'.$period_id_period_menu.'" '.$selected.'>'.
                                   $time_period.'</option>';
            }
            echo '</select>';
         echo '<br><br>';


      // More meta-data boxes
         echo 'Nation: <input type="text" size="25" name="update_nation'.$irow.'">';
         echo '&nbsp;&nbsp;';

         echo 'City: <input type="text" size="27" name="update_city'.$irow.'"><br><br>';

         echo 'What insect is featured? &nbsp; Common name:';
         echo '&nbsp;&nbsp;';
         echo '<input type="text" size="30" name="update_taxon_common_name'.$irow.'">';
         echo '<br><br>';

         echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

           echo 'Taxon order:';
           echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
           echo '<select name="update_taxon_order'.$irow.'">';
           echo '<option disabled selected value> ------------------- select taxon order ------------------- </option>';
           for ($inum_orders=0; $inum_orders<$num_orders; $inum_orders++) {
                      if ($taxon_orders[$inum_orders][0]==$iorder_selected) {
                       $selected = 'selected';
              } else { $selected = '';
              }
              $order_id_order_menu = $taxon_orders[$inum_orders][0];
              $taxon_order         = $taxon_orders[$inum_orders][1];
              echo '<option value="'.$order_id_order_menu.'" '.$selected.'>'.
                                     $taxon_order.'</option>';
              }
              echo '</select>';

         echo '<br>';
         echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

           echo 'Taxon family:';
           echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
           echo '<input type="text" size="46" name="update_taxon_family'.$irow.'">';

         echo '<br>';
         echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

           echo 'Taxon species:';
           echo '&nbsp;&nbsp;';
           echo '<input type="text" size="46" name="update_taxon_species'.$irow.'">';

         echo '<br><br>';

         echo 'Website:<br>';
         echo '<input type="text" size="66" name="update_url'.$irow.'">';
         echo '<br><br>';

         echo 'Collection (private/gallery/museum/Internet):<br>';
         echo '<input type="text" size="66" name="update_collection'.$irow.'">';
         echo '<br><br>';

         echo 'Citation (for publications):<br>';
         echo '<textarea cols="64" rows="1" name="update_citation'.$irow.'"></textarea>';
         echo '<br><br>';

         echo 'Description (text/html):<br>';
         echo '<textarea cols="64" rows="2" name="update_description'.$irow.'"></textarea>';
         echo '<br><br>';

         echo 'Comments (will not be visible on the website):<br>';
         echo '<textarea cols="64" rows="2" name="update_comments'.$irow.'"></textarea>';
         echo '<br><br>';

         echo 'Permission (license/sharing information):<br>';
         echo '<textarea cols="64" rows="2" name="update_permission_information'.$irow.'"></textarea>';
         echo '<br><br>';

         echo '</div>';
         echo '   </td>';
         echo '  </tr>';

         echo ' </table>';

         $irow=$irow+1;

      } // while

      echo '<input type="hidden" name="submit_first"     value="'.$submit_first    .'">';
      echo '<input type="hidden" name="submit_last"      value="'.$submit_last     .'">';
      echo '<input type="hidden" name="submit_email"     value="'.$submit_email    .'">';
      echo '<input type="hidden" name="num_submissions"  value="'.$num_submissions .'">';
      echo '<input type="hidden" name="userID"           value="'.$userID          .'">';
      echo '</form>';

      echo '</div>';

      include_once("./shared/footer.php");

?>