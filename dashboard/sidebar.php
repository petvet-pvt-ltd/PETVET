<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//Redirect to index page if user is not logged
if(empty($_SESSION['user_id'])){
  header("Location: ../../index.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="../../styles/dashboard/sidebar.css">
</head>
<body>
  <div class="dashboard-sidebar">
    <div class="sidebar-heading">
      <h2 class="sidebar-title">
        <img src="../../images/petvet-logo-web.png" class="petvet-logo">PETVET
      </h2>
    </div>
    

    <?php
    if($_SESSION['user_role'] == 'clinic-manager'){
      require_once '../sidebars/sidebar-clinic-manager.php';#-----------------------------manager side bar------------------------------------#
    }

    elseif ($_SESSION['user_role'] == 'admin'){
      require_once '../sidebars/sidebar-admin.php'; # ---- admin sidebar 
      }

    elseif ($_SESSION['user_role'] == 'pet-owner'){
      require_once '../sidebars/sidebar-pet-owner.php'; # ---- pet-owner sidebar
    }

    elseif ($_SESSION['user_role'] == 'vet'){
      require_once '../sidebars/sidebar-vet.php'; # ---- vet sidebar
    }


    ?>

    <ul class="sidebar-nav-bottom">
      <li class="nav-item"><img src="../../images/dashboard/account-settings.png" class="icon"> Settings</li>
      <a href="../../db/logout.php"><li class="nav-item"><img src="../../images/dashboard/logout.png" class="icon"> Logout</li></a>
    </ul>

  </div>
</body>
</html>
