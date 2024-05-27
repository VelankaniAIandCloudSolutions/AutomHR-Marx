<style>
    .spanTextButton
        {
            text-decoration: none;
            padding: 2px 6px 2px 6px;
            border-top: 1px solid #CCCCCC;
            border-right: 1px solid #333333;
            border-bottom: 1px solid #333333;
            border-left: 1px solid #CCCCCC;
            border-radius: 8px;
        }
</style>


<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class='col-md-12'>
                <!-- <div class="panel_s">
                    <div class="panel-body">
                        <div class="col-md-3">
                        <label><?php echo _l('filter');?></label>
                            <select name="status" data-width="100%" id="status" class="selectpicker">
                                <option value=''> <?php echo _l('all');?></option>
                                <option value='0'> <?php echo _l('pending');?></option>
                                <option value='2'> <?php echo _l('rejected');?></option>
                                <option value='1'> <?php echo _l('approved');?></option>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                        <label><?php echo _l('timesheet_start_date');?></label>
                            <?php echo render_date_input('startdate'); ?>
                        </div>
                        <div class="col-md-3">
                            <label><?php echo _l('timesheet_to_date');?></label>
                            <?php echo render_date_input('enddate'); ?>
                        </div>
                        <div class="col-md-3">
                        <label><?php echo _l('client');?></label>
                            <select id="clientid" name="timesheet_clientid" data-live-search="true" data-width="100%"
                                class="ajax-search" data-empty-title="<?php echo _l('client'); ?>"
                                data-none-selected-text="<?php echo _l('client'); ?>">
                            </select>
                        </div>
                        <hr />
                        <div class="clearfix"></div>

                        <div class="col-md-3">
                            <label><?php echo _l('project');?></label>
                            <div id="project_ajax_search_wrapper">
                                <select data-empty-title="<?php echo _l('project'); ?>" multiple="true"
                                    name="project_id[]" id="project_id" class="projects ajax-search"
                                    data-live-search="true" data-width="100%">
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                        <label><?php echo _l('all_staff_members');?></label>
                            <select name="timesheet_staff_id" id="timesheet_staff_id" class="selectpicker" data-width="100%">
                                <option value=""><?php echo _l('all_staff_members'); ?></option>
                                <?php foreach ($stafflist as $staff) { ?>
                                <option value="<?php echo $staff['staffid']; ?>">
                                    <?php echo get_staff_full_name($staff['staffid']); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        
                        <div class="col-md-3" style='margin-top:20px;'>
                            <a href="#" id="filter"
                                class="btn btn-primary pull-left" title="<?php echo _l('filter'); ?>"><?php echo _l('filter'); ?></a>
                        </div>

                    </div>
                </div> -->
                
                <div class="row">
                    <div class='col-md-12'>
                        <div class="panel_s">
                            <div class="panel-body">
                                <div class="clearfix"></div>
                                    <div class="panel-table-full">
                                    <?php
                                        $table_data = array(
                                        _l('sno'),
                                        'Created At',
                                        _l('staff'),
                                        'From Date',
                                        'To Date',
                                        _l('reporting_to'),
                                        _l('project'),
                                        _l('client'),
                                        "Reporting Manager Status",
                                        'Repoting Manager Comment',
                                        'Final Approval Status',
                                        'Final Comment', // Final Comment
                                        "Timesheet Download",
                                        'Action'
                                        );
                                        render_datatable($table_data,'table_timesheet_approval',
                                        // array('customizable-table'),
                                        array(
                                        'id'=>'table-table_timesheet_approval',
                                        'data-last-order-identifier'=>'table_timesheet_approval',
                                        'data-default-order'=>get_table_last_order('table_timesheet_approval'),
                                        )); 
                                    ?>

                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- end - Time sheet approval  -->
    </div>

    
<!-- Approve Comment Modal -->
<div id="myModalApprove" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <form name="approval_comments" id="approval_comments" method="post" action="<?php base_url('admin/reports/timesheet_approve')?>">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Comment for Approve</h4>
          </div>
          <div class="modal-body">
            <input type="hidden" id="rowIdField" name="rowIdField">
            <input type="hidden" id="typeField" name="typeField">
            <input type="hidden" id="approval_level" name="approval_level">

            <label>Commet/ Feedback</label>
            <textarea name="approve_comments" id="approve_comments" class="form-control"></textarea>
          </div>
          <div class="modal-footer">
            <input type="button" onclick="approve_comment(); return false;" name="Save" id="approve_btn" class="btn btn-primary" value="Save">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
    </form>
  </div>
</div>


<!-- Reject Modal -->
<div id="myModalReject" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <form name="approval_comments" id="approval_comments" method="post" >
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Comment for Reject</h4>
          </div>
          <div class="modal-body">
            <input type="hidden" id="rowIdFieldReject" name="rowIdField">
            <input type="hidden" id="typeFieldReject" name="typeField">
            <input type="hidden" id="approval_level_reject" name="approval_level_reject">

            <label>Commet/ Feedback</label>
            <textarea name="reject_comments" id="reject_comments" class="form-control"></textarea>
          </div>
          <div class="modal-footer">
            <input type="button" onclick="reject_comment(); return false;" name="Save" id="reject_btn" value="Save" class="btn btn-primary">
            <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
          </div>
        </div>
    </form>
  </div>
</div>

</div>
<?php init_tail(); ?>
<script>
var staff_member_select = $('select[name="staff_id"]');
$(function() {

    init_ajax_projects_search();

    var timesheetsTable = $('.table-timesheets-report');
    $('#apply_filters_timesheets').on('click', function(e) {
        e.preventDefault();
        timesheetsTable.DataTable().ajax.reload();
    });

    init_ajax_project_search_by_customer_id();

    $('#clientid').on('change', function() {
        var projectAjax = $('select#project_id');
        var clonedProjectsAjaxSearchSelect = projectAjax.html('').clone();
        var projectsWrapper = $('.projects-wrapper');
        projectAjax.selectpicker('destroy').remove();
        projectAjax = clonedProjectsAjaxSearchSelect;
        $('#project_ajax_search_wrapper').append(clonedProjectsAjaxSearchSelect);
        init_ajax_project_search_by_customer_id();
    });
});

//  data table

$(function(){
    var ContractsServerParams = '';
    var status = $("#status").val();
    ContractsServerParams = {status:"1"};
    var table_contract = $('.table-table_timesheet_approval');
    initDataTable(table_contract, admin_url+'reports/timesheet_approval_list_data', [0], [0], ContractsServerParams, [1, 'desc']);
});



$('#myModalApprove').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var rowId = button.data('rowid');
    var type = button.data('type');
    var approval_level = button.data('approval_level');
    var comment = $("#approve_comments").val();
    // Set input field values
    $('#rowIdField').val(rowId);
    $('#typeField').val(type);
     $('#approval_level').val(approval_level);
});

