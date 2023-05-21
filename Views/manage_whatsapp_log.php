<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <div class="page-title clearfix rounded">
            <h1><?php echo app_lang('whatsapp_log_details'); ?></h1>
            <div class="title-button-group">
                <a href="<?= get_uri("whatsapplog/clear_log"); ?>" title="" class="btn btn-primary"><?= app_lang('clear_log'); ?></a>
            </div>
        </div>
        <div class="table-responsive">
            <table id="whatsapp_log_table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    "use strict";
    $(document).ready(function () {
        $("#whatsapp_log_table").appTable({
            source: '<?php echo_uri("whatsapplog/table"); ?>',
            columns: [
                {title: '<?php echo app_lang("template_name"); ?>'},
                {title: '<?php echo app_lang("response_code") ?>'},
                {title: '<?php echo app_lang("recorded_on"); ?>'},
                {title: "<i data-feather='menu' class='icon-16'></i>", "class": "text-center option "}
            ],
            printColumns: [0,1,2,3],
            xlsColumns: [0,1,2,3]
        });
    });
</script>