

<!-- this should go after your </body> -->
<script src="//code.jquery.com/jquery-3.7.1.slim.min.js"></script>
<script src="//unpkg.com/nepali-date-picker@2.0.2/dist/nepaliDatePicker.min.js"></script>
<link rel="stylesheet" href="//unpkg.com/nepali-date-picker@2.0.2/dist/nepaliDatePicker.min.css">


<p>
    <label>Date: </label>
    <input type="text" value="" name="date" id="date-picker" placeholder="Select Date">
</p>
<script>
    $("#date-picker").nepaliDatePicker({
        dateFormat: "%y-%m-%d",
        closeOnDateSelect: true
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
        console.log(event);
        eventLog(event);
    });

    $("#date-picker").on("monthChange", function (event) {
        eventLog(event);
        console.log(event);
    });

    $("#date-picker").on("dateChange", function (event) {
        eventLog(event);
        console.log(event);
    });

    $("#date-picker").on("dateSelect", function (event) {
        eventLog(event);
        console.log(event);
    });
</script>

