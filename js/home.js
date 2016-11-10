google.load('visualization', '1.0', {'packages':['table', "corechart"]});
google.load('visualization', '1.0', {'packages':['controls']});

google.setOnLoadCallback(drawIt);
var corpid = '604035876';
var selectedUserId='';

$.urlParam = function(name){
	        var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
	        if (results===null) {
	            return 0;
	        } else {
        	  return results[1] || 0;
	        }
};        

$.urlP = function(name,url){
	        var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(url);
	        if (results===null) {
	            return 0;
	        } else {
        	  return results[1] || 0;
	        }
};        

function showIGB() { // On dom ready

    // Ajax call to the script above, assuming you saved it as "headers.php"
    $.ajax({
     url: "../php/header.php",
     success:  function(data) {

        // The trust boolean value
        var trusted = data.trusted;
        // The content of the headers, "un-prefixed"
        var values = data.values;
            console.log("Hello Eve");
            console.log(data);

        if (!trusted) { // Set to false, trust is required
            // Ask for trust
            CCPEVE.requestTrust(
                location.protocol
                + '//' +
                location.host
            );
        }
        else { // Trust is granted, we have the data
            // Lets greet the player by name
            console.log("Granted");
//            showSheet(values['charid']);
        }
     }
    });
}

function drawDashboard() {
        // Create a dashboard.
        var dashboard = new google.visualization.Dashboard(
            document.getElementById('dashboard_div'));

        // Create a range slider, passing some options
        var donutRangeSlider = new google.visualization.ControlWrapper({
          'controlType': 'NumberRangeFilter',
          'containerId': 'filter_div',
          'options': {
            'filterColumnLabel': 'K'
          }
        });
        var curr = new Date();
        var low = new Date(curr.getTime()-7*24*3600*1000);
        var donutRangeSliderDate = new google.visualization.ControlWrapper({
          'controlType': 'DateRangeFilter',
          'containerId': 'filter_div1',
//          'state': {'lowValue': 3, 'highValue': 8},
          'options': {
            'filterColumnLabel': 'Start date',
            'ui': { 
                allowTyping: false,
				allowMultiple: true,
				electedValuesLayout: 'belowStacked'
            }
          },
          state: {
            lowvalue: low, highvalue: curr
          }
        });
        var donutLastDate = new google.visualization.ControlWrapper({
          'controlType': 'DateRangeFilter',
          'containerId': 'filter_div2',
//          'state': {'lowValue': 3, 'highValue': 8},
          'options': {
            'filterColumnLabel': 'Last login',
            'ui': { 
                allowTyping: false,
				allowMultiple: true,
				electedValuesLayout: 'belowStacked'
            }
          },
          state: {
            value: ''
          }
        });
        var donutDayinPVP = new google.visualization.ControlWrapper({
          'controlType': 'NumberRangeFilter',
          'containerId': 'filter_div3',
//          'state': {'lowValue': 3, 'highValue': 8},
          'options': {
            'filterColumnLabel': 'Days in PVP',
            'ui': { 
                allowTyping: false,
				allowMultiple: true,
				electedValuesLayout: 'belowStacked'
            }
          },
          state: {
            value: ''
          }
        });
        var donutM = new google.visualization.ControlWrapper({
          'controlType': 'NumberRangeFilter',
          'containerId': 'filter_div4',
          'options': {
            'filterColumnLabel': 'Missions',
            'ui': { 
                allowTyping: false,
				allowMultiple: true,
				electedValuesLayout: 'belowStacked'
            }
          },
          state: {
            value: ''
          }
        });
        $.urlParam = function(name){
	        var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
	        if (results===null) {
	            return 0;
	        } else {
        	  return results[1] || 0;
	        }
        };        
        $days = ( $.urlParam("days") === 0 ) ? 30 : $.urlParam("days") ;
        // Create our data table.
//        var jsonData = $.ajax({
        $.ajax({
          url: "../php/getJSON.php?corpid="+corpid+"&days="+$days,
          dataType:"json",
          async: true,
          beforeSend: function() { $('#wait').show(); },
          complete: function() { $('#wait').hide(); },
          error: function(data) {
             console.log(data);
          }
//        }).responseText;
        }).done( function(jsonData) {
          var data = new google.visualization.DataTable(jsonData);
          var formatter = new google.visualization.NumberFormat(
            {groupingSymbol:',',pattern:'#'});
          formatter.format(data, 11);       
          var view= new google.visualization.DataView(data);
        view.setColumns([0,1,2,3,{column:5, calc:StringToDate, type:'date', label:'Start date'},6,
        {column:7, calc:StringToDate2, type:'date', label:'Last login'},8,9,10,11,12,13,{column:14, calc:setRating, type:'number', label:'Rating'}]);

        var cssClassNames = {
        'headerRow': 'tableHead',
        'tableRow': 'dummy',
        'oddTableRow': 'dummy',
        'selectedTableRow': 'dummy',
        'hoverTableRow': 'tableHover',
        'headerCell': 'dummy',
        'tableCell': 'dummy',
        'rowNumberCell': 'dummy'};

        var options = {'showRowNumber': false, 'allowHtml': true, 
            'cssClassNames': cssClassNames, 'sortColumn':4, sortAscending:false,
            page: 'enable',
            pageSize: 50
        };
        var table = new google.visualization.ChartWrapper({
            chartType: 'Table',
            dataTable:view,
            containerId: 'membersTable',
            options:options
//            view: { columns: [0,1,2,3,{column:4, calc:StringToDate, type:'date', label:'Start date'},5,6] }
        });
        function StringToDate(dt,row) {
            var dateArr = dt.getValue(row,5).split('-');
            var year = parseInt(dateArr[0]); 
            var month = parseInt(dateArr[1])-1; 
            var day = parseInt(dateArr[2]); 
            return new Date(year,month,day);
        }
        function StringToDate2(dt,row) {
            var dateArr = dt.getValue(row,7).split('-');
            var year = parseInt(dateArr[0]); 
            var month = parseInt(dateArr[1])-1; 
            var day = parseInt(dateArr[2]); 
            return new Date(year,month,day);
        }
        function setRating(dt,row) {
            var rating = parseInt(dt.getValue(row,2)) * 10 - parseInt(dt.getValue(row,3)) * 5 - parseInt(dt.getValue(row,9)) * 3 - parseInt(dt.getValue(row,10)) * 2 + 1000 ;
            return rating;
        }
        var addListener = google.visualization.events.addListener;
        var addListener2 = google.visualization.events.addListener;
        addListener(table, 'ready', function() { 
            google.visualization.events.removeListener(addListener);
            $('#membersTable a').click(function(e){ e.preventDefault(); showSheet($(this).attr('href').replace('#', '')); }); 
        });
//        addListener(table, 'sort', function() { $('#membersTable a').click(function(e){ e.preventDefault(); showSheet($(this).attr('href').replace('#', '')); }); });
//        addListener(view, 'ready', function() { $('#membersTable a').click(function(e){ e.preventDefault(); showSheet($(this).attr('href').replace('#', '')); }); });
//        addListener(view, 'sort', function() { $('#membersTable a').click(function(e){ e.preventDefault(); showSheet($(this).attr('href').replace('#', '')); }); });
//        addListener(dashboard, 'ready', function() { $('#dashboard_div a').click(function(e){ e.preventDefault(); showSheet($(this).attr('href').replace('#', '')); }); });
//        addListener(dashboard, 'sort', function() { $('#dashboard_div a').click(function(e){ e.preventDefault(); showSheet($(this).attr('href').replace('#', '')); }); });
        // Establish dependencies, declaring that 'filter' drives 'pieChart',
        // so that the pie chart will only display entries that are let through
        // given the chosen slider range.
//        dashboard.bind(donutRangeSlider, table);
//        dashboard.bind(donutRangeSliderDate, data);
//        table.setDataTable(view);
        dashboard.bind([donutRangeSlider,donutRangeSliderDate,donutLastDate,donutDayinPVP,donutM], table);
//        dashboard.bind([donutRangeSlider,donutRangeSliderDate,donutLastDate], view);
        //        table.draw(data, options);
        // Draw the dashboard.
       dashboard.draw(view);
//        table.draw(data);
//        donutRangeSliderDate.draw();
        });
      }

