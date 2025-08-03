<?php
// session_start();

// //Check the user is a logged
// if(!empty($_SESSION['user_id'])){

//   if($_SESSION['user_role'] == 'clinic-manager'){     # if user is a clinic manager 
//     header("Location: dashboard/clinic-manager/");
//     exit();
//   }
//   elseif($_SESSION['user_role'] == 'admin'){
//     header("Location: dashboard/admin/");             # if user is an admin
//     exit();
//   }
//   elseif($_SESSION['user_role'] == 'pet-owner'){
//     header("Location: dashboard/pet-owner/");         # if user is a pet owner
//     exit();
//   }
// }

session_start();

if (!empty($_SESSION['user_id'])) {
    switch ($_SESSION['user_role']) {
        case 'admin':
            header("Location: dashboard/admin/");
            exit();
        case 'clinic-manager':
            header("Location: dashboard/clinic-manager/");
            exit();
        case 'pet-owner':
            header("Location: dashboard/pet-owner/");
            exit();
        default:
            header("Location: dashboard/");
            exit();
    }
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

?>