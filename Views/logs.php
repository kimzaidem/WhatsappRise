<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <div class="page-title clearfix rounded">
            <h1><?php echo app_lang('whatsapp_log'); ?></h1>
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
                {title: '<?php echo app_lang("whatsapp_action_name"); ?>'},
                {title: '<?php echo app_lang("reqest_url"); ?>'},
                {title: '<?php echo app_lang("active"); ?>', "class": "max-w500"},
                {title: '<?php echo app_lang("debug_mode"); ?>'},
                {title: "<i data-feather='menu' class='icon-16'></i>", "class": "text-center option "}
            ],
            printColumns: [0,1,2,3],
            xlsColumns: [0,1,2,3]
        });
    });
</script>