function drawTable() {
    var cssClassNames = {
    'headerRow': 'tableHead',
    'tableRow': 'dummy',
    'oddTableRow': 'dummy',
    'selectedTableRow': 'dummy',
    'hoverTableRow': 'tableHover',
    'headerCell': 'dummy',
    'tableCell': 'dummy',
    'rowNumberCell': 'dummy'};

  var options = {'showRowNumber': false, 'allowHtml': true, 'cssClassNames': cssClassNames, 'sortColumn':4, sortAscending:false};

  var jsonData = $.ajax({
    url: "../php/getJSON.php",
    dataType:"json",
    async: false
  }).responseText;

  var data = new google.visualization.DataTable(jsonData);
  var conatiner = document.getElementById('membersTable');
  var table = new google.visualization.Table(conatiner);

  var addListener = google.visualization.events.addListener;
  addListener(table, 'ready', function() { $('#membersTable a').click(function(e){ e.preventDefault(); showSheet($(this).attr('href').replace('#', '')); }); });
  addListener(table, 'sort', function() { $('#membersTable a').click(function(e){ e.preventDefault(); showSheet($(this).attr('href').replace('#', '')); }); });
  table.draw(data, options);
}

function drawChart() {
  // Create and populate the data table.
  var jsonData = $.ajax({
    url: "../php/getChart.php",
    dataType:"json",
     beforeSend: function() { $('#wait2').show(); },
     complete: function() { $('#wait2').hide(); },
    async: false
  }).responseText;

  var data = new google.visualization.DataTable(jsonData);

  // Create and draw the visualization.
  new google.visualization.LineChart(document.getElementById('onlineChart')).
      draw(data, {
                  theme: "maximized",
                  legend: "none",
                  colors:['black','green','red'],
                  focusTarget: "category",
                  lineWidth: 3,
                  pointSize: 4,
		  height: 150}
          );
}

