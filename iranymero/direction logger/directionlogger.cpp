#include "directionlogger.h"
#include <stdlib.h>

DirectionLoggerClass::DirectionLoggerClass()
{
  clear();
  north_position = 0;
}

void DirectionLoggerClass::clear()
{
  count = 0;
}

boolean DirectionLoggerClass::add(byte dir)
// returns true if dir was valid, false otherwise
{
  int delta;
  int actual_direction;

  /* Theoretically required, not needed now
  if ( count >= 32768 / 256 )
    return(0);
  */
    
  if ( count == 0 ) {
    accumulated_direction = last_direction = dir;
  }
  else {
    delta = (int)dir - last_direction;
    // adjust
    if ( abs(delta) < 128 )
      actual_direction = dir;
    else if ( delta < -128 )
      actual_direction = dir+256;
    else if ( delta > 128) 
      actual_direction = dir-256;
    else // @ delta = 128
      return(0);
      
    last_direction = actual_direction;
    accumulated_direction += actual_direction;
  }
  
  count++;
  return(1);
}

boolean DirectionLoggerClass::average(byte *avg)
// returns true if avg is valid, false otherwise
{
  if ( count == 0 )
    return(0);
    
  *avg = accumulated_direction / count;
  return(1);
}

boolean DirectionLoggerClass::average_in_degrees(int *avg_in_degrees)
// calculates average direction in degrees, clockwise, North as 0
// returns true if avg is valid, false otherwise
{
  byte avg_in_position;
  
  *avg_in_degrees = 0;
  
  if ( !average(&avg_in_position) ) {
    return( 0 );
  }

  // Rotate North to zero
  // and switch to increasing in clockwise
  avg_in_position = - (avg_in_position - north_position);
  // Convert to degrees:
  // (avg_in_position % 256) * 360 / 256
  // done in two branches to avoid integer overflow
  if ( avg_in_position >= 128 ) {
    *avg_in_degrees = 180;
    avg_in_position -= 128;
  }
  *avg_in_degrees += ((unsigned int)avg_in_position * 360u) >> 8;

  return(1);
}
