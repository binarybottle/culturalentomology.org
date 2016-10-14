<?php
  // submit_info.php is a form for a potential user to upload and annotate files 
  //
  // Website by Arno Klein arno@binarybottle.com . 2016 . Apache v2.0 license
  //
  include_once("../db/culturalentomology_db.php");
  include_once("shared/header.html");
  include_once("shared/banner.html");

  $first_submission = $_POST['first_submission'];
  $debug = $_POST['debug'];
  $entry_date = date("Ymd");
  $entry_time = time();


  if ($debug==1) {
      $submit_first = "Arno";
      $submit_last  = "Klein";
      $submit_email = "arno@binarybottle.com";
      $userID       = "2";
  } else {
      $submit_first = $_POST['submit_first'];
      $submit_last  = $_POST['submit_last'];
      $submit_email = $_POST['submit_email'];
      $userID       = $_POST['userID'];
  }

  if (strlen($submit_first)==0 && strlen($submit_last)==0) {
      include_once("shared/header.html");
      include_once("shared/banner.html"); 
      echo '<div class="main"><br /><br /><span class="redfont"><p>
      Please type in a correct address '.$debug.'.
      </p></span></div>';
      die;
  }

  // Insert a new (unregistered) user into the database
  $sql_isuser = 'SELECT pk_user_id FROM users
                WHERE user_name_first="'.$submit_first.'" AND user_name_last="'.$submit_last.'"';
  $result_isuser = mysql_query($sql_isuser) or die (mysql_error());
  $num_isuser    = mysql_num_rows($result_isuser);
  // ...if it is a new user (unmatched first, last)
  if ($num_isuser==0) {
      $sql_user = 'INSERT INTO users (user_email, user_name_first, user_name_last, user_indate, user_registered)
                   VALUES ("'.$submit_email.'","'.$submit_first.'","'.$submit_last.'","'.$entry_date.'","0")'; //,"'.$.'")';
      mysql_query($sql_user) or die (mysql_error());
      
      // Get the new user's ID
      $sql_userID    = 'SELECT pk_user_id FROM users
                        WHERE user_name_first="'.$submit_first.'" AND user_name_last="'.$submit_last.'"';
      $result_userID = mysql_query($sql_userID) or die (mysql_error());
      $row_userID    = mysql_fetch_row($result_userID);
      $userID        = $row_userID[0];
  }
  // ...or if it is an existing user
  else {
      // Get the new user's ID
      $row_userID = mysql_fetch_row($result_isuser);
      $userID     = $row_userID[0];
  }

  $width = 1800;

  //---------------------
  // Permitted file types
  //---------------------
  $max_uploads = 10; // Maximum number of files per object
  $max_size = 20000000; // Maximum image size
  $max_size_string   = '20MB';
  
  $allowedExts = array(
    "bmp",
    "gif",
    "jpg",
    "jpeg",
    "pjpeg",
    "png",
    "tif",
    "tiff",
    "pdf",
    "flac",
    "mpa",
    "mp2",
    "mp3",
    "mp4",
    "mpg",
    "mpeg",
    'mov',
    "wav",
    "BMP",
    "GIF",
    "JPG",
    "JPEG",
    "PJPEG",
    "PNG",
    "TIF",
    "TIFF",
    "PDF",
    "FLAC",
    "MPA",
    "MP2",
    "MP3",
    "MP4",
    "MPG",
    "MPEG",
    'MOV',
    "WAV"
  ); 
  
  $allowedMimeTypes = array( 
    'image/bmp',
    'image/gif',
    'image/jpg',
    'image/jpeg',
    'image/pjpeg',
    'image/png',
    'image/tiff',
    'image/x-tiff',
    'application/pdf',
    'audio/flac',
    'audio/mpg',
    'audio/mp3',
    'audio/mp4',
    'audio/mpeg',
    'audio/mpeg3',
    'audio/x-mpeg-3',
    'video/mpg',
    'video/mp3',
    'video/mp4',
    'video/mpeg',
    'video/x-mpeg',
    'video/quicktime',
    'audio/wav',
    'audio/x-wav'
  );

?>

