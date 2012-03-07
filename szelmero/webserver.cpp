#include <stdio.h>

#include <stdlib.h>
#include <string.h>
#include <wiring.h>

#include "global.h"
#include "datalogger.h"
#include "webserver.h"
#include "timer.h"

WebServerClass::WebServerClass()
{
    mymac[0]=0x54; mymac[1]=0x55; mymac[2]=0x58; mymac[3]=0x10; mymac[4]=0x00; mymac[5]=0x24;
    myip[0]=192; myip[1]=168; myip[2]=2; myip[3]=9;
    strcpy(baseurl,"http://192.168.2.9/");
    mywwwport=80;
//    init();
}

void WebServerClass::init()
{ 
  es=EtherShield();
  
  /*initialize enc28j60*/
  es.ES_enc28j60Init(mymac);
  es.ES_enc28j60clkout(2); // change clkout from 6.25MHz to 12.5MHz
  delay(10);
        
	/* Magjack leds configuration, see enc28j60 datasheet, page 11 */
	// LEDA=greed LEDB=yellow
	//
	// 0x880 is PHLCON LEDB=on, LEDA=on
	// enc28j60PhyWrite(PHLCON,0b0000 1000 1000 00 00);
	es.ES_enc28j60PhyWrite(PHLCON,0x880);
	delay(500);
	//
	// 0x990 is PHLCON LEDB=off, LEDA=off
	// enc28j60PhyWrite(PHLCON,0b0000 1001 1001 00 00);
	es.ES_enc28j60PhyWrite(PHLCON,0x990);
	delay(500);
	//
	// 0x880 is PHLCON LEDB=on, LEDA=on
	// enc28j60PhyWrite(PHLCON,0b0000 1000 1000 00 00);
	es.ES_enc28j60PhyWrite(PHLCON,0x880);
	delay(500);
	//
	// 0x990 is PHLCON LEDB=off, LEDA=off
	// enc28j60PhyWrite(PHLCON,0b0000 1001 1001 00 00);
	es.ES_enc28j60PhyWrite(PHLCON,0x990);
	delay(500);
	//
  // 0x476 is PHLCON LEDA=links status, LEDB=receive/transmit
  // enc28j60PhyWrite(PHLCON,0b0000 0100 0111 01 10);
  es.ES_enc28j60PhyWrite(PHLCON,0x476);
	delay(100);
        
  //init the ethernet/ip layer:
  es.ES_init_ip_arp_udp_tcp(mymac,myip,80);
}

void WebServerClass::serve(){
  uint16_t plen, dat_p;
  int8_t cmd;
  char tmpstr[13];

  plen = es.ES_enc28j60PacketReceive(BUFFER_SIZE, buf);

  /* plen will be unequal to zero if there is a valid packet (without crc error) */
  if (plen == 0) return;
  
  // arp is broadcast if unknown but a host may also verify the mac address by sending it to a unicast address.
  if(es.ES_eth_type_is_arp_and_my_ip(buf,plen)){
    es.ES_make_arp_answer_from_request(buf);
    return;
  }

  // check if ip packets are for us:
  if(es.ES_eth_type_is_ip_and_my_ip(buf,plen)==0){
    return;
  }
  
  if(buf[IP_PROTO_P]==IP_PROTO_ICMP_V && buf[ICMP_TYPE_P]==ICMP_TYPE_ECHOREQUEST_V){
    es.ES_make_echo_reply_from_request(buf,plen);
    return;
  }
  
  // tcp port www start, compare only the lower byte
  if (buf[IP_PROTO_P]==IP_PROTO_TCP_V&&buf[TCP_DST_PORT_H_P]==0&&buf[TCP_DST_PORT_L_P]==mywwwport){
    if (buf[TCP_FLAGS_P] & TCP_FLAGS_SYN_V){
       es.ES_make_tcp_synack_from_syn(buf); // make_tcp_synack_from_syn does already send the syn,ack
       return;     
    }
    if (buf[TCP_FLAGS_P] & TCP_FLAGS_ACK_V){
      es.ES_init_len_info(buf); // init some data structures
      dat_p=es.ES_get_tcp_data_pointer();
      if (dat_p==0){ // we can possibly have no data, just ack:
        if (buf[TCP_FLAGS_P] & TCP_FLAGS_FIN_V){
          es.ES_make_tcp_ack_from_any(buf);
        }
        return;
      }
      if (strncmp("GET ",(char *)&(buf[dat_p]),4)!=0){
        // head, post and other methods for possible status codes see:
        // http://www.w3.org/Protovcols/rfc2616/rfc2616-sec10.html
        plen=es.ES_fill_tcp_data_p(buf,0,PSTR("HTTP/1.0 200 OK\r\nContent-Type: text/html\r\n\r\n<h1>200 OK</h1>"));
        goto SENDTCP;
      }
      if (strncmp("/settime?TIME=",(char *)&(buf[dat_p+4]),14)==0){
        strncpy(tmpstr,(char *)&(buf[dat_p+4+14]),12);
        tmpstr[12]=0;
        plen=updateRTCTimeFromRequest(tmpstr,buf);
        goto SENDTCP;
       }
      if (strncmp("/data ",(char *)&(buf[dat_p+4]),6)==0){
        plen=prepareDataHistoryWebPage(buf);
        goto SENDTCP;
       }
      if (strncmp("/ ",(char *)&(buf[dat_p+4]),2)==0){
        plen=prepareCurrentReadingWebPage(buf);
        goto SENDTCP;
       }
      cmd=analyseCmd((char *)&(buf[dat_p+5]));
      if (cmd==1){
        plen=prepareDataHistoryWebPage(buf);
      }
SENDTCP:  es.ES_make_tcp_ack_from_any(buf); // send ack for http get
         es.ES_make_tcp_ack_with_data(buf,plen); // send data       
    }
  }
        
}

