function toggleCalendar(objname){
	var DivDisplay = document.getElementById(objname).style;
	if (DivDisplay.display  == 'none') {
	  DivDisplay.display = 'block';
	}else{
	  DivDisplay.display = 'none';
	}
}

function setValue(objname, d){
	document.getElementById(objname).value = d;

	var dp = document.getElementById(objname+"_dp").value;
	if(dp == true){
		var date_array = d.split("-");
		document.getElementById(objname+"_day").value = date_array[2];
		document.getElementById(objname+"_month").value = date_array[1];
		document.getElementById(objname+"_year").value = date_array[0];
		
		toggleCalendar('div_'+objname);
	}
}

function tc_setDay(objname, dvalue, path){
	var obj = document.getElementById(objname);
	var date_array = obj.value.split("-");
	
	//check if date is not allow to select
	if(!isDateAllow(objname, dvalue, date_array[1], date_array[0])){
		alert("This date is not allow to select");
		
		restoreDate(objname);
	}else{
		if(isDate(dvalue, date_array[1], date_array[0])){
			obj.value = date_array[0] + "-" + date_array[1] + "-" + dvalue;
			
			var obj = document.getElementById(objname+'_frame');
			
			var year_start = document.getElementById(objname+'_year_start').value;
			var year_end = document.getElementById(objname+'_year_end').value;
			var dp = document.getElementById(objname+'_dp').value;
			var smon = document.getElementById(objname+'_mon').value;
			var da1 = document.getElementById(objname+'_da1').value;
			var da2 = document.getElementById(objname+'_da2').value;
			var sna = document.getElementById(objname+'_sna').value;
			var aut = document.getElementById(objname+'_aut').value;
			var frm = document.getElementById(objname+'_frm').value;
			var tar = document.getElementById(objname+'_tar').value;
			
			obj.src = path+"calendar_form.php?objname="+objname.toString()+"&selected_day="+dvalue+"&selected_month="+date_array[1]+"&selected_year="+date_array[0]+"&year_start="+year_start+"&year_end="+year_end+"&dp="+dp+"&mon="+smon+"&da1="+da1+"&da2="+da2+"&sna="+sna+"&aut="+aut+"&frm="+frm+"&tar="+tar;
			
			obj.contentWindow.submitNow(dvalue, date_array[1], date_array[0]);
			
		}else document.getElementById(objname+"_day").selectedIndex = date_array[2];
	}
}

function tc_setMonth(objname, mvalue, path){
	var obj = document.getElementById(objname);
	var date_array = obj.value.split("-");
	
	//check if date is not allow to select
	if(!isDateAllow(objname, date_array[2], mvalue, date_array[0])){
		alert("This date is not allow to select");
		
		restoreDate(objname);
	}else{
		if(isDate(date_array[2], mvalue, date_array[0])){
			obj.value = date_array[0] + "-" + mvalue + "-" + date_array[2];
		
			var obj = document.getElementById(objname+'_frame');
			
			var year_start = document.getElementById(objname+'_year_start').value;
			var year_end = document.getElementById(objname+'_year_end').value;
			var dp = document.getElementById(objname+'_dp').value;
			var smon = document.getElementById(objname+'_mon').value;
			var da1 = document.getElementById(objname+'_da1').value;
			var da2 = document.getElementById(objname+'_da2').value;
			var sna = document.getElementById(objname+'_sna').value;
			var aut = document.getElementById(objname+'_aut').value;
			var frm = document.getElementById(objname+'_frm').value;
			var tar = document.getElementById(objname+'_tar').value;
			
			obj.src = path+"calendar_form.php?objname="+objname.toString()+"&selected_day="+date_array[2]+"&selected_month="+mvalue+"&selected_year="+date_array[0]+"&year_start="+year_start+"&year_end="+year_end+"&dp="+dp+"&mon="+smon+"&da1="+da1+"&da2="+da2+"&sna="+sna+"&aut="+aut+"&frm="+frm+"&tar="+tar;
			
			obj.contentWindow.submitNow(date_array[2], mvalue, date_array[0]);
			
		}else document.getElementById(objname+"_month").selectedIndex = date_array[1];
	}
}

function tc_setYear(objname, yvalue, path){
	var obj = document.getElementById(objname);
	var date_array = obj.value.split("-");
	
	//check if date is not allow to select
	if(!isDateAllow(objname, date_array[2], date_array[1], yvalue)){
		alert("This date is not allow to select");
		
		restoreDate(objname);
	}else{	
		if(isDate(date_array[2], date_array[1], yvalue)){
			obj.value = yvalue + "-" + date_array[1] + "-" + date_array[2];
		
			var obj = document.getElementById(objname+'_frame');
			
			var year_start = document.getElementById(objname+'_year_start').value;
			var year_end = document.getElementById(objname+'_year_end').value;
			var dp = document.getElementById(objname+'_dp').value;
			var smon = document.getElementById(objname+'_mon').value;
			var da1 = document.getElementById(objname+'_da1').value;
			var da2 = document.getElementById(objname+'_da2').value;
			var sna = document.getElementById(objname+'_sna').value;
			var aut = document.getElementById(objname+'_aut').value;
			var frm = document.getElementById(objname+'_frm').value;
			var tar = document.getElementById(objname+'_tar').value;
			
			obj.src = path+"calendar_form.php?objname="+objname.toString()+"&selected_day="+date_array[2]+"&selected_month="+date_array[1]+"&selected_year="+yvalue+"&year_start="+year_start+"&year_end="+year_end+"&dp="+dp+"&mon="+smon+"&da1="+da1+"&da2="+da2+"&sna="+sna+"&aut="+aut+"&frm="+frm+"&tar="+tar;
			
			obj.contentWindow.submitNow(date_array[2], date_array[1], yvalue);
			
		}else document.getElementById(objname+"_year").value = date_array[0];
	}
}

