#include <RFduinoBLE.h>

// Various pinouts needed for operation
#define CONNECTED_LED_PIN (2)
#define ADVERTISEMENT_LED_PIN (4)
#define RELAY_PIN (5)
#define CURRENT_SENSOR_PIN (6)

// Accepted inputs from BLE master device
#define SET_INTERVAL   (0)
#define SET_RELAY_STATE (1)

int connected = 0;
int interval = 1000;
int seq = 0;
int elapsedTime = 0;
int tx = 0;
int on = 0;
int allow_current = 1;
char data[5];

void TIMER1_interrupt() {
  if (NRF_TIMER1->EVENTS_COMPARE[0] != 0)
  {
    elapsedTime += 50;
    if (elapsedTime >= interval) {
      if (connected) {
    
        // Get the state of the magnetic reed sensor
        int current = digitalRead(CURRENT_SENSOR_PIN) == LOW ? 0x00 : 0x01;
        
        // Fill the array of data to send
        data[0] = seq & 0xFF;
        data[1] = current & 0xFF;
        data[2] = allow_current & 0xFF;
        data[3] = (interval >> 8) & 0xFF;
        data[4] = interval & 0xFF;
        
        tx = 1;
        seq++;
      }
      elapsedTime = 0;
    }
    NRF_TIMER1->EVENTS_COMPARE[0] = 0;
  }
}

void timer_config()
{
  NRF_TIMER1->TASKS_STOP = 1;   // Stop timer
  NRF_TIMER1->MODE = TIMER_MODE_MODE_Timer;  // taken from Nordic dev zone
  NRF_TIMER1->BITMODE = (TIMER_BITMODE_BITMODE_16Bit << TIMER_BITMODE_BITMODE_Pos);
  NRF_TIMER1->PRESCALER = 4;   // SysClk/2^PRESCALER) =  16,000,000/16 = 1us resolution
  NRF_TIMER1->TASKS_CLEAR = 1; // Clear timer
  NRF_TIMER1->CC[0] = 50 * 1000; // Cannot exceed 16bits
  NRF_TIMER1->INTENSET = TIMER_INTENSET_COMPARE0_Enabled << TIMER_INTENSET_COMPARE0_Pos;  // taken from Nordic dev zone
  NRF_TIMER1->SHORTS = (TIMER_SHORTS_COMPARE0_CLEAR_Enabled << TIMER_SHORTS_COMPARE0_CLEAR_Pos);
  attachInterrupt(TIMER1_IRQn, TIMER1_interrupt);    // also used in variant.cpp to configure the RTC1
  NRF_TIMER1->TASKS_START = 1;   // Start TIMER
}

void setup() {
  RFduinoBLE.customUUID = "c97433f0-be8f-4dc8-b6f0-5343e6100eb4";
  RFduinoBLE.begin();
  pinMode(CONNECTED_LED_PIN, OUTPUT);
  pinMode(ADVERTISEMENT_LED_PIN, OUTPUT);
  pinMode(RELAY_PIN, OUTPUT);
  pinMode(CURRENT_SENSOR_PIN, INPUT);
  timer_config();
}

void RFduinoBLE_onConnect() {
  connected = 1;
  digitalWrite(CONNECTED_LED_PIN, HIGH);
}

void RFduinoBLE_onDisconnect() {
  connected = 0;
  digitalWrite(CONNECTED_LED_PIN, LOW);
}

void RFduinoBLE_onAdvertisement(bool start)
{
  digitalWrite(ADVERTISEMENT_LED_PIN, start ? HIGH : LOW);
}

void RFduinoBLE_onReceive(char *data, int len) {
  if (len >= 1) {
    switch (data[0]) {
      case SET_INTERVAL:
        if (len == 3) {
              int d = data[2];
              d += data[1] << 8;
              interval = d;
        }
        break;
      case SET_RELAY_STATE:
        if (len == 2) {
          if (data[1]) {
            allow_current = 1;
            digitalWrite(RELAY_PIN, LOW);
          } else {
            allow_current = 0;
            digitalWrite(RELAY_PIN, HIGH);
          }
        }
        break;
    }
  }
}

void loop() {
  if (tx) {
    // Send the data
    RFduinoBLE.send(data, 4);
    digitalWrite(ADVERTISEMENT_LED_PIN, HIGH);
    tx = 0;
  }
  delay(10);
  if (connected) {
    digitalWrite(ADVERTISEMENT_LED_PIN, LOW);
  }
}
