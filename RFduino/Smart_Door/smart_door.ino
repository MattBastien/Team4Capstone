#include <RFduinoBLE.h>

// Various pinouts needed for operation
#define LOCK_OPENED_PIN       (0)
#define LOCK_CLOSED_PIN       (1)
#define CONNECTED_LED_PIN     (2)
#define REED_SENSOR_PIN       (3)
#define ADVERTISEMENT_LED_PIN (4)
#define RELAY_FORWARD_PIN     (5)
#define RELAY_REVERSE_PIN     (6)

// Accepted inputs from BLE master device
#define SET_INTERVAL   (0)
#define SET_LOCK_STATE (1)

int connected = 0;
int interval = 1000;
int seq = 0;
int tx = 0;
int elapsedTime = 0;
char data[5];

const int STATE_ERROR     = -1;
const int STATE_IDLE      = 0;
const int STATE_LOCKING   = 1;
const int STATE_UNLOCKING = 2;
int state = STATE_IDLE;

const int UNLOCKED_STATE   = 0;
const int LOCKED_STATE     = 1;
const int TRANSITION_STATE = 2;
int lockState = TRANSITION_STATE;

int lock = 0;

void set_motor_state(int st) {
  if (lock == 0) {
    lock = 1;
    switch (st) {
      case STATE_UNLOCKING:
        if (lockState != UNLOCKED_STATE) {
          state = STATE_UNLOCKING;
          digitalWrite(RELAY_FORWARD_PIN, LOW);
          digitalWrite(RELAY_REVERSE_PIN, HIGH);
        }
        break;
      case STATE_LOCKING:
        if (lockState != LOCKED_STATE) {
          state = STATE_LOCKING;
          digitalWrite(RELAY_REVERSE_PIN, LOW);
          digitalWrite(RELAY_FORWARD_PIN, HIGH);
        }
        break;
      default:
        state = STATE_IDLE;
        digitalWrite(RELAY_FORWARD_PIN, LOW);
        digitalWrite(RELAY_REVERSE_PIN, LOW);
        break;
    }
    lock = 0;
  }
}

void TIMER1_interrupt() {
  if (NRF_TIMER1->EVENTS_COMPARE[0] != 0)
  {
    elapsedTime += 50;
    if (elapsedTime >= interval) {
      if (connected) {
        // Get the state of the magnetic reed sensor
        int reed = digitalRead(REED_SENSOR_PIN) == HIGH ? 0x01 : 0x00;
        
        // Fill the array of data to send
        data[0] = seq & 0xFF;
        data[1] = reed & 0xFF;
        data[2] = lockState & 0xFF;
        data[3] = (interval >> 8) & 0xFF;
        data[4] = interval & 0xFF;
        
        tx = 1;
        seq++;
        if (seq > 255) {
          seq = 0;
        }
      }
      elapsedTime = 0;
    }
    NRF_TIMER1->EVENTS_COMPARE[0] = 0;
  }
}

int LOCK_OPENED_interrupt(uint32_t pin) {
  if (state == STATE_UNLOCKING) {
    set_motor_state(STATE_IDLE);
  }
  lockState = UNLOCKED_STATE;
  return 1;
}

int LOCK_CLOSED_interrupt(uint32_t) {
  if (state == STATE_LOCKING) {
    set_motor_state(STATE_IDLE);
  }
  lockState = LOCKED_STATE;
  return 1;
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
  // Start BLE transmission with Custom UUID
  RFduinoBLE.customUUID = "c97433f0-be8f-4dc8-b6f0-5343e6100eb4";
  RFduinoBLE.begin();
  
  // Setup door stop switches
  pinMode(LOCK_OPENED_PIN, INPUT_PULLDOWN);
  pinMode(LOCK_CLOSED_PIN, INPUT_PULLDOWN);
  attachPinInterrupt(LOCK_OPENED_PIN, LOCK_OPENED_interrupt, HIGH);
  attachPinInterrupt(LOCK_CLOSED_PIN, LOCK_CLOSED_interrupt, HIGH);

  // Setup the rest of the pins
  pinMode(CONNECTED_LED_PIN, OUTPUT);
  pinMode(ADVERTISEMENT_LED_PIN, OUTPUT);
  pinMode(RELAY_FORWARD_PIN, OUTPUT);
  pinMode(RELAY_REVERSE_PIN, OUTPUT);
  pinMode(REED_SENSOR_PIN, INPUT_PULLDOWN);

  // Configure the timer
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
      case SET_LOCK_STATE:
        if (len == 2) {
          int desiredLockState = data[1] ? LOCKED_STATE : UNLOCKED_STATE;
          if (lockState != desiredLockState) {
            if (desiredLockState == LOCKED_STATE) {
              set_motor_state(STATE_LOCKING);
            } else {
              set_motor_state(STATE_UNLOCKING);
            }
          }
        }
        break;
    }
  }
}

void loop() {
  // Transmit data if necessary
  if (tx) {
    // Send the data
    RFduinoBLE.send(data, 5);
    digitalWrite(ADVERTISEMENT_LED_PIN, HIGH);
    tx = 0;
  }

  // Delay and turn off LED if necessary
  delay(50);
  if (connected) {
    digitalWrite(ADVERTISEMENT_LED_PIN, LOW);
  }
}
