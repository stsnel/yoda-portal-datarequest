$(document).ajaxSend(function(e, request, settings) {
    // Append a CSRF token to all AJAX POST requests.
    if (settings.type === 'POST' && settings.data.length) {
         settings.data
             += '&' + encodeURIComponent(YodaPortal.csrf.tokenName)
              + '=' + encodeURIComponent(YodaPortal.csrf.tokenValue);
    }
});

$(document).ready(function() {
    // Render and show the modal for assigning a data request to one or
    // more DMC members
    $("body").on("click", "button.assign", function() {
        // Get list of DMC members
        $.getJSON("/datarequest/datarequest/dmcmembers", function (data) {
            if (data.length > 0) {
                // Wipe the selection in case the user previously made a
                // selection
                $("#dmc-members-list").html("");

                // Construct the multiselect list of options (i.e. DMC members)
                for (member in data) {
                    $("#dmc-members-list").append(new Option(data[member]));
                }
            } else {
                setMessage("error", "No DMC members configured.");
            }
        });

        // Show the modal
        $("#assignForReview").modal("show");
    });

    // Assign a data request to one or more DMC members
    $("body").on("click", "button.submit-assignment", function(data) {
        // Get selected assignees
        assignees = $("#dmc-members-list").val();

        // Submit assignees to controller (which will call the appropriate
        // iRODS rule)
        $.post("/datarequest/datarequest/assignRequest",
               {"data": assignees, "requestId": requestId},
               function (data) {
        });
    });
});

