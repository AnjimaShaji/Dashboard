/* ------------------------------------------------------------------------------
	*
	*  # D3.js - vertical bar chart
	*
	*  Demo d3.js vertical bar chart setup with .tsv data source
	*
* ---------------------------------------------------------------------------- */


// Setup module
// ------------------------------

var D3BarVerticalRight = function() {

    //
    // Setup module components
    //
	
    // Chart
    var _barVerticalRight = function() {
        if (typeof d3 == 'undefined') {
            console.warn('Warning - d3.min.js is not loaded.');
            return;
		}
		
        // Main variables
        var element = document.getElementById('d3-bar-vertical-right'),
		height = 340;
		var  url = document.getElementById('role').value;
		
        // Initialize chart only if element exsists in the DOM
        if(element) {
			
            // Basic setup
            // ------------------------------
			
            // Define main variables
            var d3Container = d3.select(element),
			margin = {top: 30, right: 10, bottom: 20, left: 40},
			width = d3Container.node().getBoundingClientRect().width - margin.left - margin.right,
			height = height - margin.top - margin.bottom - 5;
			
			
			
            // Construct scales
            // ------------------------------
			
            // Horizontal
            var x = d3.scale.ordinal()
			.rangeRoundBands([0, width], .5, .2);
			
            // Vertical
            var y = d3.scale.linear()
			.range([height, 0]);
			
            // Color
            var color = d3.scale.category20c();
			
			
			
            // Create axes
            // ------------------------------
			
            // Horizontal
            var xAxis = d3.svg.axis()
			.scale(x)
			.orient("bottom");
			
            // Vertical
            var yAxis = d3.svg.axis()
			.scale(y)
			.orient("left")
			.ticks(10, "%");
			
			
			
            // Create chart
            // ------------------------------
			
            // Add SVG element
            var container = d3Container.append("svg");
			
            // Add SVG group
            var svg = container
			.attr("width", width + margin.left + margin.right)
			.attr("height", height + margin.top + margin.bottom)
			.append("g")
			.attr("transform", "translate(" + margin.left + "," + margin.top + ")");
			
			var pageParams = getUrlParams();
	        var currentString = '';
	        for (var i=0; i<pageParams.length; i++)
	        {
	            var paramVal = pageParams[i];
	            if(paramVal || paramVal != '') {
	            	currentString += '&' + pageParams[i] + '=' + pageParams[paramVal];
	            }
	        }
			
			
            // Load data
            // ------------------------------

            $.ajax({
            type: 'GET',
            url: url + '/get-attempt-vs-contact'+'?'+currentString,
            success: function(response) {
            	var bardata = [];
            	bardata[0] = [];
            	bardata[0]['letter'] = "30m";
            	if(response.lead_attempted_time_count_thirty != 0) {
            		bardata[0]['frequency'] = response.lead_contacted_time_count_thirty/response.lead_attempted_time_count_thirty;
            	} else {
            		bardata[0]['frequency'] = 0;
            	}

            	bardata[1] = [];
            	bardata[1]['letter'] = "1hr";
            	if(response.lead_attempted_time_count_one_hr != 0) {
            		bardata[1]['frequency'] = response.lead_contacted_time_count_one_hr/response.lead_attempted_time_count_one_hr;
            	} else {
            		bardata[1]['frequency'] = 0;
            	}

            	bardata[2] = [];
            	bardata[2]['letter'] = "2hr";
            	if(response.lead_attempted_time_count_two_hr != 0) {
            		bardata[2]['frequency'] = response.lead_contacted_time_count_two_hr/response.lead_attempted_time_count_two_hr;
            	} else {
            		bardata[2]['frequency'] = 0;
            	}

            	bardata[3] = [];
            	bardata[3]['letter'] = "4hr";
            	if(response.lead_attempted_time_count_four_hr != 0) {
            		bardata[3]['frequency'] = response.lead_contacted_time_count_four_hr/response.lead_attempted_time_count_four_hr;
            	} else {
            		bardata[3]['frequency'] = 0;
            	}

            	bardata[4] = [];
            	bardata[4]['letter'] = "8hr";
            	if(response.lead_attempted_time_count_eight_hr != 0) {
            		bardata[4]['frequency'] = response.lead_contacted_time_count_eight_hr/response.lead_attempted_time_count_eight_hr;
            	} else {
            		bardata[4]['frequency'] = 0;
            	}

            	bardata[5] = [];
            	bardata[5]['letter'] = ">8hr";
            	if(response.lead_attempted_time_count_gt_eight_hr != 0) {
            		bardata[5]['frequency'] = response.lead_contacted_time_count_gt_eight_hr/response.lead_attempted_time_count_gt_eight_hr;
            	} else {
            		bardata[5]['frequency'] = 0;
            	}

            	data = bardata;

            	data.forEach(function(d) {
                    d.frequency = +d.frequency;
				});
				
				
                // Set input domains
                // ------------------------------
				
                // Horizontal
                x.domain(data.map(function(d) { return d.letter; }));
				
                // // Vertical
                // y.domain([0, d3.max(data, function(d) { return d.frequency; }),100]);
				y.domain([0,1]);
				
				
                //
                // Append chart elements
                //
				
                // Append axes
                // ------------------------------
				
                // Horizontal
                svg.append("g")
				.attr("class", "d3-axis d3-axis-horizontal")
				.attr("transform", "translate(0," + height + ")")
				.call(xAxis);
				
                // Vertical
                var verticalAxis = svg.append("g")
				.attr("class", "d3-axis d3-axis-vertical")
				.call(yAxis);
				
                // Add text label
                verticalAxis.append("text")
				.attr("class", "d3-axis-title")
				.attr("transform", "rotate(-90)")
				.attr("y", 10)
				.attr("dy", ".71em")
				.style("text-anchor", "end")
				.text("");
				
				
                // Add bars
                svg.selectAll(".d3-bar")
				.data(data)
				.enter()
				.append("rect")
				.attr("class", "d3-bar")
				.attr("x", function(d) { return x(d.letter); })
				.attr("width", x.rangeBand())
				.attr("y", function(d) { return y(d.frequency); })
				.attr("height", function(d) { return height - y(d.frequency); })
				.style("fill", "steelblue");
				
				svg.append("text")
				.attr("x", (width / 2))             
				.attr("y", 0 - (margin.top / 2))
				.attr("text-anchor", "middle")  
				.style("font-size", "16px") 
				.style("text-decoration", "underline")  
				.text("Attempt Time Vs Contact Rate");
            }
		});
           
			
			
            // Resize chart
            // ------------------------------
			
            // Call function on window resize
            window.addEventListener('resize', resize);
			
            // Call function on sidebar width change
            var sidebarToggle = document.querySelector('.sidebar-control');
            sidebarToggle && sidebarToggle.addEventListener('click', resize);
			
            // Resize function
            // 
            // Since D3 doesn't support SVG resize by default,
            // we need to manually specify parts of the graph that need to 
            // be updated on window resize
            function resize() {
				
                // Layout variables
                width = d3Container.node().getBoundingClientRect().width - margin.left - margin.right;
				
				
                // Layout
                // -------------------------
				
                // Main svg width
                container.attr("width", width + margin.left + margin.right);
				
                // Width of appended group
                svg.attr("width", width + margin.left + margin.right);
				
				
                // Axes
                // -------------------------
				
                // Horizontal range
                x.rangeRoundBands([0, width], .1, .5);
				
                // Horizontal axis
                svg.selectAll('.d3-axis-horizontal').call(xAxis);
				
				
                // Chart elements
                // -------------------------
				
                // Line path
                svg.selectAll('.d3-bar').attr("x", function(d) { return x(d.letter); }).attr("width", x.rangeBand());
			}
		}
	};
	
	
    //
    // Return objects assigned to module
    //
	
    return {
        init: function() {
            _barVerticalRight();
		}
	}
}();


// Initialize module
// ------------------------------

document.addEventListener('DOMContentLoaded', function() {
    D3BarVerticalRight.init();
});