function drawIt() {
//  drawTable();
  drawChart();
//  check_sso();
  drawDashboard();
}

function showSheet(id,text='') {
  $.ajax({
    url: "../php/getSheet.php",
    data: {id: id, text: text},
    success: function(data) {
      $('.modal-body').html(data);
      $('.modal').modal();
    }
  });
}

function drawBottom() {
    $.ajax({
     url: "../php/getBottom.php",
     beforeSend: function() { $('#wait').show(); },
     complete: function() { $('#wait').hide(); },
     success: function(data) {
       $('#bottom').html(data);
       $('a[rel=bottomtip]').tooltip({placement:'right'});
       $('#bottom a').click(function(e){ e.preventDefault(); showSheet($(this).attr('href').replace('#', '')); });
     }
    });
}

function drawOptions() {
    $.ajax({
     url: "../php/getOptions.php",
     success: function(data) {
       $('#options').html(data);
     }
    });
}

function auth (p) {
  if (typeof p == "undefined") {
    p = "";
  }
  $.ajax({
  url: "../php/auth.php?"+p,
  success: function(data) {
    if (data!=0) {
      $('#status').text(data);
      $('#login').toggle();
      drawOptions();
//      $('#gather').toggle();
//      drawBottom();
//      drawTable();
      drawDashboard();
    }
  }
  });
}

