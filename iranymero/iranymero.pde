#include "AS5030.h"

void setup()
{
  Serial.begin(9600);
  
  windDirection.setup();
}

void loop()
{
  windDirection.read();
  Serial.print("Locked:");  Serial.println(windDirection.isReadingValid() ? "YES" : "no"); 
  Serial.print("Field strength (%):");  Serial.println(windDirection.magneticFieldStrengthPercent(),DEC); 
  Serial.print("Position:");  Serial.println(windDirection.angularPosition(),DEC); 
  Serial.println("");
  delay(3000);
}
