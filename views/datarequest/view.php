<div class="container page">

	<div class="row">
		<div class=col-md-12>
			<div class="panel panel-default">
		                <div class="panel-heading clearfix">
					<h3 class="panel-title pull-left">Data request: <?php echo html_escape($requestId) ?></h3>
					<div class="pull-right">
						<a class="btn btn-default" href="/datarequest/researchproposal/view/<?php echo $proposalId ?>">Back</a>
					</div>
				</div>
				<div class="panel-body">
					<label>Wave(s)</label>
					<p><?php echo html_escape($request['wave'][0]) ?></p>
					<label>Data requested</label>
					<p><?php echo html_escape($request['data']) ?></p>
					<label>Status</label>
					<p><?php echo $requestStatus ?></p>
				</div>
			</div>
		</div>
	</div>
</div>
