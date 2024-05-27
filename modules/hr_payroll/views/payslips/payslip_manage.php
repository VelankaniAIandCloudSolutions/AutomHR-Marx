<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12" id="small-table">
				<div class="panel_s">
					<div class="panel-body">
						<?php echo form_hidden('internal_id',$internal_id); ?>
						<div class="row">
							<div class="col-md-12 ">
								<h4 class="no-margin font-bold"><i class="fa fa-shopping-basket" aria-hidden="true"></i> <?php echo _l($title); ?></h4>
								<hr />
							</div>
						</div>
						<div class="row">    
							<div class="_buttons col-md-3">

								<?php if (has_permission('hrp_payslip', '', 'create') || is_admin()) { ?>
									<a href="#" onclick="new_payslip(); return false;"class="btn btn-info pull-left mright10 display-block">
										<?php echo _l('_new'); ?>
									</a>
								<?php } ?>


							</div>
						</div>

						<br/>
						<?php render_datatable(array(
							_l('id'),
							_l('payslip_name'),
							_l('payslip_template'),
							_l('payslip_month'),
							_l('staff_id_created'),
							_l('date_created'),
							_l('status'),
							_l('options'),
						),'payslip_table'); ?>


					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- add insurance type -->
<div class="modal" id="payslip_template_modal" tabindex="-1" role="dialog">
	<div class="modal-dialog popup-with modal-dialog-with">
		<?php echo form_open_multipart(admin_url('hr_payroll/payslip'), array('id'=>'add_payslip')); ?>

		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

				<h4 class="modal-title">
					<span class="edit-title"><?php echo _l('edit_payslip'); ?></span>
					<span class="add-title"><?php echo _l('new_payslip'); ?></span>
				</h4>

			</div>

			<div class="modal-body">
				<div id="additional_payslip_template"></div>
				<div id="additional_payslip_column"></div>

				<div class="row">
					<div class="col-md-12">
						<div class="col-md-4">
							<?php echo render_input('payslip_month','month',date('Y-m'), 'month'); ?>   
						</div>
						<div class="col-md-8">
							<?php echo render_input('payslip_name','payslip_name','','text'); ?>
						</div>            
					</div>
				</div>


				<div class="row">
					<div class="col-md-12">
						<div class="col-md-12"> 

							<div class="form-group">
								<label for="payslip_template_id" class="control-label"><?php echo _l('payslip_template_id_lable'); ?></label>
								<select name="payslip_template_id" id="payslip_template_id" class="selectpicker"  data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">

								</select>
							</div>
						</div>

					</div>
				</div>

			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
				<button type="button" class="btn btn-info payslip_checked"><?php echo _l('submit'); ?></button>

			</div>
		</div>
		<?php echo form_close(); ?>
	</div>

	<!-- box loading -->
	<div id="box-loading"></div>

</div> 

<?php init_tail(); ?>
<?php require 'modules/hr_payroll/assets/js/payslips/payslip_manage_js.php';?>
</body>
</html>
