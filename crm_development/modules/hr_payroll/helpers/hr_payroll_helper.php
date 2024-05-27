<?php
defined('BASEPATH') or exit('No direct script access allowed');


/**
 * Check whether column exists in a table
 * Custom function because Codeigniter is caching the tables and this is causing issues in migrations
 * @param  string $column column name to check
 * @param  string $table table name to check
 * @return boolean
 */

/**
 * get hr payroll option
 * @param  [type] $name 
 * @return [type]       
 */
function get_hr_payroll_option($name)
{
	$CI = & get_instance();
	$options = [];
	$val  = '';
	$name = trim($name);
	

	if (!isset($options[$name])) {
		// is not auto loaded
		$CI->db->select('option_val');
		$CI->db->where('option_name', $name);
		$row = $CI->db->get(db_prefix() . 'hr_payroll_option')->row();
		if ($row) {
			$val = $row->option_val;
		}
	} else {
		$val = $options[$name];
	}

	return hooks()->apply_filters('get_hr_payroll_option', $val, $name);
}

/**
 * row hr payroll options exist
 * @param  [type] $name 
 * @return [type]       
 */
function row_hr_payroll_options_exist($name){
	$CI = & get_instance();
	$i = count($CI->db->query('Select * from '.db_prefix().'hr_payroll_option where option_name = '.$name)->result_array());
	if($i == 0){
		return 0;
	}
	if($i > 0){
		return 1;
	}
}

/**
 * hr payroll payroll column exist
 * @param  [type] $name 
 * @return [type]       
 */
function hr_payroll_payroll_column_exist($key){
	$CI = & get_instance();
	$i = count($CI->db->query('Select * from '.db_prefix().'hrp_payroll_columns where function_name = '.$key)->result_array());
	if($i == 0){
		return 0;
	}
	if($i > 0){
		return 1;
	}
}

/**
 * hr profile reformat currency asset
 * @param  string $value 
 * @return string        
 */
function hr_payroll_reformat_currency($value)
{
	$f_dot = str_replace(',','', $value);
	return ((float)$f_dot + 0);
}


/**
 * hr payroll get status modules
 * @param  [type] $module_name 
 * @return [type]              
 */
function hr_payroll_get_status_modules($module_name){
	$CI             = &get_instance();

	$sql = 'select * from '.db_prefix().'modules where module_name = "'.$module_name.'" AND active =1 ';
	$module = $CI->db->query($sql)->row();
	if($module){
		return true;
	}else{
		return false;
	}
}


/**
 * hr payroll alphabeticala
 * @return [type] 
 */
function hr_payroll_alphabeticala()
{
	$alphabetical=[];
	$index =0;
	for ($char = 'A'; $char <= 'Z'; $char++) {
		if($index <= 100){
			$alphabetical[$char] = $char;
			$index++;
		}else{
			break;
		}
	}
	return $alphabetical;
}


/**
 * hr payroll get departments name
 * @param  [type] $staffid 
 * @return [type]          
 */
function hr_payroll_get_departments_name($staffid)
{
	$CI             = &get_instance();
	$str_department='';

	$departments = $CI->hr_payroll_model->get_staff_departments($staffid);
	foreach ($departments as $value) {
		if(strlen($str_department) > 0){
			$str_department .= ', '.$value['name'];
		}else{
			$str_department .= $value['name'];
		}
	}
	return $str_department;

}


/**
 * hrp attendance type
 * @return [type] 
 */
function hrp_attendance_type()
{
	$attendance_types =[];
	$attendance_types['AL'] = _l('p_x_timekeeping');
	$attendance_types['W'] = _l('W_x_timekeeping');
	$attendance_types['U'] = _l('A_x_timekeeping');
	$attendance_types['HO'] = _l('Le_x_timekeeping');
	$attendance_types['E'] = _l('E_x_timekeeping');
	$attendance_types['L'] = _l('L_x_timekeeping');
	$attendance_types['B'] = _l('CT_x_timekeeping');
	$attendance_types['SI'] = _l('OM_x_timekeeping');
	$attendance_types['M'] = _l('TS_x_timekeeping');
	$attendance_types['ME'] = _l('H_x_timekeeping');
	$attendance_types['EB'] = _l('EB_x_timekeeping');
	$attendance_types['UB'] = _l('UB_x_timekeeping');
	$attendance_types['P'] = _l('P_timekeeping');

	return $attendance_types;
}


