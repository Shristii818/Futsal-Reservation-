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
    $phone = '9090909'; // Replace with actual user data
   
    $orderTime = date('Y-m-d H:i:s'); // Current timestamp
     $status = "Pending";

    if (empty($selectedTimeSlots)) {
        die("No timeslots selected.");
    }

    $stmt = $connection->prepare("INSERT INTO tblreservation (Phone, ReservedDate, TimeSlot, OrderTime, Status) VALUES (?, ?, ?, ?, ?)");
    if ($stmt) {
        $successCount = 0;
        $errorMessages = [];

        foreach ($selectedTimeSlots as $timeSlot) {
            $stmt->bind_param("sssss", $phone, $reservedDate, $timeSlot, $orderTime, $status);
            if ($stmt->execute()) {
                $successCount++;
                $success=true;
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
                foreach ($timeSlots as $timeSlot) {
                    echo '<td><input type="checkbox" name="timeSlots[]" value="' . htmlspecialchars($timeSlot['TimeSlot']) . '"></td>';
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
    });

    $("#date-picker").on("monthChange", function (event) {
        eventLog(event);
        DateManagemnt();
    });

    $("#date-picker").on("dateChange", function (event) {
        eventLog(event);
        DateManagemnt();


    });

    $("#date-picker").on("dateSelect", function (event) {
        eventLog(event);

        DateManagemnt();



    });
    $(document).ready(function () {

        DateManagemnt();
        $(document).ready(function () {
            DateManagemnt();
////            $.ajax({
//                url: 'GetTimeSlotByReservedDate.php', // The server script
//                type: 'POST', // HTTP method
//                data: {nepaliDate: formatedNepaliDate, action: 'getReservedTimeSlots'}, // Data to send to the server
//                success: function (response) {
//                    console.log('Server Response:', response);
//
//                    // Parse response if it is a JSON string
//                    if (typeof response === "string") {
//                        response = JSON.parse(response);
//                    }
//
//                    if (response.success) {
//                        // Update the #timeTable div with the HTML from the response
//                        response.checkboxes.forEach(function (checkbox) {
//                            $('#timeTable tbody tr').append('<td>' + checkbox.html + '</td>');
//                        });
//                        bindCheckboxEvents();
//                    } else {
//                        console.error("Error in server response:", response.message);
//                        alert("Error: " + response.message);
//                    }
//                },
//                error: function (error) {
//                    console.error('AJAX Error:', error);
//                    alert('There was an issue with the request.');
//                }
//            });
        });

    });

// Function to bind checkbox change events after checkboxes are added dynamically
    function bindCheckboxEvents() {
        $('input[type="checkbox"]').on('change', function () {
            if (this.checked) {
                // Get the time slot value from the checkbox
                const timeSlot = $(this).val();
                console.log(`Checked: ${timeSlot}`);
            } else {
                console.log('Unchecked');
            }
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
