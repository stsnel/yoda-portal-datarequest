<script>
    var requestId = <?php echo $requestId; ?>;
</script>

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

<?php if ($requestStatus == "DTA_SIGNED" && $isDatamanager): ?>
    <a href="/datarequest/download_signed_dta/<?php echo html_escape($requestId) ?>" class="btn btn-default pull-right">Download signed DTA</a>
    <a href="/datarequest/data_ready/<?php echo html_escape($requestId) ?>" class="btn btn-default pull-right" role="button">Data ready</a>
<?php endif ?>
<?php if ($requestStatus == "DTA_READY" && $isRequestOwner): ?>
    <a href="/datarequest/download_dta/<?php echo html_escape($requestId) ?>" class="btn btn-default pull-right">Download DTA</a>
    <button type="button" class="btn btn-default pull-right upload_signed_dta" data-path="">Upload signed DTA</button>
<?php endif ?>
<?php if ($requestStatus == "APPROVED" && $isDatamanager): ?>
    <button type="button" class="btn btn-default pull-right upload_dta" data-path="">Upload DTA</button>
<?php endif ?>
<?php if ($requestStatus == "REVIEWED" && $isBoardMember): ?>
    <a href="/datarequest/evaluate/<?php echo html_escape($requestId) ?>" class="btn btn-default pull-right" role="button">Evaluate data request</a>
<?php endif ?>
<?php if ($requestStatus == "UNDER_REVIEW" && $isReviewer): ?>
    <a href="/datarequest/review/<?php echo html_escape($requestId) ?>" class="btn btn-default pull-right" role="button">Review data request</a>
<?php endif ?>
<?php if (in_array($requestStatus, array("DATAMANAGER_ACCEPT", "DATAMANAGER_RESUBMIT", "DATAMANAGER_REJECT")) && $isBoardMember): ?>
    <a href="/datarequest/assign/<?php echo html_escape($requestId) ?>" class="btn btn-default pull-right" role="button">Assign</a>
<?php endif ?>
<?php if ($requestStatus == "PRELIMINARY_ACCEPT" && $isDatamanager): ?>
    <a href="/datarequest/datamanagerreview/<?php echo html_escape($requestId) ?>" class="btn btn-default pull-right" role="button">Data manager review</a>
<?php endif ?>
<?php if ($requestStatus == "SUBMITTED" && $isBoardMember): ?>
    <a href="/datarequest/preliminaryreview/<?php echo html_escape($requestId) ?>" class="btn btn-default pull-right" role="button">Preliminary review</a>
<?php endif ?>
<div class="row">
    <div class=col-md-12>
        <div class="panel panel-default">
            <div class="panel-heading clearfix">
                <h3 class="panel-title pull-left">Data request: <?php echo html_escape($requestId) ?></h3>
                <div class="pull-right">
                    <a class="btn btn-default" href="/datarequest">Back</a>
                </div>
            </div>
            <div class="panel-body">
                <div class="row bs-wizard" style="border-bottom:0;">
                    <div class="col-xs-3 bs-wizard-step disabled" id="step-0">
                      <div class="text-center bs-wizard-stepnum">1. Submission</div>
                      <div class="progress"><div class="progress-bar"></div></div>
                      <a href="#" class="bs-wizard-dot"></a>
                    </div>

                    <div class="col-xs-3 bs-wizard-step disabled" id="step-1">
                      <div class="text-center bs-wizard-stepnum">2. Under review</div>
                      <div class="progress"><div class="progress-bar"></div></div>
                      <a href="#" class="bs-wizard-dot"></a>
                    </div>

                    <div class="col-xs-3 bs-wizard-step disabled" id="step-2">
                      <div class="text-center bs-wizard-stepnum">3. Reviewed</div>
                      <div class="progress"><div class="progress-bar"></div></div>
                      <a href="#" class="bs-wizard-dot"></a>
                    </div>

                    <div class="col-xs-3 bs-wizard-step disabled" id="step-3">
                      <div class="text-center bs-wizard-stepnum">4. Approved</div>
                      <div class="progress"><div class="progress-bar"></div></div>
                      <a href="#" class="bs-wizard-dot"></a>
                    </div>
                </div>
                <div class="row bs-wizard col-md-offset-2" style="border-bottom:0;">
                    <div class="col-xs-3 bs-wizard-step disabled" id="step-4">
                      <div class="text-center bs-wizard-stepnum">5. DTA ready</div>
                      <div class="progress"><div class="progress-bar"></div></div>
                      <a href="#" class="bs-wizard-dot"></a>
                    </div>

                    <div class="col-xs-3 bs-wizard-step disabled" id="step-5">
                      <div class="text-center bs-wizard-stepnum">6. DTA signed</div>
                      <div class="progress"><div class="progress-bar"></div></div>
                      <a href="#" class="bs-wizard-dot"></a>
                    </div>

                    <div class="col-xs-3 bs-wizard-step disabled" id="step-6">
                      <div class="text-center bs-wizard-stepnum">7. Data ready</div>
                      <div class="progress"><div class="progress-bar"></div></div>
                      <a href="#" class="bs-wizard-dot"></a>
                    </div>
                </div>

                <?php if (in_array($requestStatus, array("PRELIMINARY_REJECT", "PRELIMINARY_RESUBMIT", "REJECTED_AFTER_DATAMANAGER_REVIEW", "RESUBMIT_AFTER_DATAMANAGER_REVIEW", "REJECTED", "RESUBMIT"))): ?>
                    <div class="rejected"><h3>Proposal rejected</h3></div>
                <?php endif ?>

                <hr>

                    <div id="datarequest" class="metadata-form"
                         data-csrf_token_name="<?php echo rawurlencode($tokenName); ?>"
                         data-csrf_token_hash="<?php echo rawurlencode($tokenHash); ?>">
                        <p>Loading metadata <i class="fa fa-spinner fa-spin fa-fw"></i></p>
                    </div>

            </div>
        </div>
    </div>
</div>
