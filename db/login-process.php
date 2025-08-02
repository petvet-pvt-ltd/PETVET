<?php
require_once('connect.php');

//starting the session
session_start();

if(isset($_POST['login-submit'])){
    $login_email = $_POST['email'];
    $login_pass = $_POST['password'];
    
    $sql = "SELECT * FROM `$tb_name` WHERE `$tb_email` = '$login_email' AND `$tb_pass` = '$login_pass'";
    
    $result = mysqli_query($conn, $sql);

    if($result) {
        if (mysqli_num_rows($result) > 0) {
            // Fetch user data
            $userdata = mysqli_fetch_assoc($result);
    
            // Store user information in session variables            
            $_SESSION['user_id'] = $userdata['id']; // Getting number
            $_SESSION['user_role'] = $userdata['role']; // Getting role

            echo $_SESSION['user_id'];#

            // Clinic manager redirection
            if($_SESSION['user_role'] == 'clinic-manager'){
                header("Location: ../dashboard/clinic-manager/");
                exit();
            }

            // admin redirection
            elseif($_SESSION['user_role'] == 'admin'){
                header("Location: ../dashboard/admin/");
                exit();
            }

            // owner redirection
            elseif($_SESSION['user_role'] == 'pet-owner'){
                header("Location: ../dashboard/pet-owner/");
                exit();
            }
        } else {
            // Login failed
            $msg = "Incorrect Username or password !";
            // Ending the session
            session_destroy();
            // Redirect to login
            echo
            "<script>
                alert('$msg');
                window.location.href = '../login.php';
            </script>";
            
    
        }
    }
}
?>