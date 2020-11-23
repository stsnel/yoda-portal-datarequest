<script>
    <?php if (isset($previousRequestId)): ?>
        var previousRequestId = <?php echo $previousRequestId; ?>;
    <?php endif ?>
</script>

<div class="row metadata-form">
    <div class="col-md-12">
        <div class="card ">
            <div class="card-header clearfix">
                <h3 class="card-header float-left">
                    Data request submission form
                </h3>
                <div class="input-group-sm has-feedback float-right">
                    <a class="btn btn-secondary" href="/datarequest">Back</a>
                </div>
            </div>
            <div class="card-body">
                <div id="form" class="metadata-form"
                     data-csrf_token_name="<?php echo rawurlencode($tokenName); ?>"
                     data-csrf_token_hash="<?php echo rawurlencode($tokenHash); ?>">
                    <p>Loading metadata <i class="fa fa-spinner fa-spin fa-fw"></i></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/datarequest/static/js/datarequest/add.js" type="text/javascript"></script>
