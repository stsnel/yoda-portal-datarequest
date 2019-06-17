<script>
    var requestId = <?php echo $requestId; ?>;
</script>

<div class="modal" id="assignForReview">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <h3>Assign data request for review by a DMC member</h3>
                <div class="form-group">
                    <label for="dmc-members-list">Select DMC member(s) that should review this data request:</label>
                    <select multiple class="form-control" id="dmc-members-list">
                    </select>
                </div>
                <div class="help"></div><br />
                <div class="advice"></div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-default grey cancel" data-dismiss="modal">Close</button>
                <button class="btn btn-default grey submit-assignment" data-dismiss="modal">Submit</button>
            </div>
        </div>
    </div>
</div>

<?php if ($requestStatus == "assigned" && $isBoardMember && !$isRequestOwner): ?>
    <a href="/datarequest/datarequest/approve/<?php echo html_escape($requestId) ?>" class="btn btn-default pull-right">Approve request</a>
<?php endif ?>
<button type="button" class="btn btn-default pull-right assign" data-path="">Assign for review</button>
<div class="row">
    <div class=col-md-12>
        <div class="panel panel-default">
            <div class="panel-heading clearfix">
                <h3 class="panel-title pull-left">Data request: <?php echo html_escape($requestId) ?></h3>
                <div class="pull-right">
                    <a class="btn btn-default" href="datarequest">Back</a>
                </div>
            </div>
            <div class="panel-body">
                <h3>Requested data</h3>
                <label>Wave(s)</label>
                <p><?php echo html_escape($request['datarequest']['wave'][0]) ?></p>
                <label>Data requested</label>
                <p><?php echo html_escape($request['datarequest']['data']) ?></p>
                <label>Status</label>
                <p><?php echo $requestStatus ?></p>

                <h3>Research proposal</h3>
                <label>Title</label>
                <p><?php echo html_escape($request['general']['title']) ?></p>
                <label>Background of the project</label>
                <p><?php echo nl2br(html_escape($request['research_proposal']['background'])) ?></p>
                <label>Research question</label>
                <p><?php echo html_escape($request['research_proposal']['research_question']) ?></p>
                <label>Methods</label>
                <p><?php echo nl2br(html_escape($request['research_proposal']['methods'])) ?></p>
                <label>Design of the study</label>
                <p><?php echo nl2br(html_escape($request['research_proposal']['design'])) ?></p>
                <label>Study population and sample-size</label>
                <p><?php echo nl2br(html_escape($request['research_proposal']['population'])) ?></p>
                <label>Data processing and preparation</label>
                <p><?php echo nl2br(html_escape($request['research_proposal']['processing'])) ?></p>
                <label>Handling missing data</label>
                <p><?php echo nl2br(html_escape($request['research_proposal']['missing_data'])) ?></p>
                <label>Data analysis methods</label>
                <p><?php echo nl2br(html_escape($request['research_proposal']['analysis_methods'])) ?></p>
                <label>Planned subgroup analyses</label>
                <p><?php echo nl2br(html_escape($request['research_proposal']['subgroup_analyses'])) ?></p>
                <label>Planned sensitivity analyses</label>
                <p><?php echo nl2br(html_escape($request['research_proposal']['sensitivity_analyses'])) ?></p>
                <label>Timeline and milestones</label>
                <p><?php echo nl2br(html_escape($request['research_proposal']['timeline'])) ?></p>
                <label>Output</label>
                <p><?php echo nl2br(html_escape($request['research_proposal']['output'])) ?></p>
                <label>Proposed authors</label>
                <p><?php echo nl2br(html_escape($request['research_proposal']['proposed_authors'])) ?></p>

                <h3>Contact person for the proposed study</h3>
                <label>Name</label>
                <p><?php echo html_escape($request['contact']['name']) ?></p>
                <label>Institution</label>
                <p><?php echo html_escape($request['contact']['institution']) ?></p>
                <label>Department</label>
                <p><?php echo html_escape($request['contact']['department']) ?></p>
                <label>Work address</label>
                <p><?php echo html_escape($request['contact']['work_address']) ?></p>
                <label>Email</label>
                <p><?php echo html_escape($request['contact']['email']) ?></p>
                <label>Phone</label>
                <p><?php echo html_escape($request['contact']['phone']) ?></p>
                <label>Home address</label>
                <p><?php echo html_escape($request['contact']['home_address']) ?></p>

                <h3>Contributions to YOUth data collection</h3>
                <label>Contribution(s)</label>
                <ul>
                <?php if ($request['contribution']['contribution_time'] == 'Yes') { echo '<li>Time: ' . $request['contribution']['contribution_time_type'] . ', ' . $request['contribution']['contribution_time_amount'] . ' hours</li>'; } ?>
                <?php if ($request['contribution']['contribution_time'] == 'Yes') { echo '<li>Money: ' . $request['contribution']['contribution_financial_amount'] . ' euros</li>'; } ?>
                <?php if ($request['contribution']['contribution_favor'] == 'Yes') { echo '<li>Return favor: ' . nl2br(html_escape($request['contribution']['contribution_favor_description'])) . '</li>'; } ?>
                </ul>
            </div>
        </div>
    </div>
</div>
