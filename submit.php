<?php
// submit.php is a form to enter information (includes submit_info.php).
//
// Website by Arno Klein arno@binarybottle.com . 2016-2017 . Apache v2.0 license
//
 include_once("../db/culturalentomology_db.php");
 include_once("shared/header.html");
 include_once("shared/banner.html");

 $title="Insects Incorporated: Cultural Entomology Database";
 $debug = 0;

// Determine if the submit button has been clicked. 
// If so, begin validating form data.
// After http://msconline.maconstate.edu/Tutorials/PHP/PHP07/php07-02.php
if ($_POST['submitForm'] == "Submit") {

    // Determine if all fields were entered
    $valid_form = true;

    // Debugging:
    if ($debug==0) {
        if ($_POST['submit_first'] == "") {
            $valid_form = false;
            echo '<span class="redfont">&nbsp;
                    <i>Your <b>first name</b> is missing.</i></span><br />';
        }
        else {
            $submit_first = $_POST['submit_first'];
//            $submit_first = trim(mysql_real_escape_string($submit_first));
            $submit_first = trim($submit_first);
        }
    
        if ($_POST['submit_last'] == "") {
            $valid_form = false;
            echo '<span class="redfont">&nbsp;
                    <i>Your <b>last name</b> is missing.</i></span><br />';
        }
        else {
            $submit_last = $_POST['submit_last'];
            $submit_last = trim($submit_last);
//            $submit_last = trim(mysql_real_escape_string($submit_last));
        }
    
        if ($_POST['submit_email'] == "") {
            $valid_form = false;
            echo '<span class="redfont">&nbsp;
                    <i>Your <b>email address</b> is missing.</i></span><br />';
        }
        else {
            $submit_email = trim($_POST['submit_email']);
            if(!checkEmail($submit_email)) { 
                $valid_form = false;
                echo '<span class="redfont">&nbsp;
                      <i>Your <b>email address</b> is invalid.</i></span><br />';
            }
            else {
//                $submit_email = trim(mysql_real_escape_string($submit_email));
                $submit_email = trim($submit_email);
            }
        }
        $debug = $_POST['debug'];
    }

    // If all form fields were submitted properly, begin processing
    if($valid_form == false) {
        echo '<br /><span class="redfont">
              Please hit the "Back" button on your browser 
              to complete the Submission form.
              </span><br />';
        die;
    }
    else {
        @session_start();
        if ($debug==0) {
            if(($_SESSION['security_code'] == $_POST['security_code']) && (!empty($_SESSION['security_code'])) ) {
                // Insert your code for processing the form here, e.g emailing the submission, entering it into a database. 
                include_once("submit_info.php");
                die;
                unset($_SESSION['security_code']);
            } else {
                // Insert your code for showing an error message here
                echo '<br /><span class="redfont">';
                echo 'You have typed in the security text incorrectly.<br /><br /> 
                      Please hit the "Back" button on your browser and try again.';
                echo '</span><br />';
                die;
            }
        } else {
            include_once("submit_info.php");
            die;
            unset($_SESSION['security_code']);
        }
    }
}  // if ($_POST['Submit'] == "Submit")

?>

<div class="main">

<h1>Contribute to the database!</h1>

<form method="post" action="submit.php" enctype="multipart/form-data">

  <p>You can help us expand the database with well-annotated examples of cultural entomology. Please type in your name and email address to register as a contributor:</p> 

  <table border="0" cellspacing="0" cellpadding="5">
    <input name="date" type="hidden" id="date" value='<? print date("m.d.y");  ?>' />
    <tr>
      <td align="right" class="art_display_info">First name:</td>
      <td>
        <input name="submit_first" type="text" id="submit_first" />
      </td>
    </tr>
    <tr>
      <td align="right">Last name:</td>
      <td>
        <input name="submit_last" type="text" id="submit_last" />
      </td>
    </tr>
    <tr>
      <td align="right">Email:</td>
      <td align="left">
        <input name="submit_email" type="text" id="submit_email" />
      </td>
    </tr>
  </table>
  <br>
  <img src="shared/captcha/CaptchaSecurityImages.php?width=100&height=40&characters=4" />
  <br>
  <input id="security_code" name="security_code" type="text" />
  <!--br>
  Type the numbers in the box and show us you're not a robot. Refresh your browser if you have difficulty seeing them.-->
  <br><br>
  <input type="submit" name="submitForm" value="Submit" />
  <input type="hidden" name="first_submission" value="1">
  <input type="hidden" name="debug" value="<?php echo $debug; ?>">
</form>

</div>

<?php
  include_once("shared/footer.php");
  function checkEmail($submit_email) {
     if(preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-])+$/", $submit_email)){
        list($username,$domain)=split('@',$submit_email);
        if(!checkdnsrr($domain,'ANY')) {
           return false;
        }
        return true;
      }
      return false;
  }
?>