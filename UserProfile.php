<?php
session_start(); // Start the session
// Define admin phone number
define('ADMIN_PHONE', '9841798824');

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirect to the login page if not logged in
    header("Location: login.php");
    exit;
}
?>


<?php
require_once 'db.php';

$success = '';
$error = '';

function GetProfile($connection, $phone) {
    $stmt = $connection->prepare("SELECT * FROM tblUser WHERE Phone = ?");
    if ($stmt) {
        $stmt->bind_param("s", $phone);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    } else {
        $error = "Database error: Unable to prepare statement.";
    }
}
?>
<?php include'Top.php' ?>
<?php
$userResult = GetProfile($connection, $_SESSION['user_phone']);
if ($userResult && $userResult->num_rows > 0) {
    $user = $userResult->fetch_assoc(); // Fetch the user's data as an associative array
} else {
    $error = "Unable to fetch user profile. Please try again later.";
}
?>
<section style="background-color: #eee;">
    <div class="container py-5">
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb" class="bg-body-tertiary rounded-3 p-3 mb-4">
                    <ol class="breadcrumb mb-0">

                        <li class="breadcrumb-item active" aria-current="page">User Profile</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava3.webp" alt="avatar"
                             class="rounded-circle img-fluid" style="width: 150px;">

                        <p></p>
                        <div class="d-flex justify-content-center mb-2">

                            <button  type="button" data-mdb-button-init data-mdb-ripple-init class="btn btn-outline-primary ms-1">Browse Photo</button>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="mb-0">Full Name</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-muted mb-0"><?php echo $user['FullName']; ?></p>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="mb-0">Address</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-muted mb-0"><?php echo $user['Address']; ?></p>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="mb-0">Phone</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-muted mb-0"><?php echo $user['Phone']; ?></p>
                            </div>
                        </div>


                        <hr>
                        <div class="row">
                            <div class="col-sm-3">

                            </div>
                            <div class="col-sm-9">

                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-9">
                                <button  type="button" class="btn btn-secondary">Update</button>
                                <a href="MyOrderList.php"    class="btn btn-primary">Back To My Order</a>
                            </div>
                            <div class="col-sm-3">

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
<?php include'Bottom.php' ?>