function login (p) {
  $.ajax({
  type: "POST",
  url: "../php/login.php?u="+usr.value+"&p="+pwd.value,
  success: function(data) {
     console.log(data);
      getButton();
//     $('.login-form').html(data);
//     data = $_SESSION['user'];
//     $('#status').text(data);
//      $('#submit').toggle();
      drawOptions();
//      $('#gather').toggle();
//      drawBottom();
//      drawTable();
      drawDashboard();
  }
  });
}
function logout () {
  $.ajax({
  url: "../php/logout.php",
  success: function(data) {
      getButton();
//     $('.login-form').html(data);
//     $('#status').text(data);
//      $('#submit').toggle();
      drawOptions();
//      $('#gather').toggle();
//      drawBottom();
//      drawTable();
      drawDashboard();
  }
  });
}
function check_sso() {
    $.ajax({
     url: "../php/sso_super.php",
     success: function(data) {
       $('.login-sso').html(data);
        $("#ssoSelectButton").click(function(e){
    //            var hiddenSection = $('#ssoSelect').parents('section.hidden');
                var scopes = [];
    //            $('input[name="scopes"]:checked').each(function(){scopes.push($(this).val())});
                var array={action:"ssoLogin", scopes:scopes};
                $.ajax({
                    type: 'POST',
                    url: '../php/sso.php?t=' + new Date().getTime(),
                    data: array,
                    async: true,
                    success: function (data, textStatus, XHR) {
                        data=$.parseJSON(data);
                        window.location.href=data.url;
                    }
                });
        });
        if ($.urlParam("code")!=0) {
            $.ajax({
                url: "../php/sso_login.php?code="+$.urlParam("code"),
                success: function(data) {
                  window.location = '/';
                }
            });
        }
     }
    });    
}
function getButton() {
    check_sso();
    $.ajax({
     url: "../php/super.php",
     success: function(data) {
       $('.login-form').html(data);
       $('#submit').click(function(p){
         login(p);
       });
       $('#logout').click(function(){
         $("#ssoSelectButton").show();
         logout();
       });
     }
    });    
}
//function get_data () {
//  $.ajax({
//  url: "../php/gather.php",
//  success: function(data) {
//      $('#status1').text(data);
//  }
//  });
//}

!function ($) {
  $(function(){
    var $window = $(window)
//    $.ajax({
//     url: "../php/getTop.php",
//     success: function(data) {
//       $('#top').html(data);
//       $('a#topchar').click(function(e){ e.preventDefault(); showSheet($(this).attr('href').replace('#', '')); });
//       $('a#topship').click(function(e) { e.preventDefault(); $(this).closest('div').effect('shake'); } );
//     }
//    });
//    drawBottom();
    $.ajax({
     url: "../php/getCount.php",
     beforeSend: function() { $('#wait1').show(); },
     complete: function() { $('#wait1').hide(); },
     success: function(data) {
       $('.counter').html(data);
     }
    });
    $.ajax({
     url: "../php/getPilots.php",
     success: function(data) {
       $('#charlist').html(data);
       var pilots = [];
       $("#charlist").find("div").each(function(){ pilots.push($(this).attr('class')); });
       $('.search-query').typeahead({source:pilots});
       $('.search-query').keypress(function(event) {
         if ( event.which == 13 ) {
           event.preventDefault();
           pid=$("[class='"+$(this).val()+"']").attr("id");
           if (pid) showSheet(pid);
           return;
         }
       });
       $('.search-query').change(function(){
         if (event.type=='change') {
           pid=$("[class='"+$('li.active').attr('data-value')+"']").attr("id");
           if (pid) showSheet(pid);
         }
       });
     }
    });
    getButton();
    
    $('#fsp-t').click(function(){
       corpid = '604035876'; 
       getButton();
       drawOptions();
       drawDashboard();
    });
    $('#academy').click(function(){
       corpid = '98399497'; 
       getButton();
       drawOptions();
       drawDashboard();
    });
    $('#geten').click(function(){
       corpid = '98360267'; 
       getButton();
       drawOptions();
       drawDashboard();
    });

    $('#igb').click(function(){
     showIGB();
    });
//    $('#login').click(function(){
//     auth();
//    });
//    $('#gather').click(function(){
//     get_data();
//    });

//    drawTable();
//    auth("query=1");
    $('.dropdown-toggle').dropdown();
      $("li a").click(function(){
//           $('#nav li').removeClass();
//           $(this).parent().addClass('active');   
           $('#corp').text($(this).text());   
      });
      
  })
}(window.jQuery)
