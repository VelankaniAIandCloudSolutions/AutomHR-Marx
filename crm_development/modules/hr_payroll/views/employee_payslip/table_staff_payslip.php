<?php

defined('BASEPATH') or exit('No direct script access allowed');

$base_currency = get_base_currency();

$aColumns = [
	db_prefix().'hrp_payslip_details.month',
	'pay_slip_number',
	'gross_pay',
	'total_deductions',
	'income_tax_paye',
	'it_rebate_value',
	'commission_amount',
	'bonus_kpi',
	'total_insurance',
	'net_pay',
	'total_cost',
];

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'hrp_payslip_details';

$join = [
	'LEFT JOIN ' . db_prefix() . 'hrp_payslips ON ' . db_prefix() . 'hrp_payslip_details.payslip_id = ' . db_prefix() . 'hrp_payslips.id',
];

$where  = [];
$filter = [];


if($this->ci->input->post('memberid')){
	$where_staff = '';
	$staffs = $this->ci->input->post('memberid');
	if($staffs != '')
	{
		if($where_staff == ''){
			$where_staff .= ' where '.db_prefix().'hrp_payslip_details.staff_id = "'.$staffs. '"';
		}else{
			$where_staff .= ' or '.db_prefix().'hrp_payslip_details.staff_id = "' .$staffs.'"';
		}
	}
	if($where_staff != '')
	{
		array_push($where, $where_staff);
	}
}
array_push($where, 'AND '.db_prefix().'hrp_payslips.payslip_status = "payslip_closing"');


// Fix for big queries. Some hosting have max_join_limit

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix().'hrp_payslip_details.id', db_prefix().'hrp_payslip_details.json_data', db_prefix().'hrp_payslip_details.actual_workday_probation']);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
	$row = [];
	$row[] = $aRow['id'];

	if (has_permission('hrm_contract', '', 'view') || is_admin()) {
		$subjectOutput = '<a href="#" onclick="member_view_payslip(' . $aRow['id'] . ');return false;">' . $aRow['pay_slip_number'] . '</a>';
	}else{
		$subjectOutput = $aRow['pay_slip_number'];
	}

	$subjectOutput .= '<div class="row-options">';
		$subjectOutput .= '<a href="#" onclick="member_view_payslip(' . $aRow['id'] . ');return false;">' . _l('hr_view') .' </a>';
		$subjectOutput .= '| <a href="'.admin_url('hr_payroll/employee_export_pdf/'.$aRow['id'].'?output_type=I').'" target="_blank">' . _l('view_pdf_in_new_window') .' </a>';
	$subjectOutput .= '</div>';


	$row[] = $subjectOutput;

	$row[] = date('m-Y',strtotime($aRow[db_prefix().'hrp_payslip_details.month']));

	$hrp_payslip_salary_allowance = hrp_payslip_json_data_decode($aRow['json_data']);


	if( $hrp_payslip_salary_allowance['integration_hr']){
		//probation contract
		$probation_salary ='';
		$probation_salary .= _l('hrp_salary').': '.app_format_money($hrp_payslip_salary_allowance['probation_salary'], '').'<br>';
		$probation_salary .= _l('hrp_allowance').': '.app_format_money($hrp_payslip_salary_allowance['probation_allowance'], '');

		$row[] = $probation_salary;

		//formal contract
		$formal_salary ='';
		$formal_salary .= _l('hrp_salary').': '.app_format_money($hrp_payslip_salary_allowance['formal_salary'], '').'<br>';
		$formal_salary .= _l('hrp_allowance').': '.app_format_money($hrp_payslip_salary_allowance['formal_allowance'], '');

		$row[] = $formal_salary;

	}else{

		$probation_salary ='';
		$probation_salary .= _l('hrp_salary').' + '._l('hrp_allowance').': '.app_format_money($hrp_payslip_salary_allowance['probation_salary'], '').'<br>';

		$row[] = $probation_salary;

		//formal contract
		$formal_salary ='';
		$formal_salary .= _l('hrp_salary').' + '._l('hrp_allowance').': '.app_format_money($hrp_payslip_salary_allowance['formal_salary'], '').'<br>';

		$row[] = $formal_salary;

	}


	$row[] = app_format_money($aRow['gross_pay'], '');
	$row[] = app_format_money($aRow['total_deductions'], '');
	$row[] = app_format_money($aRow['income_tax_paye'], '');
	$row[] = app_format_money($aRow['it_rebate_value'],'');
	$row[] = app_format_money($aRow['commission_amount'], '');
	$row[] = app_format_money($aRow['bonus_kpi'], '');
	$row[] = app_format_money($aRow['total_insurance'], '');
	$row[] = app_format_money($aRow['net_pay'], '');
	$row[] = app_format_money($aRow['total_cost'], '');

	$row['DT_RowClass'] = 'has-row-options';
	
	$output['aaData'][] = $row;
}
