<?php
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>StarMade player statistics</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script language="javascript" type="text/javascript" src="js/jquery.js"></script>
    <script language="javascript" type="text/javascript" src="js/jquery.flot.js"></script>
    <script language="javascript" type="text/javascript" src="js/jquery.flot.stack.js"></script>
    <script language="javascript" type="text/javascript" src="js/jquery.flot.time.js"></script> 
  </head>

  <body>
	<p>StarMade - Players Per Month</p>
	<div id="starmademonthlyplaceholder" style="width:100%;height:250px;"></div>
	<br/>

	<p>StarMade - Players Per Day</p>
	<div id="starmadedailyplaceholder" style="width:100%;height:250px;"></div>
	<br/>

	<p>StarMade - Cumulative New Players Per Day</p>
	<div id="starmadecumulativenewdailyplaceholder" style="width:100%;height:250px;"></div>
	<br/>

<script>
var previousPoint = null, previousLabel = null;
var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

function DostarmadeMonthlyPlayersGraph() {
	$.get("starmademonthlyuniqueplayers.txt", 
	   function(uniquedata) {
			var uniqueplayerschartdata = [];
			var newplayerschartdata = [];
			var returningplayerschartdata = [];
			var maxvalue = 0;
			var datavalue = 0;
			
			var uniquearray = uniquedata.split(/\r\n|\r|\n/); //regex to split on line ending           
			for (index = 0; index < uniquearray.length; index++) {
				var columns = uniquearray[index].split(',');
				
				var sampledate = new Date(columns[0]).setHours(0,0,0,0);
				var datavalue = columns[1];

				uniqueplayerschartdata.push([sampledate, datavalue]);
				returningplayerschartdata.push([sampledate, datavalue]);

				if (parseInt(datavalue,10) > parseInt(maxvalue,10)) {
					maxvalue = datavalue;
				}
			}
			
			$.get("starmademonthlynewplayers.txt", 
			   function(newdata) {
					datavalue = 0;
					var newarray = newdata.split(/\r\n|\r|\n/); //regex to split on line ending           
					for (index = 0; index < newarray.length; index++) {
						var columns = newarray[index].split(',');

						var sampledate = new Date(columns[0]).setHours(0,0,0,0);						
						var datavalue = columns[1];

						newplayerschartdata.push([sampledate, datavalue]);
						
						for (index2 = 0; index2 < returningplayerschartdata.length; index2++) {
							if (returningplayerschartdata[index2][0] == sampledate) {
								returningplayerschartdata[index2][1] = returningplayerschartdata[index2][1] - datavalue;
								break;
							}
						}
																		
						if (parseInt(datavalue,10) > parseInt(maxvalue,10)) {
							maxvalue = datavalue;
						}
					}
										
					// round maxvalue to nearest 5		
					maxvalue = (Math.round(maxvalue / 5) * 5) + 5;
					
					var data = [{ data:uniqueplayerschartdata, label:"Unique Players", lines:{show:true, fill:true}},{ data:newplayerschartdata, label:"New Players", lines:{show:true, fill:true}}, { data:returningplayerschartdata, label:"Returning Players", lines:{show:true, fill:true}}];
					
					//setup plots
					var options = {
						legend:{position:"nw"},
						yaxis: { min: 0, max: maxvalue },
						xaxis: { show: true, mode: "time", minTickSize: [1, "month"]},
						grid: {
							hoverable: true,
							borderWidth: 3,
							mouseActiveRadius: 50,
							backgroundColor: { colors: ["#ffffff", "#EDF5FF"] },
							axisMargin: 20
						}
						
					};
					
					var plot = $.plot($("#starmademonthlyplaceholder"), data, options);
					$("#starmademonthlyplaceholder").UseTooltipStarMadeMonthly();
				}
			);					
		}
	);
}


$.fn.UseTooltipStarMadeMonthly = function () {
    $(this).bind("plothover", function (event, pos, item) {
        if (item) {
            if ((previousLabel != item.series.label) || (previousPoint != item.dataIndex)) {
                previousPoint = item.dataIndex;
                previousLabel = item.series.label;
                $("#tooltip").remove();
                
                var x = item.datapoint[0];
                var y = item.datapoint[1];
                var date = new Date(x);
                var color = item.series.color;

                showstarmadeTooltip(item.pageX, item.pageY, color, "<strong>" + item.series.label + "</strong><br>" + monthNames[date.getMonth()] + " : <strong>" + y + "</strong>");
            }
        } else {
            $("#tooltip").remove();
            previousPoint = null;
        }
    });
};

function showstarmadeTooltip(x, y, color, contents) {
    $('<div id="tooltip">' + contents + '</div>').css({
        position: 'absolute',
        display: 'none',
        top: y - 40,
        left: x - 120,
        border: '2px solid ' + color,
        padding: '3px',
        'font-size': '9px',
        'border-radius': '5px',
        'background-color': '#fff',
        'font-family': 'Verdana, Arial, Helvetica, Tahoma, sans-serif',
        opacity: 0.9
    }).appendTo("body").fadeIn(200);
}

DostarmadeMonthlyPlayersGraph();

