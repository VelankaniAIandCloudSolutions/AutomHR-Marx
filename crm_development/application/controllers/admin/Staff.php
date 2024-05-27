<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Staff extends AdminController
{
    /* List all staff members */
    public function index()
    {
        if (!has_permission('staff', '', 'view')) {
            access_denied('staff');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('staff');
        }
        $data['staff_members'] = $this->staff_model->get('', ['active' => 1]);
        $data['title']         = _l('staff_members');
        $this->load->view('admin/staff/manage', $data);
    }

    /* Add new staff member or edit existing */
    public function member($id = '')
    {
        if (!has_permission('staff', '', 'view')) {
            access_denied('staff');
        }
        hooks()->do_action('staff_member_edit_view_profile', $id);

        $this->load->model('departments_model');
        if ($this->input->post()) {
            $data = $this->input->post();
            // Don't do XSS clean here.
            $data['email_signature'] = $this->input->post('email_signature', false);
            $data['email_signature'] = html_entity_decode($data['email_signature']);

            if ($data['email_signature'] == strip_tags($data['email_signature'])) {
                // not contains HTML, add break lines
                $data['email_signature'] = nl2br_save_html($data['email_signature']);
            }

            $data['password'] = $this->input->post('password', false);

            if ($id == '') {
                if (!has_permission('staff', '', 'create')) {
                    access_denied('staff');
                }
                $id = $this->staff_model->add($data);
                if ($id) {
                    handle_staff_profile_image_upload($id);
                    set_alert('success', _l('added_successfully', _l('staff_member')));
                    redirect(admin_url('staff/member/' . $id));
                }
            } else {
                if (!has_permission('staff', '', 'edit')) {
                    access_denied('staff');
                }
                handle_staff_profile_image_upload($id);
                $response = $this->staff_model->update($data, $id);
                if (is_array($response)) {
                    if (isset($response['cant_remove_main_admin'])) {
                        set_alert('warning', _l('staff_cant_remove_main_admin'));
                    } elseif (isset($response['cant_remove_yourself_from_admin'])) {
                        set_alert('warning', _l('staff_cant_remove_yourself_from_admin'));
                    }
                } elseif ($response == true) {
                    set_alert('success', _l('updated_successfully', _l('staff_member')));
                }
                redirect(admin_url('staff/member/' . $id));
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('staff_member_lowercase'));
        } else {
            $member = $this->staff_model->get($id);
            if (!$member) {
                blank_page('Staff Member Not Found', 'danger');
            }
            $data['member']            = $member;
            $title                     = $member->firstname . ' ' . $member->lastname;
            $data['staff_departments'] = $this->departments_model->get_staff_departments($member->staffid);

            $ts_filter_data = [];
            if ($this->input->get('filter')) {
                if ($this->input->get('range') != 'period') {
                    $ts_filter_data[$this->input->get('range')] = true;
                } else {
                    $ts_filter_data['period-from'] = $this->input->get('period-from');
                    $ts_filter_data['period-to']   = $this->input->get('period-to');
                }
            } else {
                $ts_filter_data['this_month'] = true;
            }

            $data['logged_time'] = $this->staff_model->get_logged_time_data($id, $ts_filter_data);
            $data['timesheets']  = $data['logged_time']['timesheets'];
        }
        $this->load->model('currencies_model');
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['roles']         = $this->roles_model->get();
        $data['user_notes']    = $this->misc_model->get_notes($id, 'staff');
        $data['departments']   = $this->departments_model->get();
        $data['title']         = $title;

        $this->load->view('admin/staff/member', $data);
    }

    /* Get role permission for specific role id */
    public function role_changed($id)
    {
        if (!has_permission('staff', '', 'view')) {
            ajax_access_denied('staff');
        }

        echo json_encode($this->roles_model->get($id)->permissions);
    }

    public function save_dashboard_widgets_order()
    {
        hooks()->do_action('before_save_dashboard_widgets_order');

        $post_data = $this->input->post();
        foreach ($post_data as $container => $widgets) {
            if ($widgets == 'empty') {
                $post_data[$container] = [];
            }
        }
        update_staff_meta(get_staff_user_id(), 'dashboard_widgets_order', serialize($post_data));
    }

    public function save_dashboard_widgets_visibility()
    {
        hooks()->do_action('before_save_dashboard_widgets_visibility');

        $post_data = $this->input->post();
        update_staff_meta(get_staff_user_id(), 'dashboard_widgets_visibility', serialize($post_data['widgets']));
    }

    public function reset_dashboard()
    {
        update_staff_meta(get_staff_user_id(), 'dashboard_widgets_visibility', null);
        update_staff_meta(get_staff_user_id(), 'dashboard_widgets_order', null);

        redirect(admin_url());
    }

    public function save_hidden_table_columns()
    {
        hooks()->do_action('before_save_hidden_table_columns');
        $data   = $this->input->post();
        $id     = $data['id'];
        $hidden = isset($data['hidden']) ? $data['hidden'] : [];
        update_staff_meta(get_staff_user_id(), 'hidden-columns-' . $id, json_encode($hidden));
    }

    public function change_language($lang = '')
    {
        hooks()->do_action('before_staff_change_language', $lang);

        $this->db->where('staffid', get_staff_user_id());
        $this->db->update(db_prefix() . 'staff', ['default_language' => $lang]);
        if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            redirect(admin_url());
        }
    }

    public function timesheets()
    {
        $data['view_all'] = false;
        if (staff_can('view-timesheets', 'reports') && $this->input->get('view') == 'all') {
            $data['staff_members_with_timesheets'] = $this->db->query('SELECT DISTINCT staff_id FROM ' . db_prefix() . 'taskstimers WHERE staff_id !=' . get_staff_user_id())->result_array();
            $data['view_all']                      = true;
        }

        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('staff_timesheets', ['view_all' => $data['view_all']]);
        }

        if ($data['view_all'] == false) {
            unset($data['view_all']);
        }

        $data['reproting_to'] = $this->db->query('SELECT staffid AS reporting_to_id, firstname AS reporting_to_firstname, lastname AS reporting_to_lastname
            FROM tblstaff WHERE staffid in (select team_manage from tblstaff where staffid ='.get_staff_user_id().')')->result_array();


        $data['logged_time'] = $this->staff_model->get_logged_time_data(get_staff_user_id());
        $data['title']       = '';

         $timesheet_period= $this->db->query('Select timesheet_range FROM ' . db_prefix() . 'staff WHERE staffid = '. get_staff_user_id())->result_array();
        if(!empty($timesheet_period))
        {
            $data['timesheet_period'] = $timesheet_period[0]['timesheet_range'];
        }
        else{
            $data['timesheet_period'] = 'month';
        }

        $this->load->view('admin/staff/timesheets', $data);
    }

    public function delete()
    {
        if (!is_admin() && is_admin($this->input->post('id'))) {
            die('Busted, you can\'t delete administrators');
        }

        if (has_permission('staff', '', 'delete')) {
            $success = $this->staff_model->delete($this->input->post('id'), $this->input->post('transfer_data_to'));
            if ($success) {
                set_alert('success', _l('deleted', _l('staff_member')));
            }
        }

        redirect(admin_url('staff'));
    }

    /* When staff edit his profile */
    public function edit_profile()
    {
        hooks()->do_action('edit_logged_in_staff_profile');

        if ($this->input->post()) {
            handle_staff_profile_image_upload();
            $data = $this->input->post();
            // Don't do XSS clean here.
            $data['email_signature'] = $this->input->post('email_signature', false);
            $data['email_signature'] = html_entity_decode($data['email_signature']);

            if ($data['email_signature'] == strip_tags($data['email_signature'])) {
                // not contains HTML, add break lines
                $data['email_signature'] = nl2br_save_html($data['email_signature']);
            }

            $success = $this->staff_model->update_profile($data, get_staff_user_id());

            if ($success) {
                set_alert('success', _l('staff_profile_updated'));
            }

            redirect(admin_url('staff/edit_profile/' . get_staff_user_id()));
        }
        $member = $this->staff_model->get(get_staff_user_id());
        $this->load->model('departments_model');
        $data['member']            = $member;
        $data['departments']       = $this->departments_model->get();
        $data['staff_departments'] = $this->departments_model->get_staff_departments($member->staffid);
        $data['title']             = $member->firstname . ' ' . $member->lastname;
        $this->load->view('admin/staff/profile', $data);
    }

    /* Remove staff profile image / ajax */
    public function remove_staff_profile_image($id = '')
    {
        $staff_id = get_staff_user_id();
        if (is_numeric($id) && (has_permission('staff', '', 'create') || has_permission('staff', '', 'edit'))) {
            $staff_id = $id;
        }
        hooks()->do_action('before_remove_staff_profile_image');
        $member = $this->staff_model->get($staff_id);
        if (file_exists(get_upload_path_by_type('staff') . $staff_id)) {
            delete_dir(get_upload_path_by_type('staff') . $staff_id);
        }
        $this->db->where('staffid', $staff_id);
        $this->db->update(db_prefix() . 'staff', [
            'profile_image' => null,
        ]);

        if (!is_numeric($id)) {
            redirect(admin_url('staff/edit_profile/' . $staff_id));
        } else {
            redirect(admin_url('staff/member/' . $staff_id));
        }
    }

    /* When staff change his password */
    public function change_password_profile()
    {
        if ($this->input->post()) {
            $response = $this->staff_model->change_password($this->input->post(null, false), get_staff_user_id());
            if (is_array($response) && isset($response[0]['passwordnotmatch'])) {
                set_alert('danger', _l('staff_old_password_incorrect'));
            } else {
                if ($response == true) {
                    set_alert('success', _l('staff_password_changed'));
                } else {
                    set_alert('warning', _l('staff_problem_changing_password'));
                }
            }
            redirect(admin_url('staff/edit_profile'));
        }
    }

    /* View public profile. If id passed view profile by staff id else current user*/
    public function profile($id = '')
    {
        if ($id == '') {
            $id = get_staff_user_id();
        }

        hooks()->do_action('staff_profile_access', $id);

        $data['logged_time'] = $this->staff_model->get_logged_time_data($id);
        $data['staff_p']     = $this->staff_model->get($id);

        if (!$data['staff_p']) {
            blank_page('Staff Member Not Found', 'danger');
        }

        $this->load->model('departments_model');
        $data['staff_departments'] = $this->departments_model->get_staff_departments($data['staff_p']->staffid);
        $data['departments']       = $this->departments_model->get();
        $data['title']             = _l('staff_profile_string') . ' - ' . $data['staff_p']->firstname . ' ' . $data['staff_p']->lastname;
        // notifications
        $total_notifications = total_rows(db_prefix() . 'notifications', [
            'touserid' => get_staff_user_id(),
        ]);
        $data['total_pages'] = ceil($total_notifications / $this->misc_model->get_notifications_limit());
        $this->load->view('admin/staff/myprofile', $data);
    }

    /* Change status to staff active or inactive / ajax */
    public function change_staff_status($id, $status)
    {
        if (has_permission('staff', '', 'edit')) {
            if ($this->input->is_ajax_request()) {
                $this->staff_model->change_staff_status($id, $status);
            }
        }
    }

    /* Logged in staff notifications*/
    public function notifications()
    {
        $this->load->model('misc_model');
        if ($this->input->post()) {
            $page   = $this->input->post('page');
            $offset = ($page * $this->misc_model->get_notifications_limit());
            $this->db->limit($this->misc_model->get_notifications_limit(), $offset);
            $this->db->where('touserid', get_staff_user_id());
            $this->db->order_by('date', 'desc');
            $notifications = $this->db->get(db_prefix() . 'notifications')->result_array();
            $i             = 0;
            foreach ($notifications as $notification) {
                if (($notification['fromcompany'] == null && $notification['fromuserid'] != 0) || ($notification['fromcompany'] == null && $notification['fromclientid'] != 0)) {
                    if ($notification['fromuserid'] != 0) {
                        $notifications[$i]['profile_image'] = '<a href="' . admin_url('staff/profile/' . $notification['fromuserid']) . '">' . staff_profile_image($notification['fromuserid'], [
                            'staff-profile-image-small',
                            'img-circle',
                            'pull-left',
                        ]) . '</a>';
                    } else {
                        $notifications[$i]['profile_image'] = '<a href="' . admin_url('clients/client/' . $notification['fromclientid']) . '">
                    <img class="client-profile-image-small img-circle pull-left" src="' . contact_profile_image_url($notification['fromclientid']) . '"></a>';
                    }
                } else {
                    $notifications[$i]['profile_image'] = '';
                    $notifications[$i]['full_name']     = '';
                }
                $additional_data = '';
                if (!empty($notification['additional_data'])) {
                    $additional_data = unserialize($notification['additional_data']);
                    $x               = 0;
                    foreach ($additional_data as $data) {
                        if (strpos($data, '<lang>') !== false) {
                            $lang = get_string_between($data, '<lang>', '</lang>');
                            $temp = _l($lang);
                            if (strpos($temp, 'project_status_') !== false) {
                                $status = get_project_status_by_id(strafter($temp, 'project_status_'));
                                $temp   = $status['name'];
                            }
                            $additional_data[$x] = $temp;
                        }
                        $x++;
                    }
                }
                $notifications[$i]['description'] = _l($notification['description'], $additional_data);
                $notifications[$i]['date']        = time_ago($notification['date']);
                $notifications[$i]['full_date']   = $notification['date'];
                $i++;
            } //$notifications as $notification
            echo json_encode($notifications);
            die;
        }
    }

    public function update_two_factor()
    {
        $fail_reason = _l('set_two_factor_authentication_failed');
        if ($this->input->post()) {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('two_factor_auth', _l('two_factor_auth'), 'required');

            if ($this->input->post('two_factor_auth') == 'google') {
                $this->form_validation->set_rules('google_auth_code', _l('google_authentication_code'), 'required');
            }

            if ($this->form_validation->run() !== false) {
                $two_factor_auth_mode = $this->input->post('two_factor_auth');
                $id = get_staff_user_id();
                if ($two_factor_auth_mode == 'google') {
                    $this->load->model('Authentication_model');
                    $secret = $this->input->post('secret');
                    $success = $this->authentication_model->set_google_two_factor($secret);
                    $fail_reason = _l('set_google_two_factor_authentication_failed');
                } elseif ($two_factor_auth_mode == 'email') {
                    $this->db->where('staffid', $id);
                    $success = $this->db->update(db_prefix() . 'staff', ['two_factor_auth_enabled' => 1]);
                } else {
                    $this->db->where('staffid', $id);
                    $success = $this->db->update(db_prefix() . 'staff', ['two_factor_auth_enabled' => 0]);
                }
                if ($success) {
                    set_alert('success', _l('set_two_factor_authentication_successful'));
                    redirect(admin_url('staff/edit_profile/' . get_staff_user_id()));
                }
            }
        }
        set_alert('danger', $fail_reason);
        redirect(admin_url('staff/edit_profile/' . get_staff_user_id()));
    }

    public function verify_google_two_factor()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
            die;
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            $this->load->model('authentication_model');
            $is_success = $this->authentication_model->is_google_two_factor_code_valid($data['code'],$data['secret']);
            $result = [];

            header('Content-Type: application/json');
            if ($is_success) {
                $result['status'] = 'success';
                $result['message'] = _l('google_2fa_code_valid');;

                echo json_encode($result);
                die;
            }

            $result['status'] = 'failed';
            $result['message'] = _l('google_2fa_code_invalid');;

            echo json_encode($result);
            die;
        }
    }

    public function save_completed_checklist_visibility()
    {
        hooks()->do_action('before_save_completed_checklist_visibility');

        $post_data = $this->input->post();
        if (is_numeric($post_data['task_id'])) {
            update_staff_meta(get_staff_user_id(), 'task-hide-completed-items-'. $post_data['task_id'], $post_data['hideCompleted']);
        }
    }

     /* Cron job function for employee portal disable on last working days at 8 PM */
    public function staff_status_inactive()
    {
        ini_set("memory_limit", "-1");
        set_time_limit(0);

        $resuts = array();
        $this->db->select("custom_value.relid as staff_id");
        $this->db->from(db_prefix() . "customfieldsvalues as custom_value");
        $this->db->join(db_prefix() . "customfields as customfields","customfields.id = custom_value.fieldid","inner");
        $this->db->where("customfields.slug","staff_last_working_date");
        $this->db->where("DATE(custom_value.value)", date("Y-m-d"));
        $query = $this->db->get();
        $resuts = $query->result_array();
        if(sizeof($resuts) > 0)
        {
            // Use array_map to extract 'staff_id' values
            $staffIds = array_map(function ($item) {
                return $item['staff_id'];
            }, $resuts);

            $final_staff_ids = implode(",", $staffIds);
            $this->db->where_in("staffid", $final_staff_ids);
            $this->db->update(db_prefix() ."", array("active" => "0", "status_work" => "inactivity"));

            foreach($resuts as  $resuts_val)
            {
                log_activity('staff inactive on last working date.', $resuts_val['staff_id']);  
            }
        }
    }

    // Added new function for Time sheet approvals
    public function time_sheet_approval()
    {   
        $ts_filter_data = [];
        
        $ts_filter_data['period-from'] = $this->input->post('period_from');
        $ts_filter_data['period-to']   = $this->input->post('period_to');

        if($this->input->post("timesheet_staff_id") != "")
        {
            $id = $this->input->post("timesheet_staff_id");
        }
        else{
            $id = get_staff_user_id();
        }
        $logged_time = $this->staff_model->get_logged_time_data($id, $ts_filter_data);

        if($this->input->post('range') === 'today')
        {
            $period_from = date("Y-m-d");
            $period_to = date("Y-m-d");
        }
        else{
            $period_from    = $this->input->post('period_from');
            $period_to      = $this->input->post('period_to');
        }
        if(!empty($logged_time['timesheets']) && sizeof($logged_time['timesheets']) > 0 )
        {
            $this->db->select("id,status");
            $this->db->from('tbltime_sheet_approval');
            if($this->input->post('reporting_manager_id') != "")
            {
                $manager_ids = $this->input->post('reporting_manager_id');
                $this->db->where_in("reporting_manager_id", $manager_ids);
            }

            if($id != "")
            {
                $this->db->where_in("staff_id", $id);
            }

            if($this->input->post('range') != "")
            {
                $time_range = $this->input->post('range');
                $this->db->where_in("time_range", $time_range);
            }

            if($period_from != "" && $period_from != "0000-00-00")
            {
                $this->db->where("from_date >=", $period_from);
                $this->db->where("to_date <=", $period_to);
            }
            if($this->input->post('clientid') != "")
            {
                $clientid = $this->input->post('clientid');
                $this->db->where_in("customer_ids", $clientid);
            }

            // else{
            //     set_alert('danger', _l('client_not_selected')); die;
            // }
            if($this->input->post('project_id') != "")
            {
                $project_ids = $this->input->post('project_id');
                $this->db->where_in("project_ids", $project_ids);
            }
            else{
                set_alert('danger', _l('project_not_selected'));
            }

            if($this->input->post('reporting_manager_id') != "")
            {
                $reporting_manager_id = $this->input->post('reporting_manager_id');
                $this->db->where_in("reporting_manager_id", $reporting_manager_id);
            }
            else{
                set_alert('danger', _l('reporting_manager_not_selected'));
            }

            $this->db->where("status != '2'");
            $this->db->where("teamlead_status != '2'");

            $time_sheet_exist = $this->db->get();
            $time_sheet_exist_results = $time_sheet_exist->result_array();
            if(!empty($time_sheet_exist_results))
            {
                $pending_counts = 0;
                $approved_counts = 0;
                foreach($time_sheet_exist_results as $timesheet_val)
                {
                    if($timesheet_val['status'] == '0')
                    {
                        $pending_counts++;
                    }
                    elseif($timesheet_val['status'] == '1'){
                        $approved_counts++;
                    }
                } 
                if($pending_counts > 0)
                {
                    set_alert('warning', _l('total_pending_timesheet').' : '. $pending_counts." and "._l('total_approved_timesheet').' : '. $approved_counts);
                }
                elseif($pending_counts == 0)
                {
                    set_alert('warning',_l('timesheet_activity_exist'));
                }
            }
            else{

                $times_sheet_json_array[] = $logged_time['timesheets'];
                $approval_data = array();
                $approval_data = array(
                    "staff_id"              =>  $id,
                    "reporting_manager_id"  =>  $this->input->post('reporting_manager_id'),
                    "time_range"            =>  $this->input->post('range'),
                    "from_date"             =>  $period_from,
                    "to_date"               =>  $period_to,
                    "customer_ids"          =>  $this->input->post('clientid'),
                    "project_ids"           =>  implode(",",$this->input->post('project_id')),
                    "status"                =>  '0',
                    "logged_time_sheet"     =>  json_encode($times_sheet_json_array),
                    "contractor_id"         => $this->input->post("contractor_id"),
                    "supplier_name"         => $this->input->post("supplier_name"),
                    "consultant_name"       => $this->input->post("consultant_name"),
                    "line_manager"          => $this->input->post("line_manager"),
                    "day_display"           => $this->input->post("day_display"),
                    "created_at"            => date("Y-m-d H:i:s"),
                    "modified_at"            => date("Y-m-d H:i:s"),
                );

                $last_id = '';
                $this->db->insert(db_prefix()."time_sheet_approval",$approval_data);
                $last_id = $this->db->insert_id();
                
                $pdf_file_url = $this->timesheet_export('pdf',$approval_data);
                $excel_file_url = $this->timesheet_export('excel',$approval_data);

                $pdf_file_url_array = json_decode($pdf_file_url, true);
                $excel_file_url_array = json_decode($excel_file_url, true);
                

                $pdf_url  = $excel_url = '';
                if(isset($pdf_file_url_array['file_url']) && $pdf_file_url_array['file_url'] != "")
                {
                    $pdf_url = $pdf_file_url_array['file_url'];
                }

                if(isset($excel_file_url_array['file_url']) && $excel_file_url_array['file_url'] != "")
                {
                    $excel_url = $excel_file_url_array['file_url'];
                }

                $this->db->where("id", $last_id);
                $update_data = array();
                $update_data = array(
                    "pdf_url"       => $pdf_url,
                    "excel_url"    => $excel_url,
                );

                $this->db->update(db_prefix()."time_sheet_approval", $update_data);

                //  Email send to Reporting Manager
                $this->load->model('emails_model');
                
                $reporting_manager_id = $this->input->post('reporting_manager_id');

                $this->db->where('staffid', $reporting_manager_id);
                $this->db->select('firstname,lastname,email');
                $to = '';
                $data = $this->db->get(db_prefix() . 'staff')->row();
                
                $approval_name ='';
                if ($data) {
                    $to = $data->email;
                    if($data->firstname !="")
                    {
                        $approval_name .= $data->firstname;
                    }
                    if($data->lastname !="")
                    {
                        $approval_name .= ' '.$data->lastname;
                    }
                }

                $subject = 'Send request approval to approver on '. date("d/m/Y"). ' : Timesheet Date From -'.date("d/m/Y", strtotime($period_from)).' To -'.date("d/m/Y", strtotime($period_to));
                
                $message = 'Hi '.$approval_name.'! <br>
                        -'.get_staff_full_name($id).' has created an apply for leave and requires your approval. Please go to this link for details and approval: '.base_url('admin/reports/timesheet_approval_list');
                
                set_alert('success', _l('timesheet_send_to_reporting_manager')); 
                
                if($to != "")
                {
                    $this->emails_model->send_simple_email($to, $subject, $message);
                }

                die;
            }
        }
        else{
            set_alert('danger', _l('activity_not_recorded')); die;
        }
    }


    public function fequency_date_calculate()
    {
        $timesheet_staff_id = '';
        $start_date = $end_date = '';

        $timesheet_staff_id = $this->input->post("timesheet_staff_id");
        $frequency = $this->input->post("frequency");
        $startDate = $this->input->post("start_date");


        if($timesheet_staff_id != "")
        {
            $timesheet_period= $this->db->query('Select timesheet_range FROM ' . db_prefix() . 'staff WHERE staffid = '. $timesheet_staff_id)->result_array();
            if(!empty($timesheet_period))
            {
                $frequency = $timesheet_period[0]['timesheet_range'];
            }
            else{
                $frequency ='month';
            }
        }
        else{
            $timesheet_period= $this->db->query('Select timesheet_range FROM ' . db_prefix() . 'staff WHERE staffid = '. get_staff_user_id())->result_array();
            if(!empty($timesheet_period))
            {
                $frequency = $timesheet_period[0]['timesheet_range'];
            }
            else{
                $frequency ='month';
            }
        }

        if($frequency === 'month')
        {
            if($startDate != '')
            {
                $start_date = date("Y-m-d", strtotime($startDate));
                $end_date = date("Y-m-d", strtotime("+1 month", strtotime($startDate)));
            }
            else{
                $start_date = date("Y-m-d", strtotime("-1 month"));
                $end_date = date("Y-m-d");
            }

            // Subtract 1 day from the end date
            $end_date = date("Y-m-d", strtotime($end_date . ' -1 day'));

            $data = array(
                "start_date"    => $start_date,
                "end_date"      => $end_date
            );
            echo json_encode($data);
        }
        elseif($frequency=== 'weekly')
        {
            if($startDate != '')
            {
                $start_date = date("Y-m-d", strtotime($startDate));
                $end_date = date("Y-m-d", strtotime("+1 week", strtotime($startDate)));
            }
            else{
                $start_date = date("Y-m-d", strtotime("-1 week"));
                $end_date = date("Y-m-d");
            }
            // Subtract 1 day from the end date
            $end_date = date("Y-m-d", strtotime($end_date . ' -1 day'));

            $data = array(
                "start_date"    => $start_date,
                "end_date"      => $end_date
            );
            echo json_encode($data);
        }
        elseif($frequency=== 'biweekly')
        {
            if($startDate != '')
            {
                $start_date = date("Y-m-d", strtotime($startDate));
                $end_date = date("Y-m-d", strtotime("+2 week", strtotime($startDate)));
            }
            else{
                $start_date = date("Y-m-d", strtotime("-2 week"));
                $end_date = date("Y-m-d");
            }
            // Subtract 1 day from the end date
            $end_date = date("Y-m-d", strtotime($end_date . ' -1 day'));

            $data = array(
                "start_date"    => $start_date,
                "end_date"      => $end_date
            );
            echo json_encode($data);
        }
        else{
            return false; 
        }
        die;
    }

    public function timesheet_tracking_status()
    {
        $ts_filter_data = [];
        $ts_filter_data['period-from'] = $this->input->post('start_date');
        $ts_filter_data['period-to']   = $this->input->post('period_to');
        if($this->input->post("timesheet_staff_id") != "")
        {
            $id = $this->input->post("timesheet_staff_id");
        }
        else{
            $id = get_staff_user_id();
        }

        $logged_time = $this->staff_model->get_logged_time_data($id, $ts_filter_data);

        if($this->input->post('range') === 'today')
        {
            $period_from = date("Y-m-d");
            $period_to = date("Y-m-d");
        }
        else{
            $period_from    = $this->input->post('period_from');
            $period_to      = $this->input->post('period_to');
        }
        if(!empty($logged_time['timesheets']) && sizeof($logged_time['timesheets']) > 0 )
        {
            $this->db->select("id,status");
            $this->db->from('tbltime_sheet_approval');

            if($this->input->post('reporting_manager_id') != "")
            {
                $manager_ids = $this->input->post('reporting_manager_id');
                $this->db->where_in("reporting_manager_id", $manager_ids);
            }

            if($id != "")
            {
                $this->db->where_in("staff_id", $id);
            }

            if($this->input->post('range') != "")
            {
                $time_range = $this->input->post('range');
                $this->db->where_in("time_range", $time_range);
            }

            if($period_from != "" && $period_from != "0000-00-00")
            {
                $this->db->where("from_date >=", $period_from);
                $this->db->where("to_date <=", $period_to);
            }

            if($this->input->post('clientid') != "")
            {
                $clientid = $this->input->post('clientid');
                $this->db->where_in("customer_ids", $clientid);
            }
            // else{
            //     set_alert('danger', _l('client_not_selected')); 
            // }

            if(isset($_POST['project_id']) && $_POST['project_id'] > 0 )
            {
                $project_ids = $this->input->post('project_id');
                $this->db->where_in("project_ids", $project_ids);
            }
            // else{
            //     set_alert('danger', _l('project_not_selected'));
            // }

            if($this->input->post('reporting_manager_id') != "")
            {
                $reporting_manager_id = $this->input->post('reporting_manager_id');
                $this->db->where_in("reporting_manager_id", $reporting_manager_id);
            }

            
            if($this->input->post('contractor_id') != "")
            {
                $contractor_id = $this->input->post('contractor_id');
                $this->db->where_in("contractor_id", $contractor_id);
            }

            //  if($this->input->post('supplier_name') != "")
            // {
            //     $reporting_manager_id = $this->input->post('supplier_name');
            //     $this->db->where_in("reporting_manager_id", $reporting_manager_id);
            // }

            //  if($this->input->post('reporting_manager_id') != "")
            // {
            //     $reporting_manager_id = $this->input->post('reporting_manager_id');
            //     $this->db->where_in("reporting_manager_id", $reporting_manager_id);
            // }

            // else{
            //     set_alert('danger', _l('reporting_manager_not_selected'));
            // }

            $this->db->where("status != '2'");
            $this->db->where("teamlead_status != '2'");

            $this->db->order_by("id","desc");
            $this->db->limit(1);
            $time_sheet_exist = $this->db->get();


            $time_sheet_exist_results = $time_sheet_exist->result_array();
            // echo $this->db->last_query();
            // die;

            if(!empty($time_sheet_exist_results))
            {
                $pending_counts = 0;
                $approved_counts = 0;
                foreach($time_sheet_exist_results as $timesheet_val)
                {
                    if($timesheet_val['status'] == '0' || $timesheet_val['teamlead_status'] == '0')
                    {
                        $pending_counts++;
                    }
                    elseif($timesheet_val['status'] == '1'  && $timesheet_val['teamlead_status'] == '1'){
                        $approved_counts++;
                    }
                } 
                if($pending_counts > 0)
                {
                    $data['status'] = "0"; // pending for approval 
                }
                elseif($pending_counts == 0 && $approved_counts > 0)
                {
                    $data['status'] = "1"; // approved  timesheet by reporting manager
                }
                else{
                    $data['status'] = "3";  //  Not submitted
                }
            }
            else{
                $data['status'] = "3";  // Not submitted
            }
        }
        else
        {
            $data['status'] = "4";  // time tacking records not avaialble.
        }
        echo json_encode($data); die;
    }

    // Timesheet Export for Exmployee wise

    public function timesheet_export($file_type = '', $data = array())
    {   
        if(!empty($data))
        {
           $post_data['timesheet_staff_id'] = $data['staff_id'];
           $post_data['reporting_manager_id'] = $data['reporting_manager_id'];
           $post_data['range'] = $data['time_range'];
           $post_data['period_from'] = $data['from_date'];
           $post_data['period_to'] = $data['to_date'];
           $post_data['clientid'] = $data['customer_ids'];
           $post_data['timesheet_project_id'] = $data['project_ids'];
           $post_data['contractor_id'] = $data['contractor_id'];
           $post_data['supplier_name'] = $data['supplier_name'];
           $post_data['consultant_name'] = $data['consultant_name'];
           $post_data['line_manager'] = $data['line_manager'];
           $post_data['day_display'] = $data['day_display'];
        }
        else
        {
            $post_data = $this->input->post();
            $export = $post_data['export'];
        }

       if(!empty($post_data))
       {
        $timesheet_staff_id     = $post_data['timesheet_staff_id'];
        $range                  = $post_data['range'];
        $period_from            = $post_data['period_from'];
        $period_to              = $post_data['period_to'];
        $clientid               = $post_data['clientid'];
        $reporting_manager_id   = $post_data['reporting_manager_id'];

        $project_id = '';

        if(isset($post_data['timesheet_project_id']))
        {
             $project_id             = $post_data['timesheet_project_id'] ? $post_data['timesheet_project_id'] : '';
        }
       

        $this->db->select("taskstimer.*, tasks.name as task_name, tasks.description as task_description,
            staff.firstname as staff_first_name, staff.lastname as staff_last_name,
             reporting_manager.firstname as manager_first_name, reporting_manager.lastname as manager_last_name,
             tsa.day_display, tsa.line_manager, tsa.consultant_name , tsa.supplier_name, tsa.contractor_id,
             projects.name as project_name,
             projects.clientid as clientid,
             staff.job_position as position,
             sd.departmentid as departmentid
            ");

        $this->db->from(db_prefix()."taskstimers as taskstimer");
        $this->db->join(db_prefix()."tasks as tasks","tasks.id = taskstimer.task_id","left");
        $this->db->join(db_prefix()."projects as projects","tasks.rel_id = projects.id","left");
        $this->db->join(db_prefix()."staff as staff","staff.staffid = taskstimer.staff_id","left");
        $this->db->join(db_prefix()."staff as reporting_manager","reporting_manager.staffid = staff.team_manage","left");

        $this->db->join(db_prefix()."time_sheet_approval as tsa","tsa.staff_id = taskstimer.staff_id","left");
         $this->db->join(db_prefix()."staff_departments as sd","sd.staffid = staff.staffid","left");

        if($timesheet_staff_id != "")
        {
            $this->db->where("taskstimer.staff_id", $timesheet_staff_id);
        }
        else{
            $this->db->where("taskstimer.staff_id",get_staff_user_id());
        }

        if($period_from != "")
        {
            // $this->db->where("start_time >=", strtotime($period_from.' 00:00:00'));
            $tmp_start_date = $period_from.' 00:00:00';
            $this->db->where("start_time >=", strtotime($tmp_start_date));

            // $this->db->where("tsa.from_date >=", strtotime($period_from));

        }

        if($period_to != "")
        {
            // $this->db->where("end_time <=", strtotime($period_to."23:59:59"));
            $tmp_end_date = $period_to.' 23:59:59';
            $this->db->where("end_time <=", strtotime($tmp_end_date));

            // $this->db->where("tsa.to_date <=", strtotime($period_to));
        }

        if($clientid != "")
        {
            $this->db->where("projects.clientid", $clientid);
        }

        if($project_id != "")
        {
            $this->db->where_in("projects.id", $project_id);
        }

        
        $this->db->where("tsa.status !=2");
        $this->db->where("tsa.teamlead_status !=2");

        $this->db->where("tsa.id IN (
            (SELECT MAX(id) as id 
             FROM tbltime_sheet_approval 
             WHERE tbltime_sheet_approval.status != 2 
               AND tbltime_sheet_approval.teamlead_status != 2  
             GROUP BY staff_id, project_ids, from_date)
        )");

        $this->db->order_by("taskstimer.id","desc");

        $time_sheet_query = $this->db->get();
        
        $timesheet_task_records = $time_sheet_query->result_array();
        
        if(!empty($timesheet_task_records))
        {

            $imagePath = base_url('assets/images/marx-logo.png');
            $columnHeaders = ["Days","Date", "Tasks", "Hours"];
            $day = 0;

            $tmp_timesheet_task_records = array();

            $department = $position = '';
            
            $project_name = $timesheet_task_records[0]['project_name'];
            $contractor_id = $timesheet_task_records[0]['contractor_id'];
            $supplier_name = $timesheet_task_records[0]['supplier_name'];
            $consultant_name = $timesheet_task_records[0]['consultant_name'];
            
            $department = $timesheet_task_records[0]['departmentid'];
            $position = $timesheet_task_records[0]['position'];

            $line_manager_name = '';
            
            if(isset($timesheet_task_records[0]['line_manager']) && $timesheet_task_records[0]['line_manager'] != "")
            {
                 $line_manager_name = $timesheet_task_records[0]['line_manager'];
            }
           
            
            $address ='';
            $line_manager_type ='';
            
            foreach($timesheet_task_records as $timesheet_task_records_val)
            {

                $this->db->select("customfields.id as customfield_id,
                 customfields.slug as customfields_slug,
                 customfields.options as customfields_options,
                 customfields.fieldto as fieldto,
                 customfieldsvalues. value
                ");
                $this->db->from(db_prefix()."customfields as customfields");
                $this->db->join(db_prefix()."customfieldsvalues as customfieldsvalues","customfieldsvalues.fieldid = customfields.id","left");
                $this->db->where("customfields.fieldto","customers");
                if($timesheet_task_records_val['clientid'] > 0)
                {
                    $this->db->where("customfieldsvalues.fieldto","customers");
                    $this->db->where("customfieldsvalues.relid",$timesheet_task_records_val['clientid']);
                }

                $custom_fields_query = $this->db->get();
               
                if(!$custom_fields_query)
                {
                    return false;
                }
                
                $customer_fields_data = $custom_fields_query->result_array();

                if(!empty($customer_fields_data ))
                {
                    foreach($customer_fields_data  as $tmp_customer_fields_data)
                    {
                        if($tmp_customer_fields_data['fieldto'] =='customers' && $tmp_customer_fields_data['customfields_slug'] ==='customers_client_company_address')
                        {
                           $address =  $tmp_customer_fields_data['value'];
                        }

                        if($tmp_customer_fields_data['fieldto'] =='customers' && $tmp_customer_fields_data['customfields_slug'] ==='customers_is_line_manager_lead_architect_approver_name')
                        {
                           $line_manager_type =  $tmp_customer_fields_data['value'];
                        }
                    }
                }


                $tmp_time_sheet_data  =array();
                $tmp_time_sheet_data['day'] = $day++;
                $tmp_time_sheet_data['date'] = date("Y-m-d", $timesheet_task_records_val['start_time']);
                $tmp_time_sheet_data['task'] = '';

                if($timesheet_task_records_val['task_name'] != "")
                {
                    $tmp_time_sheet_data['task'] .= $timesheet_task_records_val['task_name'];
                }
                

                $tmp_time_sheet_data['id'] = '';

                $tmp_time_sheet_data['id'] = $timesheet_task_records_val['id'];
                 
                $tmp_time_sheet_data['task_description']  = "";

                if($timesheet_task_records_val['task_description'] != "")
                {
                    $tmp_time_sheet_data['task_description'] = strip_tags($timesheet_task_records_val['task_description']);
                }

                $tmp_time_sheet_data['task_hours'] = 0;

                if($timesheet_task_records_val['start_time'] != "" && $timesheet_task_records_val['end_time'] != "")
                {

                    $hours_minutes = $this->calculateHoursAndMinutes($timesheet_task_records_val['start_time'], $timesheet_task_records_val['end_time']);

                    $tmp_time_sheet_data['task_hours'] = $hours_minutes['hours'].":".$hours_minutes['minutes'];
                }

                array_push($tmp_timesheet_task_records, $tmp_time_sheet_data);

            }


            $csvContent = array();

            $is_display_day_column = 0;
            if(isset($post_data['day_display']) && $post_data['day_display'] == '1')
            {
                $is_display_day_column = 1;
            }
            
            $csvContent = $this->timesheet_export_json($tmp_timesheet_task_records, $imagePath, $project_name, $contractor_id, $supplier_name, $consultant_name, $period_from, $address, $line_manager_name, $line_manager_type, $is_display_day_column, $department, $position);

            $curl = curl_init();
            
            if((isset($_POST['pdf']) && $_POST['pdf'] === 'pdf') || $file_type == 'pdf')
            {
                $this->api = TIMESHEET_PDF_EXPORT;
            }
            else
            {
                $this->api = TIMESHEET_EXCEL_EXPORT;
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
                CURLOPT_POSTFIELDS =>$csvContent,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                ),
            ));

            $export_excel_timesheet = curl_exec($curl);
            curl_close($curl);
            
            if(!empty($data))
            {
                return $export_excel_timesheet;
            }
            else
            {
                echo $export_excel_timesheet; exit;
            }
        }
        else{
            set_alert('warning', "No Records Found");
        }
       }
    }

    function calculateHoursAndMinutes($startTime, $endTime) 
    {
        $startTimeUnix = is_numeric($startTime) ? $startTime : strtotime($startTime);
        $endTimeUnix = is_numeric($endTime) ? $endTime : strtotime($endTime);

        // Calculate difference in seconds
        $difference = $endTimeUnix - $startTimeUnix;

        // Calculate hours and minutes
        $hours = floor($difference / 3600); // 1 hour = 3600 seconds
        $minutes = floor(($difference % 3600) / 60); // 1 minute = 60 seconds

        return ['hours' => $hours, 'minutes' => $minutes];
    }

    function calculateTotalTime($tasks) 
    {

        // Initialize total hours and minutes
        $totalHours = 0;
        $totalMinutes = 0;

        // Iterate through each task
        foreach ($tasks as $task) {
            // Split the task_hours into hours and minutes
            list($hours, $minutes) = explode(':', $task['task_hours']);

            // Add the hours and minutes to the totals
            $totalHours += (int)$hours;
            $totalMinutes += (int)$minutes;
        }

        // Convert excess minutes to hours if greater than 59
        $extraHours = floor($totalMinutes / 60);
        $totalHours += $extraHours;

        // Calculate the remaining minutes
        $remainingMinutes = $totalMinutes % 60;

        // Return the total hours and minutes as an array
        return array($totalHours, $remainingMinutes);
    }

    function timesheet_export_json($dynamicData = array(), $imgePath='', $project_name = '', $contractor_id = '', $supplier_name = '', $consultant_name = '', $period_from = '', $address = '', $line_manager_name = '', $line_manager_type = '', $is_display_day_column = '', $department = '', $position = '')
    {
        $output= array();

        if(!empty($dynamicData))
        {
            $default_value = 'N.A.';
            $total_worked_days = 0;

            $output["logo_url"] = $imgePath;
            $output["project_name"] = $project_name;
            $output['month'] = date("m/Y", strtotime($period_from));
            $output['supplier_number'] = $supplier_name ? $supplier_name : $default_value ; // name as number
            $output['consultant_name'] = $consultant_name ? $consultant_name : $default_value;
            $output['contract_id'] = $contractor_id ? $contractor_id : $default_value;
            $output['address'] = $address ? $address : $default_value; 

            $groupedData = [];

            foreach ($dynamicData as $item) {
                $date = $item['date'];
                
                if (!isset($groupedData[$date])) {
                    $groupedData[$date] = [];
                }
                $groupedData[$date][] = $item;
            }

            $output['date_wise_tasks'] = array();
           
            foreach($groupedData as $key => $groupedData_val)
            {
                $date_wise_data = array();
                $date_wise_data['date']             = date("d/m/Y", strtotime($key));
                $date_wise_data['days']             = date("d", strtotime($key));
                
                $date_wise_data['tasks']    = array();

                $date_wise_data['days_worked']     = '1';
                $total_worked_days++;

                $task_days_total_hours = 0;
                $task_days_total_hours = $this->calculateTotalTime($groupedData_val);
                $date_wise_data['total_hours']  = $task_days_total_hours[0].":". $task_days_total_hours[1];
                

                foreach($groupedData_val as $tmp_groupedData_val)
                {   
                    $tmp_groupedData_val1 = array();
                    $tmp_groupedData_val1['id'] = $tmp_groupedData_val['id'];
                    $tmp_groupedData_val1['name'] = $tmp_groupedData_val['task'];
                    $tmp_groupedData_val1['description'] = $tmp_groupedData_val['task_description'];
                    $tmp_groupedData_val1['hours'] = $tmp_groupedData_val['task_hours'];

                    array_push($date_wise_data['tasks'] , $tmp_groupedData_val1);
                }

                array_push($output['date_wise_tasks'] , $date_wise_data);
            }

            $total_hours = $this->calculateTotalTime($dynamicData);


            $output['total_hours'] = $total_hours[0].":". $total_hours[1];

            $output['total_days_worked'] = 0;
            
            if($is_display_day_column == '1')
            {
                $output['total_days_worked'] = $total_worked_days; 
            }
            

            $year = date("Y", strtotime($period_from));
            $month = date("m", strtotime($period_from));

            $output['confirmation_date'] = date('t-m-Y', strtotime("$year-$month-01"));

            $output['display_label'] = strtoupper($line_manager_type ? $line_manager_type : $default_value);
            $output['display_value'] = strtoupper($line_manager_name ? $line_manager_name : $default_value);
        }

        if(sizeof($output) > 0)
        {
           
           $holiday_data = array();
           $this->db->select("id, off_reason, off_type, break_date as holiday_date, department, position");
            if($department != "" )
            {
                $this->db->where_in("department", $department);
            }

            if($position != "" )
            {
                $this->db->where_in("position", $position);
            }

            $holiday_data = $this->db->get(db_prefix()."day_off")->result_array();

            if(sizeof($holiday_data) > 0)
            {
                $output['holiday_details'] = $holiday_data;
            }
            else{
                $output['holiday_details'] = $holiday_data;
            }
        }
        return json_encode($output);
    }
    
}