// The returned value is stored in the global var strbuf
uint8_t WebServerClass::findKeyVal(char *str,char *key)
{
  uint8_t found=0;
  uint8_t i=0;
  char *kp;
  kp=key;
  while(*str &&  *str!=' ' && found==0){
          if (*str == *kp){
                  kp++;
                  if (*kp == '\0'){
                          str++;
                          kp=key;
                          if (*str == '='){
                                  found=1;
                          }
                  }
          }
          else{
                  kp=key;
          }
          str++;
  }
  if (found==1){
          // copy the value to a buffer and terminate it with '\0'
          while(*str &&  *str!=' ' && *str!='&' && i<STR_BUFFER_SIZE){
                  strbuf[i]=*str;
                  i++;
                  str++;
          }
          strbuf[i]='\0';
  }
  return(found);
}

int8_t WebServerClass::analyseCmd(char *str)
{
  int8_t r=-1;

  if (findKeyVal(str,"cmd")){
    if (*strbuf < 0x3a && *strbuf > 0x2f){ // is a ASCII number, return it
      r=(*strbuf-0x30);
    }
  }
  return r;
}

uint16_t WebServerClass::addDateTimeToPage(uint8_t *buf, uint16_t plen)
{
  char tmpstr[21];
  
  timer.getInStr(tmpstr);
  return( es.ES_fill_tcp_data(buf,plen,tmpstr) );
}

uint16_t WebServerClass::prepareDataHistoryWebPage(uint8_t *buf)
{
  uint16_t plen;
  uint8_t stopindex, i;
  char tmpstr[9];
  time_st t;
  time_t tsec;
  
  plen=es.ES_fill_tcp_data_p(buf,0,PSTR("HTTP/1.0 200 OK\r\nContent-Type: text/html\r\n\r\n"));
  stopindex = windData.bufferIndex;
  i = windData.skipBy(stopindex,LOG_SAFETY_SLOTS);
  timer.get(&t,true);
  tsec = timer.convertDateTimeToSeconds(&t) - LOG_ENTRY_SLOTS*60;
  do {
    if ( windData.buffer[i].avgRotations >= 0 ) {
      timer.convertDateTimeToStruct(tsec,&t);
      tmpstr[0]='0'+t.month/10; tmpstr[1]='0'+t.month%10;
      tmpstr[2]='0'+t.date/10; tmpstr[3]='0'+t.date%10;
      tmpstr[4]='0'+t.hour/10; tmpstr[5]='0'+t.hour%10;
      tmpstr[6]='0'+t.minute/10; tmpstr[7]='0'+t.minute%10;
      tmpstr[8]=0;
      plen=es.ES_fill_tcp_data(buf,plen,tmpstr);
      plen=es.ES_fill_tcp_data_p(buf,plen,PSTR(","));
      itoa(ROTATIONS_TO_KPH(windData.buffer[i].avgRotations,AVG_SECS), tmpstr, 10);
      plen=es.ES_fill_tcp_data(buf,plen,tmpstr);
      plen=es.ES_fill_tcp_data_p(buf,plen,PSTR(","));
      itoa(ROTATIONS_TO_KPH(windData.buffer[i].maxGustRotations,GUST_SECS), tmpstr, 10);
      plen=es.ES_fill_tcp_data(buf,plen,tmpstr);
      plen=es.ES_fill_tcp_data_p(buf,plen,PSTR(","));
      itoa(DIRECTION_TO_DEGREES(windData.buffer[i].avgDirection), tmpstr, 10);
      plen=es.ES_fill_tcp_data(buf,plen,tmpstr);
      plen=es.ES_fill_tcp_data_p(buf,plen,PSTR("<br>") );     
    }
    i=windData.next(i);
    tsec += 60;
  } while (i != stopindex);

  return(plen);
 }

