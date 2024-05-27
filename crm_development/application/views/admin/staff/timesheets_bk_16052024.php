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
        <?php 
        if (!isset($view_all)) { ?>
        <?php $this->load->view('admin/staff/stats'); ?>
        <?php } ?>

         <!-- start - Time sheet approval  -->
        <div class="row">
            <div class='col-md-12'>
                <label><?php echo _l('timesheet_send_to_reporting_manager');?></label>
                <span id="timesheet_status" class='' ></span>
            </div>
            <div class='col-md-12'>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="col-md-3">
                        <label><?php echo _l('reporting_to');?></label>
                            <select name="reporting_manager_id" data-live-search="true" data-width="100%" id="reporting_manager_id" class="selectpicker" data-width="100%">
                                <option value=''> <?php echo _l('reporting_to');?></option>
                                <?php foreach ($reproting_to as $reproting_to_manager) {
                                ?>
                                <option value="<?php echo $reproting_to_manager['reporting_to_id']; ?>">
                                <?php echo get_staff_full_name($reproting_to_manager['reporting_to_id']); ?>
                                </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label><?php echo _l('timesheet_filling_format');?></label>
                            <select name="timesheet_range" id="timesheet_range" class="selectpicker" disabled data-width="100%" onchange="timesheet_range();">
                                <option value=''><?php echo _l('timesheet_filling_format');?></option>
                                <option value="weekly" <?php if($timesheet_period === 'weekly'){echo"selected";}?> > <?php echo _l("1week");?> </option>
                                <option value="biweekly" <?php if($timesheet_period === 'biweekly'){echo"selected";}?> > <?php echo _l("2week");?> </option>
                                <option value="month" <?php if($timesheet_period === 'month'){echo"selected";}?> > <?php echo _l("1month");?> </option>
                            </select>
                        </div>

                        <div class="col-md-3">
                        <label><?php echo _l('timesheet_start_date');?></label>
                            <?php echo render_date_input('time_sheet_period_from'); ?>
                        </div>
                        <div class="col-md-3">
                        <label><?php echo _l('timesheet_to_date');?></label>
                            <?php echo render_date_input('time_sheet_period_to'); ?>
                        </div>
                        <hr />
                        <div class="clearfix"></div>
                        <div class="col-md-3">
                        <label><?php echo _l('client');?></label>
                            <select id="clientid" name="timesheet_clientid" data-live-search="true" data-width="100%"
                                class="ajax-search" data-empty-title="<?php echo _l('client'); ?>"
                                data-none-selected-text="<?php echo _l('client'); ?>">
                            </select>
                        </div>
                        <div class="col-md-3">
                        <label><?php echo _l('project');?></label>
                            <select data-empty-title="<?php echo _l('project'); ?>" multiple="false"
                                name="timesheet_project_id" id="project_id" class="projects ajax-search"
                                data-live-search="true" data-width="100%">
                            </select>
                        </div>

                         <div class="col-md-3">
                        <label>Contractor ID</label>
                            <input class='form-control' type="text" name="contractor_id" id="contractor_id" placeholder="Enter Contractor ID">
                        </div>

                        <div class="col-md-3">
                        <label>Supplier Name</label>
                            <input class='form-control' type="text" name="supplier_name" id="supplier_name" placeholder="Enter Supplier Name">
                        </div>

                        <hr>
                        <div class="clearfix"></div>

                        <div class="col-md-3">
                        <label>Consultant Name</label>
                            <input class='form-control' type="text" name="consultant_name" id="consultant_name" placeholder="Enter Consultant Name">
                        </div>

                        <?php if (isset($view_all)) { ?>
                        <div class="col-md-3">
                        <label><?php echo _l('all_staff_members');?></label>
                            <select name="timesheet_staff_id" id="timesheet_staff_id" class="selectpicker" data-width="100%">
                                <option value=""><?php echo _l('all_staff_members'); ?></option>
                                <option value="<?php echo get_staff_user_id(); ?>">
                                    <?php echo get_staff_full_name(get_staff_user_id()); ?></option>
                                <?php foreach ($staff_members_with_timesheets as $staff) { ?>
                                <option value="<?php echo $staff['staff_id']; ?>">
                                    <?php echo get_staff_full_name($staff['staff_id']); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <?php } ?>

                        <div class="col-md-3">
                        <label>Line Manager/ Lead Architect/ Approver Name</label>
                            <input class='form-control' type="text" name="line_manager" id="line_manager" placeholder="Enter Line Manager/ Lead Architect/ Approver Name">
                        </div>

                        <div class="col-md-3">
                        <label>Does the day column need to be displayed?</label>
                            <input type="checkbox" name="day_display" id="day_display" title="Does the day column need to be displayed?">
                        </div>

                         <hr>
                        <div class="clearfix"></div>

                        <div class="col-md-2" style='margin-top:20px;'>
                            <a href="#" id="submit_for_approval"
                                class="btn btn-primary pull-left" title="<?php echo _l('send_to_reporting_manager'); ?>"><?php echo _l('send_for_approval'); ?></a>
                        </div>

                        <div class="col-md-1" style='margin-top:20px;'>
                            <a href="#" id="export_timesheet"
                                class="btn btn-primary pull-left" ><?php echo _l('Export'); ?></a>
                        </div>
                        <div class="col-md-1" style='margin-top:20px;'>
                            <a href="#" id="pdf_timesheet"
                                class="btn btn-primary pull-left"><?php echo _l('PDF'); ?></a>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- end - Time sheet approval  -->

        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (staff_can('view-timesheets', 'reports')) { ?>
                        <a href="<?php echo site_url($this->uri->uri_string() . (!isset($view_all) ? '?view=all' : '')); ?>"
                            class="btn btn-primary"><i class="fa-regular fa-clock"></i>
                            <?php
                                echo(isset($view_all) ? _l('my_timesheets') :  _l('view_members_timesheets'));
                            ?>
                        </a>
                        <hr />
                        <?php } ?>
                        <canvas id="timesheetsChart" style="max-height:400px;" width="350" height="350"></canvas>
                        <hr />
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-md-5ths">
                                <div class="select-placeholder">
                                    <select name="range" id="range" class="selectpicker" data-width="100%">
                                        <option value="today" selected><?php echo _l('today'); ?></option>
                                        <option value="this_month">
                                            <?php echo _l('staff_stats_this_month_total_logged_time'); ?></option>
                                        <option value="last_month">
                                            <?php echo _l('staff_stats_last_month_total_logged_time'); ?></option>
                                        <option value="this_week">
                                            <?php echo _l('staff_stats_this_week_total_logged_time'); ?></option>
                                        <option value="last_week">
                                            <?php echo _l('staff_stats_last_week_total_logged_time'); ?></option>
                                        <option value="period"><?php echo _l('period_datepicker'); ?></option>
                                    </select>
                                </div>
                                <div class="row mtop15">
                                    <div class="col-md-12 period hide">
                                        <?php echo render_date_input('period-from'); ?>
                                    </div>
                                    <div class="col-md-12 period hide">
                                        <?php echo render_date_input('period-to'); ?>
                                    </div>
                                </div>
                            </div>
                            <?php if (isset($view_all)) { ?>
                            <div class="col-md-5ths">
                                <div class="select-placeholder">
                                    <select name="staff_id" id="staff_id" class="selectpicker" data-width="100%">
                                        <option value=""><?php echo _l('all_staff_members'); ?></option>
                                        <option value="<?php echo get_staff_user_id(); ?>">
                                            <?php echo get_staff_full_name(get_staff_user_id()); ?></option>
                                        <?php foreach ($staff_members_with_timesheets as $staff) { ?>
                                        <option value="<?php echo $staff['staff_id']; ?>">
                                            <?php echo get_staff_full_name($staff['staff_id']); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <?php } ?>
                            <div class="col-md-5ths">
                                <div class="select-placeholder">
                                    <select id="clientid" name="clientid" data-live-search="true" data-width="100%"
                                        class="ajax-search" data-empty-title="<?php echo _l('client'); ?>"
                                        data-none-selected-text="<?php echo _l('client'); ?>">
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-5ths">
                                <div class="select-placeholder projects-wrapper">
                                    <div id="project_ajax_search_wrapper">
                                        <select data-empty-title="<?php echo _l('project'); ?>" multiple="true"
                                            name="project_id[]" id="project_id" class="projects ajax-search"
                                            data-live-search="true" data-width="100%">
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5ths">
                                <a href="#" id="apply_filters_timesheets"
                                    class="btn btn-primary pull-left"><?php echo _l('apply'); ?></a>
                            </div>
                            <div class="mtop10 hide relative pull-right" id="group_by_tasks_wrapper">
                                <span><?php echo _l('group_by_task'); ?></span>
                                <div class="onoffswitch">
                                    <input type="checkbox" name="group_by_task" class="onoffswitch-checkbox"
                                        id="group_by_task">
                                    <label class="onoffswitch-label" for="group_by_task"></label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <hr class="no-mtop" />
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <table class="table table-timesheets-report">
                            <thead>
                                <tr>
                                    <?php if (isset($view_all)) { ?>
                                    <th><?php echo _l('staff_member'); ?></th>
                                    <?php } ?>
                                    <th><?php echo _l('project_timesheet_task'); ?></th>
                                    <th><?php echo _l('timesheet_tags'); ?></th>
                                    <?php if (get_option('round_off_task_timer_option') == 0) { ?>
                                    <th class="t-start-time"><?php echo _l('project_timesheet_start_time'); ?></th>
                                    <th class="t-end-time"><?php echo _l('project_timesheet_end_time'); ?></th>
                                    <?php } ?>
                                    <th width="150px;"><?php echo _l('note'); ?></th>
                                    <th><?php echo _l('task_relation'); ?></th>
                                    <th><?php echo _l('time_h'); ?></th>
                                    <th><?php echo _l('time_decimal'); ?></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <?php if (isset($view_all)) { ?>
                                    <td></td>
                                    <?php } ?>
                                    <td></td>
                                    <td></td>
                                    <?php if (get_option('round_off_task_timer_option') == 0) { ?>
                                    <td></td>
                                    <td></td>
                                    <?php } ?>
                                    <td></td>
                                    <td></td>
                                    <td class="total_logged_time_timesheets_staff_h"></td>
                                    <td class="total_logged_time_timesheets_staff_d"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script>


<script>
var staff_member_select = $('select[name="staff_id"]');
$(function() {

    init_ajax_projects_search();
    var ctx = document.getElementById("timesheetsChart");
    var chartOptions = {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: '',
                data: [],
                backgroundColor: [],
                borderColor: [],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            tooltips: {
                enabled: true,
                mode: 'single',
                callbacks: {
                    label: function(tooltipItems, data) {
                        return decimalToHM(tooltipItems.yLabel);
                    }
                }
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        min: 0,
                        userCallback: function(label, index, labels) {
                            return decimalToHM(label);
                        },
                    }
                }]
            },
        }
    };

    var timesheetsTable = $('.table-timesheets-report');
    $('#apply_filters_timesheets').on('click', function(e) {
        e.preventDefault();
        timesheetsTable.DataTable().ajax.reload();
    });

    $('body').on('change', '#group_by_task', function() {
        <?php if (get_option('round_off_task_timer_option') == 0) { ?>
        var tApi = timesheetsTable.DataTable();
        var visible = $(this).prop('checked') == false;
        var tEndTimeIndex = $('.t-end-time').index();
        var tStartTimeIndex = $('.t-start-time').index();
        if (tEndTimeIndex == -1 && tStartTimeIndex == -1) {
            tStartTimeIndex = $(this).attr('data-start-time-index');
            tEndTimeIndex = $(this).attr('data-end-time-index');
        } else {
            $(this).attr('data-start-time-index', tStartTimeIndex);
            $(this).attr('data-end-time-index', tEndTimeIndex);
        }
        tApi.column(tEndTimeIndex).visible(visible, false).columns.adjust();
        tApi.column(tStartTimeIndex).visible(visible, false).columns.adjust();
        tApi.ajax.reload();
        <?php } else { ?>
        timesheetsTable.DataTable().ajax.reload();
        <?php } ?>
    });

    var timesheetsChart;
    var Timesheets_ServerParams = {};
    Timesheets_ServerParams['range'] = '[name="range"]';
    Timesheets_ServerParams['period-from'] = '[name="period-from"]';
    Timesheets_ServerParams['period-to'] = '[name="period-to"]';
    Timesheets_ServerParams['staff_id'] = '[name="staff_id"]';
    Timesheets_ServerParams['project_id'] = 'select#project_id';
    Timesheets_ServerParams['clientid'] = 'select#clientid';
    Timesheets_ServerParams['group_by_task'] = '[name="group_by_task"]:checked';
    initDataTable('.table-timesheets-report', window.location.href, undefined, undefined,
        Timesheets_ServerParams, [<?php if (isset($view_all)) {
                                echo 3;
                            } else {
                                echo 2;
                            } ?>, 'desc']);

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

    timesheetsTable.on('init.dt', function() {
        var $dtFilter = $('body').find('.dataTables_filter');
        var $gr = $('#group_by_tasks_wrapper').clone()
        $('#group_by_tasks_wrapper').remove();
        $gr.removeClass('hide');
        $gr.find('span').css('position', 'absolute');
        $gr.find('span').css('top', '1px');
        $gr.find('span').css((isRTL == 'true' ? 'right' : 'left'), '-110px');
        $dtFilter.before($gr, '<div class="clearfix"></div>');
        $dtFilter.addClass('mtop15');
    });

    timesheetsTable.on('draw.dt', function() {
        var TimesheetsTable = $(this).DataTable();
        var logged_time = TimesheetsTable.ajax.json().logged_time;
        var chartResponse = TimesheetsTable.ajax.json().chart;
        var chartType = TimesheetsTable.ajax.json().chart_type;
        $(this).find('tfoot').addClass('bold');
        $(this).find('tfoot td.total_logged_time_timesheets_staff_h').html(
            "<?php echo _l('total_logged_hours_by_staff'); ?>: " + logged_time.total_logged_time_h);
        $(this).find('tfoot td.total_logged_time_timesheets_staff_d').html(
            "<?php echo _l('total_logged_hours_by_staff'); ?>: " + logged_time.total_logged_time_d);
        if (typeof(timesheetsChart) !== 'undefined') {
            timesheetsChart.destroy();
        }
        if (chartType != 'month') {
            chartOptions.data.labels = chartResponse.labels;
        } else {
            chartOptions.data.labels = [];
            for (var i in chartResponse.labels) {
                chartOptions.data.labels.push(moment(chartResponse.labels[i]).format("MMM Do YY"));
            }
        }
        chartOptions.data.datasets[0].data = [];
        chartOptions.data.datasets[0].backgroundColor = [];
        chartOptions.data.datasets[0].borderColor = [];
        for (var i in chartResponse.data) {
            chartOptions.data.datasets[0].data.push(chartResponse.data[i]);
            if (chartResponse.data[i] == 0) {
                chartOptions.data.datasets[0].backgroundColor.push('rgba(167, 167, 167, 0.6)');
                chartOptions.data.datasets[0].borderColor.push('rgba(167, 167, 167, 1)');
            } else {
                chartOptions.data.datasets[0].backgroundColor.push('rgba(132, 197, 41, 0.6)');
                chartOptions.data.datasets[0].borderColor.push('rgba(132, 197, 41, 1)');
            }
        }

        var selected_staff_member = staff_member_select.val();
        var selected_staff_member_name = staff_member_select.find('option:selected').text();
        chartOptions.data.datasets[0].label = $('select[name="range"] option:selected').text() + (
            selected_staff_member != '' && selected_staff_member != undefined ? ' - ' +
            selected_staff_member_name : '');
        setTimeout(function() {
            timesheetsChart = new Chart(ctx, chartOptions);
        }, 30);
        do_timesheets_title();
    });
});

