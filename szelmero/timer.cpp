#include <string.h>
#include <avr/io.h>

#include "DS1307.h"
#include "timer.h"

void TimerClass::getTimeFromDS1307()
{
  // Read time from 1307
  time.second = RTC.get(DS1307_SEC,true);  // refresh buffer before reading
  time.minute = RTC.get(DS1307_MIN,false);
  time.hour   = RTC.get(DS1307_HR,false);
  time.dow    = RTC.get(DS1307_DOW,false);
  time.date   = RTC.get(DS1307_DATE,false);
  time.month  = RTC.get(DS1307_MTH,false);
  time.year   = RTC.get(DS1307_YR,false);
}

void TimerClass::syncronize()
{
  getTimeFromDS1307();
}

void TimerClass::init()
{
  // Init globals
  RTC.start();
  syncronize();
}

int TimerClass::setRTC(time_st *new_time)
{
  if ( new_time->year > 99 ) return -1;
  if ( new_time->month > 12 ) return -1;
  switch ( new_time->month ) {
    case 1:
    case 3:
    case 5: 
    case 7:
    case 8:
    case 10:
    case 12:
      if ( new_time->date > 31 ) return -1;
      break;
    case 4:
    case 6:
    case 9: 
    case 11:
      if ( new_time->date > 30 ) return -1;
      break;
    case 2:
      if ( new_time->date > 28 ) return -1;   // Won't allow 02/29 in leap years -- doesn't matter
      break;
    default:
      return -1;
  }
  if ( new_time->dow > 6 ) return -1;
  if ( new_time->hour > 23 ) return -1;
  if ( new_time->minute > 59 ) return -1;
  if ( new_time->second > 59 ) return -1;

  RTC.stop();
  RTC.start();
  RTC.set(DS1307_SEC,new_time->second);
  RTC.set(DS1307_MIN,new_time->minute);
  RTC.set(DS1307_HR,new_time->hour);
  RTC.set(DS1307_DOW,new_time->dow);
  RTC.set(DS1307_DATE,new_time->date);
  RTC.set(DS1307_MTH,new_time->month);
  RTC.set(DS1307_YR,new_time->year);

  getTimeFromDS1307();

  return 0;
}

void TimerClass::get(time_st *t, int8_t refresh)
{
  if ( refresh ) getTimeFromDS1307();
  memcpy((void *)t,(const void *)&time,sizeof(struct time_st));
}

void TimerClass::getInStr(char timestr[21])
{
  uint8_t i=0;
  
  getTimeFromDS1307();
  timestr[i++]='2'; timestr[i++]='0'; timestr[i++]='0'+time.year/10; timestr[i++]='0'+time.year%10; timestr[i++]='.';
  timestr[i++]='0'+time.month/10; timestr[i++]='0'+time.month%10; timestr[i++]='.';
  timestr[i++]='0'+time.date/10; timestr[i++]='0'+time.date%10; timestr[i++]='.';
  timestr[i++]=' ';
  timestr[i++]='0'+time.hour/10; timestr[i++]='0'+time.hour%10; timestr[i++]=':';
  timestr[i++]='0'+time.minute/10; timestr[i++]='0'+time.minute%10; timestr[i++]=':';
  timestr[i++]='0'+time.second/10; timestr[i++]='0'+time.second%10;
  timestr[i++]=0;
}

int TimerClass::setFromStr(char time_str[3*2+3*2])
{
// input YYMMDDHHMMSS
  time_st new_time;
  
  new_time.year = (uint8_t) ( (time_str[0]-48)*10 + (time_str[1]-48) );
  new_time.month = (uint8_t) ( (time_str[2]-48)*10 + (time_str[3]-48) );
  new_time.date = (uint8_t) ( (time_str[4]-48)*10 + (time_str[5]-48) );
  new_time.hour = (uint8_t) ( (time_str[6]-48)*10 + (time_str[7]-48) );
  new_time.minute = (uint8_t) ( (time_str[8]-48)*10 + (time_str[9]-48) );
  new_time.second = (uint8_t) ( (time_str[10]-48)*10 + (time_str[11]-48) );
  
  // set dow
  time_t new_time_in_secs = convertDateTimeToSeconds(&new_time);
  convertDateTimeToStruct(new_time_in_secs, &new_time);
  
  return setRTC(&new_time);
}

/* Return 1 if YEAR + TM_YEAR_BASE is a leap year.  */
int TimerClass::leapYear (int year)
{
  year += TM_YEAR_BASE;
  return
    (year & 3) == 0
     && ( (year % 100 != 0) || (year % 400 == 0) );
}

/* How many days come before each month (0-12).  */
static const unsigned int __mon_yday[2][13] =
  {
    /* Normal years.  */
    { 0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334, 365 },
    /* Leap years.  */
    { 0, 31, 60, 91, 121, 152, 182, 213, 244, 274, 305, 335, 366 }
  };

time_t TimerClass::convertDateTimeToSeconds(time_st *t)
{
  time_t timeInSeconds;
  int i, days=0;
  
  // Add up days
  for (i=0; i<t->year; i++)
    days += __mon_yday[leapYear(i)][12];
  days += __mon_yday[leapYear(t->year)][t->month-1];
  days += t->date-1;

  timeInSeconds = (time_t)days * (time_t)86400;
  
  // Add up seconds in the given day
  timeInSeconds += (time_t) t->hour * (time_t)(60*60);
  timeInSeconds += (time_t) t->minute * (time_t)(60);
  timeInSeconds += (time_t) t->second;
  
  return( timeInSeconds );
}

void TimerClass::convertDateTimeToStruct(time_t timeInSeconds, time_st *t)
{
  t->second = timeInSeconds % 60;
  timeInSeconds = timeInSeconds/60;
  t->minute = timeInSeconds % 60;
  timeInSeconds = timeInSeconds/60;
  t->hour = timeInSeconds % 24;
  timeInSeconds = timeInSeconds/24;

  // we now have the number of days since "zero" in timeInSeconds
  // let's adjust dow based on this
  t->dow = (timeInSeconds + TM_YEAR_BASE_JAN1_DOW) % 7;

  // let's figure years
  for (t->year = 0;
       timeInSeconds > 365+leapYear(t->year);
       timeInSeconds -= 365+leapYear(t->year))
    t->year++;

  // we now have only months and days in timeInSeconds
  // let's get months
  for (t->month = 1;
       timeInSeconds > __mon_yday[leapYear(t->year)][t->month];
       )
    t->month++;

  // and now days
  t->date = timeInSeconds - (__mon_yday[leapYear(t->year)][t->month-1]);
}

TimerClass timer;
