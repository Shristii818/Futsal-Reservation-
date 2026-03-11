<?php
session_start(); // Start the session
// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirect to the login page if not logged in
    header("Location: login.php");
    exit;
}
?>

<?php include 'Top.php'; ?>
<?php require_once 'db.php'; ?>
<link href="css/reservationTable.css" rel="stylesheet">

<?php
$success = false;
$error = '';

function getTimeSlots($connection) {
    $timeStmt = $connection->prepare("SELECT * FROM tblTime");
    $timeStmt->execute();
    $timeSlots = $timeStmt->get_result();
    $timeStmt->close();

    return $timeSlots;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Assume connection is established in $connection
    $selectedTimeSlots = $_POST['timeSlots'] ?? []; // Fetch selected checkboxes
    $reservedDate = $_POST['date']; // Selected date
    $phone = $_SESSION['user_phone']; // Retrieve the phone number
    // Set your local timezone
    date_default_timezone_set('Asia/Kathmandu'); // Replace with your timezone
    $orderTime = date('h:i A'); // Current timestamp
    $status = "Pending";

    if (empty($selectedTimeSlots)) {
        $error = "No timeslots selected.";
    }

    $stmt = $connection->prepare("INSERT INTO tblreservation (Phone, ReservedDate, TimeSlot, OrderTime, Status) VALUES (?, ?, ?, ?, ?)");
    if ($stmt) {
        $successCount = 0;
        $errorMessages = [];

        foreach ($selectedTimeSlots as $timeSlot) {
            $stmt->bind_param("sssss", $phone, $reservedDate, $timeSlot, $orderTime, $status);
            if ($stmt->execute()) {
                $successCount++;
                $success = true;
            } else {
                $errorMessages[] = "Error saving timeslot $timeSlot: " . $stmt->error;
            }
        }

        $stmt->close();

        // Feedback to user
        if ($successCount > 0) {
            // echo "$successCount timeslot(s) reserved successfully.";
        }
        if (!empty($errorMessages)) {
            //echo "Some errors occurred:<br>" . implode("<br>", $errorMessages);
        }
    } else {
        // Handle statement preparation error
        $error = "Database error: Unable to prepare statement.";
    }
}
?>


<form method="post" action="">
    <div class="container-fluid">

        <div class="card mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Reserve Time Slot</h6>
            </div>
            <div class="card-body">
                <label>Date: </label>
                <input type="text" name="date" id="date-picker" placeholder="Select Date" required>
                <button type="submit" class="btn btn-secondary">Reserve</button>
                <a href="MyOrderList.php" class="btn btn-primary">MyOrderList</a>
            </div>
        </div>

    </div>

    <h1>Daily Time Table</h1>
    <table id='timeTable'>
        <thead>
            <tr><th>Date/Time Slot</th>
                <?php
                $timeSlots = getTimeSlots($connection);
                foreach ($timeSlots as $timeSlot) {
                    echo '<th>' . htmlspecialchars($timeSlot['TimeSlot']) . '</th>';
                }
                ?>

        </thead>
        <tbody>

            <tr>
                <td class='time-slot'></td>
                <?php
                $timeSlots = getTimeSlots($connection);
                date_default_timezone_set('Asia/Kathmandu'); // Set your timezone
// Get the current time
                $currentHour = date('g:i A');

                foreach ($timeSlots as $timeSlot) {
                    // Extract the end time from the time slot
//                    $endTime = trim(substr($timeSlot['TimeSlot'], stripos($timeSlot['TimeSlot'], "To") + 2));
//
//                    // Convert end time and current hour to DateTime for comparison
//                    $endTimeObj = DateTime::createFromFormat('g:i A', $endTime);
//                    $currentHourObj = DateTime::createFromFormat('g:i A', $currentHour);
//                    
//                    $myslot=[];
//                    if ($endTimeObj < $currentHourObj) {
//                       
//                    } else {
//                        $myslot=false;
//                    }

                    echo '<td><input type="checkbox" name="timeSlots[]" value="' . htmlspecialchars($timeSlot['TimeSlot']) . '" 
                data-time-slot="' . htmlspecialchars($timeSlot['TimeSlot']) . '"></td>';
                }
                ?>
            </tr>
        </tbody>
    </table>


</form>



