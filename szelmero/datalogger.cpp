#include <wiring.h>
#include "datalogger.h"

WindDataLoggerClass::WindDataLoggerClass()
{
  for (bufferIndex=0; bufferIndex<LOG_TOTAL_SLOTS; bufferIndex++) {
    buffer[bufferIndex].avgRotations = -1;
    buffer[bufferIndex].maxGustRotations = -1;
  }
  bufferIndex = 0;
  initializeEntry();
}

void WindDataLoggerClass::initializeEntry()
{
  buffer[bufferIndex].avgRotations = 0;
  buffer[bufferIndex].maxGustRotations = 0;
  gustRotations = 0;
}

void WindDataLoggerClass::recordGust()
// Will be fired when a gust averaging period completes
{
  if ( gustRotations > buffer[bufferIndex].maxGustRotations )
    buffer[bufferIndex].maxGustRotations = gustRotations;
  gustRotations = 0;
}

void WindDataLoggerClass::recordAvg()
// Will be fired when an averaging period completes
{
  bufferIndex = next(bufferIndex);
  initializeEntry();
}


WindDataLoggerClass windData;
