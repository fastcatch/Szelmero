typedef unsigned char byte;
typedef signed char boolean;

class DirectionLoggerClass
{
  public:
    DirectionLoggerClass();
    void clear();
    boolean add(byte direction);
    boolean average(byte *avg);
    boolean average_in_degrees(int *avg_in_degrees);

    byte north_position;  // Set this to true North's position
    
  private:
    int  last_direction;
    int  accumulated_direction;
    byte count;
};
