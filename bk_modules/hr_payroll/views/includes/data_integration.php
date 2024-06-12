<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php echo form_open_multipart(admin_url('hr_payroll/data_integration'), array('id'=>'data_integration')); ?>
<div class="row">
	<div class="col-md-12">
		<h4 class="no-margin font-bold h4-color" ><i class="fa fa-chain-broken" aria-hidden="true"></i> <?php echo _l('data_integration')?></h4>
		<hr class="hr-color" >
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="form-group">
			<div class="checkbox checkbox-primary">
				<input  type="checkbox" id="integrated_hrprofile" name="integrated_hrprofile" <?php if(get_hr_payroll_option('integrated_hrprofile') == 1 ){ echo 'checked';} ?> value="integrated_hrprofile" <?php if($hr_profile_active == false){echo ' disabled';} ?>>
				<label for="integrated_hrprofile"><?php echo _l('integrated_hrprofile'); ?>

				<a href="#" class="pull-right display-block input_method"><i class="fa fa-question-circle i_tooltip" data-toggle="tooltip" title="" data-original-title="<?php echo html_entity_decode($hr_profile_title); ?>"></i></a>
			</label>
		</div>
	</div>
</div>
</div>


<div class="row">
	<div class="col-md-12">
		<div class="form-group">
			<div class="checkbox checkbox-primary">
				<input type="checkbox" id="integrated_timesheets" name="integrated_timesheets" <?php if(get_hr_payroll_option('integrated_timesheets') == 1 ){ echo 'checked';} ?> value="integrated_timesheets" <?php if($timesheets_active == false){echo ' disabled';} ?>>
				<label for="integrated_timesheets"><?php echo _l('integrated_timesheets'); ?>

				<a href="#" class="pull-right display-block input_method"><i class="fa fa-question-circle i_tooltip" data-toggle="tooltip" title="" data-original-title="<?php echo html_entity_decode($timesheets_title); ?>"></i></a>
			</label>
		</div>
	</div>
</div>
</div>

<?php 
$attendance_types = hrp_attendance_type();
$actual_workday   = explode(',', get_hr_payroll_option('integration_actual_workday'));
$paid_leave       = explode(',', get_hr_payroll_option('integration_paid_leave'));
$unpaid_leave     = explode(',', get_hr_payroll_option('integration_unpaid_leave'));
?>

	<div class="row">
		<div class="col-md-4">
			<div class="form-group">
				<label for="standard_working_time" class="control-label clearfix"><small class="req text-danger">* </small><?php echo _l('standard_working_time_of_month'); ?><a href="#" class="pull-right display-block input_method"><i class="fa fa-question-circle i_tooltip" data-toggle="tooltip" title="" data-original-title="<?php echo _l('tooltip_standard_working_time'); ?>"></i></a></label>
				<input type="number" min="0" max="1000" id="standard_working_time" name="standard_working_time" class="form-control" value="<?php echo get_hr_payroll_option('standard_working_time'); ?>" required>
			</div>
		</div>
	</div>

<div class="col-md-12 option-show <?php if(get_hr_payroll_option('integrated_timesheets') == 1){ echo '';}else{ echo 'hide';}  ?>">


	<div class="row">
		<div class="col-md-4">
			<div class="form-group select-placeholder ">
				<label for="integration_actual_workday" class="control-label"><small class="req text-danger">* </small><?php echo _l('integration_actual_workday'); ?><a href="#" class="pull-right display-block input_method"><i class="fa fa-question-circle i_tooltip" data-toggle="tooltip" title="" data-original-title="<?php echo _l('tooltip_actual_workday'); ?>"></i></a></label>
				<select name="integration_actual_workday[]" id="integration_actual_workday" multiple="true" class="form-control selectpicker" data-actions-box="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" required>
					<?php foreach ($actual_workday_type as $key => $value) { ?>

						<?php 
						$selected ='';
						if(in_array($key, $actual_workday)){
							$selected .= ' selected';
						}
						?>
						<option value="<?php echo html_entity_decode($key); ?>" <?php echo  html_entity_decode($selected)?>><?php  echo html_entity_decode($value); ?></option>

					<?php } ?>
				</select>
			</div>
		</div>
	</div>


	<div class="row">
		<div class="col-md-4">
			<div class="form-group">
				<label for="integration_paid_leave" class="control-label"><small class="req text-danger">* </small><?php echo _l('integration_paid_leave'); ?><a href="#" class="pull-right display-block input_method"><i class="fa fa-question-circle i_tooltip" data-toggle="tooltip" title="" data-original-title="<?php echo _l('tooltip_paid_leave'); ?>"></i></a></label>
				<select name="integration_paid_leave[]" class="form-control selectpicker" multiple="true" id="integration_paid_leave" data-actions-box="true" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" required> 
					<?php foreach ($paid_leave_type as $key => $value) { ?>

						<?php 
						$selected ='';
						if(in_array($key, $paid_leave)){
							$selected .= ' selected';
						}
						?>
						<option value="<?php echo html_entity_decode($key); ?>" <?php echo html_entity_decode($selected); ?>><?php  echo html_entity_decode($value); ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-4">
			<div class="form-group">
				<label for="integration_unpaid_leave" class="control-label"><small class="req text-danger">* </small><?php echo _l('integration_unpaid_leave'); ?><a href="#" class="pull-right display-block input_method"><i class="fa fa-question-circle i_tooltip" data-toggle="tooltip" title="" data-original-title="<?php echo _l('tooltip_unpaid_leave'); ?>"></i></a></label>
				<select name="integration_unpaid_leave[]" class="form-control selectpicker" multiple="true" id="integration_unpaid_leave" data-actions-box="true" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" required> 
					<?php foreach ($unpaid_leave_type as $key => $value) { ?>

						<?php 
						$selected ='';
						if(in_array($key, $unpaid_leave)){
							$selected .= ' selected';
						}
						?>
						<option value="<?php echo html_entity_decode($key); ?>" <?php echo  $selected?>><?php echo html_entity_decode($value); ?></option>

					<?php } ?>
				</select>
			</div>
		</div>
	</div>
</div>


<div class="row">
	<div class="col-md-12">
		<div class="form-group">
			<div class="checkbox checkbox-primary">
				<input type="checkbox" id="integrated_commissions" name="integrated_commissions" <?php if(get_hr_payroll_option('integrated_commissions') == 1 ){ echo 'checked';} ?> value="integrated_commissions" <?php if($commissions_active == false){echo ' disabled';} ?>>
				<label for="integrated_commissions"><?php echo _l('integrated_commissions'); ?>

				<a href="#" class="pull-right display-block input_method"><i class="fa fa-question-circle i_tooltip" data-toggle="tooltip" title="" data-original-title="<?php echo html_entity_decode($commissions_title); ?>"></i></a>
			</label>
		</div>
	</div>
</div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="modal-footer">
			<?php if(is_admin()){ ?>
				<button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
			<?php } ?>
		</div>
	</div>
</div>
<?php echo form_close(); ?>


<div class="clearfix"></div>

</body>
</html>


