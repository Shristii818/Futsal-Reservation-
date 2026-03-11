<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirect to the login page if not logged in
    header("Location: login.php");
    exit;
}
?>


<?php

require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'getReservedTimeSlots') {
    $nepaliDate = $_POST['nepaliDate'] ?? '';
    $response = ['success' => false, 'checkboxesStatus' => [], 'message' => ''];

    try {
        // Fetch reserved slots
        $stmt = $connection->prepare("SELECT * FROM tblreservation WHERE ReservedDate = ?");
        $stmt->bind_param("s", $nepaliDate);
        $stmt->execute();
        $reservedSlotsResult = $stmt->get_result();

        $stmt->close();

        // Fetch all time slots
        $timeStmt = $connection->prepare("SELECT * FROM tblTime");
        $timeStmt->execute();
        $timeSlotsResult = $timeStmt->get_result();

        $timeStmt->close();

        $result = []; // Initialize an empty array for the results

        foreach ($timeSlotsResult as $timeslot) {
            $isMatched = false; // Assume the current time slot is not reserved

            foreach ($reservedSlotsResult as $reservedSlot) {
                if (($timeslot['TimeSlot'] === $reservedSlot['TimeSlot']) && ($reservedSlot["Status"]==="Pending"||$reservedSlot["Status"]==="Approved")) {
                    $isMatched = true; // Mark as matched
                    break; // Exit the inner loop once a match is found
                }
            }

            $result[] = $isMatched; // Append the result for this time slot
        }

        $response['checkboxesStatus'] = $result; // Store the results in the response
        $response['success'] = true;

// Optionally, output the response as JSON
         json_encode($response);
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
    exit;
}
?>
