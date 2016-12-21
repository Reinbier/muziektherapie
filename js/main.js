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

    $(document).on('click', 'button[type="submit"]', function (e) {

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
    var therapistData = {
        Name: $("#createTherapistForm").find("#input-Naam").val(),
        Address: $("#createTherapistForm").find("#input-Adres").val(),
        Place: $("#createTherapistForm").find("#input-Woonplaats").val(),
        Phone: $("#createTherapistForm").find("#input-Telefoon").val(),
        Gender: $("#createTherapistForm").find("input[name=radio-Geslacht]:checked").val()
    };
    
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