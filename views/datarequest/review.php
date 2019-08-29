<script>
    var requestId = <?php echo $requestId; ?>;
    var view = 'browse';
</script>

<div class="row">
    <div class="col-md-6">
        <div class="row">
            <div class="panel panel-default">
                <div class="panel-heading clearfix">
                    <h3 class="panel-title pull-left">Data request <?php echo html_escape($requestId) ?></h3>
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

    <div class=col-md-6>
        <div class="row">
            <div class="panel panel-default">
                <div class="panel-heading clearfix">
                    <h3 class="panel-title pull-left">
                        Data request review form
                    </h3>
                </div>
                <div class="panel-body">
                        <div id="form" class="metadata-form"
                             data-csrf_token_name="<?php echo rawurlencode($tokenName); ?>"
                             data-csrf_token_hash="<?php echo rawurlencode($tokenHash); ?>">
                            <p>Loading metadata <i class="fa fa-spinner fa-spin fa-fw"></i></p>
                        </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script type="text/javascript">
</script>
<script src="/datarequest/static/js/datarequest/review.js" type="text/javascript"></script>
