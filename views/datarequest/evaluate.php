<script>
    var requestId = <?php echo $requestId; ?>;
</script>

<div class="row">
    <div class="col-md-12">
        <div id="evaluation" class="metadata-form"
             data-csrf_token_name="<?php echo rawurlencode($tokenName); ?>"
             data-csrf_token_hash="<?php echo rawurlencode($tokenHash); ?>">
            <p>Loading metadata <i class="fa fa-spinner fa-spin fa-fw"></i></p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card ">
            <div class="card-header clearfix">
                <h3 class="card-header float-left">Board of Directors response to data manager review (if any)</h3>
            </div>
            <div class="card-body">
                <div id="assign" class="metadata-form"
                     data-csrf_token_name="<?php echo rawurlencode($tokenName); ?>"
                     data-csrf_token_hash="<?php echo rawurlencode($tokenHash); ?>">
                    <p>Loading metadata <i class="fa fa-spinner fa-spin fa-fw"></i></p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card ">
            <div class="card-header clearfix">
                <h3 class="card-header float-left">Data manager review</h3>
            </div>
            <div class="card-body">
                <div id="datamanagerReview" class="metadata-form"
                     data-csrf_token_name="<?php echo rawurlencode($tokenName); ?>"
                     data-csrf_token_hash="<?php echo rawurlencode($tokenHash); ?>">
                    <p>Loading metadata <i class="fa fa-spinner fa-spin fa-fw"></i></p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card ">
            <div class="card-header clearfix">
                <h3 class="card-header float-left">Board of Directors preliminary review</h3>
            </div>
            <div class="card-body">
                <div id="preliminaryReview" class="metadata-form"
                     data-csrf_token_name="<?php echo rawurlencode($tokenName); ?>"
                     data-csrf_token_hash="<?php echo rawurlencode($tokenHash); ?>">
                    <p>Loading metadata <i class="fa fa-spinner fa-spin fa-fw"></i></p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="row">
            <div class="card ">
                <div class="card-header clearfix">
                    <h3 class="card-header float-left">Data request <?php echo html_escape($requestId) ?></h3>
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
    <div id="reviews"></div>
</div>

<script src="/datarequest/static/js/datarequest/evaluate.js" type="text/javascript"></script>
