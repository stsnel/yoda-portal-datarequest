<div class="container page">
	<div class="row">
		<div class=col-md-12>
			<div class="panel panel-default">
		                <div class="panel-heading clearfix">
					<h3 class="panel-title pull-left">Research proposal: <?php echo html_escape($proposal['title']) ?></h3>
					<div class="pull-right">
						<a class="btn btn-default" href="/datarequest">Back</a>
					</div>
				</div>
				<div class="panel-body">
					<label>Title</label>
					<p><?php echo html_escape($proposal['title']) ?></p>
					<label>Proposal</label>
					<p><?php echo html_escape($proposal['body']) ?></p>
					<label>Status</label>
					<p><?php echo $proposalStatus ?></p>
					<?php if ($proposalStatus == "submitted" && $isBoardMember): ?>
						<a href="/datarequest/approve/<?php echo $rpid ?>" class="btn btn-info">Approve proposal</a>
					<?php endif ?>
				</div>
			</div>
		</div>
	</div>
</div>
