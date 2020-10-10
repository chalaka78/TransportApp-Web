<?php

if (isset($_POST['signin'])) {
    require_once 'dbconnect.php';
    session_start();

    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);

    //Check Fields are Empty
    if (empty($email) || empty($password)) {
        $_SESSION['status'] = "emptyFields";
        header("Location: ../index.php?err=kjkj");
        exit();
    } else {
        $sql = "SELECT * FROM users WHERE email=?";
        $stmt = mysqli_stmt_init($con);

        //Check SQL is coorect
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            $_SESSION['status'] = "sqlError";
            header("Location: ../index.php");
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, 's', $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if ($row = mysqli_fetch_assoc($result)) {
                $isVerified = $row['verified'];
                $dbPass = $row['password'];
                $dbUid = $row['uid'];
                $dbRole = $row['role'];
                $dbEmail = $row['email'];

                //Take User Name
                $username_query = "SELECT * FROM user_details WHERE uid = {$dbUid}";
                $resultset = mysqli_query($con, $username_query);
                $rowName = mysqli_fetch_assoc($resultset);
                $username = $rowName['name'];


                $pwdCheck = password_verify($password, $dbPass);
                if ($pwdCheck == false) {
                    $_SESSION['status'] = "invalidPassword";
                    header("Location: ../index.php");
                    exit();
                } else if ($pwdCheck == true) {

                    if (isset($_POST['rememberme'])) {
 
                        $_SESSION['verified'] = $isVerified;
                        $_SESSION['role'] = $dbRole;
                        $_SESSION['email'] = $dbEmail;
                        $_SESSION['name'] = $username;

                        if ($isVerified == true) {
                            $_SESSION['Userid'] = $dbUid;
                            setcookie('emailcookie', $email, time() + (86400 * 30), "/");
                            setcookie('passwordcookie', $password, time() + (86400 * 30), "/");

                            if ($dbRole == 0) {
                                $_SESSION['status'] = "loginSuccess";
                                header("Location: ../Views/Customer/dashboard.php");
                                exit();
                            } else if ($dbRole == 1) {
                                $_SESSION['status'] = "loginSuccess";
                                header("Location: ../Views/Driver/dashboard.php");
                                exit();
                            } else if ($dbRole == 2) {
                                $_SESSION['status'] = "loginSuccess";
                                header("Location: ../Views/Inspector/dashboard.php");
                                exit();
                            } else {
                                $_SESSION['status'] = "invalidUser";
                                header("Location: ../index.php");
                                exit();
                            }
                        } else {
                            $_SESSION['status'] = "notVerified";
                            header("Location: ../Views/email-verify.php");
                            exit();
                        }
                    } else {
                        
                        $_SESSION['verified'] = $isVerified;
                        $_SESSION['role'] = $dbRole;
                        $_SESSION['email'] = $dbEmail;
                        $_SESSION['name'] = $username;

                        if ($isVerified == true) {
                            
                            $_SESSION['Userid'] = $dbUid;
                            setcookie('emailcookie', '', time() - (86400 * 30), "/");
                            setcookie('passwordcookie', '', time() - (86400 * 30), "/");

                            if ($dbRole == 0) {
                                $_SESSION['status'] = "loginSuccess";
                                header("Location: ../Views/Customer/dashboard.php");
                                exit();
                            } else if ($dbRole == 1) {
                                $_SESSION['status'] = "loginSuccess";
                                header("Location: ../Views/Driver/dashboard.php");
                                exit();
                            } else if ($dbRole == 2) {
                                $_SESSION['status'] = "loginSuccess";
                                header("Location: ../Views/Inspector/dashboard.php");
                                exit();
                            } else {
                                $_SESSION['status'] = "invalidUser";
                                header("Location: ../index.php");
                                exit();
                            }
                        } else {
                            $_SESSION['status'] = "notVerified";
                            header("Location: ../Views/email-verify.php");
                            exit();
                        }
                    }
                } else {
                    $_SESSION['status'] = "invalidPassword";
                    header("Location: ../index.php");
                    exit();
                }
            } else {
                $_SESSION['status'] = "invalidUser";
                header("Location: ../index.php");
                exit();
            }
        }
    }
} else {
    header("Location: ../index.php");
    exit();
}