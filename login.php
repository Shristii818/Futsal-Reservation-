<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>SB Admin 2 - Login</title>

        <!-- Custom fonts for this template-->
        <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
         <script src="//code.jquery.com/jquery-3.7.1.slim.min.js"></script>
        <script src="//unpkg.com/nepali-date-picker@2.0.2/dist/nepaliDatePicker.min.js"></script>
        <link rel="stylesheet" href="//unpkg.com/nepali-date-picker@2.0.2/dist/nepaliDatePicker.min.css">
        <link
            href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
            rel="stylesheet">

        <!-- Custom styles for this template-->
        <link href="css/sb-admin-2.min.css" rel="stylesheet">

    </head>
    <?php
    session_start();
    require_once 'db.php';
    $error = '';
    $flag = false;

    function userLogin($connection, $phone, $password) {
        global $error;

        $stmt = $connection->prepare("SELECT * FROM tblUser WHERE Phone = ?");
        if ($stmt) {
            $stmt->bind_param("s", $phone);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();

                // Verify the password
                if (password_verify($password, $user['Password'])) {
                    // Valid login
                    $_SESSION['loggedin'] = true;
                    $_SESSION['user_phone'] = $phone; // Store phone for later use
                    $_SESSION['full_Name'] = $user['FullName'];

                    // Redirect to another page (e.g., dashboard.php) on successful login
                    //this number is assumed to admin number
                    if ($phone === "9841798824") {
                        echo'hhhhhhh';
                        header('Location: MyOrderList.php');
                        exit(); // Always call exit() after header() to stop further execution
                    } else {
                        header('Location: MyPage.php');
                        exit(); // Always call exit() after header() to stop further execution
                    }
                } else {
                    $error = "Invalid UserName Or password.";
                }
            } else {
                $error = "User does not exist with this phone number.";
            }

            $stmt->close();
        } else {
            $error = "Database error: Unable to prepare statement.";
        }

        return false; // Login failed
    }

// Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $phone = htmlspecialchars(trim($_POST['phone']));
        $password = $_POST['password'];
        
           
        if (!preg_match("/^\+?\d{10,10}$/", $phone)) {
            $error = "Invalid phone number. Please enter a valid phone number with Exactly 10 digit.";
        }
        // Validate passwords
        elseif (empty($password)) {
            $error = "Password is required.";
        } else {
            $_SESSION['current_Nep_Date']= $_POST['formattedDate'];
           
            $flag = userLogin($connection, $phone, $password);
            // Close the database connection
            $connection->close();
        }
    }
    ?>


    <body class="bg-gradient-primary">

        <div class="container">

            <!-- Outer Row -->
            <div class="row justify-content-center">

                <div class="col-xl-10 col-lg-12 col-md-9">

                    <div class="card o-hidden border-0 shadow-lg my-5">
                        <div class="card-body p-0">
                            <!-- Nested Row within Card Body -->
                            <div class="row">
                                <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                                <div class="col-lg-6">
                                    <div class="p-5">
                                        <div class="text-center">
                                            <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                                        </div>
                                        <form class="user" method="post" action="">
                                            <input type="hidden" id="formattedDate" name="formattedDate" value="">
                                            <div class="form-group">
                                                <input type="number" name="phone" class="form-control form-control-user"
                                                       id="exampleInputPhon" aria-describedby="emailHelp"
                                                       placeholder="Enter Phone...">
                                            </div>
                                            <div class="form-group">
                                                <input type="password" name="password" class="form-control form-control-user"
                                                       id="exampleInputPassword" placeholder="Password">
                                            </div>
                                            <div class="form-group">
                                                <div class="custom-control custom-checkbox small">
                                                    <input type="checkbox" class="custom-control-input" id="customCheck">
                                                    <label class="custom-control-label" for="customCheck">Remember
                                                        Me</label>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-user btn-block">
                                                Login
                                            </button
                                            <hr>

                                        </form>
                                        <hr>
                                        <div class="text-center">
                                            <a class="small" href="forgot-password.html">Forgot Password?</a>
                                        </div>
                                        <div class="text-center">
                                            <a class="small" href="register.php">Create an Account!</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>
        <!-- Bootstrap Modal -->
        <div class="modal fade" id="resultModal" tabindex="-1" role="dialog" aria-labelledby="resultModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="resultModalLabel">Login Status</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Message will be inserted dynamically -->
<?php
if (!empty($error)) {
    echo htmlspecialchars($error);
}
?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Bootstrap core JavaScript-->
        <script src="vendor/jquery/jquery.min.js"></script>
        <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

        <!-- Core plugin JavaScript-->
        <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

        <!-- Custom scripts for all pages-->
        <script src="js/sb-admin-2.min.js"></script>
        <script>
<?php if (!empty($error)): ?>
                $(document).ready(function () {
                    $('#resultModal').modal('show');
                });
<?php endif; ?>

            $(document).ready(function () {

                var currentDate = new Date();
                var currentNepaliDate = calendarFunctions.getBsDateByAdDate(currentDate.getFullYear(), currentDate.getMonth() + 1, currentDate.getDate());
                var formatedNepaliDate = calendarFunctions.bsDateFormat("%y-%m-%d", currentNepaliDate.bsYear, currentNepaliDate.bsMonth, currentNepaliDate.bsDate);
                document.getElementById('formattedDate').value = formatedNepaliDate;
               
            });


        </script>

    </body>

</html>