function yearEnter(e){
	var characterCode;
	
	if(e && e.which){ //if which property of event object is supported (NN4)
		e = e;
		characterCode = e.which; //character code is contained in NN4's which property
	}else{
		e = event;
		characterCode = e.keyCode; //character code is contained in IE's keyCode property
	}
	
	if(characterCode == 13){ 
		//if Enter is pressed, do nothing		
		return true;
	}else return false;
}


// Declaring valid date character, minimum year and maximum year
var minYear=2007;
var maxYear=2020;

function isInteger(s){
	var i;
    for (i = 0; i < s.length; i++){   
        // Check that current character is number.
        var c = s.charAt(i);
        if (((c < "0") || (c > "9"))) return false;
    }
    // All characters are numbers.
    return true;
}

function stripCharsInBag(s, bag){
	var i;
    var returnString = "";
    // Search through string's characters one by one.
    // If character is not in bag, append to returnString.
    for (i = 0; i < s.length; i++){   
        var c = s.charAt(i);
        if (bag.indexOf(c) == -1) returnString += c;
    }
    return returnString;
}

function is_leapYear(year){
	return (year % 4 == 0) ?
		!(year % 100 == 0 && year % 400 != 0)	: false;
}

function daysInMonth(month, year){
	var days = new Array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
	return (month == 2 && is_leapYear(year)) ? 29 : days[month-1];
}
	
function DaysArray(n) {
	for (var i = 1; i <= n; i++) {
		this[i] = 31
		if (i==4 || i==6 || i==9 || i==11) {this[i] = 30}
		if (i==2) {this[i] = 29}
   } 
   return this
}

function isDate(strDay, strMonth, strYear){
/*
	//bypass check date	
	strYr=strYear
	if (strDay.charAt(0)=="0" && strDay.length>1) strDay=strDay.substring(1)
	if (strMonth.charAt(0)=="0" && strMonth.length>1) strMonth=strMonth.substring(1)
	for (var i = 1; i <= 3; i++) {
		if (strYr.charAt(0)=="0" && strYr.length>1) strYr=strYr.substring(1)
	}
	month=parseInt(strMonth)
	day=parseInt(strDay)
	year=parseInt(strYr)
	if (strMonth.length<1 || month<1 || month>12){
		alert("Please enter a valid month")
		return false
	}
	if (strDay.length<1 || day<1 || day>31 || day > daysInMonth(month, year)){
		alert("Please enter a valid day")
		return false
	}
	if (strYear.length != 4 || year==0 || year<minYear || year>maxYear){
		alert("Please enter a valid 4 digit year between "+minYear+" and "+maxYear)
		return false
	}*/
	return true
}

function isDateAllow(objname, strDay, strMonth, strYear){	
	var da1 = document.getElementById(objname+"_da1").value;
	var da2 = document.getElementById(objname+"_da2").value;
	
	if(parseInt(strDay)>0 && parseInt(strMonth)>0 && parseInt(strYear)>0){	
		if(da1 || da2){
			var date2Set = new Date();
			date2Set.setFullYear(parseInt(strYear), parseInt(strMonth), parseInt(strDay));
			
			if(da1 && da2){
				var da1Arr = da1.split('-', 3);			
				var da2Arr = da2.split('-', 3);
				
				var da1Date=new Date();
				da1Date.setFullYear(parseInt(da1Arr[0]),parseInt(da1Arr[1]),parseInt(da1Arr[2]));
							
				var da2Date=new Date();
				da2Date.setFullYear(parseInt(da2Arr[0]),parseInt(da2Arr[1]),parseInt(da2Arr[2]));
				
				return (date2Set>=da1Date && date2Set<=da2Date) ? true : false;
			}else if(da1){
				var da1Arr = da1.split('-', 3);			
				
				var da1Date=new Date();
				da1Date.setFullYear(da1Arr[0],da1Arr[1],da1Arr[2]);
							
				return (date2Set>=da1Date) ? true : false;
			}else{
				var da2Arr = da2.split('-', 3);			
				
				var da2Date=new Date();
				da2Date.setFullYear(da2Arr[0],da2Arr[1],da2Arr[2]);
				
				alert(date2Set);
				alert(da2Date);
				
				return (date2Set<=da2Date) ? true : false;
			}
		}else return true;
	}else return true; //always return true if date not completely set
}

function restoreDate(objname){
	//get the store value
	var storeValue = document.getElementById(objname).value;
	var storeArr = storeValue.split('-', 3);
	
	//set it
	document.getElementById(objname+'_day').value = storeArr[2];
	document.getElementById(objname+'_month').value = storeArr[1];
	document.getElementById(objname+'_year').value = storeArr[0];
}