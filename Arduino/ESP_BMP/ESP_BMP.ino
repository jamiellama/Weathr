#include <Wire.h>
#include <SPI.h>
#include <Adafruit_Sensor.h>
#include <Adafruit_BME280.h>
#include <ESP8266WiFi.h>
#include <Adafruit_GFX.h>
#include <Adafruit_SSD1306.h>
#include <NTPClient.h>
#include <WiFiUdp.h>
#include <ESP8266HTTPClient.h>

#define SEALEVELPRESSURE_HPA (1013.25)
#define SCREEN_WIDTH 128
#define SCREEN_HEIGHT 64

const char* ssid = "BT-KCCP8G";
const char* password = "Banana12";

const long utcOffsetInSeconds = 3600;
char daysOfTheWeek[7][12] = {"Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"};
WiFiUDP ntpUDP;
NTPClient timeClient(ntpUDP, "pool.ntp.org", utcOffsetInSeconds);

Adafruit_BME280 bme;
Adafruit_SSD1306 oled(SCREEN_WIDTH, SCREEN_HEIGHT, &Wire, -1);

float temperature;
float humidity;
float humidityRaw;
float pressure;
float pressureRaw;

void setup()
{
  Serial.begin(9600);
  delay(10);

  // Sensor Connectivity Check
  if (!bme.begin(0x76))
  {
    Serial.println("Cannot find BMP280");
    printSensorError();
    while (true);
  }

  if (!oled.begin(SSD1306_SWITCHCAPVCC, 0x3C))
  {
    Serial.println(F("Cannot find SSD1306"));
    printSensorError();
    while (true);
  }

  // Start time client
  timeClient.begin();

  //oled.clearDisplay();
  //oled.setTextSize(2);
  //oled.setTextColor(WHITE);
  //oled.setCursor(0,10);
  //oled.println("Connecting");
  //oled.setCursor(0,30);
  //oled.println("to WiFi...");
  //oled.display();

  // Start wifi client
  WiFi.begin(ssid, password);
  Serial.print("Connecting to ");
  Serial.print(ssid); Serial.println(" ...");

  while (WiFi.status() != WL_CONNECTED)
  {
    Serial.print('.'); Serial.print(' ');
    printWifiConnect();
  }

  Serial.println('\n');
  Serial.println("Success!");
  Serial.print("NodeMCU IP address:\t");
  Serial.println(WiFi.localIP());

}

void loop()
{
  if (WiFi.status() != WL_CONNECTED)
  {
    wifiReconnect();
  }
  else
  {
    readBME();

    serialBME();

    printTime();
    delay(5000);
    printTemperature();
    delay(5000);
    printHumidity();
    delay(5000);
    printPressure();
    delay(5000);
    printTime();
    delay(5000);
    printTemperature();
    delay(5000);
    printHumidity();
    delay(5000);
    printPressure();
    delay(5000);

    http();
  }

}


void wifiReconnect()
{
  WiFi.disconnect();
  WiFi.begin(ssid, password);
  Serial.print("Reconnecting to ");
  Serial.print(ssid); Serial.println(" ...");

  int i = 0;
  while (WiFi.status() != WL_CONNECTED)
  {
    delay(1000);
    Serial.print(++i); Serial.print(' ');
  }

  Serial.println('\n');
  Serial.println("Success!");
}

void http()
{
  String posting;
  String apiKey = "jpETCif5i45C5aDjBGiH";
  HTTPClient http;

  posting = "apiKey=" + apiKey + "&outdoorTemp=" + String(temperature) + "&outdoorHumid=" + String(humidity) + "&outdoorPress=" + String(pressure) ;
  
  http.begin("http://weathr.jamielarkin.co.uk/services/post_data.php");

  http.addHeader("Content-Type", "application/x-www-form-urlencoded");

  int response = http.POST(posting);
  String payload = http.getString();

  Serial.println(posting);
  Serial.println(response);
  Serial.println(payload);

  http.end();
}

void readBME()
{
  temperature = bme.readTemperature();
  humidityRaw = bme.readHumidity();
  pressureRaw = (bme.readPressure() / 100.0F);

  humidity = round(humidityRaw);
  pressure = round(pressureRaw);
}

void serialBME()
{
  timeClient.update();
  Serial.println('\n');

  Serial.print(daysOfTheWeek[timeClient.getDay()]);
  Serial.print(" - ");
  Serial.println(timeClient.getFormattedTime());

  Serial.print("Temperature: ");
  Serial.print(temperature);
  Serial.println("*C");

  Serial.print("Humidity: ");
  Serial.print(humidity);
  Serial.println("%");

  Serial.print("Pressure: ");
  Serial.print(pressure);
  Serial.println("hPa");
}

void printSensorError()
{
  oled.clearDisplay();
  oled.setTextSize(2);
  oled.setTextColor(WHITE);
  oled.setCursor(0, 10);
  oled.println("ERROR!");
  oled.setCursor(0, 30);
  oled.println("Sensor");
  oled.setCursor(0, 50);
  oled.println("Failure");
  oled.display();
}

void printWifiConnect()
{
  oled.clearDisplay();
  oled.setTextSize(2);
  oled.setTextColor(WHITE);
  oled.setCursor(0, 10);
  oled.println("Connecting");
  oled.setCursor(0, 30);
  oled.println("to WiFi.");
  oled.display();

  delay(1000);

  oled.clearDisplay();
  oled.setTextSize(2);
  oled.setTextColor(WHITE);
  oled.setCursor(0, 10);
  oled.println("Connecting");
  oled.setCursor(0, 30);
  oled.println("to WiFi..");
  oled.display();

  delay(1000);

  oled.clearDisplay();
  oled.setTextSize(2);
  oled.setTextColor(WHITE);
  oled.setCursor(0, 10);
  oled.println("Connecting");
  oled.setCursor(0, 30);
  oled.println("to WiFi...");
  oled.display();
  delay(1000);
}

void printTemperature()
{
  oled.clearDisplay();
  oled.setTextSize(2);
  oled.setTextColor(WHITE);
  oled.setCursor(20, 0);
  oled.println("Outdoor");
  oled.setCursor(33, 18);
  oled.println("Temp:");
  oled.setTextSize(3);
  oled.setCursor(19, 42);
  oled.print(temperature, 1);
  oled.setTextSize(2);
  oled.print("C");
  oled.display();
}

void printHumidity()
{
  oled.clearDisplay();
  oled.setTextSize(2);
  oled.setTextColor(WHITE);
  oled.setCursor(20, 0);
  oled.println("Outdoor");
  oled.setCursor(12, 18);
  oled.println("Humidity:");
  oled.setTextSize(3);
  oled.setCursor(40, 42);
  oled.print(humidity, 0);
  oled.setTextSize(2);
  oled.print("%");
  oled.display();
}

void printPressure()
{
  oled.clearDisplay();
  oled.setTextSize(2);
  oled.setTextColor(WHITE);
  oled.setCursor(4, 0);
  oled.println("Barometric");
  oled.setCursor(10, 18);
  oled.println("Pressure:");
  oled.setTextSize(3);
  oled.setCursor(8, 42);
  oled.print(pressure, 0);
  oled.setTextSize(2);
  oled.print("hPa");
  oled.display();
}

void printTime()
{
  oled.clearDisplay();
  oled.setTextSize(3);
  oled.setTextColor(WHITE);
  oled.setCursor(20, 20);
  oled.print(timeClient.getFormattedTime());
  oled.display();
}