function DostarmadeDailyPlayersGraph() {
    $.get("starmadedailyplayercount.txt", 
	   function(uniquedata) {
			var uniqueplayerschartdata = [];
			var newplayerschartdata = [];
			var returningplayerschartdata = [];
			var maxvalue = 0;
			var datavalue = 0;
			
			var uniquearray = uniquedata.split(/\r\n|\r|\n/); //regex to split on line ending           
			for (index = 0; index < uniquearray.length; index++) {
				var columns = uniquearray[index].split(',');

				var sampledate = new Date(columns[0]).setHours(0,0,0,0);				
				var datavalue = columns[1];

				uniqueplayerschartdata.push([sampledate, datavalue]);
				returningplayerschartdata.push([sampledate, datavalue]);

				if (parseInt(datavalue,10) > parseInt(maxvalue,10)) {
					maxvalue = datavalue;
				}
			}
			
			$.get("starmadedailynewplayers.txt", 
			   function(newdata) {
					datavalue = 0;
					var newarray = newdata.split(/\r\n|\r|\n/); //regex to split on line ending           
					for (index = 0; index < newarray.length; index++) {
						var columns = newarray[index].split(',');

						var sampledate = new Date(columns[0]).setHours(0,0,0,0);						
						var datavalue = columns[1];

						newplayerschartdata.push([sampledate, datavalue]);
						
						for (index2 = 0; index2 < returningplayerschartdata.length; index2++) {
							if (returningplayerschartdata[index2][0] == sampledate) {
								returningplayerschartdata[index2][1] = returningplayerschartdata[index2][1] - datavalue;
								break;
							}
						}
																		
						if (parseInt(datavalue,10) > parseInt(maxvalue,10)) {
							maxvalue = datavalue;
						}
					}
										
					// round maxvalue to nearest 5		
					maxvalue = (Math.round(maxvalue / 5) * 5) + 5;
					
					var data = [{ data:uniqueplayerschartdata, label:"Unique Players", lines:{show:true, fill:true}},{ data:newplayerschartdata, label:"New Players", lines:{show:true, fill:true}}, { data:returningplayerschartdata, label:"Returning Players", lines:{show:true, fill:true}}];
					
					//setup plots
					var options = {
						legend:{position:"nw"},
						yaxis: { min: 0, max: maxvalue },
						xaxis: { show: true, mode: "time", minTickSize: [1, "day"]},
						grid: {
							hoverable: true,
							borderWidth: 3,
							mouseActiveRadius: 50,
							backgroundColor: { colors: ["#ffffff", "#EDF5FF"] },
							axisMargin: 20
						}
						
					};
					
					var plot = $.plot($("#starmadedailyplaceholder"), data, options);
					$("#starmadedailyplaceholder").UseTooltipStarMadeDaily();
				}
			);
		}
	);
}

$.fn.UseTooltipStarMadeDaily = function () {
    $(this).bind("plothover", function (event, pos, item) {
        if (item) {
            if ((previousLabel != item.series.label) || (previousPoint != item.dataIndex)) {
                previousPoint = item.dataIndex;
                previousLabel = item.series.label;
                $("#tooltip").remove();
                
                var x = item.datapoint[0];
                var y = item.datapoint[1];
                var date = new Date(x);
                var color = item.series.color;

                showstarmadeTooltip(item.pageX, item.pageY, color, "<strong>" + item.series.label + "</strong><br>" + monthNames[date.getMonth()] + " " + date.getDate() + " : <strong>" + y + "</strong>");
            }
        } else {
            $("#tooltip").remove();
            previousPoint = null;
        }
    });
};

DostarmadeDailyPlayersGraph();

function DoStarMadeCumulativeNewPlayersGraph() {
    $.get("starmadecumulativeplayers.txt", 
	   function(cumulativedata) {
			var cumulativenewplayerschartdata = [];
			var maxvalue = 0;
			var datavalue = 0;
			
			var cumulativearray = cumulativedata.split(/\r\n|\r|\n/); //regex to split on line ending           
			for (index = 0; index < cumulativearray.length; index++) {
				var columns = cumulativearray[index].split(',');

				var sampledate = new Date(columns[0]).setHours(0,0,0,0);				
				var datavalue = columns[1];

				cumulativenewplayerschartdata.push([sampledate, datavalue]);

				if (parseInt(datavalue,10) > parseInt(maxvalue,10)) {
					maxvalue = datavalue;
				}
			}

			// round maxvalue to nearest 5		
			maxvalue = (Math.round(maxvalue / 5) * 5) + 5;
			
			var data = [{ data:cumulativenewplayerschartdata, label:"Cumulative New Players", lines:{show:true, fill:true}}];
			
			//setup plots
			var options = {
				legend:{position:"nw"},
				yaxis: { min: 0, max: maxvalue },
				xaxis: { show: true, mode: "time", minTickSize: [1, "day"]},
				grid: {
					hoverable: true,
					borderWidth: 3,
					mouseActiveRadius: 50,
					backgroundColor: { colors: ["#ffffff", "#EDF5FF"] },
					axisMargin: 20
				}
				
			};
			
			var plot = $.plot($("#starmadecumulativenewdailyplaceholder"), data, options);
			$("#starmadecumulativenewdailyplaceholder").UseTooltipStarMadeCumulativeDaily();
		}
	);
}

$.fn.UseTooltipStarMadeCumulativeDaily = function () {
    $(this).bind("plothover", function (event, pos, item) {
        if (item) {
            if ((previousLabel != item.series.label) || (previousPoint != item.dataIndex)) {
                previousPoint = item.dataIndex;
                previousLabel = item.series.label;
                $("#tooltip").remove();
                
                var x = item.datapoint[0];
                var y = item.datapoint[1];
                var date = new Date(x);
                var color = item.series.color;

                showstarmadeTooltip(item.pageX, item.pageY, color, "<strong>" + item.series.label + "</strong><br>" + monthNames[date.getMonth()] + " " + date.getDate() + " : <strong>" + y + "</strong>");
            }
        } else {
            $("#tooltip").remove();
            previousPoint = null;
        }
    });
};

DoStarMadeCumulativeNewPlayersGraph();
</script>

</body>
</html>
