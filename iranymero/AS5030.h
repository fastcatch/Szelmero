/*
  AS5030.h - library for Austria Microsystems AS5030
*/

// ensure this library description is only included once
#ifndef AS5030_h
#define AS5030_h

#ifndef byte
typedef unsigned char byte;
#endif
#ifndef boolean
typedef unsigned char boolean;
#endif

// define pins
#define AS5030_SELECT_PIN 7     /* = CS */
#define AS5030_CLOCK_PIN 6      /* = CLK */
#define AS5030_DATA_PIN 8       /* = DIO */
#define AS5030_MAG_RANGE_PIN 15 /* = MagRngn */
#define AS5030_DX_PIN 16        /* = DX */
#define AS5030_PWM_PIN 17       /* = PWM */

// library interface description
class AS5030
{
  // user-accessible "public" interface
  public:
    AS5030();
    void setup();
    int read();
    int isReadingValid();
    unsigned int magneticFieldStrengthPercent();
    unsigned int angularPosition();

  // library-accessible "private" interface
  private:
    boolean C2_status;
    boolean locked;
    byte position;
    byte field_strength;

};

extern AS5030 windDirection;

#endif
 