uint16_t WebServerClass::prepareCurrentReadingWebPage(uint8_t *buf)
{
  uint16_t plen;
  uint8_t i;
  char tmpstr[4];
  
  plen=es.ES_fill_tcp_data_p(buf,0,PSTR("HTTP/1.0 200 OK\r\nContent-Type: text/html\r\n\r\n"));

  plen=es.ES_fill_tcp_data_p(buf,plen,PSTR("<html><body>"));
  
  i = windData.skipBy(windData.bufferIndex,-1);
  if ( windData.buffer[i].avgRotations >= 0 ) {
    plen=es.ES_fill_tcp_data_p(buf,plen,PSTR("<h1>&Aacute;tlag:"));
    
    itoa(ROTATIONS_TO_KPH(windData.buffer[i].avgRotations,AVG_SECS), tmpstr, 10);
    plen=es.ES_fill_tcp_data(buf,plen,tmpstr);
    
    plen=es.ES_fill_tcp_data_p(buf,plen,PSTR("<br>Max sz&eacute;ll&ouml;k&eacute;s:"));
    itoa(ROTATIONS_TO_KPH(windData.buffer[i].maxGustRotations,GUST_SECS), tmpstr, 10);
    plen=es.ES_fill_tcp_data(buf,plen,tmpstr);
    
    plen=es.ES_fill_tcp_data_p(buf,plen,PSTR("<br>Ir&aacute;ny:"));
    itoa(DIRECTION_TO_DEGREES(windData.buffer[i].avgDirection), tmpstr, 10);
    plen=es.ES_fill_tcp_data(buf,plen,tmpstr);

    plen=es.ES_fill_tcp_data_p(buf,plen,PSTR("</h1>"));
  }
  else {
    plen=es.ES_fill_tcp_data_p(buf,plen,PSTR("Sajnos nincs &eacute;rv&eacute;nyes adat"));
  }
  
  plen=es.ES_fill_tcp_data_p(buf,plen,PSTR("<hr>"));
  plen=addDateTimeToPage(buf,plen);
  plen=es.ES_fill_tcp_data_p(buf,plen,PSTR("</body></html>"));

  return(plen);
 }


uint16_t WebServerClass::updateRTCTimeFromRequest(char *arg, uint8_t *buf)
{
  uint16_t plen;
  char tmpstr[21];

  plen=es.ES_fill_tcp_data_p(buf,0,PSTR("HTTP/1.0 200 OK\r\nContent-Type: text/html\r\n\r\n"));

  plen=es.ES_fill_tcp_data_p(buf,plen,PSTR("<html><body>"));

  if ( timer.setFromStr(arg) == 0 ) {    
    plen=es.ES_fill_tcp_data_p(buf,plen,PSTR("Ido es datum frissitve: "));
    plen=addDateTimeToPage(buf,plen);
  }
  else {
    plen=es.ES_fill_tcp_data_p(buf,plen,PSTR("Hibas datum es ido: "));
    plen=es.ES_fill_tcp_data(buf,plen,arg);
  }
  
  plen=es.ES_fill_tcp_data_p(buf,plen,PSTR("</body></html>"));

  return(plen);
 }
 
WebServerClass webserver;
