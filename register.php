<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="User registration page">
    <meta name="author" content="Your Name">

    <title>SB Admin 2 - Register</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body class="bg-gradient-primary">
    <?php
    require_once 'db.php';

    // Initialize variables for messages
    $success = false;
    $error = '';
    $userExist = false;

    function checkUserByPhone($connection, $phone) {
        $flag = false;
        $stmt = $connection->prepare("SELECT * FROM tblUser WHERE Phone = ?");
        if ($stmt) {
            $stmt->bind_param("s", $phone);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $flag = true;
            }

            $stmt->close();
        } else {
            // Handle statement preparation error
            $error = "Database error: Unable to prepare statement.";
        }

        return $flag;
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // CSRF token validation can be added here

        // Retrieve and sanitize inputs
        $fullName = htmlspecialchars(trim($_POST['fullName']));
        $address = htmlspecialchars(trim($_POST['address']));
        $phone = htmlspecialchars(trim($_POST['phone']));
        $password = $_POST['password'];
        $repeatPassword = $_POST['repeatPassword'];

        // Validate inputs
        if (empty($fullName)) {
            $error = "Full Name is required.";
        }
        // Validate phone number format
        elseif (!preg_match("/^\+?\d{10,10}$/", $phone)) {
            $error = "Invalid phone number. Please enter a valid phone number with 10 to 15 digits.";
        }
        // Validate passwords
        elseif (empty($password)) {
            $error = "Password is required.";
        } elseif ($password !== $repeatPassword) {
            $error = "Passwords do not match.";
        }
        else {
            // Check if user already exists
            if (checkUserByPhone($connection, $phone)) {
                $userExist = true;
                $error = "An account with this phone number already exists.";
            } else {
                // Hash the password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Insert user data into the database
                $stmt = $connection->prepare("INSERT INTO tblUser (FullName, Address, Phone, Password) VALUES (?, ?, ?, ?)");
                if ($stmt) {
                    $stmt->bind_param("ssss", $fullName, $address, $phone, $hashedPassword);

                    if ($stmt->execute()) {
                        $success = true; // Mark insertion as successful
                    } else {
                        $error = "Error: " . $stmt->error;
                    }

                    $stmt->close();
                } else {
                    $error = "Database error: Unable to prepare statement.";
                }
            }
        }

        // Close the connection
        $connection->close();
    }
    ?>

    <div class="container">

        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <!-- Nested Row within Card Body -->
                <div class="row">
                    <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>
                    <div class="col-lg-7">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Create an Account!</h1>
                            </div>
                            <form class="user" method="post" action="">
                                <!-- You can add CSRF token here -->
                                <div class="form-group">
                                    <input type="text" name="fullName" class="form-control form-control-user" id="exampleInputFullName"
                                           placeholder="Full Name" required value="<?= isset($_POST['fullName']) ? htmlspecialchars($_POST['fullName']) : '' ?>">
                                </div>
                                <div class="form-group">
                                    <input type="text" name="address" class="form-control form-control-user" id="exampleInputAddress"
                                           placeholder="Address" value="<?= isset($_POST['address']) ? htmlspecialchars($_POST['address']) : '' ?>">
                                </div>
                                <div class="form-group">
                                    <input type="text" name="phone" class="form-control form-control-user" id="exampleInputPhone"
                                           placeholder="Phone" required value="<?= isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : '' ?>">
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="password" name="password" class="form-control form-control-user"
                                               id="exampleInputPassword" placeholder="Password" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="password" name="repeatPassword" class="form-control form-control-user"
                                               id="exampleRepeatPassword" placeholder="Repeat Password" required>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-user btn-block">
                                    Register Account
                                </button>
                                <hr>

                            </form>
                            <hr>
                            
                            <div class="text-center">
                                <a class="small" href="login.php">Already have an account? Login!</a>
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
                        <h5 class="modal-title" id="resultModalLabel">Registration Status</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Message will be inserted dynamically -->
                        <?php
                        if ($success) {
                            echo "Account created successfully!";
                        } elseif ($userExist) {
                            echo "An account with this phone number already exists.";
                        } elseif (!empty($error)) {
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

    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Trigger Modal Based on PHP Variables -->
    <script>
        <?php if ($success || $userExist || !empty($error)): ?>
            $(document).ready(function () {
                $('#resultModal').modal('show');
            });
        <?php endif; ?>
    </script>

</body>

</html>
