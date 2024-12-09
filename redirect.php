
<?php
include 'config.php';
include 'signin.php';

                            // auto redirect to index.php if user is logged in
                        if (isset($_SESSION['user_id'])) {
                            // auto redirect to crud/index.php if user is admin
                            $user_id = $_SESSION['user_id'];
                            $sql = "SELECT roles FROM users WHERE id='$user_id'";
                            $query = mysqli_query($link, $sql);
                            $data = mysqli_fetch_array($query);

                            if ($data['roles'] == 'admin') {
                                header("Location: crud/index.php");  
                            } else {
                                header("Location: crud/user.php");
                            }
                            exit();
                            }
?>