function do_timesheets_title() {
    var _temp;
    var range = $('select[name="range"]');
    var _range_heading = range.find('option:selected').text();
    if (range.val() != 'period') {
        _temp = _range_heading;
    } else {
        _temp = _range_heading + ' (' + $('input[name="period-from"]').val() + ' - ' + $('input[name="period-to"]')
            .val() + ') ';
    }
    $('head title').html(_temp + (staff_member_select.find('option:selected').text() != '' ? ' - ' + staff_member_select
        .find('option:selected').text() : ''));
}


$("#submit_for_approval").on("click", function(){
    var range = $("#timesheet_range").val();
    var period_from = $("#time_sheet_period_from").val();
    var period_to = $("#time_sheet_period_to").val();
    var project_id = $("#project_id").val();
    var clientid = $("#clientid").val();
    var reporting_manager_id = $("#reporting_manager_id").val();
    var timesheet_staff_id = $("#timesheet_staff_id").val();

    $.ajax({
        type: "POST",
        url: "<?php echo base_url('admin/staff/time_sheet_approval');?>",
        data: {timesheet_staff_id:timesheet_staff_id, range:range, period_from:period_from, period_to:period_to, project_id:project_id, clientid:clientid, reporting_manager_id:reporting_manager_id},
        success: function(response) {
            // console.log(response);
            // location.reload();
        }
    });
});


