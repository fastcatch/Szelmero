#ifndef GLOBAL_H
#define GLOBAL_H

#include <inttypes.h>

#define DEBOUNCE_MILLIS 50L

#define GUST_SECS 5 /* average gusts over this many seconds, better be a divisor of AVG_SECS */
#define AVG_SECS 60 /* average long term wind speed over this many seconds */
#define ROTATIONS_TO_KPH(R,SECS) (( (uint16_t)50 * R / (uint16_t)SECS / (uint16_t)10 )) /* One rotation is 4,5 km/h; 45->50: precise rounding */

struct windDataEntryType {
  int16_t avgRotations;
  int8_t  maxGustRotations;
};

void heartbeatSetup();
void heartbeat();

#endif
