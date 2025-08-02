<?php
session_start();
//Check the user is a logged
if(!empty($_SESSION['user_id'])){

  if($_SESSION['user_role'] == 'clinic-manager'){     # if user is a clinic manager 
    header("Location: dashboard/clinic-manager/");
    exit();
  }
}



?>