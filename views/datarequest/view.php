<script>
    var requestId = <?php echo $requestId; ?>;
</script>

<div class="modal" id="assignForReview">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <h3>Assign data request for review by a DMC member</h3>
                <div class="form-group">
                    <label for="dmc-members-list">Select DMC member(s) that should review this data request:</label>
                    <select multiple class="form-control" id="dmc-members-list">
                    </select>
                </div>
                <div class="help"></div><br />
                <div class="advice"></div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-default grey cancel" data-dismiss="modal">Close</button>
                <button class="btn btn-default grey submit-assignment" data-dismiss="modal">Submit</button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="uploadDTA">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <h3>Upload a DTA (to be signed by the researcher).</h3>
                <div class="form-group">
                    <form id="dta" enctype="multipart/form-data">
                        <label for="file">Select a document to upload:</label><br />
                        <input type="file" name="file" id="file" />
                    </form>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-default grey cancel" data-dismiss="modal">Close</button>
                <button class="btn btn-default grey submit_dta" data-dismiss="modal">Upload</button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="uploadSignedDTA">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <h3>Upload the signed DTA.</h3>
                <div class="form-group">
                    <form id="signed_dta" enctype="multipart/form-data">
                        <label for="file">Select a document to upload:</label><br />
                        <input type="file" name="file" id="file" />
                    </form>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-default grey cancel" data-dismiss="modal">Close</button>
                <button class="btn btn-default grey submit_signed_dta" data-dismiss="modal">Upload</button>
            </div>
        </div>
    </div>
</div>

<?php if ($requestStatus == "dta_signed" && $isDatamanager): ?>
    <a href="/datarequest/download_signed_dta/<?php echo html_escape($requestId) ?>" class="btn btn-default pull-right">Download signed DTA</a>
<?php endif ?>
<?php if ($requestStatus == "dta_ready" && $isRequestOwner): ?>
    <a href="/datarequest/download_dta/<?php echo html_escape($requestId) ?>" class="btn btn-default pull-right">Download DTA</a>
    <button type="button" class="btn btn-default pull-right upload_signed_dta" data-path="">Upload signed DTA</button>
<?php endif ?>
<?php if ($requestStatus == "approved" && $isDatamanager): ?>
    <button type="button" class="btn btn-default pull-right upload_dta" data-path="">Upload DTA</button>
<?php endif ?>
<?php if ($requestStatus == "reviewed" && $isBoardMember): ?>
    <a href="/datarequest/evaluate/<?php echo html_escape($requestId) ?>" class="btn btn-default pull-right" role="button">Evaluate data request</a>
<?php endif ?>
<?php if ($requestStatus == "assigned" && $isReviewer): ?>
    <a href="/datarequest/review/<?php echo html_escape($requestId) ?>" class="btn btn-default pull-right" role="button">Review data request</a>
<?php endif ?>
    <a href="/datarequest/datarequest/approve/<?php echo html_escape($requestId) ?>" class="btn btn-default pull-right">Approve request</a>
<?php if ($requestStatus == "submitted" && $isDatamanager): ?>
    <button type="button" class="btn btn-default pull-right assign" data-path="">Assign for review</button>
<?php endif ?>
<div class="row">
    <div class=col-md-12>
        <div class="panel panel-default">
            <div class="panel-heading clearfix">
                <h3 class="panel-title pull-left">Data request: <?php echo html_escape($requestId) ?></h3>
                <div class="pull-right">
                    <a class="btn btn-default" href="datarequest">Back</a>
                </div>
            </div>
            <div class="panel-body">


                    <div id="datarequest" class="metadata-form"
                         data-csrf_token_name="<?php echo rawurlencode($tokenName); ?>"
                         data-csrf_token_hash="<?php echo rawurlencode($tokenHash); ?>">
                        <p>Loading metadata <i class="fa fa-spinner fa-spin fa-fw"></i></p>
                    </div>

            </div>
        </div>
    </div>
</div>
