
<script>

    "use strict";

var InvoiceServerParams={};

var payslip_table = $('.table-payslip_table');
initDataTable(payslip_table, admin_url+'hr_payroll/payslip_table',[0],[0], InvoiceServerParams, [0 ,'desc']);

$('#date_add').on('change', function() {
    payslip_table.DataTable().ajax.reload().columns.adjust().responsive.recalc();
});


var hidden_columns = [0];
$('.table-payslip_table').DataTable().columns(hidden_columns).visible(false, false);

appValidateForm($("body").find('#add_payslip'), {
    'payslip_month': 'required',
    'payslip_name': 'required',
    'payslip_template_id': 'required',
});   

function new_payslip(){
    "use strict";

    $('#payslip_template_modal').modal('show');
    $('.edit-title').addClass('hide');
    $('.add-title').removeClass('hide');
    $('#additional_payslip_template').html('');
    $('#additional_payslip_column').html('');

    $('#add_payslip_template input[name="templates_name"]').val('');

    var id = '';
    requestGetJSON('hr_payroll/get_payslip_template/' + id).done(function (response) {
    	$("select[id='payslip_template_id']").html('');
        $("select[id='payslip_template_id']").append(response.payslip_template_selected).selectpicker('refresh');

    });

}

function edit_payslip(invoker,id){
    "use strict";

    $('#additional_payslip_template').html('');
    $('#additional_payslip_column').html('');

    requestGetJSON('hr_payroll/get_payslip_template/' + id).done(function (response) {
        
        $('#additional_payslip_template').append(hidden_input('id',id));

        $('#add_payslip_template input[name="templates_name"]').val(response.payslip_template_data.templates_name);
        $('#add_payslip_template select[name="manager_id"]').val(response.payslip_template_data.manager_id).selectpicker('refresh');
        $('#add_payslip_template select[name="follower_id"]').val(response.payslip_template_data.follower_id).selectpicker('refresh');

        $("select[id='payslip_id_copy']").html('');
        $("select[id='payslip_id_copy']").append(response.payslip_template_selected).selectpicker('refresh');

        $("select[id='payslip_columns']").html('');
        $("select[id='payslip_columns']").append(response.payslip_column_selected).selectpicker('refresh');
         
       	init_selectpicker(); 
        $(".selectpicker").selectpicker('refresh');
    });

    $('#payslip_template_modal').modal('show');
    $('.add-title').addClass('hide');
    $('.edit-title').removeClass('hide');
}

$('.payslip_checked').on('click', function(event) {
    "use strict";

    var payslip_month = $("body").find(' input[name="payslip_month"]').val();
    var payslip_name = $("body").find(' input[name="payslip_name"]').val();
    var payslip_template_id = $("body").find(' select[id="payslip_template_id"]').val();

    if (payslip_name !== '' && payslip_template_id.length > 0 && payslip_month != '' ) {
        var data={};
        data.payslip_month = payslip_month;
        data.payslip_name = payslip_name;
        data.payslip_template_id = payslip_template_id;

        $(event).attr( "disabled", "disabled" );
        $.post(admin_url + 'hr_payroll/payslip_checked', data).done(function(response) {
            response = JSON.parse(response);

            if (response.status === true || response.status == 'true') {
                $('#add_payslip').submit()
            } else {
                $(event).removeAttr('disabled')
                alert_float('warning', response.message, 5000);
            }
        });
    }

});

</script>