$(document).ready(function(){

    var frequency = $('#timesheet_range').val();
    var timesheet_staff_id = $("#timesheet_staff_id").val();
    var start_date = $("#time_sheet_period_from").val();

    var period_to = $("#time_sheet_period_to").val();
    //var project_id = $("#project_id").val();
    var project_id = $('select[name="timesheet_project_id[]"]').val();
    var clientid = $("#clientid").val();
    var reporting_manager_id = $("#reporting_manager_id").val();

    fequncy_date_calculate(frequency, timesheet_staff_id, start_date);
});

function fequncy_date_calculate(frequency, timesheet_staff_id, start_date)
{
    $.ajax({
        type: "POST",
        url: "<?php echo base_url('admin/staff/fequency_date_calculate');?>", 
        data: {frequency:frequency,timesheet_staff_id:timesheet_staff_id,start_date:start_date},
        success: function(response) {
            var obj = JSON.parse(response);
            $("#time_sheet_period_from").val(obj.start_date);
            $("#time_sheet_period_to").val(obj.end_date);

            timesheet_tracking_status(obj.start_date, obj.end_date);
        }
    });
}

$("#time_sheet_period_from").on('change', function(){
    var frequency = $('#timesheet_range').val();
    var timesheet_staff_id = $("#timesheet_staff_id").val();
    var start_date = $("#time_sheet_period_from").val();
    var period_to = $("#time_sheet_period_to").val();
    var clientid = $("#clientid").val();
    var reporting_manager_id = $("#reporting_manager_id").val();
    var project_id = $('select[name="timesheet_project_id[]"]').val();
    fequncy_date_calculate(frequency, timesheet_staff_id, start_date);
});

