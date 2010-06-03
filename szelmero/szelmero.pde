#include <Wire.h>

#include <enc28j60.h>
#include <etherShield.h>
#include <ip_arp_udp_tcp.h>
#include <net.h>

#include <avr/interrupt.h>
#include <avr/io.h>
#include <ctype.h>
#include <inttypes.h>

#include "global.h"
#include "timer.h"
#include "datalogger.h"
#include "webserver.h"

#define TIMER_IRQ 0 /* DS1307 SQW out connected to this interrupt */
#define WINDSPEED_IRQ 1 /* Rotating cups "rotation complete" connected to this interrupt */

/************** HEARTBEAT ACTION *********************/
volatile uint8_t heartbeatGustSecs=0;;
volatile uint8_t heartbeatAvgSecs=0;;
void heartbeat()
{
  if ( ++heartbeatGustSecs >= GUST_SECS ) {
    windData.recordGust();
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

int availableMemory() 
{
  int size = 2048;
  byte *buf;
  while ((buf = (byte *) malloc(--size)) == NULL);
  free(buf);
  return size;
}

/* ================================================*/
void setup()
{
//  Serial.begin(9600);

  webserver.init();
  timer.init();
  attachInterrupt(TIMER_IRQ, heartbeat, FALLING);
  attachInterrupt(WINDSPEED_IRQ, recordRotation, FALLING);

//  Serial.print("Free:");  Serial.println(availableMemory());
}

void loop()
{
  // Serve web page as needed
  webserver.serve();
}
