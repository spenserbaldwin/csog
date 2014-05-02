<?php

if (isset($_POST['deleteConfirm']) && $_POST['deleteConfirm'] == 1)
{
  $sql = "SELECT id FROM projects WHERE id = ".$_POST['projectID']." && users_id = ". $_SESSION['USER']['ID'];
  $result = $database->query($sql);
  if($result->num_rows == 1)
  {
    $delete = $result->fetch_assoc();
    $sql = "DELETE FROM projects WHERE id = ". $delete['id'];
    $database->query($sql);
  }
}


if (isset($_POST['createProjectSubmit']) && $_POST['createProjectSubmit'] == 1)
{
  $projectName = $_POST['projectName'];
  $date = $_POST['date'];
  $parcelIDNumber = $_POST['parcelIDNumber'];
  $projectAddress = $_POST['projectAddress'];
  $city = $_POST['projectCity'];
  $state = $_POST['projectState'];
  $zip = $_POST['projectZip'];
  $gps = $_POST['gps'];
  $latitude = $_POST['latitude'];
  $longitude = $_POST['longitude'];
  $systemStreetAddress = $_POST['systemStreetAddress'];
  $webAddress = $_POST['webAddress'];
  $other = $_POST['other'];
  $mailingAddress = $_POST['mailingAddress'];
  $userID = $_SESSION['USER']['ID'];
  $image = "";
  $easement_description = $_POST['easement_description'];
  $contact_phone = $_POST['phone_number'];
  $contact_name = $_POST['name'];
  $contact_address = $_POST['contact_address'];
  $contact_city = $_POST['contact_city'];
  $contact_state = $_POST['contact_state'];
  $contact_zip = $_POST['contact_zip'];
  $contact_email = $_POST['email'];
  $easement = $_POST['easement'];

  $allowedExts = array("gif", "jpeg", "jpg", "png");
  $temp = explode(".", $_FILES["projectLogo"]["name"]);
  $extension = end($temp);
  if ((($_FILES["projectLogo"]["type"] == "image/gif")
    || ($_FILES["projectLogo"]["type"] == "image/jpeg")
    || ($_FILES["projectLogo"]["type"] == "image/jpg")
    || ($_FILES["projectLogo"]["type"] == "image/pjpeg")
    || ($_FILES["projectLogo"]["type"] == "image/x-png")
    || ($_FILES["projectLogo"]["type"] == "image/png"))
    && ($_FILES["projectLogo"]["size"] < 2000000)
    && in_array($extension, $allowedExts))
  {
    if ($_FILES["projectLogo"]["error"] > 0)
    {
    } else
    {
      $filenamesave = uniqid(time()) . "." . $extension; 
      move_uploaded_file($_FILES["projectLogo"]["tmp_name"],FS_PATH . "media/uploads/" . $filenamesave);
      $image = $filenamesave;
    }
  }
  if(isset($_POST['editProjectSubmit']) && $_POST['editProjectSubmit'] != 0)
  {
    if($image == "")
    {
      $image = $_POST['defaultFile'];
    }
    $sql = "UPDATE projects SET name='$projectName', date='$date', parcelIDNumber='$parcelIDNumber', gps='$gps', latitude='$latitude', longitude='$longitude', systemStreetAddress='$systemStreetAddress', webAddress='$webAddress', other='$other', mailingAddress='$mailingAddress', file='$image', projectAddress='$projectAddress', city='$city', state='$state', zip='$zip', contact_name='$contact_name',contact_phone='$contact_phone',contact_address='$contact_address',contact_city='$contact_city',contact_state='$contact_state',contact_zip='$contact_zip',easement_desc='$easement_description', contact_email='$contact_email', easement='$easement' WHERE id=".$_POST['editProjectSubmit'];
    $database->query($sql);
  } else
  {
    $sql = "INSERT INTO projects (name, date, parcelIDNumber, gps, latitude, longitude, systemStreetAddress, webAddress, other, mailingAddress, users_id, file, projectAddress, city, state, zip, easement_desc, contact_phone, contact_name, contact_address, contact_city, contact_state, contact_zip, contact_email, easement) VALUES ('$projectName','$date','$parcelIDNumber','$gps','$latitude','$longitude','$systemStreetAddress','$webAddress','$other','$mailingAddress','$userID','$image','$projectAddress','$city','$state','$zip','$easement_description','$contact_phone','$contact_name','$contact_address','$contact_city','$contact_state','$contact_zip','$contact_email','$easement')";
    $database->query($sql);
  }

}

?>
<div id="projects_table">
  <h2>Welcome <?php echo $_SESSION['USER']['Name']; ?> from <?php echo $_SESSION['USER']['CompanyName']; ?></h2>
  <a href="<?php echo WS_URL . 'account/edit/' ?>">Edit your profile</a><br /><br /><br />
<?php
$sql = "SELECT * FROM projects WHERE users_id = '".$_SESSION['USER']['ID']."'";
$DBresult = $database->query($sql);

if ($DBresult->num_rows)
{
  ?>
  <h2>Your CSOG projects are:</h2>
  <ul>
  <?php 
  while ($projectData = $DBresult->fetch_assoc())
  {
    if( $projectData['name'] != '' ){
      $projectName = $projectData['name'];
    } else {
      $projectName = 'Project '. $projectData['id'];
    }
    
    $projectDate = $projectData['date'];
    $startPage = "organization";

    if($projectDate == "")
    {
      $projectDate = "Never Edited";
    } else {
      $projectDate = date("d/m/Y", strtotime($projectDate));
    } 

    echo '<li><a href="' . WS_URL . 'projects/view/' . $projectData['id'].'"' ;
    //echo $startPage;
    echo ' class="projects_tableProjectName">' . $projectName . '</a> (' . $projectDate . ') | <a href="' . WS_URL . 'projects/clone/'.$projectData['id'].'/">Clone</a> | <a href="' . WS_URL . 'projects/edit/'.$projectData['id'].'/">Edit</a> | <a href="' . WS_URL . 'projects/delete/' . $projectData['id'] . '/">Delete</a></li>';
  }
  ?>
  </ul>
  <?php
} else {
  echo '<h2>You have no projects.</h2>';
}
?>
  <br /><br />
  <h2>Would you like to <a href="<?php echo WS_URL?>projects/new/">create a new project?</a></h2>
  <br /><br /><br />
  <h2>Contact a Site Administrator</h2>
  <form id="contact-form-projects" method="post">
    <div id="contact-form-name-div"><label for="name">Name</label><input type="text" id="contact-form-name" name="name" value="<?php echo $_SESSION['USER']['Name']; ?>"/></div>
    <div id="contact-form-email-div"><label for="email">Email</label><input type="email" id="contact-form-email" name="email" value="<?php echo $_SESSION['USER']['Email']; ?>"/ /></div>
    <div id="contact-form-message-div"><label for="message">Message</label><textarea name="message" id="projects_message"></textarea></div>
    <input type="submit" value="Send Email" id="projects_submit" class="form-button" />
  </form>
</div>
