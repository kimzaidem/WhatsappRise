<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <div class="page-title clearfix rounded">
            <h1><?php echo app_lang('whatsapp_template_details'); ?></h1>
            <div class="title-button-group">
                <a href="javascript:void(0)" title="" class="btn btn-primary load_data"><?= app_lang('load_data'); ?></a>
            </div>
        </div>
        <div class="table-responsive">
            <table id="whatsapp_template_list_table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">

    "use strict";

    $(document).ready(function () {

        $("#whatsapp_template_list_table").appTable({
            source: '<?php echo_uri("whatsapp/template_list_table"); ?>',
            columns: [
                {title: '#'},
                {title: '<?php echo app_lang("template_name"); ?>'},
                {title: '<?php echo app_lang("language"); ?>'},
                {title: '<?php echo app_lang("category"); ?>'},
                {title: '<?php echo app_lang("status"); ?>'},
                {title: '<?php echo app_lang("body_data"); ?>'},
            ],
            printColumns: [0,1,2,3,4,5],
            xlsColumns: [0,1,2,3,4,5]
        });

        $('.load_data').on('click', function(event) {
            event.preventDefault();
            $.ajax({
                url: 'get_business_information',
                type: 'GET',
                dataType: 'JSON',
            })
            .done(function(res) {
                appAlert.success(res.message, {duration: 10000});
                $('#whatsapp_template_list_table').DataTable().ajax.reload();
            });
        });
        
    });

</script>