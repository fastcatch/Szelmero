#include "wiring.h"
#include "AS5030.h"

AS5030::AS5030()
{
}

void AS5030::setup()
{
  // setup pins for 3-wire R/W mode
  /* DATA_PIN it is R/W really, so it is not globally set */
  pinMode(AS5030_CLOCK_PIN, OUTPUT);
  pinMode(AS5030_SELECT_PIN, OUTPUT);

  // give some default values
  digitalWrite(AS5030_CLOCK_PIN, HIGH);
  digitalWrite(AS5030_SELECT_PIN, HIGH);
}

int AS5030::read()
// read the current data from sensor
// return true if data is valid
{
  int  reading;
  int  i;
  
  // 
  // send read command (5 bits of 0)
  //
  digitalWrite(AS5030_SELECT_PIN, HIGH);
  digitalWrite(AS5030_CLOCK_PIN, LOW);
  pinMode(AS5030_DATA_PIN, OUTPUT);  
  digitalWrite(AS5030_DATA_PIN, LOW);  // Command is all 0's, set it only once
  delayMicroseconds(1);
  for (int i=0; i<5; i++)
  {
    digitalWrite(AS5030_CLOCK_PIN, LOW);
    delayMicroseconds(1);
    digitalWrite(AS5030_CLOCK_PIN, HIGH);
    delayMicroseconds(1);
  }
  
  //
  // shift in data  
  //
  unsigned int dataBit;
  reading = 0;
  pinMode(AS5030_DATA_PIN, INPUT);  
  for (int i=15; i>=0; i--)
  {
    digitalWrite(AS5030_CLOCK_PIN, LOW);
    delayMicroseconds(1);
    digitalWrite(AS5030_CLOCK_PIN, HIGH);
    delayMicroseconds(1);
    
    reading = (reading << 1) | digitalRead(AS5030_DATA_PIN);
  }
  // Let the chip go
  digitalWrite(AS5030_SELECT_PIN, LOW);
  
  //
  // split-and-store
  //
  C2_status = ((reading & 0x8000) != 0);
  field_strength = (((~reading) >> 8) & 0x3F);
  position = reading & 0xFF;
  locked =  ((reading & 0x4000) != 0) && (field_strength > 8);  /* field strength & value is empirical not as per spec! */ 

  return isReadingValid();
}

int AS5030::isReadingValid()
{
  return(locked);
}

unsigned int AS5030::magneticFieldStrengthPercent()
{
  return( (unsigned int)field_strength * 100u / 0x3Fu );
}

unsigned int AS5030::angularPosition()
{
  return(position);
}

//////////////////////////////////////////////////////

AS5030 windDirection=AS5030();
