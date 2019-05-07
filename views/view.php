<script>
    var browsePageItems = <?php echo $items; ?>;
    var proposalId = <?php echo $rpid; ?>;
    var view = 'browse';
</script>

<div class="row">
	<a href="/datarequest/datarequest/add?proposalId=<?php echo html_escape($rpid) ?>" class="btn btn-default pull-right" role="button">Add data request</a>
</div>

<div class="container page">

	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
			<div class="panel-heading clearfix">
				<h3 class="panel-title pull-left">Data requests</h3>
			</div>
			<div class="panel-body">
				<table id="file-browser" class="table yoda-table table-striped">
					<thead>
						<tr>
							<th>User</th>
							<th>Name</th>
							<th>Submission date</th>
							<th>Status</th>
						</tr>
					</thead>
				</table>
			</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class=col-md-12>
			<div class="panel panel-default">
		                <div class="panel-heading clearfix">
					<h3 class="panel-title pull-left">Research proposal: <?php echo html_escape($proposal['general']['title']) ?></h3>
					<div class="pull-right">
						<a class="btn btn-default" href="/datarequest">Back</a>
					</div>
				</div>
				<div class="panel-body">
					<label>Title</label>
					<p><?php echo html_escape($proposal['general']['title']) ?></p>
					<label>Status</label>
					<p><?php echo $proposalStatus ?></p>
					<?php if ($proposalStatus == "submitted" && $isBoardMember): ?>
						<a href="/datarequest/researchproposal/approve/<?php echo html_escape($rpid) ?>" class="btn btn-info">Approve proposal</a>
					<?php endif ?>
					<h2>Contact person for the proposed study</h2>
					<label>Name</label>
					<p><?php echo html_escape($proposal['contact']['name']) ?></p>
					<label>Institution</label>
					<p><?php echo html_escape($proposal['contact']['institution']) ?></p>
					<label>Department</label>
					<p><?php echo html_escape($proposal['contact']['department']) ?></p>
					<label>Work address</label>
					<p><?php echo html_escape($proposal['contact']['work_address']) ?></p>
					<label>Email</label>
					<p><?php echo html_escape($proposal['contact']['email']) ?></p>
					<label>Phone</label>
					<p><?php echo html_escape($proposal['contact']['phone']) ?></p>
					<label>Home address</label>
					<p><?php echo html_escape($proposal['contact']['home_address']) ?></p>

					<h2>Data request</h2>
					<label>Background of the project</label>
					<p><?php echo nl2br(html_escape($proposal['data_request']['background'])) ?></p>
					<label>Research question</label>
					<p><?php echo html_escape($proposal['data_request']['research_question']) ?></p>
					<label>Methods</label>
					<p><?php echo nl2br(html_escape($proposal['data_request']['methods'])) ?></p>
					<label>Design of the study</label>
					<p><?php echo nl2br(html_escape($proposal['data_request']['design'])) ?></p>
					<label>Study population and sample-size</label>
					<p><?php echo nl2br(html_escape($proposal['data_request']['population'])) ?></p>
					<label>Data processing and preparation</label>
					<p><?php echo nl2br(html_escape($proposal['data_request']['processing'])) ?></p>
					<label>Handling missing data</label>
					<p><?php echo nl2br(html_escape($proposal['data_request']['missing_data'])) ?></p>
					<label>Data analysis methods</label>
					<p><?php echo nl2br(html_escape($proposal['data_request']['analysis_methods'])) ?></p>
					<label>Planned subgroup analyses</label>
					<p><?php echo nl2br(html_escape($proposal['data_request']['subgroup_analyses'])) ?></p>
					<label>Planned sensitivity analyses</label>
					<p><?php echo nl2br(html_escape($proposal['data_request']['sensitivity_analyses'])) ?></p>
					<label>Timeline and milestones</label>
					<p><?php echo nl2br(html_escape($proposal['data_request']['timeline'])) ?></p>
					<label>Output</label>
					<p><?php echo nl2br(html_escape($proposal['data_request']['output'])) ?></p>
					<label>Proposed authors</label>
					<p><?php echo nl2br(html_escape($proposal['data_request']['proposed_authors'])) ?></p>

					<h2>Contributions to YOUth data collection</h2>
					<label>Contribution(s)</label>
					<ul>
					<?php if ($proposal['contribution']['contribution_time'] == 'Yes') { echo '<li>Time: ' . $proposal['contribution']['contribution_time_type'] . ', ' . $proposal['contribution']['contribution_time_amount'] . ' hours</li>'; } ?>
					<?php if ($proposal['contribution']['contribution_time'] == 'Yes') { echo '<li>Money: ' . $proposal['contribution']['contribution_financial_amount'] . ' euros</li>'; } ?>
					<?php if ($proposal['contribution']['contribution_favor'] == 'Yes') { echo '<li>Return favor: ' . nl2br(html_escape($proposal['contribution']['contribution_favor_description'])) . '</li>'; } ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
