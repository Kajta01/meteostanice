      // Main script file for index.html


      // Live-update interval in miliseconds
      var INTERVAL = 60000;
      //file to get data from
      var phpFile = "retrieve.php";

      // Initialization...
      $(document).ready(function(){
 
        Highcharts.setOptions(globalOptions);
        displayValues();
        setFontSize();
        setInterval(timer, 1000);
        
        $.getJSON(phpFile, {"type":"b"}, function(data){
          var chart2 = createRangeChart(data);
        });

        $.getJSON(phpFile, {"type":"a"}, function(data){
          var chart = createChart(data);
          setInterval(
            function(){ updateChart(chart); displayValues(); }, 
            INTERVAL
          );
        });     

      });

      // Change font size on vindow resize
      $(window).resize(function(){
        setFontSize();
      });

      // Create 1 week chart
      function createChart(data){
          options1.series[0].data = parseData(data, "t");
          options1.series[1].data = parseData(data, "p");
          options1.series[2].data = parseData(data, "h");
          options1.chart.renderTo = "canvas";
          var ch = new Highcharts.StockChart(options1);
          ch.series[2].hide();
          addPlotLines(ch);
          return ch;      
      }

      // Create temperature range chart
      function createRangeChart(data){
          options2.series[0].data = parseData(data, "xt");
          options2.series[1].data = parseData(data, "xp");
          options2.chart.renderTo = "canvas2";
          var ch = new Highcharts.StockChart(options2); 
          ch.series[1].hide();
          return ch;         
      }

      // Live-update 1 week chart
      function updateChart(chart){
        $.getJSON( phpFile, {"type":"c"}, function( data ) {
          chart.series[0].addPoint([parseTime(data[0]), temp(data)], true, false);
          chart.series[1].addPoint([parseTime(data[0]), pres(data)], true, false);
          chart.series[2].addPoint([parseTime(data[0]), hum(data)], true, false);
          //console.log(data);
        });
      }

      // Add vertical lines between days
      function addPlotLines(chart){
        var d = new Date();
        d.setHours(0,0,0,0);

        for (i = 0; i < 6; i++){
          chart.xAxis[0].addPlotLine({
            value: d.getTime(),
            color: 'white',
            width: 3
          });
          d.setDate(d.getDate() - 1);
        } 
      }

      // Display current values on top of the page
      function displayValues(){
        $.getJSON(phpFile, {"type":"c"}, function(data){
          $("#t").text(temp(data)+" °C ");
          $("#p").text(pres(data)+" HPa ");
          $("#h").text(hum(data)+" % ");
          $("#dp").text(dewPoint(temp(data), hum(data))+" °C ");
          $("#hi").text(heatIndex(temp(data), hum(data)));
        });
      }

      // Live-update date ant time
      function timer() {
          var d = new Date();
          
          var h = d.getHours();
          var m = d.getMinutes();
          var s = d.getSeconds();
          h = ( h < 10 ? "0" : "" ) + h;
          m = ( m < 10 ? "0" : "" ) + m;
          s = ( s < 10 ? "0" : "" ) + s;
          var timeString = h + ":" + m + ":" + s;

          var dateString = d.getDate() + ". " + (d.getMonth() + 1) + ". " + d.getFullYear() + " ";

          var weekdays = new Array(7);
          weekdays[0]=  "Ne";
          weekdays[1] = "Po";
          weekdays[2] = "Út";
          weekdays[3] = "St";
          weekdays[4] = "Čt";
          weekdays[5] = "Pá";
          weekdays[6] = "So";

          var weekDayString = weekdays[d.getDay()] + "&nbsp &nbsp";

          $("#time").html(weekDayString + dateString + " &nbsp &nbsp &nbsp &nbsp" + timeString);
      }

      // Calculate dew point
      function dewPoint(temp, hum){
        tem = -1.0*temp;
        es = 6.112*Math.exp(-1.0*17.67*tem/(243.5 - tem));
        ed = hum/100.0*es;
        eln = Math.log(ed/6.112);
        td = -243.5*eln/(eln - 17.67 );
        return Math.round(td*100)/100;
      }  

      // Calculate heat index
      function heatIndex(temp, hum){

        if(temp > 27.0 && hum > 40.0){
          var t=temp*(9/5) + 32;
          var r = hum;
          var t2=Math.pow(t, 2);
          var t3=Math.pow(t, 3);
          var rh2=Math.pow(r, 2);
          var rh3=Math.pow(r, 3);

          var index =16.923+0.185212*t+5.37941*r-0.100254*t*r+ 0.941695e-2*t2+0.728898e-2*rh2+0.345372e-3*t2*r- 
                    0.814971e-3*t*rh2+0.102102e-4*t2*rh2- 0.38646e-4*t3+0.291583e-4*rh3+0.142721e-5*t3*r+ 
                    0.197483e-6*t*rh3-0.218429e-7*t3*rh2+ 0.843296e-9*t2*rh3-0.481975e-10*t3*rh3;

          var m = 5/9*(index-32);
          return Math.round(m*100)/100 + " °C";
        }  
        else return " - ";
      }
 
      // Prepare data array for charts
      function parseData(data, type){
        var data2 = [];
        $.each(data, function( index, value ) {
          if(type == "t"){
            data2[index] = [parseTime(value[0]), temp(value)];
          }
          if(type == "p"){
            data2[index] = [parseTime(value[0]), pres(value)];
          }
          if(type == "h"){
            data2[index] = [parseTime(value[0]), hum(value)];
          }
          if(type == "xt"){
            data2[index] = [parseDate(value[0]), temp(value), tmax(value)];
          } 
          if(type == "xp"){
            data2[index] = [parseDate(value[0]), pres(value)];
          }        
        });
        return data2;
      }

      // convert MySQL timestamp to jvascript date
      function parseTime(timestamp) {
          var t = timestamp.split(/[- :]/);
          var d = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
          return Date.parse(d);
      }

      // convert MySQL date to jvascript date
      function parseDate(date) {
          var t = date.split("-");
          var d = new Date(t[0], t[1]-1, t[2]);
          return Date.parse(d);
      }

      // retrieve temperature / tmin data
      function temp(row){
        var temp = parseInt(row[2])/100;
        return temp;
      }
      // retrieve tmax data
      function tmax(row){
        var temp = parseInt(row[3])/100;
        return temp;
      }

      // retrieve pressure data
      function pres(row){
        var temp = parseInt(row[1])/10;
        return temp;
      }

      // retrieve humidity data
      function hum(row){
        var temp = parseInt(row[3])/10;
        return temp;
      }

      // Dynamically update font size
      function setFontSize(){
          $("#p,#h,#hi,#dp").css("font-size", ($("#p").width() < 250) ? $("#p").width()/5.5 : "45px");
          $("#t").css("font-size", ($("#t").width() < 500) ? $("#t").width()/5 : "100px");
          $("#time").css("font-size", ($("#t").width() < 500) ? $("#t").width()/18 : "28px");
      }
   