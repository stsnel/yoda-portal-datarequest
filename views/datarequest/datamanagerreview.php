<script>
    var requestId = <?php echo $requestId; ?>;
</script>

<div class="row">
    <div class="col-md-12">
        <div id="datamanagerReview" class="metadata-form"
             data-csrf_token_name="<?php echo rawurlencode($tokenName); ?>"
             data-csrf_token_hash="<?php echo rawurlencode($tokenHash); ?>">
            <p>Loading metadata <i class="fa fa-spinner fa-spin fa-fw"></i></p>
        </div>
        <hr>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card ">
            <div class="card-header clearfix">
                <h5 class="card-header float-left">Board of Directors preliminary review</h5>
            </div>
            <div class="card-body">
                <div id="preliminaryReview" class="metadata-form"
                     data-csrf_token_name="<?php echo rawurlencode($tokenName); ?>"
                     data-csrf_token_hash="<?php echo rawurlencode($tokenHash); ?>">
                    <p>Loading metadata <i class="fa fa-spinner fa-spin fa-fw"></i></p>
                </div>
            </div>
        </div>
        <div class="card ">
            <div class="card-header clearfix">
                <h5 class="card-header float-left">Data request <?php echo html_escape($requestId) ?></h5>
            </div>
            <div class="card-body">
                <div id="datarequest" class="metadata-form"
                     data-csrf_token_name="<?php echo rawurlencode($tokenName); ?>"
                     data-csrf_token_hash="<?php echo rawurlencode($tokenHash); ?>">
                    <p>Loading metadata <i class="fa fa-spinner fa-spin fa-fw"></i></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/datarequest/static/js/datarequest/datamanagerreview.js" type="text/javascript"></script>
