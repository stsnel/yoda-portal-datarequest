$(document).ajaxSend(function(e, request, settings) {
    // Append a CSRF token to all AJAX POST requests.
    if (settings.type === 'POST' && settings.data.length) {
         settings.data
             += '&' + encodeURIComponent(YodaPortal.csrf.tokenName)
              + '=' + encodeURIComponent(YodaPortal.csrf.tokenValue);
    }
});

$( document ).ready(function() {
    if ($('#file-browser').length) {
        startBrowsing(browsePageItems, proposalId);
    }

    // Render and show the modal for assigning a research proposal to one or
    // more DMC members
    $("body").on("click", "button.assign", function() {
        // Get list of DMC members
        $.getJSON("/datarequest/researchproposal/dmcmembers", function (data) {
            if (data.length > 0) {
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

    // Assign a research proposal to one or more DMC members
    $("body").on("click", "button.submit-assignment", function(data) {
        // Get selected assignees
        assignees = $("#dmc-members-list").val();

        // Submit assignees to controller (which will call the appropriate
        // iRODS rule)
        $.post("/datarequest/researchproposal/assignProposal",
               {"data": assignees, "researchProposalId": proposalId},
               function (data) {
                   // Wipe the selection after successful assignment (in case
                   // the user wants to use the modal again right away, e.g.
                   // when the assignees were incorrect)
                   $("#dmc-members-list").html("");
        });
    });
});

function buildFileBrowser()
{
    var url = "/datarequest/datarequest/overview/" + proposalId;

    var fileBrowser = $('#file-browser').DataTable();

    fileBrowser.ajax.url(url).load();

    return true;
}

function startBrowsing(items, proposalId)
{
    $('#file-browser').DataTable( {
        "bFilter": false,
        "bInfo": false,
        "bLengthChange": false,
        "language": {
            "emptyTable": "No research proposals present"
  },
        "ajax": {
            url: "/datarequest/datarequest/overview" + proposalId,
            error: function (xhr, error, thrown) {
                $("#file-browser_processing").hide()
                setMessage('error', 'Something went wrong. Please try again or refresh page.');
                return true;
            },
            dataSrc: function (json) {
                jsonString = JSON.stringify(json);

                resp = JSON.parse(jsonString);

                if (resp.status == 'Success' ) {
                    return resp.data;
                }
                else {
                    setMessage('error', resp.statusInfo);
                    return true;
                }
            }
        },
        "ordering": false,
        "processing": true,
        "serverSide": true,
        "iDeferLoading": 0,
        "pageLength": items,
        "drawCallback": function(settings) {
        }
    });

    buildFileBrowser();
}
