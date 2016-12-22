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

    if ($('#progressChart').length)
    {
        drawGraph();
    }
    
    

    // catch every button-click
    $(document).on('click', 'button[type="submit"]', function (e) {

        // do stuff only for ajax buttons
        if (!$(this).hasClass('nonajax'))
        {
            // prevent default 'submit-action'
            e.preventDefault();

            // create initial array with vars
            var parameters = {url: null, data: null};

            switch ($(this).attr('id'))
            {
                case 'button-createTherapist':
                parameters = createTherapist();
                break;
                case '':
                break;
            }

            if (parameters.url !== null)
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
    var therapistData = getAllInputData("createTherapistForm");

    return {
        url: '/include/ajax/therapist.php',
        data: {
            action: JSON.stringify('create'),
            therapistParams: JSON.stringify(therapistData)
        }
    };
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
        if($(this).is("input:radio"))
        {
            var name = $(this).attr("name")
            inputParams[column] = $("input[name=" + name +"]:checked").val();
        }
        else // text or textarea get values
        {
            inputParams[column] = $(this).val();
        }
    });
    // return the array of parameters
    return inputParams;
}

/*function drawGraph()
{
    new Morris.Area({
        element: 'progressChart',
        data: [
            {measurement: '2008', points: 20},
            {measurement: '2009', points: 10},
            {measurement: '2010', points: 5},
            {measurement: '2011', points: 5},
            {measurement: '2012', points: 20},
            {measurement: '2013', points: 5},
            {measurement: '2014', points: 5},
            {measurement: '2015', points: 20},
        ],


        xkey: 'measurement',
        ykeys: ['points'],
        labels: ['punten']
    });
}
*/
function drawGraph()
{
 var request = $.ajax({
    url: "/include/ajax/client.php",
    type: "POST",
    data: {action: JSON.stringify("drawGraph")},
    dataType: "json",

});
 request.done(function (msg) {
    new Morris.Line({
        element: 'progressChart',
        data: msg,
        xkey: 'measurement',
        parseTime: false,
        ykeys: ['points'],
        labels: ['score'],
        padding: 50,
    });
});
 request.fail(function (jqXHR, textStatus) {
    displayPopup('Er is iets mis gegaan', '<p>De verbinding met de server is verbroken.. (E501)</p>' + textStatus, '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>');
});
}