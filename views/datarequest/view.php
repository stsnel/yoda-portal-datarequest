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

<?php if ($requestStatus == "assigned" && $isBoardMember && !$isRequestOwner): ?>
    <a href="/datarequest/datarequest/approve/<?php echo html_escape($requestId) ?>" class="btn btn-default pull-right">Approve request</a>
<?php endif ?>
<button type="button" class="btn btn-default pull-right assign" data-path="">Assign for review</button>
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
