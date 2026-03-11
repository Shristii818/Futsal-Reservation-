<?php

require_once 'db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get phone and time slot
    $phone = isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : null;
    $timeSlot = isset($_POST['time_slot']) ? htmlspecialchars($_POST['time_slot']) : null;
   

    // Get action
    $action = isset($_POST['action']) ? htmlspecialchars($_POST['action']) : null;

    if ($phone && $timeSlot && $action) {
        
        if ($action === 'approve') {
           
            echo 'action bhitra';
            // update reservationdata into the database
            $stmt = $connection->prepare("Update tblreservation set  Status='Approved' where Phone = ? and TimeSlot=?");
            if ($stmt) {
                echo 'statement bhitra';
                $stmt->bind_param("ss", $phone, $timeSlot);

                if ($stmt->execute()) {
                    header("Location: MyOrderList.php");
                } else {
                    $error = "Error: " . $stmt->error;
                }

                $stmt->close();
            } else {
                $error = "Database error: Unable to prepare statement.";
            }
        } elseif ($action === 'cancel') {
            // update reservationdata into the database
            $stmt = $connection->prepare("Update tblreservation set  Status='Cancelled' where Phone = ? and TimeSlot=?");
            if ($stmt) {
                $stmt->bind_param("ss", $phone, $timeSlot);

                if ($stmt->execute()) {
                    // Redirect to another page
                    header("Location: MyOrderList.php");
                    exit; // Ensure no further code is executed
                } else {
                    $error = "Error: " . $stmt->error;
                }

                $stmt->close();
            } else {
                $error = "Database error: Unable to prepare statement.";
            }
        }
    } else {
        echo "Invalid request.";
    }
}