<?php
  //-------------------------------
  // Populate form's dropdown lists
  //-------------------------------
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
  $sql_order_menu    = "SELECT pk_order_id, sort_order_id, taxon_order FROM taxon_orders ORDER BY sort_order_id";
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
?>

<?php
  //------------------------------------------------------------------------------------
  // Post form
  // (Adapted from code downloaded from www.plus2net.com and 
  //  http://stackoverflow.com/questions/11601342/upload-doc-or-pdf-using-php)
  //------------------------------------------------------------------------------------
  if ($_POST['submitForm'] == "SUBMIT")
  {

    //----------------------------------------------------------------------------------
    // Find next pk_object_id
    //----------------------------------------------------------------------------------
    $sql_max_id = "SELECT max(pk_object_id) as num FROM objects";
    $maxquery = mysql_query($sql_max_id) or die (mysql_error());
    while($row = mysql_fetch_assoc($maxquery)) {
        $next_id = $row['num'] + 1;
    }

    //----------------------------------------------------------------------------------
    // Upload each file after checking size, extension, and format
    //----------------------------------------------------------------------------------
    $filenames = array();
    while(list($key2,$value2) = each($_FILES["files"]["name"]))
    {
      if(!empty($value2)) {
          $file_type = $_FILES["files"]["type"][$key2]; 
          $file_size = $_FILES["files"]["size"][$key2]; 
          $file_tmp_name = $_FILES['files']['tmp_name'][$key2];
          $extension = end(explode(".", $value2));
      
          // File size error
          if ( $max_size < $file_size ) {
              include_once("shared/header.html");
              include_once("shared/banner.html"); 
              echo '<br /><span class="redfont"><p>'.$file_size . ' is too large.</p><p>
              Please hit the back button on your browser and upload smaller files.
              </p></span>';
              die;
          }
      
          // File extension error
          if ( ! ( in_array($extension, $allowedExts ) ) ) {
              include_once("shared/header.html");
              include_once("shared/banner.html"); 
              echo '<br /><span class="redfont"><p>
              Please hit the back button on your browser and upload appropriate file types 
              ('.$extension.' is not a valid extension).
              </p></span>';
              die;
          }
    
          // File format error
          if ( ! ( in_array( $file_type, $allowedMimeTypes ) ) ) 
          {      
              include_once("shared/header.html");
              include_once("shared/banner.html"); 
              echo '<br /><span class="redfont"><p>
              Please hit the back button on your browser and upload appropriate file formats
              ('.$file_type.' is not a valid format).
              </p></span>';
              die;
          }

          // Copy files to image upload directory (with new, unique names)
          else
          {
              $filename     = $value2;
              $time_now     = time();
              $uniq_index   = $key2+1;
              //$new_filename = $submit_last.'_'.$time_now.'_'.$uniq_index.'_'.$filename;
              $new_filename = $next_id.'_'.$uniq_index.'_'.$time_now.'_'.$submit_last.'_'.$filename;
              $new_filepath = $submissions_path."/".$new_filename;
              $filenames[] = $new_filename;
              move_uploaded_file($file_tmp_name, $new_filepath);  
          }
      }
    }

    //--------------------
    // Prepare SQL command
    //--------------------
    $title = $_POST[title];
    $category1 = $_POST[category1];
    $category2 = $_POST[category2];
    $category3 = $_POST[category3];
    $category4 = $_POST[category4];
    $creator = $_POST[creator];
    $year = $_POST[year];
    $object_medium = $_POST[object_medium];
    $object_dimensions = $_POST[object_dimensions];
    $time_period = $_POST[time_period];
    $nation = $_POST[nation];
    $state = $_POST[state];
    $city = $_POST[city];
    $taxon_common_name = $_POST[taxon_common_name];
    $taxon_order = $_POST[taxon_order];
    $taxon_family = $_POST[taxon_family];
    $taxon_species = $_POST[taxon_species];
    $url = $_POST[url];
    $collection = $_POST[collection];
    $citation = $_POST[citation];
    $description = $_POST[description];
    $comments = $_POST[comments];
    $permission_information = $_POST[permission_information];

    $title = trim(mysql_real_escape_string($title));
    $category1 = trim(mysql_real_escape_string($category1));
    $category2 = trim(mysql_real_escape_string($category2));
    $category3 = trim(mysql_real_escape_string($category3));
    $category4 = trim(mysql_real_escape_string($category4));
    $creator = trim(mysql_real_escape_string($creator));
    $year  = trim(mysql_real_escape_string($year));
    $object_medium = trim(mysql_real_escape_string($object_medium));
    $object_dimensions = trim(mysql_real_escape_string($object_dimensions));
    $time_period = trim(mysql_real_escape_string($time_period));
    $nation = trim(mysql_real_escape_string($nation));
    $state = trim(mysql_real_escape_string($state));
    $city = trim(mysql_real_escape_string($city));
    $taxon_common_name = trim(mysql_real_escape_string($taxon_common_name));
    $taxon_order = trim(mysql_real_escape_string($taxon_order));
    $taxon_family = trim(mysql_real_escape_string($taxon_family));
    $taxon_species = trim(mysql_real_escape_string($taxon_species));
    $url = trim(mysql_real_escape_string($url));
    $collection = trim(mysql_real_escape_string($collection));
    $citation = trim(mysql_real_escape_string($citation));
    $description = trim(mysql_real_escape_string($description));
    $comments = trim(mysql_real_escape_string($comments));
    $permission_information = trim(mysql_real_escape_string($permission_information));

    //----------------
    // Run SQL command
    //----------------
    $sql = 'INSERT INTO objects (pk_object_id, filename1, filename2, filename3, filename4, filename5, filename6, filename7, filename8, filename9, filename10, entry_date, entry_time, registered, fk_user_id, title, category1, category2, category3, category4, creator, year, object_medium, object_dimensions, time_period, nation, state, city, taxon_common_name, taxon_order, taxon_family, taxon_species, url, collection, citation, description, comments, permission_information)
            VALUES ("'.$next_id.'","'.$filenames[0].'","'.$filenames[1].'","'.$filenames[2].'","'.$filenames[3].'","'.$filenames[4].'","'.$filenames[5].'","'.$filenames[6].'","'.$filenames[7].'","'.$filenames[8].'","'.$filenames[9].'","'.$entry_date.'","'.$entry_time.'","0","'.$userID.'","'.$title.'","'.$category1.'","'.$category2.'","'.$category3.'","'.$category4.'","'.$creator.'","'.$year.'","'.$object_medium.'","'.$object_dimensions.'","'.$time_period.'","'.$nation.'","'.$state.'","'.$city.'","'.$taxon_common_name.'","'.$taxon_order.'","'.$taxon_family.'","'.$taxon_species.'","'.$url.'","'.$collection.'","'.$citation.'","'.$description.'","'.$comments.'","'.$permission_information.'")';

    echo $sql_image.'<br>';
    mysql_query($sql) or die (mysql_error());

  }  // if ($_POST['submitForm'] == "SUBMIT")