function approve_comment()
{
    var comment = $("#approve_comments").val();
    var rowId = $("#rowIdField").val();
    var type = $("#typeField").val();
    var approval_level = $("#approval_level").val();
    if(approval_level == 2)
    {
        var action_url = "<?php echo base_url('admin/reports/timesheet_approve');?>";
    }
    else{
         var action_url = "<?php echo base_url('admin/reports/teamlead_timesheet_approve');?>";
    }
    $.ajax({
        type: "POST",
        url: action_url, 
        data:{id:rowId,type:type,comment:comment },
        success: function(response) {
            location.reload();
        }
    });
}

$('#myModalReject').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var rowId = button.data('rowid');
    var type = button.data('type');
    var comment = $("#reject_comments").val();

    var approval_level = button.data('approval_level');
    // Set input field values
    $('#reject_comments').prop('required', true);
    $('#rowIdFieldReject').val(rowId);
    $('#typeFieldReject').val(type);
    $('#approval_level_reject').val(approval_level);
});

function reject_comment()
{

    var comment = $("#reject_comments").val();
    var rowId = $("#rowIdFieldReject").val();
    var type = $("#typeFieldReject").val();
    var approval_level = $("#approval_level_reject").val();

    if(approval_level == 2)
    {
        var action_url = "<?php echo base_url('admin/reports/timesheet_reject');?>";
    }
    else{
         var action_url = "<?php echo base_url('admin/reports/teamlead_timesheet_reject');?>";
    }

    if ($('#reject_comments').prop('required') && $('#reject_comments').val() === '') {
            event.preventDefault(); // Prevent form submission
            alert('Please fill in the required field.');
            return false;
        }
    
    $.ajax({
        type: "POST",
        url: action_url,
        data:{id:rowId,type:type,comment:comment },
        success: function(response) {
            location.reload();
        }
    });
}

</script>

</body>

</html>