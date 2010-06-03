#include <inttypes.h>

#include "pins_arduino.h"

int read_pwm_cycle(uint8_t pin, uint8_t state, unsigned long timeout, unsigned long *t1, unsigned long *t2)
{
  	uint8_t bit = digitalPinToBitMask(pin);
	uint8_t port = digitalPinToPort(pin);
	uint8_t stateMask = (state ? bit : 0);
	unsigned long t1_width = 0;
        unsigned long t2_width = 0;
	
	// convert the timeout from microseconds to a number of times through
	// the initial loop; it takes 16 clock cycles per iteration.
	unsigned long numloops = 0;
	unsigned long maxloops = microsecondsToClockCycles(timeout) / 16;
	
	// wait for the cycle to start
	while ((*portInputRegister(port) & bit) == stateMask)
		if (numloops++ == maxloops)
			return 0;
	while ((*portInputRegister(port) & bit) != stateMask)
		if (numloops++ == maxloops)
			return 0;
	
	// wait for the first half of the cycle to end
	while ((*portInputRegister(port) & bit) == stateMask)
		t1_width++;
	// wait for the second half of the cycle to end
	while ((*portInputRegister(port) & bit) != stateMask)
		t2_width++;

	// Convert the reading to microseconds. The loop has been determined to be 10 clock cycles long.
        // In addition we have about 16 clocks between the edge and the start of the loop.
	*t1 = clockCyclesToMicroseconds(t1_width * 10 + 16); 
	*t2 = clockCyclesToMicroseconds(t2_width * 10); 
  
  return 1;
}

/////////////////////////////////////////////////////////////////////////////////////////////////////

#define PWM_IN_PIN 12

void setup()
{
  pinMode(PWM_IN_PIN, INPUT);
}

void loop()
{
  unsigned long t1, t2;
  
  read_pwm_cycle(PWM_IN_PIN, HIGH, 10000, &t1, &t2);
}
