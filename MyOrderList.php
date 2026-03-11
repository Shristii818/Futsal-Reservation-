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

include 'Top.php';
require_once 'db.php';
?>
<link href="css/reservationTable.css" rel="stylesheet">

<?php
function getReservation($connection, $phone) {
    $filter = isset($_GET['filter']) ? $_GET['filter'] : '';

    if ($phone === ADMIN_PHONE) {
        if ($filter === 'pending') {
            $query = "SELECT * FROM tblreservation WHERE Status = 'Pending' ORDER BY ReservedDate";
            $reserveStmt = $connection->prepare($query); // No parameter needed
        } else {
            $query = "SELECT * FROM tblreservation WHERE ReservedDate = ? ORDER BY ReservedDate";
            $reserveStmt = $connection->prepare($query);
            $reserveStmt->bind_param("s", $_SESSION['current_Nep_Date']);
        }
    } else {
        $query = "SELECT * FROM tblreservation WHERE Phone = ? ORDER BY ReservedDate";
        $reserveStmt = $connection->prepare($query);
        $reserveStmt->bind_param("s", $phone);
    }

    $reserveStmt->execute();
    $result = $reserveStmt->get_result();
    $reserveStmt->close();

    return $result;
}


?>



<div class="container-fluid">
    <h3>My Order List</h3>
    <div class="row">
        <div class="col-sm-9">
            <a  href="MyPage.php" class="btn btn-secondary">Back To My Order</a>
             <?php if ($_SESSION['user_phone'] === ADMIN_PHONE) { ?>
                 <a href="MyOrderList.php?filter=pending" class="btn btn-primary">View Full List</a>

                <?php } ?>
            
        </div>
        <div class="col-sm-3">

        </div>
    </div>
    <?php
    $reservationResult = getReservation($connection, $_SESSION['user_phone']); // Retrieve reservations
    ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Phone</th>
                <th>Reserved Date</th>
                <th>Time Slot</th>
                <th>Order Time</th>
                <th>Status</th>
                <?php if ($_SESSION['user_phone'] === ADMIN_PHONE) { ?>
                    <th>Action</th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>

            <?php foreach ($reservationResult as $reservation) { ?>
                <tr>
                    <!-- Hidden Fields -->
            <input type="hidden" name="reservations[<?php echo htmlspecialchars($reservation['Phone']); ?>][Phone]" value="<?php echo htmlspecialchars($reservation['Phone']); ?>">
            <input type="hidden" name="reservations[<?php echo htmlspecialchars($reservation['Phone']); ?>][TimeSlot]" value="<?php echo htmlspecialchars($reservation['TimeSlot']); ?>">

            <td><?php echo htmlspecialchars($reservation['Phone']); ?></td>
            <td><?php echo htmlspecialchars($reservation['ReservedDate']); ?></td>
            <td><?php echo htmlspecialchars($reservation['TimeSlot']); ?></td>
            <td><?php echo htmlspecialchars($reservation['OrderTime']); ?></td>
            <td><?php echo htmlspecialchars($reservation['Status']); ?></td>
            <?php if ($_SESSION['user_phone'] === ADMIN_PHONE) { ?>
                <td>
                    <form method="POST" action="process_reservation.php" style="display:inline;">
                        <input type="hidden" name="phone" value="<?php echo htmlspecialchars($reservation['Phone']); ?>">
                        <input type="hidden" name="time_slot" value="<?php echo htmlspecialchars($reservation['TimeSlot']); ?>">
                        <button type="submit" name="action" value="approve" class="btn btn-success btn-sm">Approve</button>
                    </form>
                    <form method="POST" action="process_reservation.php" style="display:inline;">
                        <input type="hidden" name="phone" value="<?php echo htmlspecialchars($reservation['Phone']); ?>">
                        <input type="hidden" name="time_slot" value="<?php echo htmlspecialchars($reservation['TimeSlot']); ?>">
                        <button type="submit" name="action" value="cancel" class="btn btn-danger btn-sm">Cancel</button>
                    </form>
                </td>
            <?php } ?>
            </tr>
        <?php } ?>


        </tbody>
    </table>
</div>
<script>
   

    </script>
<?php
// Close the connection
$connection->close();
include 'Bottom.php';
?>


