 <?php
 $table_data = array(

 	_l('id'),
 	_l('ps_pay_slip_number'),
 	_l('month'),
 	_l('hrp_probation_contract'),
 	_l('hrp_formal_contract'),
 	_l('ps_gross_pay'),
 	_l('ps_total_deductions'),
 	_l('ps_income_tax_paye'),
 	_l('ps_it_rebate_value'),
 	_l('ps_commission_amount'),
 	_l('ps_bonus_kpi'),
 	_l('ps_total_insurance'), 
 	_l('ps_net_pay'), 
 	_l('ps_total_cost'), 

 );
 
 render_datatable($table_data,'staff_payslip');
 ?>
 <div id="contract_modal_wrapper"></div>
