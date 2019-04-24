<div class="row metadata-form">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading clearfix">
                <h3 class="panel-title pull-left">
                    Research proposal submission form
                </h3>
                <div class="input-group-sm has-feedback pull-right">
                    <a class="btn btn-default" href="/datarequest">Back</a>
                </div>
            </div>
            <div class="panel-body">
                <div id="form" class="metadata-form"
                    data-path="<?php echo rawurlencode($path); ?>"
                    data-csrf_token_name="<?php echo rawurlencode($tokenName); ?>"
                    data-csrf_token_hash="<?php echo rawurlencode($tokenHash); ?>">
                    <p>Loading form data <i class="fa fa-spinner fa-spin fa-fw"></i></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript"></script>
<script src="/datarequest/static/js/researchproposal/add.js" type="text/javascript"></script>