?>

<div class="main">

<?php
  //------------------------------------------------------------------------------------
  // Message for first submission
  //------------------------------------------------------------------------------------
  if ($first_submission==1) {
      echo '<h1>Submit a cultural entomology object!</h1>
      <div class="textblocks">
      <p>This page allows you to submit files and any information you can related to a single cultural entomology object.  Any files you submit will be presented in the order you upload them (this can be useful for sequential pages of a book, for example).  Acceptable file formats include jpg, gif, bmp, png, tiff, pdf, mpa, mp3, mov, and wav, and must be no larger than '.$max_size_string. ' each.
      </p>
      <p>We will review your submission for possible inclusion in the database and website. Content you submit to the database should not contain third party copyrighted material, or material that is subject to other third party proprietary rights, unless you have permission.</p>
      </div>';
      $first_submission = 0;
  //------------------------------------------------------------------------------------
  // Message for subsequent submissions and review email
  //------------------------------------------------------------------------------------
  } else {
      echo '<h1>Thank you!  Your submission will be reviewed shortly.</h1>
      <h2>Please feel free to submit another object:</h2>';

      $review_link  = 'http://culturalentomology.org/submit_review.php?userID='.$userID.
                      '&firstname='.$submit_first.'&lastname='.$submit_last.'&email='.$submit_email.
                      '&entry_time='.$entry_time;
      $mail_to      = $admin_email;
      $mail_from    = 'From: Insects Incorporated database of cultural entomology';
      $mail_subject = 'Submission to the Insects Incorporated database of cultural entomology';
      $mail_body    = "
      
                       New submission for review!
      
                       $review_link
      
                       Name:  $submit_first $submit_last (ID: $userID)
                       Email: $submit_email
      
                      ";
      
      if (!mail($mail_to, $mail_subject, $mail_body, $mail_from)) {
        echo '<p><span class="redfont">Notification message delivery failed. Please contact barrett[at]pupating.org.</span></p><br /><br />';
      }
  }
