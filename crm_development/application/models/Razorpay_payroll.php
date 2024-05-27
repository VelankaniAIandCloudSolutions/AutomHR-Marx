        <?php 

        defined('BASEPATH') or exit('No direct script access allowed');

        class Razorpay_payroll extends CI_Model
        {
            private $api;
            private $api_key;
            private $api_id;

            public function __construct()
            {
                parent::__construct();
                $this->api = RAZORPAY_PAYROLL_API;

                $this->api_key = RAZORPAY_API_KEY;
                $this->api_id = RAZORPAY_API_SECRET;

                $this->load->database();


            }

        /*  
        Module Name     : RazorpayXpayroll API History 
        Description     : add history
        Author          : Ankit Agrawal
        Date            : 12/04/2024     
        */
        public function razorpay_history_insert($response)
        {   
            if(!empty($response))
            {
                $data = array();

                $data = array(
                    'module'        => $response['module'],
                    'action'        => $response['action'],
                    'api_url'       => $response['api_url'],
                    'request_data'  => $response['request_data'],
                    'response_data' => $response['response_data'],
                    'staffid'       => $response['staffid'],
                    'date_added'    => $response['date_added'],
                    'added_by'      => $response['added_by'],
                    'status'        => $response['status']
                );

                $table = db_prefix() . "razorpay_payroll_history";
                $this->db->insert($table, $data);
                $last_insert_id = $this->db->insert_id();


                if($last_insert_id > 0)
                {
                    $this->db->where("staffid",$data['staffid']);
                    $this->db->update(db_prefix()."staff", array("razorpay_payroll_history_id" => $last_insert_id));     
                } 
            }
        }

        /*  
        Module Name     : Curl Init funciton 
        Description     : Curl Init funciton 
        Author          : Ankit Agrawal
        Date            : 12/04/2024   
        */
        public function curl_operation($json_data, $api = '')
        {
            $curl = curl_init();
            if($api !="")
            {
                $this->api = $api;
            }

            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->api,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>$json_data,
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            return $response;
        }

        /*  
        Module Name     : Get Employee All infomation
        Description     : employee or staff information fetch from database staff table.
        Author          : Ankit Agrawal
        Date            : 15/04/2024   
        */

        public function staff_information($staff_id = '')
        {
            $output = array();
            $output['status'] = false;
            if($staff_id != "")
            {
                $this->db->select("staff.*, hr_job_position.position_name as job_position, departments.name as department_name");
                $this->db->from(db_prefix()."staff as staff");
                $this->db->join(db_prefix()."hr_job_position as hr_job_position","hr_job_position.position_id = staff.job_position","left");
                $this->db->join(db_prefix()."staff_departments as staff_departments", "staff_departments.staffid = staff.staffid", "left");
                $this->db->join(db_prefix()."departments as departments", "departments.departmentid = staff_departments.departmentid", "left");

                $this->db->where("staff.staffid", $staff_id);
                $query = $this->db->get(); 
                
                $resposne = $query->row_array();


                //  Custom fields data

                $this->db->select("customfields.*, customfieldsvalues.value as value");
                $this->db->from(db_prefix()."customfields as customfields");
                $this->db->join(db_prefix()."customfieldsvalues as customfieldsvalues", "customfieldsvalues.fieldid = customfields.id","inner");
                $this->db->where("customfieldsvalues.relid", $staff_id);
                $this->db->where("customfields.fieldto","staff");
                $query = $this->db->get(); 
                
                $custom_resposne = array();

                $custom_resposne = $query->result_array();
                if(!empty($custom_resposne))
                {
                    $resposne['customfields_data'] = $custom_resposne;
                }
                else{
                    $resposne['customfields_data'] = array();
                }
                $output['status'] = true;
                $output['resposne'] = $resposne;
            }
            else{
                $output['resposne'] = '';
            }
            return $output;
        }

        /*  
        Module Name     : Employee Dismiss 
        Description     : Employee Dismiss on Razorpay Payroll system
        Author          : Ankit Agrawal
        Date            : 15/04/2024  
        */

        public function dismiss_employee($user_id)
        {

            $staff_information = $this->staff_information($user_id);

            $req_data = array();

            if( $staff_information['status'] == '1')
            {
                $req_data = $staff_information['resposne'];
            }

            if(empty($req_data))
            {
                return false;
            }
            $auth = array();
            $auth = array(
                "id"    =>  $this->api_id,
                "key"   =>  $this->api_key
            );

            $request = array();
            $request = array(
                "type"  =>  "people",
                "sub-type"  =>  "dismiss",
            );

            $temp_data = array();
            $temp_data = array(
                "email" =>  $req_data['email'],
                "dateOfDismissal"  => date("d/m/Y")
            );

            $request_data = array();
            $request_data = array(
                "auth"      =>  $auth,
                "request"   =>  $request,
                "data"      =>  $temp_data
            );
            $tmp_response = $this->curl_operation(json_encode($request_data));

            $module_name ='employee';
            $action ='dismiss';

            $response_data = json_decode($tmp_response, true);
            $data = array();
            $data = array(
                "module"        =>  $module_name,
                "action"        =>  $action,
                "api_url"       =>  $this->api,
                "request_data"  =>  json_encode($request_data),
                "response_data" =>  $tmp_response,
                "staffid"       =>  $user_id,
                "date_added"    =>  date("Y-m-d H:i:s"),
                "added_by"      => $this->session->userdata("staff_user_id")
            );

            if(isset($response_data['error']) && !empty($response_data['error']))
            {
                $data['status'] =   '0';
            }
            else{
                $data['status'] =   '1';
            }

            $this->razorpay_history_insert($data);
            return;
        }

        /*  
        Module Name     : Employee Added 
        Description     : Employee Add on Razorpay Payroll system
        Author          : Ankit Agrawal
        Date            : 17/04/2024  
        */
        public function add_employee($user_id= '')
        {   
            $staff_information = $this->staff_information($user_id);

            $req_data = array();

            if( $staff_information['status'] == '1')
            {
                $req_data = $staff_information['resposne'];
            }

            if(empty($req_data))
            {
                return false;
            }
            $auth = array();
            $auth = array(
                "id"    =>  $this->api_id,
                "key"   =>  $this->api_key
            );

            $request = array();
            $request = array(
                "type"  =>  "people",
                "sub-type"  =>  "create",
            );
            $full_name = '';
            if($req_data['firstname'] != "")
            {
                $full_name .= $req_data['firstname'];
            }
            
            if($req_data['lastname'] != "")
            {
                $full_name .= $req_data['lastname'];
            }

            $temp_data = array();
            $temp_data = array(
                "email" =>  $req_data['email'],
                "name"  =>  $full_name,
                "type"  =>  "employee"
            );

            $request_data = array();

            $request_data = array(
                "auth"      =>  $auth,
                "request"   =>  $request,
                "data"      =>  $temp_data
            );

            $tmp_response = $this->curl_operation(json_encode($request_data));
            $response_data = json_decode($tmp_response, true);

            $module_name = 'employee';
            $action ='create';

            $history_data = array();        
            $history_data = array(
                "module"        =>  $module_name,
                "action"        =>  $action,
                "api_url"       =>  $this->api,
                "request_data"  =>  json_encode($request_data),
                "response_data" =>  $tmp_response,
                "staffid"       =>  $user_id,
                "date_added"    =>  date("Y-m-d H:i:s"),
                "added_by"      => $this->session->userdata("staff_user_id")
            );

            if(isset($response_data['error']) && !empty($response_data['error']))
            {
                $history_data['status'] =   '0';
            }
            else{
                $history_data['status'] =   '1';
            }

            $this->razorpay_history_insert($history_data);

            $this->update_employee($user_id, $action);
            return;
        }

        /*  
        Module Name     : Employee Edit/ Update 
        Description     : Employee Edit/ Update on Razorpay Payroll system
        Author          : Ankit Agrawal
        Date            : 14/12/2023   
        */
    public function update_employee($user_id= '', $module_action= '')
    {   
        $staff_information = $this->staff_information($user_id);
        
        $req_data = array();
        
        $ctc = 0; 
        $doj = '';
        
        if($staff_information['status'] == '1')
        {
            $req_data = $staff_information['resposne'];

            $customer_fields = $req_data['customfields_data'];
            if(!empty($customer_fields))
            {
                foreach($customer_fields as $customer_fields_val)
                {
                    if($customer_fields_val['slug'] === 'staff_cost_to_company')
                    {
                        $ctc = $customer_fields_val['value'];
                    }
                    if($customer_fields_val['slug'] === 'staff_date_of_joining')
                    {
                        $doj = $customer_fields_val['value'];
                    }
                }
            }
        }

        if(empty($req_data))
        {
            return false;
        }
        $auth = array();
        $auth = array(
            "id"    =>  $this->api_id,
            "key"   =>  $this->api_key
        );
        
        if(!empty($req_data['team_manage']) && $req_data['team_manage'] > 0)
        {
            $teamLead_id = (int) $req_data['team_manage'];
            $teamLead_type = "employee";
        }
        else{
            $teamLead_id = '';
            $teamLead_type = 'employee';
        }

        $module_name = 'employee';
        if($module_action != '')
        {
            $action = $module_action;
        }
        else{
            $action = 'update';
        }
        
        $emp_id = $user_id;

        $auth = array();
        $auth = array(
            "id"    =>  $this->api_id,
            "key"   =>  $this->api_key
        );

        $request = array();
        $request = array(
            "type"  =>  "people",
            "sub-type"  =>  "edit",
        );

        if($req_data['religion'])
        {
            $state = $req_data['religion'];
        }
        else{
           $state = 'Karnataka'; 
        }
        
        $pan_no = $bank_holder_name = $bank_name = $bank_ac_no = $ifsc_code = $account_type = '';

        $full_name = '';
        if($req_data['firstname'] != "")
        {
            $full_name .= $req_data['firstname'];
        }
        
        if($req_data['lastname'] != "")
        {
            $full_name .= $req_data['lastname'];
        }

        if(isset($req_data['bank_ifsc']) && $req_data['bank_ifsc'] !="")
        {
            $req_data['bank_ifsc']  = $req_data['bank_ifsc'] ;
        }
        else{
            $req_data['bank_ifsc'] = '';
        }

        $temp_data = array();
        $temp_data = array(
            "email"                 =>  $req_data['email'],
            "title"                 =>  ucwords($req_data['job_position']),
            "department"            =>  ucwords($req_data['department_name']),
            "phone-number"          =>  $req_data['phonenumber'],
            "employee-id"           =>  (int) $user_id,
            "pt-enabled"            =>  true,
            "hiring-date"           =>  date("d/m/Y", strtotime($doj)),
            "state"                 =>  $state,
            "bank-ifsc"             =>  $req_data['bank_ifsc'],
            "bank-account-number"   =>  $req_data['account_number'] 
        );

        if($teamLead_id != "" && $teamLead_id > 0)
        {
            $temp_data['manager-employee-id'] = (int)$teamLead_id;
            $temp_data['manager-employee-type'] = $teamLead_type;
        }


        $request_data = array();
        $request_data = array(
            "auth"      =>  $auth,
            "request"   =>  $request,
            "data"      =>  $temp_data
        );

        $tmp_response = $this->curl_operation(json_encode($request_data));
        $response_data = json_decode($tmp_response, true);


        $history_data = array();        
        $history_data = array(
            "module"        =>  $module_name,
            "action"        =>  $action,
            "api_url"       =>  $this->api,
            "request_data"  =>  json_encode($request_data),
            "response_data" =>  $tmp_response,
            "staffid"       =>  $user_id,
            "date_added"    =>  date("Y-m-d H:i:s"),
            "added_by"      => $this->session->userdata("staff_user_id")
        );

        if(isset($response_data['error']) && !empty($response_data['error']))
        {
            $history_data['status'] =   '0';
        }
        else{
            $history_data['status'] =   '1';
        }

        $this->razorpay_history_insert($history_data);
        $this->set_salary($user_id);
         return;
    }


    /*  
        Module Name     : Employee Attendance Checkin/ Checkcout
        Description     : Employee  Attendance Checkin/ Checkcout on Razorpay Payroll system
        Author          : Ankit Agrawal
        Date            : 15/12/2023   
    */

    public function check_employee_exits($user_id= '')
    {
        $staff_information = $this->staff_information($user_id);
        $req_data = array();

        if( $staff_information['status'] == '1')
        {
            $req_data = $staff_information['resposne'];
        }

        if(empty($req_data))
        {
            return false;
        }

        $module_name = 'employee';
        $action = 'view';
        $email = $req_data['email'];
        
        $auth = array();
        $auth = array(
            "id"    =>  $this->api_id,
            "key"   =>  $this->api_key
        );

        $request = array();
        $request = array(
            "type"  =>  "people",
            "sub-type"  => "view",
        );
        $temp_data = array();
        $temp_data = array(
            "email"             =>  $email,
            "employee-type"     =>  "employee"
        );
        
        $request_data = array();
        $request_data = array(
            "auth"      =>  $auth,
            "request"   =>  $request,
            "data"      =>  $temp_data
        );

        $api = RAZORPAY_PAYROLL_API;
        $tmp_response = $this->curl_operation(json_encode($request_data), $api);
        $response_data = json_decode($tmp_response, true);
       
        $history_data = array();        
        $history_data = array(
            "module"        =>  $module_name,
            "action"        =>  $action,
            "api_url"       =>  $this->api,
            "request_data"  =>  json_encode($request_data),
            "response_data" =>  $tmp_response,
            "staffid"       =>  $user_id,
            "date_added"    =>  date("Y-m-d H:i:s"),
            "added_by"      => $this->session->userdata("staff_user_id")
        );

        if(isset($response_data['error']) && !empty($response_data['error']))
        {
            $history_data['status'] =   '0';
        }
        else{
            $history_data['status'] =   '1';
        }

        $this->razorpay_history_insert($history_data);
        
        return $history_data['status'];
    }

    /*  
        Module Name     : Employee Set Salary
        Description     : Employee  Set Salary on Razorpay Payroll system
        Author          : Ankit Agrawal
        Date            : 18/04/2024   
    */

    public function set_salary($user_id= '')
    {
        $staff_information = $this->staff_information($user_id);
        
        $req_data = array();
        
        $ctc = 0; 
        
        if($staff_information['status'] == '1')
        {
            $req_data = $staff_information['resposne'];

            $customer_fields = $req_data['customfields_data'];
            if(!empty($customer_fields))
            {
                foreach($customer_fields as $customer_fields_val)
                {
                    if($customer_fields_val['slug'] === 'staff_cost_to_company')
                    {
                        $ctc = $customer_fields_val['value'];
                    }
                    // if($customer_fields_val['slug'] === 'staff_date_of_joining')
                    // {
                    //     $doj = $customer_fields_val['value'];
                    // }
                }
            }
        }

        if(empty($req_data))
        {
            return false;
        }
        $auth = array();
        $auth = array(
            "id"    =>  $this->api_id,
            "key"   =>  $this->api_key
        );


        $request = array();
        $request = array(
            "type"  =>  "people",
            "sub-type"  => "set-salary",
        );

        
        $temp_data = array();
        $temp_data = array(
            "employee-id"               =>  (int) $user_id,
            "custom-salary-structure"   =>  false,
            "annual-ctc"                =>  $ctc
        );
        // $temp_data['salary-structure']= array();
        // $temp_data['salary-structure']= array(
        //     "basic"                 =>  isset($monthly_data[1]) ? $monthly_data[1] : 0,
        //     "da"                    =>  isset($monthly_data[2]) ? $monthly_data[2] : 0,
        //     "hra"                   =>  isset($monthly_data[3]) ? $monthly_data[3] : 0,
        //     "special-allowance"     =>  isset($monthly_data[4]) ? $monthly_data[4] : 0,
        //     "lta"                   =>  isset($monthly_data[5]) ? $monthly_data[5] : 0
        // );
        // $temp_data['salary-structure']['custom-allowances']= array();
        // $temp_data['salary-structure']['custom-allowances']= array(
        //     array(
        //         "name"      => "Conveyance Allowance",
        //         "amount"    =>  isset($monthly_data[6]) ? $monthly_data[6] : 0,
        //         "taxable"   =>  "yes"
        //     ),
        //     array(
        //         "name"      => "Medical Allowance",
        //         "amount"    =>  isset($monthly_data[7]) ? $monthly_data[7] : 0,
        //         "taxable"   =>  "no"
        //     ),
        //     array(
        //         "name"      => "Variable",
        //         "amount"    =>  isset($monthly_data[8]) ? $monthly_data[8] : 0,
        //         "taxable"   =>  "yes"
        //     )
        //     //  array(
        //     //     "name"      => "PF Amount Employer (Annual)",
        //     //     "amount"    =>  isset($monthly_data[9]) ? $monthly_data[9] : 0,
        //     //     "taxable"   =>  "yes"
        //     // )
        // );

        // $temp_data['salary-structure']['deductions']= array(
        //     // array(
        //     //     "name"      => "PF Amount Employee",
        //     //     "amount"    =>  isset($deduction_monthly_data[1]) ? $deduction_monthly_data[1] : 0,
        //     //     "taxable"   =>  false
        //     // ),
        //     array(
        //         "name"      => "Accomodation Deduction",
        //         "amount"    =>  isset($deduction_monthly_data[2]) ? $deduction_monthly_data[2] : 0,
        //         "taxable"   =>  false
        //     ),
        //     array(
        //         "name"      => "Other Deduction",
        //         "amount"    =>  isset($deduction_monthly_data[3]) ? $deduction_monthly_data[3] : 0,
        //         "taxable"   =>  false
        //     )
        // );

        $request_data = array();
        $request_data = array(
            "auth"      =>  $auth,
            "request"   =>  $request,
            "data"      =>  $temp_data
        );

        $module_name = 'employee';
        $action = 'salary update';
        $tmp_response = $this->curl_operation(json_encode($request_data));
        $response_data = json_decode($tmp_response, true);
        
        $history_data = array();        
        $history_data = array(
            "module"        =>  $module_name,
            "action"        =>  $action,
            "api_url"       =>  $this->api,
            "request_data"  =>  json_encode($request_data),
            "response_data" =>  $tmp_response,
            "staffid"       =>  $user_id,
            "date_added"    =>  date("Y-m-d H:i:s"),
            "added_by"      => $this->session->userdata("staff_user_id")
        );

        if(isset($response_data['error']) && !empty($response_data['error']))
        {
            $history_data['status'] =   '0';
        }

        else{
            $history_data['status'] =   '1';
        }

        $this->razorpay_history_insert($history_data);
    }

    }
