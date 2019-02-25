<script>
    var browsePageItems = <?php echo $items; ?>;
    var browseStartDir = '<?php echo rawurlencode($dir); ?>';
    var view = 'browse';
</script>

<script>
    // Added for selection of target for vault package @TODO names to be changed and to be added by controller
//    var revisionItemsPerPage = <?php echo $items; ?>;
    var browseDlgPageItems = <?php echo $items; //$dlgPageItems; ?>;
//    var view = 'revision';
</script>

<!-- <?php echo $searchHtml; ?> -->

<div class="row">
    <a href="datarequest/add" class="btn btn-default pull-right" role="button">Add research proposal</a>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="row">
            <table id="file-browser" class="table yoda-table table-striped">
                <thead>
                    <tr>
			<th>User</th>
                        <th>Name</th>
                        <th>Submission date</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
