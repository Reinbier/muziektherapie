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
    
    // used on buttons within the modal-popup, when they have the 'data-reload' attribute assigned, then reload the page.
    $(document).on("click", "button", function() {
        if($(this)[0].hasAttribute("data-reload"))
        {
            location.reload();
        }
    });
    
    // enable functionality of bootstrap tooltips
    $(function(){
        $('[data-toggle=tooltip], .tooltip-toggle').tooltip();
    });

    // draw the graph when the element for the progressChart exist on the page
    if ($('#progressChart').length)
    {
        drawGraph($("#progressChart").data("treatmentid"), $("#progressChart").data("role"));
    }
    
    // assign functionality to the datatables
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
    
    $(document).on("click", ".actionTreatment", function() {
        var treatmentID = $(this).data('treatmentid');
        var action = $(this).data('action');
        var nl_word = (action == "finish" ? "afronden" : "be&euml;ndigen");
        
        displayPopup("Bevestiging nodig", '<p>Weet u zeker dat u deze behandeling wilt ' + nl_word + '?</p>', '<button type="button" class="btn btn-success" onclick="actionTreatment(\''+action+'\',\''+treatmentID+'\')" data-dismiss="modal">Ja, doorgaan</button><button type="button" class="btn btn-default" data-dismiss="modal">Nee, ga terug</button>');
    });

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
                case 'button-createKin':
                    parameters = createKin();
                    break;
                case 'button-createQuestionlist':
                    parameters = createQuestionList();
                    break;
                case 'button-createTreatment':
                    parameters = createTreatment();
                    break;
                case 'button-createMeasurement':
                    parameters = createMeasurement();
                    break;
                case 'button-addKinToTreatment':
                    parameters = addKinToTreatment();
                    break;
                case 'button-fillInQuestionList':
                    parameters = fillInQuestionList();
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
                    displayPopup('Er is iets mis gegaan', '<p>De verbinding met de server is verbroken.. (E501)</p>', '<button type="button" class="btn btn-default" data-dismiss="modal">Sluiten</button>');
                });
            }
        }
    });

});

function actionTreatment(todo, treatmentID)
{
    // process request via AJAX
    var request = $.ajax({
        url: '/include/ajax/therapist.php',
        type: "POST",
        data: {
            action: JSON.stringify("actionTreatment"),
            todo: todo,
            treatmentID: treatmentID
        },
        dataType: "json"
    });
    request.done(function (msg) {
        displayPopup(msg.title, '<p>' + msg.text + '</p>', msg.buttons);
    });
    request.fail(function (jqXHR, textStatus) {
        displayPopup('Er is iets mis gegaan', '<p>De verbinding met de server is verbroken.. (E503)</p>', '<button type="button" class="btn btn-default" data-dismiss="modal">Sluiten</button>');
    });
}

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

function createKin()
{
    if ($("#createKinForm")[0].checkValidity())
    {
        var kinData = getAllInputData("createKinForm");
        var roleName = $("#roleName").text();
        
        return {
            url: '/include/ajax/therapist.php',
            data: {
                action: JSON.stringify('createKin'),
                kinParams: JSON.stringify(kinData),
                roleName: JSON.stringify(roleName)
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

function fillInQuestionList()
{
    if ($("#fillInQuestionListForm")[0].checkValidity())
    {
        var questionListData = getQuestionListFillInData();
        var userID = $("#fillInQuestionListForm").data("userid");

        return {
            url: '/include/ajax/questionlist.php',
            data: {
                action: JSON.stringify('fillInQuestionList'),
                questionListParams: JSON.stringify(questionListData),
                userID: JSON.stringify(userID)
            }
        };
    }
    return null;
}

function createTreatment()
{
    if ($("#createTreatmentForm")[0].checkValidity())
    {
        var name = $("#treatmentName").val();
        var client = $("#selectClient option:selected").val();
        var therapist = $("#selectClient").data("therapist");
        
        return {
            url: '/include/ajax/therapist.php',
            data: {
                action: JSON.stringify('createTreatment'),
                name: JSON.stringify(name),
                client: JSON.stringify(client),
                therapist: JSON.stringify(therapist)
            }
        };
    }
    return null;
}

function createMeasurement()
{
    if ($("#createMeasurementForm")[0].checkValidity())
    {
        var name = $("#measurementName").val();
        var questionlistID = $("#selectQlist option:selected").val();
        var treatmentID = $("#selectQlist").data("treatmentid");
        
        return {
            url: '/include/ajax/therapist.php',
            data: {
                action: JSON.stringify('createMeasurement'),
                name: JSON.stringify(name),
                questionlistID: JSON.stringify(questionlistID),
                treatmentID: JSON.stringify(treatmentID)
            }
        };
    }
    return null;
}

function addKinToTreatment()
{
    if ($("#addKinToTreatmentForm")[0].checkValidity())
    {
        var kinID = $("#selectKin option:selected").val();
        var treatmentID = $("#selectKin").data("treatmentid");

        return {
            url: '/include/ajax/therapist.php',
            data: {
                action: JSON.stringify('addKinToTreatment'),
                kinID: JSON.stringify(kinID),
                treatmentID: JSON.stringify(treatmentID)
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

function drawGraph(treatmentid, role)
{
    var request = $.ajax({
        url: "/include/ajax/" + role + ".php",
        type: "POST",
        data: {action: JSON.stringify("drawGraph"),
                treatmentid: JSON.stringify(treatmentid),
                role: JSON.stringify(role)},
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
                element: "progressChart",
                data: msg.result,
                xkey: 'measurement',
                parseTime: false,
                ykeys: $yKeys,
                labels: $yKeys,
                padding: 75
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
        displayPopup('Er is iets mis gegaan', '<p>De verbinding met de server is verbroken.. (E502)</p>', '<button type="button" class="btn btn-default" data-dismiss="modal">Sluiten</button>');
    });
}