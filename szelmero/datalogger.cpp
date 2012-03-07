#include <wiring.h>
#include "datalogger.h"

WindDataLoggerClass::WindDataLoggerClass()
{
  for (bufferIndex=0; bufferIndex<LOG_TOTAL_SLOTS; bufferIndex++) {
    buffer[bufferIndex].avgRotations = -1;
    buffer[bufferIndex].maxGustRotations = -1;
    buffer[bufferIndex].avgDirection = -1;
  }
  bufferIndex = 0;
  initializeEntry();
}

void WindDataLoggerClass::initializeEntry()
{
  buffer[bufferIndex].avgRotations = 0;
  buffer[bufferIndex].maxGustRotations = 0;
  buffer[bufferIndex].avgDirection = -1;
  gustRotations = 0;
  accumulatedDirection = 0;
  lastDirection = 0;
  cntDirection = 0;
}

void WindDataLoggerClass::recordGust()
// Will be fired when a gust averaging period completes
{
  if ( gustRotations > buffer[bufferIndex].maxGustRotations )
    buffer[bufferIndex].maxGustRotations = gustRotations;
  gustRotations = 0;
}

void WindDataLoggerClass::recordDirection()
// Fetch direction and "add" to sample "total"
{
  int16_t delta;
  int16_t actualDirection;
  int16_t dir;

  /*** Get direction from sensor ***/
  if ( !windDirection.read() )
    return;
  dir = windDirection.angularPosition();
  
  /* Theoretically required, not needed now
  if ( count >= 32768 / 256 )
    return;
  */
    
  if ( cntDirection == 0 ) {
    accumulatedDirection = lastDirection = dir;
  }
  else {
    delta = dir - lastDirection;
    // adjust
    if ( abs(delta) < 128 )
      actualDirection = dir;
    else if ( delta < -128 )
      actualDirection = dir+256;
    else if ( delta > 128) 
      actualDirection = dir-256;
    else // @ delta = 128
      return;
      
    lastDirection = actualDirection;
    accumulatedDirection += actualDirection;
  }
  
  cntDirection++;
}

void WindDataLoggerClass::recordAvg()
// Will be fired when an averaging period completes
{
  /*** Compute avg angle ***/
  uint8_t avgDirInPosition;
  if ( cntDirection > 0 ) {
    // Calculate and adjust for North
    avgDirInPosition = accumulatedDirection / cntDirection - NORTH_POSITION;
    // Store in position units (0..255), convert to degrees in output if needed
    buffer[bufferIndex].avgDirection = avgDirInPosition;
  }
    
  /*** Advance buffer ***/
  bufferIndex = next(bufferIndex);
  initializeEntry();
}


WindDataLoggerClass windData;