function timesheet_tracking_status(start_date, period_to)
{
    var frequency = $('#timesheet_range').val();
    var timesheet_staff_id = $("#timesheet_staff_id").val();
    var clientid = $("#clientid").val();
    var reporting_manager_id = $("#reporting_manager_id").val();
    //var project_id = $('select[name="timesheet_project_id[]"]').val();
    var project_id = $("#project_id").val();

    var contractor_id = $("#contractor_id").val();
    var supplier_name = $("#supplier_name").val();
    var consultant_name = $("#consultant_name").val();


    if(project_id == '')
    {
        project_id = 0;
    }

    $.ajax({
        type: "POST",
        url: "<?php echo base_url('admin/staff/timesheet_tracking_status');?>", 
        data:{frequency:frequency,timesheet_staff_id:timesheet_staff_id,start_date:start_date,period_to:period_to, project_id:project_id,clientid:clientid, reporting_manager_id:reporting_manager_id, contractor_id:contractor_id, supplier_name:supplier_name, consultant_name:consultant_name},
        success: function(response) {
            var obj = JSON.parse(response);
            if(obj.status == 0)
            {
                $('#timesheet_status')
                .removeClass() // Remove existing classes
                .addClass('bg-warning spanTextButton') // Add the new class
                .text('Timesheet Pending for Approval'); // Set the new text 

                $("#submit_for_approval").hide();
            }
            else if(obj.status == 1)
            {
                $('#timesheet_status')
                .removeClass() // Remove existing classes
                .addClass('bg-success spanTextButton') // Add the new class
                .text('Timesheet Approved by Manager'); // Set the new text 
                $("#submit_for_approval").hide();
            }
            else if(obj.status == 3)
            {
                $('#timesheet_status')
                .removeClass() // Remove existing classes
                .addClass('bg-primary spanTextButton') // Add the new class
                .text('Timesheet Not Submitted for Approval');
                // $("#submit_for_approval").hide();
            }
            else if(obj.status == 4)
            {
                $('#timesheet_status')
                .removeClass() // Remove existing classes
                .addClass('bg-warning spanTextButton') // Add the new class
                .text('Timesheet Records Not Available.');
            }
            else{
                $('#timesheet_status')
                .removeClass() // Remove existing classes
                .addClass('bg-danger spanTextButton') // Add the new class
                .text('Timesheet Rejected by Manager'); // Set the new text 
            }
        }
    });
}



