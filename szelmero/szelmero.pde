#include <Wire.h>

#include <enc28j60.h>
#include <etherShield.h>
#include <ip_arp_udp_tcp.h>
#include <net.h>

#include <avr/interrupt.h>
#include <avr/io.h>
#include <avr/wdt.h>
#include <ctype.h>
#include <inttypes.h>

#include "global.h"
#include "timer.h"
#include "AS5030.h"
#include "datalogger.h"
#include "webserver.h"

#define TIMER_IRQ 0 /* DS1307 SQW out connected to this interrupt */
#define WINDSPEED_IRQ 1 /* Rotating cups "rotation complete" connected to this interrupt */

/************** HEARTBEAT ACTION *********************/
volatile uint8_t heartbeatGustSecs;
volatile uint8_t heartbeatAvgSecs;
void heartbeat()
{
  if ( ++heartbeatGustSecs >= GUST_SECS ) {
    windData.recordGust();
    windData.recordDirection();
    heartbeatGustSecs = 0;
  }
  if ( ++heartbeatAvgSecs >= AVG_SECS ) {
    windData.recordAvg();
    heartbeatAvgSecs = 0;
  }
}
/************** HEARTBEAT ACTION END *****************/

/************** ROTATION ACTION *********************/
void recordRotation()
{
  windData.rotationComplete();
}
/************** ROTATION ACTION END *****************/
#ifdef DEBUG
int availableMemory() 
{
  int size = 2048;
  byte *buf;
  while ((buf = (byte *) malloc(--size)) == NULL);
  free(buf);
  return size;
}
#endif

/* ================================================*/
void setup()
{
  // Clear and disable watchdog on and for bootup
  MCUSR=0;
  wdt_disable();
  
  // Init components
  webserver.init();
  timer.init();
  windDirection.setup();

  attachInterrupt(TIMER_IRQ, heartbeat, FALLING);
  attachInterrupt(WINDSPEED_IRQ, recordRotation, FALLING);

#ifdef DEBUG
  Serial.begin(9600);
  Serial.print("Free:");  Serial.println(availableMemory());
#endif

#ifdef DEBUG
  // Calibration
  int16_t dir;
  do {
    if ( windDirection.read() ) {
      dir = windDirection.angularPosition();
      Serial.print(dir, DEC);
      Serial.print("=");
      Serial.print(DIRECTION_TO_DEGREES(dir), DEC);
      Serial.println("degrees");
    }
    else {
      Serial.println("No direction data");
    }
    delay(250);
  } while ( Serial.available() == 0 );
#endif

  // Enable reset watchdog
  wdt_enable(WDTO_2S);
}

void loop()
{
  // Serve web page as needed
  webserver.serve();
  
  // Pet the watchdog
  wdt_reset();
}
