#include <enc28j60.h>
#include <etherShield.h>
#include <ip_arp_udp_tcp.h>
#include <net.h>

#include <inttypes.h>

#define BUFFER_SIZE 500
#define STR_BUFFER_SIZE 22

class WebServerClass {
  private:
    // please modify the following two lines. mac and ip have to be unique
    // in your local area network. You can not have the same numbers in
    // two devices:
    uint8_t mymac[6]; 
    uint8_t myip[4];
    char baseurl[23];  // "http://192.168.90.188/";
    uint16_t mywwwport; // listen port for tcp/www (max range 1-254)
    // or on a different port:
    //static char baseurl[]="http://10.0.0.24:88/";
    //static uint16_t mywwwport =88; // listen port for tcp/www (max range 1-254)
    //

    EtherShield es;
    char strbuf[STR_BUFFER_SIZE+1];
    uint8_t buf[BUFFER_SIZE+1];
    
    int8_t analyseCmd(char *str);
    uint8_t findKeyVal(char *str,char *key);

    uint16_t addDateTimeToPage(uint8_t *buf, uint16_t plen);
    uint16_t prepareDataHistoryWebPage(uint8_t *buf);
    uint16_t prepareCurrentReadingWebPage(uint8_t *buf);
    uint16_t updateRTCTimeFromRequest(char *arg, uint8_t *buf);
    
  public:
    WebServerClass();
    void init();
    void serve();
};

extern WebServerClass webserver;
