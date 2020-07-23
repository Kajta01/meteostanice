// Configuration file for charts displayed in index.html

var globalOptions = {
  global: {
    useUTC: false
  }
}
      

// 1 week overview chart
var options1 = {
      
  chart: {
    type: 'areaspline',
    zoomType: "x",
    backgroundColor: "rgba(100,100,100,0.8)",
    spacingRight: 5,
    spacingLeft: 5,
    plotBackgroundColor: "rgba(150,150,150,0.6)",
  },
              
  colors: [
    "rgb(92,255,105)", 'black', "rgb(54,161,255)", '#f7a35c', '#8085e9', 
    '#f15c80', '#e4d354', '#8085e8', '#8d4653', '#91e8e1'
  ],
              
  legend: {
    enabled: true,
    floating: true,
    align: 'right',
    verticalAlign: 'top',
    x: -30,
    itemStyle: {
      color: 'white',
      fontSize: "15px", 
      fontWeight: "bold" 
    },
    itemHiddenStyle: {
      color: "gray"
    },
    itemHoverStyle: {
      color: "black"
    }
  },
              
  navigator: {
    margin: 10,
    outlineColor: 'white',
    outlineWidth: 1,
    maskFill: 'rgba(100, 100, 150, 0.6)',
    maskInside: false,
    xAxis: {
      labels: {
        style: {
          color: "white"
        }
      }
    }
  },
              
  credits: {
    style: {
      color: "white"
    }
  },
  
  rangeSelector: {
    inputEnabled: false, 
    buttonTheme: {
      fill: 'none',
      stroke: "white",
      'stroke-width': 1,
      style: {
        color: 'white',
        fontWeight: 'bold',
      },
      states: {
        hover: {
          fill: 'rgba(0,0,128,0.6)',
          stroke: "white",
        },
        select: {
          fill: 'rgba(0,0,128,0.6)',
          stroke: "white",
          'stroke-width': 2,
          style: {
            color: "white"
          }
        }
      }                                                 
    },                
    labelStyle: {
      "display": "none"
    },
    buttons: [{
      type: 'minute',
      count: 180,
      text: '3h'
    }, {
      type: 'day',
      count: 1,
      text: '1d'
    }, {
      type: 'day',
      count: 3,
      text: '3d'
    }, {
      type: 'all',
      text: 'Vše'
    }],
    selected: 2
  },

  scrollbar: {
    enabled: false
  },
       
  xAxis: {
    gridLineWidth: 1,
    gridLineColor: "white",
    tickLength: 0,
    ordinal: false,
    labels: {
      style: {
        color: 'white'
      }
    },
    dateTimeLabelFormats: {
      millisecond: '%H:%M:%S.%L',
      second: '%H:%M:%S',
      minute: '%H:%M',
      hour: '%H:%M',
      day: '%a %e. %b',
      week: '%e. %b',
      month: '%b \'%y',
      year: '%Y'
    }
  },

  yAxis:[
  {
    offset: 0,
    gridLineWidth: 1,
    gridLineColor: "white",
    showEmpty: false,
    labels: {
      align: "left",
      x: 5,
      y: -3,
      formatter: function () {
        return this.value + "°C";
      },
      style: {
        color: 'white',
        "font-size": "10"
      }
    },               
    plotLines: [{
      color: 'red', 
      value: '0', 
      width: '2'   
    }]
  },
  {
    offset: 0,
    opposite: false,
    showEmpty: false,  
    allowDecimals: false,
    labels: {
      align: "right",
      x: -5,
      formatter: function () {
        return this.value + "<br>HPa";
      },
      style: {
        color: 'white',
      }
    } 
  },
  {
    offset: 0,
    showEmpty: false,
    allowDecimals: false,
    labels: {
      align: "left",
      x: 5,
      y: 12,
      formatter: function () {
        return this.value + "%";
      },
      style: {
        color: 'white',
      }
    }     
  }
  ],

  tooltip: {
    valueDecimals: 2             
  },
                                             
              series: [{
                  yAxis: 0,
                  zIndex: 1,
                  name: 'Teplota',
                  lineColor: "rgba(0,255,50,0.9)",
                  lineWidth: 2,
                  fillColor:{
                    linearGradient: { x1: 0, x2: 0, y1: 0, y2: 1 },
                    stops: [
                        [0, "rgba(0,255,75,0.5)"],
                        [1, "rgba(191,227,194,0.5)"]
                    ]
                  },
                  tooltip:{
                    valueSuffix: " °C",
                    valueDecimals: 2
                  },
                  data: []
              },{
                  yAxis: 1,
                  zIndex: 3,
                  name: "Tlak",
                  type: "spline",
                  lineColor: "rgba(0,0,0,0.9)",
                  tooltip:{
                    valueSuffix: " HPa",
                    valueDecimals: 1
                  },
                  data: []
              },{
                  yAxis: 2,
                  zIndex: 2,
                  name: "Vlhkost",
                  lineColor: "rgba(0,50,255,0.9)",
                  lineWidth: 2,
                  fillColor: "rgba(0,50,255,0.1)",
                  tooltip:{
                    valueSuffix: " %",
                    valueDecimals: 1
                  },
                  data: []
              }]

        };

        // Long-time chart
        var options2 = {
          chart: {
                type: 'columnrange',
                zoomType: "x",
                height: 300,
                backgroundColor: "rgba(100,100,100,0.8)",
                spacingRight: 5,
                spacingLeft: 10,
                plotBackgroundColor: "rgba(150,150,150,0.6)",
            },
            colors: ["rgb(92,255,105)", 'black', "rgb(54,161,255)", '#f7a35c', '#8085e9', 
                '#f15c80', '#e4d354', '#8085e8', '#8d4653', '#91e8e1'
            ],

            rangeSelector: {
                  inputEnabled: false,
                  buttonTheme: {
                    fill: 'none',
                    stroke: "white",
                    'stroke-width': 1,
                    style: {
                        color: 'white',
                        fontWeight: 'bold',
                    },
                    states: {
                        hover: {
                          fill: 'rgba(0,0,128,0.6)',
                          stroke: "white",
                        },
                        select: {
                            fill: 'rgba(0,0,128,0.6)',
                            stroke: "white",
                            'stroke-width': 2,
                            style: {
                              color: "white"
                            }
                        }
                    }                                                 
                  },
                  labelStyle: {
                      "display": "none"
                  },

                  buttons: [{
                    type: 'week',
                    count: 2,
                    text: '14d'
                  }, {
                    type: 'month',
                    count: 1,
                    text: '1m'
                  }, {
                    type: 'month',
                    count: 3,
                    text: '3m'
                  },{
                    type: 'year',
                    count: 1,
                    text: 'rok'
                  },{
                    type: 'all',
                    text: 'Vše'
                  }],
                  selected: 1
              },

            scrollbar: {
                enabled: false
            },

            tooltip: {
                valueSuffix: '°C'
            },
            
           legend: {
                  enabled: true,
                  floating: true,
                  align: 'right',
                  verticalAlign: 'top',
                  x: -30,
                  itemStyle: {
                    color: 'white',
                    fontSize: "15px", 
                    fontWeight: "bold" 
                  },
                  itemHiddenStyle: {
                    color: "gray"
                  },
                  itemHoverStyle: {
                    color: "black"
                  }
              },

            navigator: {
                  margin: 10,
                  outlineColor: 'white',
                  outlineWidth: 1,
                  maskFill: 'rgba(100, 100, 150, 0.6)',
                  maskInside: false,
      
                  xAxis: {
                    showLastLabel: false,
                    minRange: 31*24*3600*1000,
                    labels: {
                      style: {
                        color: "white"
                      }
                    }
                  },
                  series: {
                    type: "columnrange",
                    color: "rgba(92,255,105,0.6)"

                  }
              },
              credits: {
                style: {
                  color: "white"
                }
              },
            xAxis: {

                  tickLength: 0,
                  ordinal: false,
                  minRange: 1000 * 3600 * 24 * 7,
                  minTickInterval: 1000 * 3600 * 24,
                  labels: {
                     
                     style: {
                        color: 'white',
                     }
                  }


            },

            yAxis:[{
              offset: 0,
                    gridLineWidth: 1,
                    showEmpty: false,
                    labels: {
                       align: "left",
                       x: 5,
                       y: -3,
                       formatter: function () {
                            return this.value + "°C";
                       },
                       style: {
                          color: 'white',
                          "font-size": "10"
                       }
                    }
            },{
              offset: 0,
                    opposite: false,
                    showEmpty: false,  
                    allowDecimals: false,
                    labels: {
                       align: "right",
                       x: -5,
                       formatter: function () {
                            return this.value + "<br>HPa";
                       },
                       style: {
                          color: 'white',
                       }
                    } 
            }],

            series: [{
              yAxis: 0,
              name: 'Teplota',
              color: "rgba(0,255,75,0.6)",
              tooltip:{
                valueSuffix: " °C",
                valueDecimals: 2
              },
              data: []
            },{
              yAxis: 1,
              name: "Tlak",
              type: "spline",
              color: "black",
              tooltip:{
                valueSuffix: " HPa",
                valueDecimals: 1
              },
              data: []
            }]

        };
       
