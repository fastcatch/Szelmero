#ifndef TIMER_H
#define TIMER_H

#include <inttypes.h>
#include "global.h"

#define TM_YEAR_BASE 2000
#define TM_YEAR_BASE_JAN1_DOW 5 /* days: 0=Monday,...,6=Sunday */

typedef long int time_t;

struct time_st {
  uint8_t year;
  uint8_t month;
  uint8_t date;
  uint8_t dow;
  uint8_t hour;
  uint8_t minute;
  uint8_t second;
};

class TimerClass {
  private:
    time_st time;
  
    void getTimeFromDS1307();
    int leapYear(int year);
    int setRTC(time_st *new_time);

  public:    
    void init();
    void syncronize();
    void get(time_st *, int8_t refresh);
    void getInStr(char timestr[21]);
    int setFromStr(char time_str[3*2+3*2]);
    time_t convertDateTimeToSeconds(time_st *t);
    void convertDateTimeToStruct(time_t timeInSeconds, time_st *t);
};

extern TimerClass timer;

//extern volatile unsigned int seconds_since_last_clock_update;
#endif
