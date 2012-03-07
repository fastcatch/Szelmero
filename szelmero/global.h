#ifndef GLOBAL_H
#define GLOBAL_H

#include <inttypes.h>

// #define DEBUG

#define DEBOUNCE_MILLIS 50L

#define GUST_SECS 5 /* average gusts over this many seconds, better be a divisor of AVG_SECS */
#define AVG_SECS 60 /* average long term wind speed over this many seconds */
#define ROTATIONS_TO_KPH(R,SECS) (( (uint16_t)50 * R / (uint16_t)SECS / (uint16_t)10 )) /* One rotation is 4,5 km/h; 45->50: precise rounding */
#define DIRECTION_TO_DEGREES(ANGLE) ( (ANGLE==-1) ? -1 : (((uint16_t)180 * (uint16_t)ANGLE ) >> 7) ) /* 360 degrees / 256 units == 360/256 == 180/128  */

#define NORTH_POSITION 0

struct windDataEntryType {
  int16_t avgRotations;
  int16_t avgDirection;
  int8_t  maxGustRotations;
};

void heartbeatSetup();
void heartbeat();

#endif
