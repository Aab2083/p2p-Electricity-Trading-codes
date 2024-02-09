<!DOCTYPE HTML>
<html>

<head>
    <title>ESP32 WITH MYSQL DATABASE</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css"
        integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr"
        crossorigin="anonymous">
    <link rel="icon" href="data:,">
    <style>
        html {
            font-family: Arial;
            display: inline-block;
            text-align: center;
        }

        p {
            font-size: 1.2rem;
        }

        h4 {
            font-size: 0.8rem;
        }

        body {
            margin: 0;
        }

        .topnav {
            overflow: hidden;
            background-color: #0c6980;
            color: white;
            font-size: 1.2rem;
        }

        .content {
            padding: 5px;
        }

        .card {
            background-color: white;
            box-shadow: 0px 0px 10px 1px rgba(140, 140, 140, .5);
            border: 1px solid #0c6980;
            border-radius: 15px;
        }

        .card.header {
            background-color: #0c6980;
            color: white;
            border-bottom-right-radius: 0px;
            border-bottom-left-radius: 0px;
            border-top-right-radius: 12px;
            border-top-left-radius: 12px;
        }

        .cards {
            max-width: 700px;
            margin: 0 auto;
            display: grid;
            grid-gap: 2rem;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        }

        .reading {
            font-size: 1.3rem;
        }

        .packet {
            color: #bebebe;
        }

        .statusColor {
            color: #fd7e14;
        }

        .currentColor {
            color: #1b78e2;
        }

        .voltageColor {
            color: #1b78e2;
        }

        .powerColor {
            color: #1b78e2;
        }

        .unitreadColor {
            color: #1b78e2;
        }

        .LEDColor {
            color: #183153;
        }

        /* ----------------------------------- Toggle Switch */
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }

        .switch input {
            display: none;
        }

        .sliderTS {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #D3D3D3;
            -webkit-transition: .4s;
            transition: .4s;
            border-radius: 34px;
        }

        .sliderTS:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 4px;
            bottom: 4px;
            background-color: #f7f7f7;
            -webkit-transition: .4s;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.sliderTS {
            background-color: #00878F;
        }

        input:focus+.sliderTS {
            box-shadow: 0 0 1px #2196F3;
        }

        input:checked+.sliderTS:before {
            -webkit-transform: translateX(26px);
            -ms-transform: translateX(26px);
            transform: translateX(26px);
        }

        .sliderTS:after {
            content: 'OFF';
            color: white;
            display: block;
            position: absolute;
            transform: translate(-50%, -50%);
            top: 50%;
            left: 70%;
            font-size: 10px;
            font-family: Verdana, sans-serif;
        }

        input:checked+.sliderTS:after {
            left: 25%;
            content: 'ON';
        }

        input:disabled+.sliderTS {
            opacity: 0.3;
            cursor: not-allowed;
            pointer-events: none;
        }

        /* ----------------------------------- */
    </style>
</head>

