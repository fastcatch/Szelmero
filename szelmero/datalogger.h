#ifndef WINDMETER_H
#define WINDMETER_H

#include <inttypes.h>

#include "global.h"

#define LOG_ENTRY_SLOTS 15
#define LOG_SAFETY_SLOTS 2 /* Provides room for processing the queue without overwriting from another thread (IRQ) */
#define LOG_TOTAL_SLOTS (LOG_ENTRY_SLOTS+LOG_SAFETY_SLOTS)

class WindDataLoggerClass
{
  public:
    windDataEntryType volatile buffer[LOG_TOTAL_SLOTS];
    volatile uint8_t bufferIndex;

    WindDataLoggerClass();
    void rotationComplete() { buffer[bufferIndex].avgRotations++; gustRotations++; }
    void recordGust();
    void recordAvg();
    uint8_t next(uint8_t index) { return( index >= LOG_TOTAL_SLOTS-1 ? 0 : index+1 ); }
    uint8_t skipBy(uint8_t index, int8_t skipSlots) { return( (index+skipSlots) % LOG_TOTAL_SLOTS ); }
    
  private:
    volatile unsigned long lastRotationAtMillis;
    volatile int8_t gustRotations;
    
    void initializeEntry();
};

extern WindDataLoggerClass windData;
#endif