/**
 * hrp get timesheets status
 * @return [type] 
 */
function hrp_get_timesheets_status()
{
	if(hr_payroll_get_status_modules('timesheets') && get_hr_payroll_option('integrated_timesheets') == 1){
		$rel_type = 'hr_timesheets';
	}else{
		$rel_type = 'none';
	}

	return $rel_type;   
}

/**
 * hrp get hr profile status
 * @return [type] 
 */
function hrp_get_hr_profile_status()
{
	if(hr_payroll_get_status_modules('hr_profile') && (get_hr_payroll_option('integrated_hrprofile') == 1)){
		$rel_type = 'hr_records';
	}else{
		$rel_type = 'none';
	}

	return $rel_type;
}


/**
 * hrp get commission status
 * @return [type]
 */
function hrp_get_commission_status()
{
	if(hr_payroll_get_status_modules('commission') && (get_hr_payroll_option('integrated_commissions') == 1)){
		$rel_type = 'commission';
	}else{
		$rel_type = 'none';
	}

	return $rel_type;
}



	/**
	 * list hr payroll permisstion
	 * @return [type] 
	 */
	function list_hr_payroll_permisstion()
	{
		$hr_payroll_permissions=[];
		$hr_payroll_permissions[]='hrp_employee';
		$hr_payroll_permissions[]='hrp_attendance';
		$hr_payroll_permissions[]='hrp_commission';
		$hr_payroll_permissions[]='hrp_deduction';
		$hr_payroll_permissions[]='hrp_bonus_kpi';
		$hr_payroll_permissions[]='hrp_insurrance';
		$hr_payroll_permissions[]='hrp_payslip';
		$hr_payroll_permissions[]='hrp_payslip_template';
		$hr_payroll_permissions[]='hrp_income_tax';
		$hr_payroll_permissions[]='hrp_report';
		$hr_payroll_permissions[]='hrp_setting';
		
		return $hr_payroll_permissions;
	}


	/**
	 * hr payroll get staff id hr permissions
	 * @return [type] 
	 */
	function hr_payroll_get_staff_id_hr_permissions()
	{
		$CI = & get_instance();
		$array_staff_id = [];
		$index=0;

		$str_permissions ='';
		foreach (list_hr_payroll_permisstion() as $per_key =>  $per_value) {
			if(strlen($str_permissions) > 0){
				$str_permissions .= ",'".$per_value."'";
			}else{
				$str_permissions .= "'".$per_value."'";
			}

		}


		$sql_where = "SELECT distinct staff_id FROM ".db_prefix()."staff_permissions
		where feature IN (".$str_permissions.")
		";
		
		$staffs = $CI->db->query($sql_where)->result_array();

		if(count($staffs)>0){
			foreach ($staffs as $key => $value) {
				$array_staff_id[$index] = $value['staff_id'];
				$index++;
			}
		}
		return $array_staff_id;
	}


	/**
	 * hr payroll get staff id dont permissions
	 * @return [type] 
	 */
	function hr_payroll_get_staff_id_dont_permissions()
	{
		$CI = & get_instance();

		$CI->db->where('admin != ', 1);

		if(count(hr_payroll_get_staff_id_hr_permissions()) > 0){
			$CI->db->where_not_in('staffid', hr_payroll_get_staff_id_hr_permissions());
		}
		return $CI->db->get(db_prefix().'staff')->result_array();
		
	}


	/**
	 * date to column name
	 * @return [type] 
	 */
	function date_to_column_name()
	{
		$date=[];

		$date['01'] = 'day_1';
		$date['02'] = 'day_2';
		$date['03'] = 'day_3';
		$date['04'] = 'day_4';
		$date['05'] = 'day_5';
		$date['06'] = 'day_6';
		$date['07'] = 'day_7';
		$date['08'] = 'day_8';
		$date['09'] = 'day_9';
		$date['10'] = 'day_10';
		$date['11'] = 'day_11';
		$date['12'] = 'day_12';
		$date['13'] = 'day_13';
		$date['14'] = 'day_14';
		$date['15'] = 'day_15';
		$date['16'] = 'day_16';
		$date['17'] = 'day_17';
		$date['18'] = 'day_18';
		$date['19'] = 'day_19';
		$date['20'] = 'day_20';
		$date['21'] = 'day_21';
		$date['22'] = 'day_22';
		$date['23'] = 'day_23';
		$date['24'] = 'day_24';
		$date['25'] = 'day_25';
		$date['26'] = 'day_26';
		$date['27'] = 'day_27';
		$date['28'] = 'day_28';
		$date['29'] = 'day_29';
		$date['30'] = 'day_30';
		$date['31'] = 'day_31';

		return $date;
	}


	/**
	 * payroll system column
	 * @return [type] 
	 */
	function payroll_system_columns()
	{
		$payroll_system_columns = [];

		$payroll_system_columns[] = 'staff_id';
		$payroll_system_columns[] = 'pay_slip_number';
		$payroll_system_columns[] = 'payment_run_date';
		$payroll_system_columns[] = 'employee_number';
		$payroll_system_columns[] = 'employee_name';
		$payroll_system_columns[] = 'dept_name';
		$payroll_system_columns[] = 'standard_workday';
		$payroll_system_columns[] = 'actual_workday';
		$payroll_system_columns[] = 'paid_leave';
		$payroll_system_columns[] = 'unpaid_leave';
		$payroll_system_columns[] = 'gross_pay';
		$payroll_system_columns[] = 'income_tax_paye';
		$payroll_system_columns[] = 'total_deductions';
		$payroll_system_columns[] = 'net_pay';
		$payroll_system_columns[] = 'it_rebate_code';
		$payroll_system_columns[] = 'it_rebate_value';
		$payroll_system_columns[] = 'income_tax_code';
		$payroll_system_columns[] = 'commission_amount';
		$payroll_system_columns[] = 'bonus_kpi';
		$payroll_system_columns[] = 'total_cost';
		$payroll_system_columns[] = 'total_insurance';
		$payroll_system_columns[] = 'salary_of_the_probationary_contract';
		$payroll_system_columns[] = 'salary_of_the_formal_contract';
		$payroll_system_columns[] = 'taxable_salary';
		$payroll_system_columns[] = 'actual_workday_probation';
		$payroll_system_columns[] = 'total_hours_by_tasks';
		$payroll_system_columns[] = 'salary_from_tasks';
		$payroll_system_columns[] = 'bank_name';
		$payroll_system_columns[] = 'account_number';

		return $payroll_system_columns;

	}

	/**
	 * payroll system columns dont format
	 * @return [type] 
	 */
	function payroll_system_columns_dont_format()
	{
		$payroll_system_columns = [];

		$payroll_system_columns[] = 'staff_id';
		$payroll_system_columns[] = 'pay_slip_number';
		$payroll_system_columns[] = 'payment_run_date';
		$payroll_system_columns[] = 'employee_number';
		$payroll_system_columns[] = 'employee_name';
		$payroll_system_columns[] = 'dept_name';
		$payroll_system_columns[] = 'it_rebate_code';
		$payroll_system_columns[] = 'income_tax_code';
		$payroll_system_columns[] = 'account_number';

		return $payroll_system_columns;

	}


	/**
	 * luckysheet header format
	 * @return [type] 
	 */
	function luckysheet_header_format()
	{
		$v=[];
		$v['bg'] = '#fff000'; //	background	background color	#fff000
		$v['bl'] = 1; //	Bold	0 Regular, 1 Bold
		$v['fs'] = 12; //	font size	14
		$v['ht'] = 0; //	horizontaltype	Horizontal alignment	0 center, 1 left, 2 right
		$v['vt'] = 0; //	verticaltype	Vertical alignment	0 middle, 1 up, 2 down

		return $v;
	}


	/**
	 * luckysheet row format
	 * @return [type] 
	 */
	function luckysheet_row_format()
	{
		$v=[];
		$v['bl'] = 0; //	Bold	0 Regular, 1 Bold
		$v['fs'] = 11; //	font size	14
		$v['vt'] = 0; //	verticaltype	Vertical alignment	0 middle, 1 up, 2 down

		return $v;

	}


	/**
	 * hrp file force contents
	 * @param  [type]  $filename 
	 * @param  [type]  $data     
	 * @param  integer $flags    
	 * @return [type]            
	 */
	function hrp_file_force_contents($filename, $data, $flags = 0){
		if(!is_dir(dirname($filename)))
			mkdir(dirname($filename).'/', 0777, TRUE);
		return file_put_contents($filename, $data,$flags);
	}
	
	/**
	 * hrp reformat currency
	 * @param  [type] $value 
	 * @return [type]        
	 */
	function hrp_reformat_currency($value)
	{

		$f_dot = str_replace(',','', $value);

		if(is_numeric($f_dot)){
			return ((float)$f_dot + 0);
		}
		return $value;
	}


	/**
	 * hrp payslip number to anphabe
	 * @return [type] 
	 */
	function hrp_payslip_number_to_anphabe()
	{
		$alphas = $cells = range('A', 'Z');
		foreach($alphas as $alpha) {
			foreach($alphas as $beta) {
				$cells[] = $alpha.$beta;
			}
		}

		return $cells;
	}


	/**
	 * hrp payslip replace string
	 * @param  [type] $file 
	 * @return [type]       
	 */
	function hrp_payslip_replace_string($file)
	{
	   $file = str_replace("&lt;", "<", $file) ;
	   $file = str_replace("&gt;", ">", $file) ;
	   $file = str_replace("&gt", ">", $file) ;
	   $file = str_replace("&nbsp;", " ", $file) ;
	   $file = str_replace("&amp;", "&", $file) ;
	   $file = str_replace("&quot;", '"', $file) ;
	   $file = str_replace(	"&apos;", "'", $file) ;
	   $file = str_replace(	"&apos;", "'", $file) ;

	   $file = str_replace(	"#replace#", ",(", $file) ;
	   $file = str_replace(	" replace#", ",(", $file) ;
	   $file = str_replace(	"#replace2#", ",IF(", $file) ;


	   return $file;
	}


	/**
	 * get payslip template name
	 * @param  [type] $id 
	 * @return [type]     
	 */
	function get_payslip_template_name($id)
	{
		$CI             = &get_instance();
		$payslip_template_name='';

		$CI->db->select('templates_name');
		$CI->db->where('id', $id);
		$payslip_template = $CI->db->get(db_prefix() . 'hrp_payslip_templates')->row();
		if ($payslip_template) {
			$payslip_template_name .= $payslip_template->templates_name;
		}

		return $payslip_template_name;

	}

	/**
	 * get staffid by permission
	 * @return [type] 
	 */
	function get_staffid_by_permission($newquerystring='')
	{
		$str_where='';

		$CI             = &get_instance();

		if(hrp_get_hr_profile_status() == 'hr_records'){
			$CI->load->model('hr_profile/hr_profile_model');
			$staff_ids = $CI->hr_profile_model->get_staff_by_manager();
		}else{
			$staff_ids = [0 => get_staff_user_id()];
		}

		if(count($staff_ids) > 0){
			if(strlen($newquerystring) > 0){
				$str_where .= "staffid IN (".implode(',', $staff_ids).") AND ".$newquerystring;
			}else{
				$str_where .= "staffid IN (".implode(',', $staff_ids).")";
			}

		}else{
			$str_where .= "staffid  IN (0)";
		}

		return $str_where;
	}

	/**
	 * get array staffid by permission
	 * @param  string $newquerystring 
	 * @return [type]                 
	 */
	function get_array_staffid_by_permission()
	{
		$str_where='';

		$CI             = &get_instance();

		if(hrp_get_hr_profile_status() == 'hr_records'){
			$CI->load->model('hr_profile/hr_profile_model');
			$staff_ids = $CI->hr_profile_model->get_staff_by_manager();
		}else{
			$staff_ids = [0 => get_staff_user_id()];
		}

		return $staff_ids;
		
	}

	/**
	 * hrp payslip json data decode
	 * @param  string $json_data 
	 * @return [type]            
	 */
	function hrp_payslip_json_data_decode($json_data='')
	{
		$CI             = &get_instance();

		$probation_salary_list 	= '';
		$probation_allowance_list = '';
		$formal_salary_list 	= '';
		$formal_allowance_list 	= '';

		$earning_salary_list 	= '';
		$earning_allowance_list 	= '';

		$probation_contract_list = '';
		$formal_contract_list 	 = '';

		$integration_hr 		= false;
		$probation_salary 		= 0;
		$probation_allowance 	= 0;

		$formal_salary 			= 0;
		$formal_allowance 		= 0;

		$earnings_list_data=[];

	   if(hrp_get_hr_profile_status() == 'hr_records'){
			$CI->load->model('hr_profile/hr_profile_model');

	    	//get earning list from setting
			$hr_records_earnings_list = $CI->hr_payroll_model->hr_records_get_earnings_list();
			foreach ($hr_records_earnings_list as $key => $value) {
				$name ='';
				
				switch ($value['rel_type']) {
					case 'salary':
						$probationary_code = 'st1_'.$value['rel_id'];
						$formal_code = 'st2_'.$value['rel_id'];
						break;

					case 'allowance':
						$probationary_code = 'al1_'.$value['rel_id'];
						$formal_code  = 'al2_'.$value['rel_id'];
						break;
					
					default:
						# code...
						break;
				}

				if($value['short_name'] != ''){
					$name .= $value['short_name'];
				}elseif($value['description'] != ''){
					$name .= $value['description'];
				}elseif($value['code'] != ''){
					$name .= $value['code'];
				}elseif($value['id'] != ''){
					$name .= $value['id'];
				}

				$earnings_list_data[$probationary_code] = $value;
				$earnings_list_data[$formal_code] = $value;

			}
	    }else{
	    	$earnings_list = $CI->hr_payroll_model->get_earnings_list();

			foreach ($earnings_list as $key => $value) {
				$name ='';

				$array_earnings_list_probationary['earning1_'.$value['id']] = 0;
				$array_earnings_list_formal['earning2_'.$value['id']] = 0;

				$earnings_list_data['earning1_'.$value['id']] = $value;
				$earnings_list_data['earning2_'.$value['id']] = $value;

			}
	    }

		if(strlen($json_data) > 2){
			$salary_allowance_data = json_decode($json_data, true);

			foreach ($salary_allowance_data as $key => $value) {

				if(preg_match('/^st1_/', $key) ){
					$probation_salary += (float)$value;
					$integration_hr = true;

					$_name ='';
					if(isset($earnings_list_data[$key])){
						$_name .= $earnings_list_data[$key]['description'];
					}

					$probation_salary_list .= '<tr class="project-overview">
					<td  width="50%" >'. $_name .'</td>
					<td class="text-left">'. app_format_money($value, '').'</td>
					</tr>';

				}elseif(preg_match('/^al1_/', $key) ){
					$probation_allowance += (float)$value;
					$integration_hr = true;

					$_name ='';
					if(isset($earnings_list_data[$key])){
						$_name .= $earnings_list_data[$key]['description'];
					}

					$probation_allowance_list .= '<tr class="project-overview">
					<td  width="50%" >'.$_name .'</td>
					<td class="text-left">'. app_format_money($value, '').'</td>
					</tr>';
					
				}elseif(preg_match('/^st2_/', $key) ){
					$formal_salary += (float)$value;
					$integration_hr = true;

					$_name ='';
					if(isset($earnings_list_data[$key])){
						$_name .= $earnings_list_data[$key]['description'];
					}

					$formal_salary_list .= '<tr class="project-overview">
					<td  width="50%" >'. $_name .'</td>
					<td class="text-left">'. app_format_money($value, '').'</td>
					</tr>';

				}elseif(preg_match('/^al2_/', $key)){
					$formal_allowance += (float)$value;
					$integration_hr = true;

					$_name ='';
					if(isset($earnings_list_data[$key])){
						$_name .= $earnings_list_data[$key]['description'];
					}

					$formal_allowance_list .= '<tr class="project-overview">
					<td  width="50%" >'. $_name .'</td>
					<td class="text-left">'. app_format_money($value, '').'</td>
					</tr>';

				}elseif(preg_match('/^earning1_/', $key) ){
					$probation_salary += (float)$value;

					$_name ='';
					if(isset($earnings_list_data[$key])){
						$_name .= $earnings_list_data[$key]['description'];
					}

					$earning_salary_list .= '<tr class="project-overview">
					<td  width="50%" >'. $_name .'</td>
					<td class="text-left">'. app_format_money($value, '').'</td>
					</tr>';

				}elseif(preg_match('/^earning2_/', $key) ){
					$formal_salary += (float)$value;

					$_name ='';
					if(isset($earnings_list_data[$key])){
						$_name .= $earnings_list_data[$key]['description'];
					}

					$earning_allowance_list .= '<tr class="project-overview">
					<td  width="50%" >'. $_name .'</td>
					<td class="text-left">'. app_format_money($value, '').'</td>
					</tr>';

				}

			}

		}

		if($integration_hr){

			$probation_contract_list .= '<tr class="project-overview">
												<td  width="50%" ><b>'._l('hrp_salary').'</b></td>
												<td  width="50%" ></td>
											</tr>'.$probation_salary_list;

			$probation_contract_list .= '<tr class="project-overview">
												<td  width="50%" ><b>'._l('hrp_allowance').'</b></td>
												<td  width="50%" ></td>
											</tr>'.$probation_allowance_list;


			$formal_contract_list .= '<tr class="project-overview">
												<td  width="50%" ><b>'._l('hrp_salary').'</b></td>
												<td  width="50%" ></td>
											</tr>'.$formal_salary_list;

			$formal_contract_list .= '<tr class="project-overview">
												<td  width="50%" ><b>'._l('hrp_allowance').'</b></td>
												<td  width="50%" ></td>
											</tr>'.$formal_allowance_list;
											

		}else{

			$probation_contract_list .= '<tr class="project-overview">
												<td  width="50%" ><b>'._l('hrp_salary').' + '._l('hrp_allowance').'</b></td>
												<td  width="50%" ></td>
											</tr>'.$earning_salary_list;

											
			$formal_contract_list .= '<tr class="project-overview">
												<td  width="50%" ><b>'._l('hrp_salary').' + '._l('hrp_allowance').'</b></td>
												<td  width="50%" ></td>
											</tr>'.$earning_allowance_list;

		}

		$data=[];
		$data['integration_hr'] = $integration_hr;
		$data['probation_salary'] = $probation_salary;
		$data['probation_allowance'] = $probation_allowance;
		$data['formal_salary'] = $formal_salary;
		$data['formal_allowance'] = $formal_allowance;

		$data['probation_contract_list'] = $probation_contract_list;
		$data['formal_contract_list'] = $formal_contract_list;

		return $data;

	}

	if (!function_exists('cal_to_jd')) {
		// define('CAL_GREGORIAN', 0);
		function cal_to_jd($calendar, $m, $d, $y) {
			// return unixtojd( mktime(0, 0, 0, $m, $d, $y));
			// This is unusable. Julian Day start at noon, not midnight
			// 86400 is the number of seconds in a day;
			// 2440587.5 is the julian day at 1/1/1970 0:00 UTC.
			return round(mktime(12, 0, 0, $m, $d, $y) / 86400 + 2440587.5);
		}
	}

	if (!function_exists('jddayofweek')) {
		function jddayofweek($julianday, $mode) {
			// $dow = (1 + $julianday) % 7; // returns 0 for Sundays.
			// $dow = ($julianday % 7) + 1; // returns 7 for Sundays.
			if($mode == 0){
				return (1 + $julianday) % 7;
			}else{
				return ($julianday % 7) + 1;
			}
			
		}
	}

	if (!function_exists('cal_days_in_month')) {
		define('CAL_GREGORIAN', 0);
		function cal_days_in_month($calendar, $month, $year) {
			return date('t', mktime(0, 0, 0, $month, 1, $year));
		}
	}