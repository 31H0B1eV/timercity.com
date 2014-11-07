<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Global time</title>
		<meta charset="UTF-8">
		<meta name=description content="">
		<meta name=viewport content="width=device-width, initial-scale=1">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<!-- Bootstrap CSS -->
		<link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet" media="screen">
        <style>

            body {
                background: #fff;
            }

            h1  {
                text-shadow: 1px 1px 2px black, 0 0 1em red; /* Параметры тени */
                color: white; /* Белый цвет текста */
                font-size: 2em; /* Размер надписи */
            }

            svg{
                stroke: #000;
                font-family: "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif;
            }

            #rim {
                fill: none;
                stroke: #999;
                stroke-width: 3px;
            }

            .second-hand{
                stroke-width:3;

            }

            .minute-hand{
                stroke-width:8;
                stroke-linecap:round;
            }

            .hour-hand{
                stroke-width:12;
                stroke-linecap:round;
            }

            .hands-cover{
                stroke-width:3;
                fill:#fff;
            }

            .second-tick{
                stroke-width:3;
                fill:#000;
            }

            .hour-tick{
                stroke-width:8; //same as the miute hand
            }

            .second-label{
                font-size: 12px;
            }

            .hour-label{
                font-size: 24px;
            }


        </style>
	</head>
	<body>
		<h1 class="text-center">Local time: Donetsk(UA) / Indianapolis(USA)</h1>
        <script></script>
		<!-- jQuery -->
		<script src="//code.jquery.com/jquery.js"></script>
		<!-- Bootstrap JavaScript -->
		<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
        <script src="js/d3.js"></script>
        <script>

            var radians = 0.0174532925,
                    clockRadius = 200,
                    margin = 50,
                    width = (clockRadius+margin)*2,
                    height = (clockRadius+margin)*2,
                    hourHandLength = 2*clockRadius/3,
                    minuteHandLength = clockRadius,
                    secondHandLength = clockRadius-12,
                    secondHandBalance = 30,
                    secondTickStart = clockRadius;
            secondTickLength = -10,
                    hourTickStart = clockRadius,
                    hourTickLength = -18
            secondLabelRadius = clockRadius + 16;
            secondLabelYOffset = 5
            hourLabelRadius = clockRadius - 40
            hourLabelYOffset = 7;


            var hourScale = d3.scale.linear()
                    .range([0,330])
                    .domain([0,11]);

            var minuteScale = secondScale = d3.scale.linear()
                    .range([0,354])
                    .domain([0,59]);

            var handData = [
                {
                    type:'hour',
                    value:0,
                    length:-hourHandLength,
                    scale:hourScale
                },
                {
                    type:'minute',
                    value:0,
                    length:-minuteHandLength,
                    scale:minuteScale
                },
                {
                    type:'second',
                    value:0,
                    length:-secondHandLength,
                    scale:secondScale,
                    balance:secondHandBalance
                }
            ];

            function drawClock(clockRadius, offset, guid){ //create all the clock elements
                //updateData(offset);	//draw them in the correct starting position

                setInterval(function(){
                    updateData(offset);
                    moveHands(guid);
                }, 1000);

                var width = (clockRadius+margin)*2,// replace global definition in function scope.
                        height = (clockRadius+margin)*2,
                        secondTickStart = clockRadius,
                        hourTickStart = clockRadius,
                        secondLabelRadius = clockRadius + 16,
                        hourLabelRadius = clockRadius - 40

                var svg = d3.select("body").append("svg")
                        .attr("width", width)
                        .attr("height", height);

                var face = svg.append('g')
                        .attr('id','clock-face')
                        .attr('transform','translate(' + (clockRadius + margin) + ',' + (clockRadius + margin) + ')');

                //add marks for seconds
                face.selectAll('.second-tick')
                        .data(d3.range(0,60)).enter()
                        .append('line')
                        .attr('class', 'second-tick')
                        .attr('x1',0)
                        .attr('x2',0)
                        .attr('y1',secondTickStart)
                        .attr('y2',secondTickStart + secondTickLength)
                        .attr('transform',function(d){
                            return 'rotate(' + secondScale(d) + ')';
                        });
                //and labels

                face.selectAll('.second-label')
                        .data(d3.range(5,61,5))
                        .enter()
                        .append('text')
                        .attr('class', 'second-label')
                        .attr('text-anchor','middle')
                        .attr('x',function(d){
                            return secondLabelRadius*Math.sin(secondScale(d)*radians);
                        })
                        .attr('y',function(d){
                            return -secondLabelRadius*Math.cos(secondScale(d)*radians) + secondLabelYOffset;
                        })
                        .text(function(d){
                            return d;
                        });

                //... and hours
                face.selectAll('.hour-tick')
                        .data(d3.range(0,12)).enter()
                        .append('line')
                        .attr('class', 'hour-tick')
                        .attr('x1',0)
                        .attr('x2',0)
                        .attr('y1',hourTickStart)
                        .attr('y2',hourTickStart + hourTickLength)
                        .attr('transform',function(d){
                            return 'rotate(' + hourScale(d) + ')';
                        });

                face.selectAll('.hour-label')
                        .data(d3.range(3,13,3))
                        .enter()
                        .append('text')
                        .attr('class', 'hour-label')
                        .attr('text-anchor','middle')
                        .attr('x',function(d){
                            return hourLabelRadius*Math.sin(hourScale(d)*radians);
                        })
                        .attr('y',function(d){
                            return -hourLabelRadius*Math.cos(hourScale(d)*radians) + hourLabelYOffset;
                        })
                        .text(function(d){
                            return d;
                        });


                var hands = face.append('g').attr('id','clock-hands' + guid);

                face.append('g').attr('id','face-overlay')
                        .append('circle').attr('class','hands-cover')
                        .attr('x',0)
                        .attr('y',0)
                        .attr('r',clockRadius/20);

                hands.selectAll('line')
                        .data(handData)
                        .enter()
                        .append('line')
                        .attr('class', function(d){
                            return d.type + '-hand';
                        })
                        .attr('x1',0)
                        .attr('y1',function(d){
                            return d.balance ? d.balance : 0;
                        })
                        .attr('x2',0)
                        .attr('y2',function(d){
                            return d.length;
                        })
                        .attr('transform',function(d){
                            return 'rotate('+ d.scale(d.value) +')';
                        });
            }

            function moveHands(area){
                d3.select('#clock-hands'+area).selectAll('line')
                        .data(handData)
                        .transition()
                        .attr('transform',function(d){
                            return 'rotate('+ d.scale(d.value) +')';
                        });
            }

            function updateData(offset){
                var t = new Date();
                var localTime = t.getTime();
                var localOffset = t.getTimezoneOffset() * 60000;
                var utc = localTime + localOffset;
                var result = utc + (3600000*offset); // here we get time with offset.
                var nd = new Date(result);

                handData[0].value = (nd.getHours() % 12) + nd.getMinutes()/60;
                handData[1].value = nd.getMinutes();
                handData[2].value = nd.getSeconds();
            }

            d3.select(self.frameElement).style("height", height + "px");

            drawClock(200, 3, 'Donetsk');
            drawClock(200, -5, 'Indianapolis');
        </script>
	</body>
</html>