#include <cstdlib>
#include <iostream>

using namespace std;

#include "directionlogger.h"

DirectionLoggerClass  directions;

int main(int argc, char *argv[])
{
  int i, avg_deg, data;
  byte  avg;
      
  directions.clear();
  directions.north_position = 64;
  
  for (i=1; i<argc; i++) {
    data = atoi(argv[i]);
    if ( i>1 )
      cout << ",";
    cout << data;
    directions.add(data);
  }
  directions.average(&avg);
  directions.average_in_degrees(&avg_deg);
  cout << " => " << (int)avg << " = " << avg_deg << " degrees\n";  
}
