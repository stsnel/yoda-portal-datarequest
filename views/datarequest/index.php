<script>
    var browsePageItems = <?php echo $items; ?>;
</script>

<div class="row">
    <a href="/datarequest/datarequest/add" class="btn btn-default pull-right" role="button">Add data request</a>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="row">
            <table id="file-browser" class="table yoda-table table-striped">
                <thead>
                    <tr>
			<th>User</th>
                        <th>Request ID</th>
                        <th>Research proposal</th>
                        <th>Submission date</th>
			<th>Status</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
