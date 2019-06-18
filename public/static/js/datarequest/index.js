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
        startBrowsing(browsePageItems);
    }
});

function buildFileBrowser()
{
    var url = "/datarequest/datarequest/overview_data";

    var fileBrowser = $('#file-browser').DataTable();

    fileBrowser.ajax.url(url).load();

    return true;
}

function startBrowsing(items)
{
    $('#file-browser').DataTable( {
        "bFilter": false,
        "bInfo": false,
        "bLengthChange": false,
        "language": {
            "emptyTable": "No research proposals present"
  },
        "ajax": {
            url: "/datarequest/researchproposal/data",
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
