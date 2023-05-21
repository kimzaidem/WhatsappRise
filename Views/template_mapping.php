<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <div class="page-title clearfix rounded">
            <h1><?php echo app_lang('whatsapp_template_mapping'); ?></h1>
                <div class="title-button-group">
                    <a href="<?= get_uri("whatsapp/form"); ?>" class="btn btn-primary"><i data-feather='plus-circle' class='icon-16'></i> <?= app_lang('add'); ?></a>
                </div>
        </div>
        <div class="table-responsive">
            <table id="whatsapp-table" class="display" cellspacing="0" width="100%">
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    "use strict";
    $(document).ready(function () {
        $("#whatsapp-table").appTable({
            source: '<?php echo_uri("whatsapp/table"); ?>',
            columns: [
                {title: '<?php echo app_lang("template_name"); ?>'},
                {title: '<?php echo app_lang("category"); ?>'},
                {title: '<?php echo app_lang("send_to"); ?>'},
                {title: '<?php echo app_lang("active"); ?>', "class": "max-w500"},
                {title: '<?php echo app_lang("debug_mode"); ?>'},
                {title: '<?php echo app_lang("actions"); ?>'},
            ],
            printColumns: [0,1,2],
            xlsColumns: [0,1,2]
        });

        $(document).on('change', '#active', function(event) {
            event.preventDefault();
            var id = $(this).data('id');
            var status = 0;
            if ($(this).is(':checked')) {
                status = 1;
            }
            $.ajax({
                url: '<?= get_uri('whatsapp/change_status_active/'); ?>'+id,
                type: 'POST',
                dataType: 'json',
                data: {'status':status},
            })
            .done(function(response) {
                if (response.success==true) {
                    appAlert.success(response.message, {duration: 10000});
                }
            })
        });

        $(document).on('change', '#debug_mode', function(event) {
            event.preventDefault();
            var id = $(this).data('id');
            var debug_mode = 0;
            if ($(this).is(':checked')) {
                debug_mode = 1;
            }
            $.ajax({
                url: '<?= get_uri('whatsapp/change_debug_mode/'); ?>'+id,
                type: 'POST',
                dataType: 'json',
                data: {'debug_mode':debug_mode},
            })
            .done(function(response) {
                if (response.success==true) {
                    appAlert.success(response.message, {duration: 10000});
                }
            })
        });

    });
</script>