?>

<div class="textblocks">
<hr size="1" />

<?php
  
  //--------------------------------------------
  // Submission form
  //--------------------------------------------
  echo '<form method="post" action="submit_info.php" enctype="multipart/form-data">';

  // Files
  echo "<table border='0' width='".$width."' cellspacing='5' cellpadding='0' align='left'>";
  echo 'Upload up to '.$max_uploads.' files for this object; select an image for the first to present on the website:';
  for($i=1; $i<=$max_uploads; $i++) {
      echo "<tr><td>&nbsp;&nbsp;&nbsp;$i</td><td>
           <input type=file name='files[]' class='bginput' size='50'></td></tr>";
  }
  echo '</td></tr></table></div>';
  echo '<hr size="1" />';
  echo '<div class="textblocks">';

  // Primary category
  echo 'Category:';
  echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
  echo '<select name="category1">';
  echo '<option disabled selected value> -- select category -- </option>';
  for ($inum_categories=0; $inum_categories<$num_categories; $inum_categories++) {
      $selected = '';
      //$category_id_category_menu = $categories[$inum_categories][0];
      $category = $categories[$inum_categories][1];
      //echo '<option value="'.$category_id_category_menu.'" '.$selected.'>'.
      //                       $category.'</option>';
      echo '<option value="'.$category.'" '.$selected.'>'.$category.'</option>';
  }
  echo '</select>';

  // Additional categories
  echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
  for ($icategory=2; $icategory<=4; $icategory++) {
      echo '<select name="category'.$icategory.'">';
      echo '<option disabled selected value> ----- another? ----- </option>';
      for ($inum_categories=0; $inum_categories<$num_categories; $inum_categories++) {
          $selected = '';
          //$category_id_category_menu = $categories[$inum_categories][0];
          $category = $categories[$inum_categories][1];
          //echo '<option value="'.$category_id_category_menu.'" '.$selected.'>'.
          //                       $category.'</option>';
          echo '<option value="'.$category.'" '.$selected.'>'.$category.'</option>';
      }
      echo '</select>';
  }
  echo '<hr size="1" />';

  // Title
  echo 'Title:';
  echo '&nbsp;&nbsp;';
  echo '<input type="text" size="66" name="title">';
  echo '<br><br>';

  // Creator, year, medium, dimensions
  echo 'Author / artist / musician (creator):';
  echo '&nbsp;&nbsp;';
  echo '<input type="text" size="66" name="creator">';
  echo '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
  
  echo 'Year (YYYY):';
  echo '&nbsp;&nbsp;';
  echo '<input type="text" size="4" name="year">';
  echo '&nbsp;&nbsp;';
  
  echo 'Medium:';
  echo '&nbsp;&nbsp;';
  echo '<input type="text" size="12" name="object_medium">';
  echo '&nbsp;&nbsp;';
  
  echo 'Dimensions:';
  echo '&nbsp;&nbsp;';
  echo '<input type="text" size="12" name="object_dimensions">';
  echo '<br><br>';
  
  
  // Time periods
  echo 'Subject time period:';
  echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
  echo '<select name="time_period">';
  echo '<option disabled selected value> ------- select time period ------- </option>';
  for ($inum_periods=0; $inum_periods<$num_periods; $inum_periods++) {
      if ($time_periods[$inum_periods][0]==$iperiod_selected) {
          $selected = 'selected';
      } else {
          $selected = '';
      }
      //$period_id_period_menu = $time_periods[$inum_periods][0];
      $time_period = $time_periods[$inum_periods][1];
      //echo '<option value="'.$period_id_period_menu.'" '.$selected.'>'.$time_period.'</option>';
      echo '<option value="'.$time_period.'" '.$selected.'>'.$time_period.'</option>';
  }
  echo '</select>';
  echo '<br><br>';

  // Nation, state, city, name, taxon, website, collection, citation, description, comments, permission
  echo 'Nation: <input type="text" size="15" name="nation">&nbsp;&nbsp;';
  echo 'State/Province: <input type="text" size="15" name="state">&nbsp;&nbsp;';
  echo 'City: <input type="text" size="15" name="city"><br><br>';
  echo 'What insect is featured? &nbsp; Common name:';
  echo '&nbsp;&nbsp;';
  echo '<input type="text" size="30" name="taxon_common_name">';
  echo '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
  
  echo 'Taxon order:';
  echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
  echo '<select name="taxon_order">';
  echo '<option disabled selected value> ------------------- select taxon order ------------------- </option>';
  for ($inum_orders=0; $inum_orders<$num_orders; $inum_orders++) {
      if ($taxon_orders[$inum_orders][0]==$iorder_selected) {
               $selected = 'selected';
      } else { $selected = '';
      }
      //$order_id_order_menu = $taxon_orders[$inum_orders][0];
      $taxon_order         = $taxon_orders[$inum_orders][1];
      //echo '<option value="'.$order_id_order_menu.'" '.$selected.'>'.
      echo '<option value="'.$taxon_order.'" '.$selected.'>'.
                             $taxon_order.'</option>';
  }
  echo '</select>';
  echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
  
  echo 'Taxon family (e.g., Apidae):';
  echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
  echo '<input type="text" size="46" name="taxon_family">';
  echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
  
  echo 'Taxon species (e.g., <i>Apis mellifera</i>):';
  echo '&nbsp;&nbsp;';
  echo '<input type="text" size="46" name="taxon_species">';
  echo '<br><br>';
  
  echo 'Website:<br>';
  echo '<input type="text" size="66" name="url">';
  echo '<br><br>';
  
  echo 'Collection (private/gallery/museum/Internet):<br>';
  echo '<input type="text" size="66" name="collection">';
  echo '<br><br>';
  
  echo 'Citation (for publications):<br>';
  echo '<textarea cols="64" rows="1" name="citation"></textarea>';
  echo '<br><br>';
  
  echo 'Description (text/html):<br>';
  echo '<textarea cols="64" rows="2" name="description"></textarea>';
  echo '<br><br>';
  
  echo 'Comments (will not be visible on the website):<br>';
  echo '<textarea cols="64" rows="2" name="comments"></textarea>';
  echo '<br><br>';
  
  echo 'Permission (license/sharing information):<br>';
  echo '<textarea cols="64" rows="2" name="permission_information"></textarea>';
  echo '<br><br>';
  
  
  // Line & buttons
  echo '<hr size="1" />';
  $spc1 = '<br>';
  $spc2 = '&nbsp;&nbsp;&nbsp;&nbsp;';
  echo $spc1.'<input type="submit"  name="submitForm" value="SUBMIT" />';
  //echo $spc2.'<input type="reset"  value="CLEAR"  />';
  echo '<br><br><br><br>';
  echo '</div>';
  echo '</td></tr></table>';
  
  echo '<input type="hidden" name="submit_first"     value="'.$submit_first    .'">';
  echo '<input type="hidden" name="submit_last"      value="'.$submit_last     .'">';
  echo '<input type="hidden" name="submit_email"     value="'.$submit_email    .'">';
  echo '<input type="hidden" name="userID"           value="'.$userID          .'">';
  echo '<input type="hidden" name="first_submission" value="'.$first_submission.'">';
  echo '<input type="hidden" name="entry_time"       value="'.$entry_time      .'">';
  echo '<input type="hidden" name="debug"            value="'.$debug           .'">';

  echo '</form>';
  echo '</div>';
  
  include_once("./shared/footer.php");

?>