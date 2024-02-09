//esp32_01

#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include <ACS712.h>

// Constants for sensor pins
#define currentSensorPin 34
#define voltageSensorPin 35
#define ON_Board_LED 2
#define buying_mode 14
#define selling_mode 12

// WiFi credentials
const char* ssid = "Oneplus9";
const char* password = "11223344";

// Variables for HTTP POST request data
String postData = "";
String payload = "";

// Variables for sensor data
float send_voltage = 0.0;
float send_current = 0.0;
String send_Status = "";
float unitsTransferred = 0.0;
float currentMultiplier = 0.1;
unsigned long lastMillis = 0;
int adc_value = 0;
int adc_voltage = 0;
float power = 0;


// Floats for resistor values in divider (in ohms)
float R1 = 30000.0;
float R2 = 5700.0;

void handleHTTPError(int httpCode) {
  if (httpCode < 0) {
    Serial.printf("HTTP error: %s\n", HTTPClient::errorToString(httpCode).c_str());
    // Add more error handling logic here if needed
  }
}

void sendHTTPRequest(const String& url, const String& postData, String& payload) {
  HTTPClient http;

  Serial.println("---------------HTTP Request---------------");
  Serial.print("URL: ");
  Serial.println(url);
  Serial.print("POST Data: ");
  Serial.println(postData);

  http.begin(url);
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");

  int httpCode = http.POST(postData.c_str());

  payload = http.getString();

  Serial.print("HTTP Code: ");
  Serial.println(httpCode);
  Serial.print("Response Payload: ");
  Serial.println(payload);

  handleHTTPError(httpCode);

  http.end();
  Serial.println("------------------------------------------");
}

void control_modes() {
    Serial.println();
    Serial.println("---------------control_modes()");
    Serial.print("Received payload: ");
    Serial.println(payload);

    // Check if payload is empty
    if (payload.length() == 0) {
        Serial.println("Payload is empty");
        Serial.println("---------------");
        return;
    }

    // Check for success message
    if (payload.startsWith("Connected to the database successfully!")) {
        Serial.println("Connection success message received");
        Serial.println("---------------");
        return;
    }

    // Attempt to parse JSON
    DynamicJsonDocument doc(1024);
    DeserializationError error = deserializeJson(doc, payload);

    // Check for parsing errors
    if (error) {
        Serial.print("deserializeJson() failed: ");
        Serial.println(error.c_str());
        Serial.println("---------------");
        return;
    }

    // Continue with JsonObject
    JsonObject myObject = doc.as<JsonObject>();

    // Check for error in the JSON response
    if (myObject.containsKey("error")) {
        const char* errorMessage = myObject["error"];
        Serial.print("Error from server: ");
        Serial.println(errorMessage);
        Serial.println("---------------");
        return;
    }

    // Continue processing the JSON data as before
    if (myObject.containsKey("buying_mode")) {
        const char* buyingMode = myObject["buying_mode"];
        Serial.print("myObject[\"buying_mode\"] = ");
        Serial.println(buyingMode);

        if (strcmp(buyingMode, "ON") == 0) {
            digitalWrite(buying_mode, HIGH);
            Serial.println("Buying Mode is active");
        } else if (strcmp(buyingMode, "OFF") == 0) {
            digitalWrite(buying_mode, LOW);
            Serial.println("Buying Mode is off");
        }
    }

    if (myObject.containsKey("selling_mode")) {
        const char* sellingMode = myObject["selling_mode"];
        Serial.print("myObject[\"selling_mode\"] = ");
        Serial.println(sellingMode);

        if (strcmp(sellingMode, "ON") == 0) {
            digitalWrite(selling_mode, HIGH);
            Serial.println("Selling Mode is active");
        } else if (strcmp(sellingMode, "OFF") == 0) {
            digitalWrite(selling_mode, LOW);
            Serial.println("Selling Mode is off");
        }
    }

    Serial.println("---------------");
}




void get_sensor_data() {
  // Add your existing get_sensor_data logic here
    Serial.println();
  Serial.println("-------------get_sensor_data()");
  
  unsigned long currentMillis = millis();
  if (currentMillis - lastMillis >= 1000) {
    lastMillis = currentMillis;
    
    // Read voltage sensor
    
    // Read the Analog Input
   adc_value = analogRead(voltageSensorPin);
     send_voltage = (adc_value * 0.0044);// 0.00517 initial
   
   // Determine voltage at ADC input
  // adc_voltage  = (adc_value * 3.3) / 4095.0; 
   // Calculate voltage at divider input
    // send_voltage = adc_voltage / (R2/(R1+R2)) ;
    

    // Read current sensor
    int rawCurrent = analogRead(currentSensorPin);
    send_current = (rawCurrent - 2048) * (3.3 / 4096.0) / currentMultiplier; // Assuming ACS712 is centered at 1.65V for ESP32

    // Calculate power in Watts
     power = send_voltage * send_current;
      Serial.printf("Power: %.3f W\n", power);

    // Calculate units transferred (assuming 1 unit = 1 Watt-hour)
    unitsTransferred += (abs(power) / 3600000.0);

    Serial.printf("ut: %.3f W\n", unitsTransferred);


  // Check if any reads failed.
  if (isnan(send_voltage) || isnan(send_current)) {
    Serial.println("Failed to read from sensor!");
    send_voltage = 0;
    send_current = 0;
    send_Status = "FAILED";
  } else {
    send_Status = "SUCCEED";
  }
  
  Serial.printf("Voltage : %.3f V\n", send_voltage);
  Serial.printf("Current : %.3f A\n", send_current);
  Serial.printf("Status of the Sensor : %s\n", send_Status);
  Serial.println("-------------");
}

}

void setup() {
  Serial.begin(115200);

  // Initialize pins and turn off LEDs
  pinMode(ON_Board_LED, OUTPUT);
  pinMode(buying_mode, OUTPUT);
  pinMode(selling_mode, OUTPUT);
  digitalWrite(ON_Board_LED, LOW);
  digitalWrite(buying_mode, LOW);
  digitalWrite(selling_mode, LOW);

  // Connect to WiFi
  WiFi.mode(WIFI_STA);
  WiFi.begin(ssid, password);

  Serial.println("Connecting to WiFi");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nConnected to WiFi");

  delay(5000);
}

void loop() {
  if (WiFi.status() == WL_CONNECTED) {

// Prepare JSON data
StaticJsonDocument<200> jsonDoc;
jsonDoc["id"] = "esp32_01";
serializeJson(jsonDoc, payload);


// Fetch data
sendHTTPRequest("http://192.168.104.25/mysql_esp/getdata.php", "id=esp32_01", payload);
Serial.println("Response from server:");
Serial.println(payload);
control_modes();
delay(1000);


    // Get sensor data
    get_sensor_data();

    // Send sensor data to the server
    String buying_mode_State = (digitalRead(buying_mode) == 1) ? "ON" : "OFF";
    String selling_mode_State = (digitalRead(selling_mode) == 1) ? "ON" : "OFF";
    postData = "id=esp32_01";
    postData += "&voltage=" + String(send_voltage);
    postData += "&current=" + String(send_current);
    postData += "&units_transfered=" + String(unitsTransferred);
    postData += "&status=" + send_Status;
    postData += "&buying_mode=" + buying_mode_State;
    postData += "&selling_mode=" + selling_mode_State;
    
    sendHTTPRequest("http://192.168.104.25/mysql_esp/update_record.php", postData, payload);

    delay(4000);
  }
}
