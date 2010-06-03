insert into rawdata (at,avgspeed,maxgust) select from_unixtime(60*floor(unix_timestamp(at)/(60))),avg(speed),max(speed) from t group by floor(unix_timestamp(at)/(60));
