<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div id="leave-reports" class="hide reports_fr">
   <table class="table table-leave-report scroll-responsive">
      <thead>
         <tr>
            <th><?php echo _l('staffid'); ?></th>
            <th><?php echo _l('full_name'); ?></th>
            <th><?php echo _l('total_annual_leave'); ?></th>
            <?php for ($i = 1; $i <= 12; $i++) { ?>
               <th style="padding:20px"><?php echo _l('month_' . $i) . ' (Eligible)'; ?></th>
               <th style="padding:20px"><?php echo _l('month_' . $i) . ' (Booked)'; ?></th>
               <th style="padding:20px"><?php echo _l('month_' . $i) . '(Available)'; ?></th>
            <?php } ?>
            <th><?php echo _l('the_total_was_off'); ?></th>
            <th><?php echo _l('number_leave_days_allowed'); ?></th>
         </tr>
      </thead>
      <tbody>
      </tbody>
   </table>
</div>