<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<style>
    .input-wide {
        width: 100px; /* Adjust the width as needed */
    }
</style>

<div class="col-md-12" style="overflow-x: auto;">
    <?php 
    $leave_setting_data = $leave_setting_data;
    ?>

    <form id="leave_setting" method="post" name="leave_setting" action="<?php echo admin_url('timesheets/leave_setting_department_wise')?>">
        <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
        <div class="row">
            <div class="col-md-4">
                <label>Year</label> 
                <select class="form-control" name="year" id="year">
                    <?php
                    $previous_year = date("Y", strtotime("-1 year"));
                    $current_year = date("Y");
                    echo '<option value="' . $previous_year . '">' . $previous_year . '</option>';
                    echo '<option value="' . $current_year . '" selected>' . $current_year . '</option>';
                    for ($i = 1; $i <= 5; $i++) {
                        $future_year = date("Y", strtotime("+" . $i . " years"));
                        echo '<option value="' . $future_year . '">' . $future_year . '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>
                        Leave Type
                        <hr> Department
                    </th>
                    <th><?php echo _l('annual_leave') ?></th>
                    <th><?php echo _l('maternity_leave') ?></th>
                    <th><?php echo _l('private_work_without_pay') ?></th>
                    <th><?php echo _l('sick_leave') ?></th>
                    <?php
                    foreach ($type_of_leave as $value) { ?>
                        <th><?php echo html_entity_decode($value['type_name']); ?></th>
                    <?php }
                    ?>
                </tr>
            </thead>
            <tbody>
            <?php
            foreach ($department as $dept_value) {
                // Find the matching leave setting data for the current department
                $leave_data = [];
                foreach ($leave_setting_data as $setting) {
                    if ($setting['department_id'] == $dept_value['departmentid']) {
                        $leave_data = json_decode($setting['leave_data'], true);
                        break;
                    }
                }
            ?>
                <tr>
                    <th scope="col"><?php echo html_entity_decode($dept_value['name']); ?></th>
                    <td><input type="number" name="leaves[<?php echo $dept_value['departmentid']; ?>][annual_leave]" class="form-control input-wide" pattern="[0-9]" min="0" value="<?php echo isset($leave_data['annual_leave']) ? $leave_data['annual_leave'] : ''; ?>"></td>
                    <td><input type="number" name="leaves[<?php echo $dept_value['departmentid']; ?>][maternity_leave]" class="form-control input-wide" pattern="[0-9]" min="0" value="<?php echo isset($leave_data['maternity_leave']) ? $leave_data['maternity_leave'] : ''; ?>"></td>
                    <td><input type="number" name="leaves[<?php echo $dept_value['departmentid']; ?>][private_work_without_pay]" class="form-control input-wide" pattern="[0-9]" min="0" value="<?php echo isset($leave_data['private_work_without_pay']) ? $leave_data['private_work_without_pay'] : ''; ?>"></td>
                    <td><input type="number" name="leaves[<?php echo $dept_value['departmentid']; ?>][sick_leave]" class="form-control input-wide" pattern="[0-9]" min="0" value="<?php echo isset($leave_data['sick_leave']) ? $leave_data['sick_leave'] : ''; ?>"></td>
                    <?php foreach ($type_of_leave as $value) { ?>
                        <td><input type="number" name="leaves[<?php echo $dept_value['departmentid']; ?>][<?php echo $value['id']; ?>]" class="form-control input-wide" pattern="[0-9]" min="0" value="<?php echo isset($leave_data[$value['id']]) ? $leave_data[$value['id']] : ''; ?>"></td>
                    <?php } ?>
                </tr>
            <?php
            }
            ?>
            <tr>
                <td colspan="9">
                    <input type="submit" name="submit_btn" id="submit_btn" class="btn btn-success">
                </td>
            </tr>
            </tbody>
        </table>
    </form>
</div>