$("#export_timesheet").on("click", function(){
    var range = $("#timesheet_range").val();
    var period_from = $("#time_sheet_period_from").val();
    var period_to = $("#time_sheet_period_to").val();
    var project_id = $("#project_id").val();
    var clientid = $("#clientid").val();
    var reporting_manager_id = $("#reporting_manager_id").val();
    var timesheet_staff_id = $("#timesheet_staff_id").val();

    var contractor_id = $("#contractor_id").val();
    var supplier_name = $("#supplier_name").val();
    var consultant_name = $("#consultant_name").val();

    $.ajax({
        type: "POST",
        url: "<?php echo base_url('admin/staff/timesheet_export');?>",
        data: {timesheet_staff_id:timesheet_staff_id, range:range, period_from:period_from, period_to:period_to, project_id:project_id, clientid:clientid, reporting_manager_id:reporting_manager_id , contractor_id:contractor_id, supplier_name:supplier_name, consultant_name:consultant_name },
        success: function(response) {
            console.log(response);
             convertHtmlToExcel(response);

        },
        error: function(xhr, status, error) {
            console.error('AJAX error:', error);
        }
    });
});

$("#pdf_timesheet").on("click", function(){
    var range = $("#timesheet_range").val();
    var period_from = $("#time_sheet_period_from").val();
    var period_to = $("#time_sheet_period_to").val();
    var project_id = $("#project_id").val();
    var clientid = $("#clientid").val();
    var reporting_manager_id = $("#reporting_manager_id").val();
    var timesheet_staff_id = $("#timesheet_staff_id").val();

    var contractor_id = $("#contractor_id").val();
    var supplier_name = $("#supplier_name").val();
    var consultant_name = $("#consultant_name").val();

    $.ajax({
        type: "POST",
        url: "<?php echo base_url('admin/staff/timesheet_export');?>",
        data: {timesheet_staff_id:timesheet_staff_id, range:range, period_from:period_from, period_to:period_to, project_id:project_id, clientid:clientid, reporting_manager_id:reporting_manager_id , contractor_id:contractor_id, supplier_name:supplier_name, consultant_name:consultant_name,pdf:pdf },
        success: function(response) {
            console.log(response);
             convertHtmlToExcel(response);

        },
        error: function(xhr, status, error) {
            console.error('AJAX error:', error);
        }
    });
});



// Function to convert HTML to Excel
function convertHtmlToExcel(htmlContent) {
    // Convert HTML string to a DOM element
    var parser = new DOMParser();
    var doc = parser.parseFromString(htmlContent, 'text/html');
    
    // Select the table element from the DOM
    var table = doc.querySelector('table');
    
    // Convert the table to a workbook
    var workbook = XLSX.utils.table_to_book(table);
    
    // Save the workbook as an Excel file
    XLSX.writeFile(workbook, 'export.xlsx');
}


</script>
</body>

</html>