<body>
    <div class="topnav">
        <h3>ESP32 DATA MONITORING AND CONTROLLING</h3>
    </div>

    <br>

    <!-- __ DISPLAYS MONITORING AND CONTROLLING ____________________________________________________________________________________________ -->
    <div class="content">
        <div class="cards">

            <!-- == MONITORING ======================================================================================== -->
            <div class="card">
                <div class="card header">
                    <h3 style="font-size: 1rem;">MONITORING</h3>
                </div>

                <!-- Displays the current and status values received from ESP32. *** -->
                <h4 class="statusColor"><i class="fas fa-thermometer-half"></i> status</h4>
                <p class="statusColor"><span class="reading"><span id="ESP32_01_stat"></span></p>
                <h4 class="currentColor"><i class="fas fa-tint"></i> current</h4>
                <p class="currentColor"><span class="reading"><span id="ESP32_01_curren"></span> A</span></p>
                <h4 class="voltageColor"><i class="fas fa-tint"></i> voltage</h4>
                <p class="voltageColor"><span class="reading"><span id="ESP32_01_volt"></span> V</span></p>
                <h4 class="powerColor"><i class="fas fa-tint"></i> power</h4>
                <p class="powerColor"><span class="reading"><span id="ESP32_01_pow"></span> W</span></p>
                <!-- *********************************************************************** -->

                <p class="unitreadColor"><span>units transferred : </span><span id="ESP32_01_units_trans"></span></p>
            </div>
            <!-- ======================================================================================================= -->

            <!-- == CONTROLLING ======================================================================================== -->
            <div class="card">
                <div class="card header">
                    <h3 style="font-size: 1rem;">CONTROLLING</h3>
                </div>

                <!-- Buttons for controlling the LEDs on Slave 2. ************************** -->
                <h4 class="LEDColor"><i class="fas fa-lightbulb"></i> Buying Mode</h4>
                <label class="switch">
                    <input type="checkbox" id="ESP32_01_Togbuying_mode"
                        onclick="GetTogBtnstatus('ESP32_01_Togbuying_mode')">
                    <div class="sliderTS"></div>
                </label>
                <h4 class="LEDColor"><i class="fas fa-lightbulb"></i> Selling mode</h4>
                <label class="switch">
                    <input type="checkbox" id="ESP32_01_Togselling_mode"
                        onclick="GetTogBtnstatus('ESP32_01_Togselling_mode')">
                    <div class="sliderTS"></div>
                </label>
                <!-- *********************************************************************** -->
            </div>
            <!-- ======================================================================================================= -->

        </div>
    </div>

    <br>

    <div class="content">
        <div class="cards">
            <div class="card header" style="border-radius: 15px;">
                <h3 style="font-size: 0.7rem;">LAST TIME RECEIVED DATA FROM ESP32 [ <span id="ESP32_01_LTRD"></span> ]</h3>
                <button onclick="window.open('recordtable.php', '_blank');">Open Record Table</button>
                <h3 style="font-size: 0.7rem;"></h3>
            </div>
        </div>
    </div>
    <!-- ___________________________________________________________________________________________________________________________________ -->

    <script>
        // Function to update toggle button status
    async function UpdateToggleButtonStatus(id, modes, status) {
        try {
            const response = await fetch("update_modes.php", {
                method: "POST",
                body: new URLSearchParams({
                    'id': id,
                    'modes': modes,
                    'status': status
                }),
            });

            if (!response.ok) {
                console.error("Error updating modes:", response.statusText);
            }
        } catch (error) {
            console.error("Error updating modes:", error.message);
        }
    }

    // Function to handle toggle button clicks
    function GetTogBtnstatus(togbtnid) {
        if (togbtnid === "ESP32_01_Togbuying_mode" || togbtnid === "ESP32_01_Togselling_mode") {
            const togbtnchecked = document.getElementById(togbtnid).checked;
            const status = togbtnchecked ? "ON" : "OFF";
            const modes = togbtnid === "ESP32_01_Togbuying_mode" ? "buying_mode" : "selling_mode";

            // Update the status of the toggle button on the server
            UpdateToggleButtonStatus("esp32_01", modes, status);
        }
    }
    document.addEventListener("DOMContentLoaded", function () {
    // Initialize your HTML elements
    document.getElementById("ESP32_01_stat").innerHTML = "NN";
    document.getElementById("ESP32_01_curren").innerHTML = "NN";
    document.getElementById("ESP32_01_volt").innerHTML = "NN";
    document.getElementById("ESP32_01_pow").innerHTML = "NN";
    document.getElementById("ESP32_01_units_trans").innerHTML = "NN";
    document.getElementById("ESP32_01_LTRD").innerHTML = "NN";

    // Fetch data initially and set interval for periodic updates
    Get_Data("esp32_01");
    setInterval(function () {
        Get_Data("esp32_01");
    }, 5000);

    // Function to fetch data from getdata.php
    async function Get_Data(id) {
        try {
            const response = await fetch("getdata.php", {
                method: "POST",
                body: new URLSearchParams({ 'id': id }), // Use URLSearchParams to properly format the body
            });

            if (response.ok) {
                const myObj = await response.json();

                // Update your HTML elements here
                document.getElementById("ESP32_01_stat").innerHTML = myObj.status || "NN";
                document.getElementById("ESP32_01_curren").innerHTML = myObj.current || "NN";
                document.getElementById("ESP32_01_volt").innerHTML = myObj.voltage || "NN";
                document.getElementById("ESP32_01_pow").innerHTML = myObj.power || "NN";
                document.getElementById("ESP32_01_units_trans").innerHTML = myObj.units_transfered || "NN";
                document.getElementById("ESP32_01_LTRD").innerHTML = "Time : " + myObj.ls_time + " | Date : " +
                    myObj.ls_date + " (dd-mm-yyyy)";
            } else {
                console.error("Error fetching data:", response.statusText);
            }
        } catch (error) {
            console.error("Error fetching data:", error.message);
        }
    }

    
});
</script>


</body>

</html>
