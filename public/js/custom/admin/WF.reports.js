(function(){
    window.WF = window.WF || {};
    WF.reports = (function(){
        return {
            customFields : null,
            CustomResetFields : null,
            urlPrefix : null,
            csvFileName : null,
            exportStatus: null,
            exportStatusArr:["Appending to CSV", "Restructuring Data sets", "Accumulate JSON data in buckets", 
                             "Fetching data chunks", "Building map-reduce algorithm", "Matching B-Tree index", 
                             "Scanning collections", "Identifying replica sets", "Locating shards", "Processing request"],
            exportStatusArrTemp:[],
            csvFetchTimeoutHandle : null,
            selectedParams : [],
            wavesurfer : null,
            initCalllog :   function(urlPrefix) {
                WF.reports.urlPrefix = urlPrefix;
                $(document).ready(function(){
                    $('.close').click(function(){
                        window.clearTimeout(WF.reports.csvFetchTimeoutHandle);
                    });
                    setTimeout(function () {
                        $("button:contains('Display all')").click(function () {
                            if ($('#callTable').hasClass('display-all')) {
                                WF.reports._createCookie('WF-DA','on','0','0');
                                $('.cbr-replaced').each(function () {
                                    if (!$(this).hasClass('cbr-checked')) {
                                        $(this).addClass('cbr-checked')
                                    }
                                });
                            } else {
                                WF.reports._createCookie('WF-DA','off','0','0');
                                var defaultFields = WF.reports.get('CustomResetFields');
                                $('#display .cbr-replaced').each(function () {
                                    var label = $(this).parent().find('label').text();
                                    var labelfor = $(this).parent().find('label').attr('for');
                                    var input = $(this).parent().find('input').val();

                                    if ($.inArray(label.replace(/\s+/g, ''), defaultFields.split(',')) == -1) {
                                        var input = $(this).parent().find('input').val();
                                        if ($(this).hasClass('cbr-checked')) {
                                            $(this).removeClass('cbr-checked');
                                            $('#' + input).css("display", "none");
                                            $('tr').find("td[data-columns=" + input + "]").css("display", "none");
                                        }
                                    }
                                });
                            }
                        });
                        if(WF.reports._getCookie('WF-DA') == 'on') {
                            $("button:contains('Display all')").addClass('btn-primary');
                        }
                    }, 5000);
         

                    $('.busy-callees').click(function() {
                        var agentNumber = $(this).text().substr(1);
                        var agentRecordList = $(this).siblings('.agent-record-list').val();
                        var isRecordAvailable = false;
                        var row = $(this).closest('.callDtls');
                        $('#callee_recodings_modal .modal-body').html(null);
                        agentRecordListJson = $.parseJSON(agentRecordList);
                        if (agentRecordListJson) {
                            $.each(agentRecordListJson, function(agentRecord, agent) {
                                if (agentRecord.indexOf(agentNumber) !== -1) {
                                    isRecordAvailable = true;
                                    if (row.attr('id') >= '5ae2d9358991f7291b768d14') {
                                        audioPlayer = '<audio src="https://storage.googleapis.com/waybeo-ford/mumbai/' + agentRecord + '.mp3" preload="metadata" controls>';
                                    } else {
                                        audioPlayer = '<audio src="https://storage.googleapis.com/waybeo-ford/mumbai/' + agentRecord + '.wav" preload="metadata" controls>';
                                    }
                                    playerDiv = '<div class="row"> <div class="col-md-12">' + audioPlayer + '</div></div>';
                                    $('#callee_recodings_modal .modal-body').append(playerDiv);
                                }
                            });
                        }
                        if (!isRecordAvailable) {
                            $('#callee_recodings_modal .modal-body').text('No Records Available');
                        }
                        $('#callee_recodings_modal').modal('show', {backdrop: 'static'});
                    });
                    $('#callee_recodings_modal .close').click(function() {
                        $('#callee_recodings_modal .modal-body').html(null)
                    });
                    $('.insights_img').click(function() {
                        $('#insights_modal').modal('show', {backdrop: 'static'});
                        var callRecUrl = $(this).parent().find('.call_rec').val();
                        $('#call_rec_url_modal').val(callRecUrl);
                        var carTags = $(this).parent().find('.car_tags').val();
                        WF.reports._setModalCarTags(carTags);
                        var keywordTags = $(this).parent().find('.keyword_tags').val();
                        WF.reports._setModalKeywordTags(keywordTags);
                        wavesurfer.load(callRecUrl);
                    });
                    $('#playPause').click(function() {
                        wavesurfer.playPause();
                    });
                    $("input[name='wav_options']").change(function(e){
                        if ($(this).attr('id') == 'conversation_wave') {
                            var callRecUrl = $('#call_rec_url_modal').val();
                            wavesurfer.load(callRecUrl);
                        } else if ($(this).attr('id') == 'customer_wave') {
                            var callRecUrl = $('#call_rec_url_modal').val().replace('.mp3', '-con.wav');
                            wavesurfer.load(callRecUrl);
                        } else {
                            var callRecUrl = $('#call_rec_url_modal').val().replace('.mp3', '-out.mp3');
                            wavesurfer.load(callRecUrl);
                        }
                    });
                    $('#insights_modal .close').click(function() {
                        wavesurfer.stop();
                    });

                    // $("#DomId").change(function(){
                    //     if($('#DomId').val() === '0' || $.trim($('#DomId').val()) === "") {
                    //         $('#RsmId').empty();
                    //         $('#RsmId').append($('<option>', {
                    //             value: 0,
                    //             text: 'Select RSM',
                    //             selected: 'selected'
                    //         }));
                    //         var jsonData = {};
                    //         $.ajax({
                    //             url: '/' + urlPrefix + '/reports/get-rsm/',
                    //             data: jsonData,
                    //             success: function (response) {
                    //                 $.each(response, function (i, rsm) {
                    //                     $('#RsmId').append($('<option>', {
                    //                         value: rsm.id,
                    //                         text: rsm.name
                    //                     }));
                    //                 });
                    //             }
                    //         });

                    //         $('#StoreId').empty();
                    //         $('#StoreId').append($('<option>', {
                    //             value: 0,
                    //             text: 'Select Dealer',
                    //             selected: 'selected'
                    //         }));
                    //         var jsonData = {};
                    //         $.ajax({
                    //             url: '/' + urlPrefix + '/reports/get-dealer/',
                    //             data: jsonData,
                    //             success: function (response) {

                    //                 $.each(response, function (i, dealer) {

                    //                     $('#StoreId').append($('<option>', {
                    //                         value: dealer.id,
                    //                         text: dealer.name
                    //                     }));
                    //                 });
                    //             }
                    //         });
                    //     }
                    //     else {
                    //         $('#RsmId').empty();
                    //         $('#RsmId').append($('<option>', {
                    //             value: 0,
                    //             text: 'Select RSM',
                    //             selected: 'selected'
                    //         }));
                    //         var jsonData = {};
                    //         $.ajax({
                    //             url: '/' + urlPrefix + '/reports/get-dom-rsm/' + $('#DomId').val(),
                    //             data: jsonData,
                    //             success: function (response) {
                    //                 $.each(response, function (i, rsm) {
                    //                     $('#RsmId').append($('<option>', {
                    //                         value: rsm.id,
                    //                         text: rsm.name
                    //                     }));
                    //                 });
                    //             }
                    //         });
                    //     }
                    // });
                    
                    // $("#RsmId").change(function(){
                    //     console.log($.trim($('#RsmId').val()));
                    //     if($('#RsmId').val() === '0' || $.trim($('#RsmId').val()) === "") {

                    //         $('#StoreId').empty();
                    //         $('#StoreId').append($('<option>', {
                    //             value: 0,
                    //             text: 'Select Dealer',
                    //             selected: 'selected'
                    //         }));
                    //         var jsonData = {};

                    //         if($('#DomId').val() === '0' || $.trim($('#DomId').val()) === "") {
                    //             $.ajax({
                    //                 url: '/' + urlPrefix + '/reports/get-dealer/',
                    //                 data: jsonData,
                    //                 success: function (response) {

                    //                     $.each(response, function (i, dealer) {

                    //                         $('#StoreId').append($('<option>', {
                    //                             value: dealer.id,
                    //                             text: dealer.name
                    //                         }));
                    //                     });
                    //                 }
                    //             });
                    //         }else {
                    //             $.ajax({
                    //                 url: '/' + urlPrefix + '/reports/get-dom-dealer/' + $('#DomId').val(),
                    //                 data: jsonData,
                    //                 success: function (response) {

                    //                     $.each(response, function (i, dealer) {

                    //                         $('#StoreId').append($('<option>', {
                    //                             value: dealer.id,
                    //                             text: dealer.name
                    //                         }));
                    //                     });
                    //                 }
                    //             });
                    //         }                            
                    //     }
                    //     else {
                    //         $('#StoreId').empty();
                    //         $('#StoreId').append($('<option>', {
                    //             value: 0,
                    //             text: 'Select Dealer',
                    //             selected: 'selected'
                    //         }));
                    //         var jsonData = {};
                    //         $.ajax({
                    //             url: '/' + urlPrefix + '/reports/get-rsm-dealer/' + $('#RsmId').val(),
                    //             data: jsonData,
                    //             success: function (response) {

                    //                 $.each(response, function (i, dealer) {

                    //                     $('#StoreId').append($('<option>', {
                    //                         value: dealer.id,
                    //                         text: dealer.name
                    //                     }));
                    //                 });
                    //             }
                    //         });
                    //     }
                    // });

                });

                $('#filter').click(function(){
                    WF.reports.setCustomFields();
                    var qString = '?';
                    if ($('#StateId').length) {
                        if ($('#StateId').val() != '')
                            qString += '&StateId=' + $('#StateId').val();
                        else
                            qString += '&StateId=';
                    }
                    if ($('#CityId').length) {
                        if ($('#CityId').val() != '')
                            qString += '&CityId=' + $('#CityId').val();
                        else
                            qString += '&CityId=';
                    }
                    if ($('#Location').length) {
                        if ($('#Location').val() != '')
                            qString += '&Location=' + $('#Location').val();
                        else
                            qString += '&Location=';
                    }

                    if($('#StoreId').length) {
                        if($('#StoreId').val() != '')
                            qString += '&StoreId='+$('#StoreId').val();
                        else
                            qString += '&StoreId=';
                    }
                    if($('#CallerId').length) {
                        if($('#CallerId').val() != '')
                            qString += '&CallerId='+$('#CallerId').val();
                        else
                            qString += '&CallerId=';
                    }
                    if($('#Status').length) {
                        if($('#Status').val() != '')
                            qString += '&Status='+$('#Status').val();
                        else
                            qString += '&Status=';
                    }
                    if($('#date_from').length) {
                        if($('#date_from').val() != '')
                            qString += '&date_from='+$('#date_from').val();
                        else
                            qString += '&date_from=';
                    }
                    if($('#date_to').length) {
                        if($('#date_to').val() != '')
                            qString += '&date_to='+$('#date_to').val();
                        else
                            qString += '&date_to=';
                    }
                    // if($('#CallerId').length) {
                    //     if($('#CallerId').val() != '')
                    //         qString += '&CallerId='+$('#CallerId').val();
                    //     else
                    //         qString += '&CallerId=';
                    // }
                    if($('#Region').length) {
                        if($('#Region').val() != '')
                            qString += '&Region='+$('#Region').val();
                        else
                            qString += '&Region=';
                    }
                    if($('#Cluster').length) {
                        if($('#Cluster').val() != '')
                            qString += '&Cluster='+$('#Cluster').val();
                        else
                            qString += '&Cluster=';
                    }
                    if($('#Type').length) {
                        if($('#Type').val() != '')
                            qString += '&Type='+$('#Type').val();
                        else
                            qString += '&Type=';
                    }
                    // if($('#City').length) {
                    //     if($('#City').val() != '')
                    //         qString += '&City='+$('#City').val();
                    //     else
                    //         qString += '&City=';
                    // }
                    // if($('#Type').length) {
                    //     if($('#Type').val() != '')
                    //         qString += '&Type='+$('#Type').val();
                    //     else
                    //         qString += '&Type=';
                    // }
                    // if($('#callType').length) {
                    //     if($('#callType').val() != '')
                    //         qString += '&callType='+$('#callType').val();
                    //     else
                    //         qString += '&callType=';
                    // }
                    // if($('#Location').length) {
                    //     if($('#Location').val() != '')
                    //         qString += '&Location='+$('#Location').val();
                    //     else
                    //         qString += '&Location=';
                    // }
                    // if($('#unique').length) {
                    //     if($('#unique').val() != '')
                    //         qString += '&Unique='+$('#unique').val();
                    //     else
                    //         qString += '&Unique=';
                    // }
                    // if($('#VirtualNumberType').length) {

                    //     if($('#VirtualNumberType').val() != '')
                    //         qString += '&VirtualNumberType='+$('#VirtualNumberType').val();
                    //     else
                    //         qString += '&VirtualNumberType=';
                    // }
                    qString += '&Fields='+ WF.reports.getCustomFields();
                    document.location.href = '/' + urlPrefix + '/reports' + qString;
                });
                $('#export').click(function(){
                    WF.reports.setCustomFields();
                    var qString = '?';
                    var label;
                     if($('#StoreId').length) {
                        if($('#StoreId').val() != '')
                            qString += '&StoreId='+$('#StoreId').val();
                        else
                            qString += '&StoreId=';
                    }
                    if ($('#StateId').length) {
                        if ($('#StateId').val() != '')
                            qString += '&StateId=' + $('#StateId').val();
                        else
                            qString += '&StateId=';
                    }
                    if ($('#CityId').length) {
                        if ($('#CityId').val() != '')
                            qString += '&CityId=' + $('#CityId').val();
                        else
                            qString += '&CityId=';
                    }
                    if ($('#Location').length) {
                        if ($('#Location').val() != '')
                            qString += '&Location=' + $('#Location').val();
                        else
                            qString += '&Location=';
                    }
                    if($('#Status').length) {
                        if($('#Status').val() != '')
                            qString += '&Status='+$('#Status').val();
                        else
                            qString += '&Status=';
                    }
                    if($('#date_from').length) {
                        if($('#date_from').val() != '')
                            qString += '&date_from='+$('#date_from').val();
                        else
                            qString += '&date_from=';
                    }
                    if($('#date_to').length) {
                        if($('#date_to').val() != '')
                            qString += '&date_to='+$('#date_to').val();
                        else
                            qString += '&date_to=';
                    }
                    if($('#CallerId').length) {
                        if($('#CallerId').val() != '')
                            qString += '&CallerId='+$('#CallerId').val();
                        else
                            qString += '&CallerId=';
                    }
                    if($('#Region').length) {
                        if($('#Region').val() != '')
                            qString += '&Region='+$('#Region').val();
                        else
                            qString += '&Region=';
                    }
                    if($('#Cluster').length) {
                        if($('#Cluster').val() != '')
                            qString += '&Cluster='+$('#Cluster').val();
                        else
                            qString += '&Cluster=';
                    }
                    if($('#Type').length) {
                        if($('#Type').val() != '')
                            qString += '&Type='+$('#Type').val();
                        else
                            qString += '&Type=';
                    }
                    // if($('#City').length) {
                    //     if($('#City').val() != '')
                    //         qString += '&City='+$('#City').val();
                    //     else
                    //         qString += '&City=';
                    // }
                    // if($('#Type').length) {
                    //     if($('#Type').val() != '')
                    //         qString += '&Type='+$('#Type').val();
                    //     else
                    //         qString += '&Type=';
                    // }
                    // if($('#callType').length) {
                    //     if($('#callType').val() != '')
                    //         qString += '&callType='+$('#callType').val();
                    //     else
                    //         qString += '&callType=';
                    // }
                    // if($('#Location').length) {
                    //     if($('#Location').val() != '')
                    //         qString += '&Location='+$('#Location').val();
                    //     else
                    //         qString += '&Location=';
                    // }
                    // if($('#unique').length) {
                    //     if($('#unique').val() != '')
                    //         qString += '&Unique='+$('#unique').val();
                    //     else
                    //         qString += '&Unique=';
                    // }
                    // if($('#VirtualNumberType').length) {
                    //     if($('#VirtualNumberType').val() != '')
                    //         qString += '&VirtualNumberType='+$('#VirtualNumberType').val();
                    //     else
                    //         qString += '&VirtualNumberType=';
                    // }
                    qString += '&Fields='+ WF.reports.getCustomFields();
                    
                    if(1) {
                        window.clearTimeout(WF.reports.csvFetchTimeoutHandle);
                            $.ajax({
                                type: 'GET',
                                url: '/'+urlPrefix+'/reports/is-background-export' + qString,
                                success: function(response) {
                                    if (response.background_export) {
                                        $('#modalAjax').modal('show', {backdrop: 'static'});
                                        $('#modalAjax').find('.modal-title').text('Generating report');
                                        $('#modalAjax').find('.modal-dialog').css('width', '50%');
                                        $('#modalAjax').find('.modal-footer').css('display', 'none');
                                        var modalBody = '<img src="/assets/images/export-inprogress.gif" width="90%" style="margin-left: 30px;">'+
                                        			 '<p style="text-align:center;padding-top:20px;color:#008FC4;" id="modal-status"><strong>Processing request</strong></p>';
                                        $('#modalAjax .modal-body').html(modalBody);
                                        WF.reports.exportStatusArrTemp = WF.reports.exportStatusArr;
	                                    WF.reports.exportStatus=setInterval(function(){
	                                    		if (WF.reports.exportStatusArrTemp.length==0)
	                                    			WF.reports.exportStatusArrTemp=["Appending to CSV", "Restructuring Data sets", "Accumulate JSON data in buckets", 
	                                    			                             "Fetching data chunks"];
	                                    		$("#modal-status").html("<strong>"+WF.reports.exportStatusArrTemp.pop()+"</strong>");
	                                    	},1000)
                                        $.ajax({
                                            url: "/"+urlPrefix+"/reports/process-export" + qString,
                                            success: function (response) {
                                                if(response.status) {
                                                    $('#modalAjax .modal-body').html(response);
                                                    cbr_replace();
                                                } else {
                                                    var msg_failure = '<h4>Something went wrong.</h4>';
                                                    if(response.reason == '1') {
//                                                        msg_failure = '<h4>An export job is already in progress. Please try after some time.</h4>';
//                                                        $('#modalAjax .modal-body').html(msg_failure);
                                                    } else if(response.reason == '2') {
//                                                        msg_failure = '<h4>Please Wait your export is being processed.</h4>';
//                                                        $('#modalAjax .modal-body').html(msg_failure);
                                                        WF.reports.csvFileName = {"file":response.file};
                                                        WF.reports.getCsvFileByName(urlPrefix);
                                                    }
                                                }
                                            }
                                        });
                                    } else {
                                        document.location.href = '/' + urlPrefix + '/reports/export' + qString;
                                    }
                                }
                            });
                    } else {
                        document.location.href = '/' + urlPrefix + '/reports/export' + qString;
                    }
                });
                $('#reset').click(function(){
                    document.location.href = '/' + urlPrefix + '/reports/' ;
                });
                
                // $('.callDtls').click(function(){
                //     var _id = $(this).attr('id');
                //     document.location.href = '/' + urlPrefix + '/reports/call/' + _id;
                // });
                WF.reports.updatePagination();
            },
            pagination: function (dom) {
                var page = WF.setupPaginator.setup(dom);
                if (page) {
                    var freezeDiv = document.createElement("div");
                    freezeDiv.id = "freezeDiv";
                    freezeDiv.style.cssText = "position:absolute; top:0; right:0; width:" 
                            + screen.width 
                            + "px; height:1330px; background-color: #000000; opacity:0.5; filter:alpha(opacity=50)";
                    document.getElementsByTagName("tbody")[0].appendChild(freezeDiv);
                    WF.reports.setCustomFields();
                    var qString = '?';
                    qString += 'page=' + $.trim(page);
                    if ($('#StateId').length) {
                        if ($('#StateId').val() != '')
                            qString += '&StateId=' + $('#StateId').val();
                        else
                            qString += '&StateId=';
                    }
                    if ($('#CityId').length) {
                        if ($('#CityId').val() != '')
                            qString += '&CityId=' + $('#CityId').val();
                        else
                            qString += '&CityId=';
                    }
                    if ($('#Location').length) {
                        if ($('#Location').val() != '')
                            qString += '&Location=' + $('#Location').val();
                        else
                            qString += '&Location=';
                    }
                    if($('#StoreId').length) {
                        if($('#StoreId').val() != '')
                            qString += '&StoreId='+$('#StoreId').val();
                        else
                            qString += '&StoreId=';
                    }
                    if($('#Status').length) {
                        if($('#Status').val() != '')
                            qString += '&Status='+$('#Status').val();
                        else
                            qString += '&Status=';
                    }
                    if($('#date_from').length) {
                        if($('#date_from').val() != '')
                            qString += '&date_from='+$('#date_from').val();
                        else
                            qString += '&date_from=';
                    }
                    if($('#date_to').length) {
                        if($('#date_to').val() != '')
                            qString += '&date_to='+$('#date_to').val();
                        else
                            qString += '&date_to=';
                    }
                    if($('#CallerId').length) {
                        if($('#CallerId').val() != '')
                            qString += '&CallerId='+$('#CallerId').val();
                        else
                            qString += '&CallerId=';
                    }
                    if($('#Region').length) {
                        if($('#Region').val() != '')
                            qString += '&Region='+$('#Region').val();
                        else
                            qString += '&Region=';
                    }
                    if($('#Cluster').length) {
                        if($('#Cluster').val() != '')
                            qString += '&Cluster='+$('#Cluster').val();
                        else
                            qString += '&Cluster=';
                    }
                    if($('#Type').length) {
                        if($('#Type').val() != '')
                            qString += '&Type='+$('#Type').val();
                        else
                            qString += '&Type=';
                    }
                    qString += '&Fields='+ WF.reports.getCustomFields();
                    window.location = '/' + WF.reports.urlPrefix + '/reports' + qString;
                    }
            },
            updatePagination: function () {
                $('.pages li').unbind();
                $('.pages li').click(function () {
                    if ($(this).children().attr('class') != 'active' && $(this).children().attr('class') != 'disabled')
                        WF.reports.pagination($(this));
                });
            },
            showCustomTableData : function () {
                setTimeout(function(){
                    var defaultFields = WF.reports.get('CustomFields');
                    $('#display .cbr-replaced').each(function() {
                            var label = $(this).parent().find('label').text();
                            var labelfor = $(this).parent().find('label').attr('for');
                            var input = $(this).parent().find('input').val();

                            if($.inArray(label.replace(/\s+/g, ''),defaultFields.split(',')) == -1){
                                    var input = $(this).parent().find('input').val();
                                    if($(this).hasClass('cbr-checked')) {
                                            $(this).removeClass('cbr-checked');
                                            $('#'+input).css("display","none");
                                            $('tr').find("td[data-columns="+ input +"]").css("display","none");
                                    }
                            }
                    })
                }, 100);
            },
            // getMappedCustomField : function(){
            //     return {
            //         CallStartDate : 'DateTime',
            //         Duration : 'TotalDuration',
            //         VirtualNumber : 'MaskedNumber',
            //         Dealer : 'Dealer',
            //         City : 'Location',
            //         CustomerNumber : 'CallerId',
            //         Type : 'Type',
            //         Status : 'Status',
            //         IVRKeyPress : 'IvrLog',
            //         CallRecording : 'Recording',
            //     };
            // },
            getCustomFields : function() {
                var fields = [];
                if ($("button:contains('Display all')").hasClass('btn-primary')) {
                    $('#display .cbr-replaced').each(function() {
                            var label = $(this).parent().find('label').text();
                            fields.push(label.replace(/\s+/g, ''));
                    });
                } else {
                    $('#display .cbr-checked').each(function() {
                            var label = $(this).parent().find('label').text();
                            fields.push(label.replace(/\s+/g, ''));
                    });
                }
                return fields.toString();
            },
            setCustomFields : function(fields) {
                if(typeof fields == 'undefined') {
                    fields = WF.reports.getCustomFields();
                }
                    WF.reports.set('CustomFields', fields);
            },
            get: function(key) {
                if(WF.reports.localStorageSupport()) {
                    return window.localStorage.getItem(key);
                } else {
                    return WF.reports.customFields;
                }
            },
            set: function(key, object) {

                if(WF.reports.localStorageSupport()) {
                    window.localStorage.setItem(key, object);
                } else {
                    WF.reports.customFields = object;
                }
            },
            remove: function(key) {
                window.localStorage.removeItem(key);
            },
            localStorageSupport :   function () {
                if(typeof Storage !== "undefined") {
                    return true;
                } else {
                    return false;
                }
            },
            _setModalCarTags : function(carTags) {
                carTags = JSON.parse(carTags);
                var tagHtml = '';
                if (carTags) {
                    $.each(carTags, function(key, tag) {
                        tagHtml += '<button class="btn btn-white disabled btn-xs">' + tag + '</button>';
                    });
                }
                $('#carTagDiv').html(tagHtml);
            },
            _setModalKeywordTags : function(keywordTags) {
                keywordTags = JSON.parse(keywordTags);
                var tagHtml = '';
                if (keywordTags) {
                    $.each(keywordTags, function(key, tag) {
                        tagHtml += '<button class="btn btn-gray disabled btn-xs">' + tag + '</button>';
                    });
                }
                $('#keywordTagDiv').html(tagHtml);
            },
            _createCookie : function (e, t, n, r) {
                if (n != "0") {
                    var i = new Date;
                    i.setTime(i.getTime() + n * 24 * 60 * 60 * 1e3);
                    var s = "; expires=" + i.toGMTString()
                } else if (r != "0") {
                    var i = new Date;
                    i.setTime(i.getTime() + 10 * 60 * 60 * 1e3);
                    var s = "; expires=" + i.toGMTString()
                } else
                    var s = "";
                document.cookie = e + "=" + t + s + "; path=/;";
            },
            _getCookie : function (e) {
                var t = e + "=";
                var n = document.cookie.split(";");
                for (var r = 0; r < n.length; r++) {
                    var i = n[r].replace(/ /g, "");
                    if (i.indexOf(t) == 0)
                        return i.substring(t.length, i.length)
                }
                return ""
            },
            getCsvFileByName :  function(urlPrefix) {
                $.ajax({
                    type: 'GET',
                    dataType: 'json',
                    data	:	WF.reports.csvFileName,
                    url: '/'+urlPrefix+'/reports/get-csv-file',
                    success: function(data)
                    {
                        if (data) {
                            if(data.status) {
                                var downloadText='<p style="text-align:center;color:darkblue">'+
                                				 'Your download will begin automatically. If it does not, please '+
                                				 '<a href="' + data.path + '" download="' + data.file + '" style="text-decoration: underline;">click here</a>'+
                                				 ' to download</p>';
                                $('#modalAjax .modal-body').html(downloadText);
                                var anchor = document.createElement('a');
                                anchor.href = data.path;
                                anchor.target = '_blank';
                                anchor.download = data.file;
                                anchor.click();
                                clearInterval(WF.reports.exportStatus);
                            } else {
                                WF.reports.csvFetchTimeoutHandle = setTimeout(WF.reports.getCsvFileByName(urlPrefix),1000);
                            }
                        }
                    }
                });
            },
            downloadCsvFile :   function()
            {
                $.ajax({
                    type: 'GET',
                    dataType: 'json',
                    data	:	WF.reports.csvFileName,
                    url: '/admin/reports/force-csv-download',
                    success: function(data)
                    {
                        if (data) {
                            if(data.status) {

                            } else {
                                console.log("downloadCsvFile");
                            }
                        }
                    }
                });
            }
        }
    })();
})();