<!-- Bootstrap Modal -->
<div class="modal fade" id="resultModal" tabindex="-1" role="dialog" aria-labelledby="resultModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resultModalLabel">Reservation Status</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?= $success ? "Reservation successful!" : htmlspecialchars($error) ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<script>
    var currentDate = new Date();
    var currentNepaliDate = calendarFunctions.getBsDateByAdDate(currentDate.getFullYear(), currentDate.getMonth() + 1, currentDate.getDate());
    var formatedNepaliDate = calendarFunctions.bsDateFormat("%y-%m-%d", currentNepaliDate.bsYear, currentNepaliDate.bsMonth, currentNepaliDate.bsDate);


    $("#date-picker").nepaliDatePicker({
        dateFormat: "%y-%m-%d",
        closeOnDateSelect: true,
        minDate: formatedNepaliDate



    });


    function eventLog(event) {
        var datePickerData = event.datePickerData;

        var outputData = {
            "type": event.type,
            "message": event.message,
            "datePickerData": datePickerData
        };

        var output = '<p><code>▸ ' + JSON.stringify(outputData) + '</code></p>';
        $('.output').append(output);
    }

    $("#date-picker").on("show", function (event) {
        var output = '<p><code>▸ Show event trigger</code></p>';
        $('.output').append(output);
    });

    $("#date-picker").on("yearChange", function (event) {

        eventLog(event);
        DateManagemnt();
        AjaxCallForCheckboxDisable();
    });

    $("#date-picker").on("monthChange", function (event) {
        eventLog(event);
        DateManagemnt();
        AjaxCallForCheckboxDisable();
    });

    $("#date-picker").on("dateChange", function (event) {
        eventLog(event);
        DateManagemnt();
        AjaxCallForCheckboxDisable();


    });

    $("#date-picker").on("dateSelect", function (event) {
        eventLog(event);

        DateManagemnt();
        AjaxCallForCheckboxDisable();



    });
    $(document).ready(function () {

        DateManagemnt();
        AjaxCallForCheckboxDisable();

    });

    function AjaxCallForCheckboxDisable()
    {

        let checkboxes = document.querySelectorAll('input[type="checkbox"]');

// Loop through each checkbox and check the corresponding value in response.checkboxesStatus
        checkboxes.forEach(function (checkbox) {
            // Disable the checkbox based on the corresponding value in response.checkboxesStatus

            checkbox.disabled = false;

        });
        var selectedDate = $("#date-picker").val();

        // Helper function to convert time string to minutes from midnight
        function convertToMinutes(timeStr) {
            let [time, ampm] = timeStr.split(' '); // Split the time and AM/PM
            let [hours, minutes] = time.split(':'); // Split hours and minutes

            // Convert hours and minutes to integer
            hours = parseInt(hours);
            minutes = parseInt(minutes);

            // Adjust for AM/PM
            if (ampm === 'PM' && hours !== 12) {
                hours += 12; // Convert PM hours to 24-hour format
            } else if (ampm === 'AM' && hours === 12) {
                hours = 0; // Convert 12 AM to 0 hours
            }

            // Return total minutes since midnight
            return hours * 60 + minutes;
        }
        $(document).ready(function () {

            $.ajax({
                url: 'GetTimeSlotByReservedDate.php', // The server script
                type: 'POST', // HTTP method
                data: {nepaliDate: selectedDate, action: 'getReservedTimeSlots'}, // Data to send to the server
                success: function (response) {

                    // Parse response if it is a JSON string
                    if (typeof response === "string") {
                        response = JSON.parse(response);
                    }

                    if (response.success) {

                        console.log(response.checkboxesStatus);
                        // Loop through checkboxesStatus and disable corresponding checkboxes
                        let checkboxes = document.querySelectorAll('input[type="checkbox"]');


                        // Get current date and time
                        let now = new Date();
                        // Get the hour and minute
                        let hours = now.getHours();
                        let minutes = now.getMinutes();

                        // Convert to 12-hour format
                        let ampm = hours >= 12 ? 'PM' : 'AM';
                        hours = hours % 12;
                        hours = hours ? hours : 12; // Adjust hour to 12-hour format (0 becomes 12)
                        minutes = minutes < 10 ? '0' + minutes : minutes; // Add leading zero if minutes is less than 10

                        // Format time
                        let currentTime = hours + ':' + minutes + ' ' + ampm;

                        console.log(currentTime); // Output: e.g. "7:30 PM"
                        // Loop through each checkbox and check the corresponding value in response.checkboxesStatus
                        checkboxes.forEach(function (checkbox, index) {



                            let parts = checkbox.value.split('To');

                            // The second part will be after "To"
                            let endTime = parts[1].trim();
                            // Convert both times to minutes
                            let currentMinutes = convertToMinutes(currentTime);
                            let endMinutes = convertToMinutes(endTime);
                            // Disable the checkbox based on the corresponding value in response.checkboxesStatus
                            if (($("#date-picker").val() === formatedNepaliDate))
                            {
                                if ((response.checkboxesStatus[index] === true || endMinutes < currentMinutes)) {

                                    checkbox.disabled = true;
                                }
                            }
                            else
                            {
                                if (response.checkboxesStatus[index] === true ) {

                                    checkbox.disabled = true;
                                }
                            }

                        });

                    } else {
                        console.error("Error in server response:", response.message);
                        alert("Error: " + response.message);
                    }
                },
                error: function (error) {
                    console.error('AJAX Error:', error);
                    alert('There was an issue with the request.');
                }
            });
        });
    }


    function DateManagemnt()
    {
        var selectedDate = '';
        if ($("#date-picker").val() === "")
        {
            selectedDate = formatedNepaliDate;
            $("#date-picker").val(selectedDate);

        } else
        {
            var selectedDate = $("#date-picker").val();
        }


        const words = selectedDate.split('-');
        const eny = (calendarFunctions.getNumberByNepaliNumber(words[0]));
        const enm = (calendarFunctions.getNumberByNepaliNumber(words[1]));
        const end = (calendarFunctions.getNumberByNepaliNumber(words[2]));
        const fullDate = calendarFunctions.bsDateFormat("%y %M, %d %D", eny, enm, end);
        console.log(fullDate);
        $(".time-slot").text(fullDate);
    }

<?php if ($success || !empty($error)): ?>
        $(document).ready(function () {
            $('#resultModal').modal('show');
        });
<?php endif; ?>




</script>

<?php
// Close the connection
$connection->close();
?>
<?php include 'Bottom.php'; ?>
