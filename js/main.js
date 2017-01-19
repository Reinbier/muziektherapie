/* 
 * @file    main.js 
 * @author  Reinier Gombert
 * @date    14-12-2016
 * 
 * Description:
 * 
 * This file handles most javascript in the website.
 * Here the forms will be send, and popups displayed.
 */


$(document).ready(function () {

    // assign datetimepicker functionality to the date-forminputs
    $('.datetimepicker').datetimepicker({
        locale: 'nl',
        format: 'DD-MM-YYYY',
        allowInputToggle: true
    });

    if ($('#progressChart').length)
    {
        drawGraph(0);
    }
    else if ($('#progressChartOverview').length)
    {
        drawGraph($("#progressChartOverview").data("userid"));
    }
    
    if ($(".dataTable").length)
    {
        $(".dataTable").DataTable({
             "sPaginationType": "simple_numbers",
             "oLanguage": {
                 "sLengthMenu": "_MENU_ Resultaten/pagina",
                 "sZeroRecords": "Geen resultaten gevonden",
                 "sInfo": "_START_ - _END_ van de _TOTAL_ resultaten",
                 "sInfoEmpty": "0 resultaten",
                 "sInfoFiltered": "(gefilterd uit _MAX_ resultaten)",
                 "sSearch": "Zoeken:",
                 "oPaginate": {
                     "sFirst": "Eerste",
                     "sPrevious": "&larr;",
                     "sNext": "&rarr;",
                     "sLast": "Laatste"
                 }
             },
             "iDisplayLength": 10,
             "order": [[0, "desc"]]
         });
     }

    // catch every button-click
    $(document).on('click', 'button[type="submit"]', function (e) {

        // do stuff only for ajax buttons
        if (!$(this).hasClass('nonajax'))
        {
            // create initial array with vars
            var parameters = null;

            switch ($(this).attr('id'))
            {
                case 'button-createTherapist':
                    parameters = createTherapist();
                    break;
                case 'button-createClient':
                    parameters = createClient();
                    break;
                case 'button-createQuestionlist':
                    parameters = createQuestionList();
                    break;
            }

            if (parameters !== null)
            {
                // process request via AJAX
                var request = $.ajax({
                    url: parameters.url,
                    type: "POST",
                    data: parameters.data,
                    dataType: "json"
                });
                request.done(function (msg) {
                    displayPopup(msg.title, '<p>' + msg.text + '</p>', msg.buttons);
                    $(".btnReset").trigger("click");
                });
                request.fail(function (jqXHR, textStatus) {
                    displayPopup('Er is iets mis gegaan', '<p>De verbinding met de server is verbroken.. (E501)</p>' + textStatus, '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>');
                });
            }
        }
    });

});


function createTherapist()
{
    if ($("#createTherapistForm")[0].checkValidity())
    {
        var therapistData = getAllInputData("createTherapistForm");

        return {
            url: '/include/ajax/therapist.php',
            data: {
                action: JSON.stringify('createTherapist'),
                therapistParams: JSON.stringify(therapistData)
            }
        };
    }
    return null;
}

function createClient()
{
    if ($("#createClientForm")[0].checkValidity())
    {
        var clientData = getAllInputData("createClientForm");

        return {
            url: '/include/ajax/therapist.php',
            data: {
                action: JSON.stringify('createClient'),
                clientParams: JSON.stringify(clientData)
            }
        };
    }
    return null;
}

function createQuestionList()
{
    if ($("#createQuestionListForm")[0].checkValidity())
    {
        var questionListData = getQuestionListCreateData();

        return {
            url: '/include/ajax/questionlist.php',
            data: {
                action: JSON.stringify('createQuestionList'),
                questionListParams: JSON.stringify(questionListData)
            }
        };
    }
    return null;
}

function displayPopup(title, body, footer)
{
    // change the values of the title, body and footer
    $('.modal-title').html(title);
    $('.modal-body').html(body);
    $('.modal-footer').html(footer);
    // show the modal
    $('.modal').modal('toggle');
}

function getAllInputData(formID)
{
    // create an empty erray for input parameters
    var inputParams = {};
    // for each input or textarea within the form, get the name of the column (corresponding with the name in the table)
    // and save its value
    $("#" + formID + " input,textarea").each(function () {

        // get column from data attribute
        var column = $(this).data("column");

        // for radio buttons, only get the checked value of course
        if ($(this).is("input:radio"))
        {
            var name = $(this).attr("name")
            inputParams[column] = $("input[name=" + name + "]:checked").val();
        } 
        else // text or textarea get values
        {
            if(!($(this).val() == "")) // check for empty, because not every field is mandatory
            {
                inputParams[column] = $(this).val();
            }
        }
    });
    // return the array of parameters
    return inputParams;
}

function drawGraph(userid)
{
    var chart = "progressChart";
    var script = "client";
    if(userid > 0)
    {
        chart += "Overview";
        script = "therapist";
    }
    
    var request = $.ajax({
        url: "/include/ajax/" + script + ".php",
        type: "POST",
        data: {action: JSON.stringify("drawGraph"),
                userid: JSON.stringify(userid)},
        dataType: "json"
    });
    request.done(function (msg) {
        if(msg.status == "ok")
        {
            // first determine the set of keys
            var params = msg.result[0];
            
            var $yKeys = [];
            $.each(params, function(i, v){
                if(i != "measurement")
                {
                    $yKeys.push(i);
                }
            });
            
            new Morris.Line({
                element: chart,
                data: msg.result,
                xkey: 'measurement',
                parseTime: false,
                ykeys: $yKeys,
                labels: $yKeys,
                padding: 50
            });
        }
        else
        {
            $('#progressChart').html(
                '<div class="alert alert-info">' +
                    msg.message +
                '</div>'
            );
        }
    });
    request.fail(function (jqXHR, textStatus) {
        displayPopup('Er is iets mis gegaan', '<p>De verbinding met de server is verbroken.. (E502)</p>' + textStatus, '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>');
    });
}