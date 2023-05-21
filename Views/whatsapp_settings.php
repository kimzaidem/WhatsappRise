<div id="page-content" class="page-wrapper clearfix">
    <div class="row">
        <div class="col-sm-3 col-lg-2">
            <?php
            $tab_view['active_tab'] = "whatsapp_cloud_api";
            echo view("settings/tabs", $tab_view);
            ?>
        </div>

        <div class="col-sm-9 col-lg-10">
            <?php echo form_open(get_uri("whatsapp/save_whatsapp_settings"), array("id" => "whatsapp-settings-form", "class" => "general-form dashed-row", "role" => "form")); ?>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <!-- Icon -->
                            <span class="help" data-container="body" data-bs-toggle="tooltip" title="<?php echo app_lang('phone_number_id_description') ?>"><i data-feather="help-circle" class="icon-16"></i></span>
                             <!-- Over -->
                            <label for="whatsapp_phone_number_id" class=""><?php echo app_lang('phone_number_id'); ?></label>
                            <div class="">
                                <?php
                                    echo form_input(array(
                                        "id" => "whatsapp_phone_number_id",
                                        "name" => "whatsapp_phone_number_id",
                                        "value" => get_setting('whatsapp_phone_number_id'),
                                        "class" => "form-control",
                                        "data-rule-required" => true,
                                        "data-msg-required" => app_lang("field_required"),
                                    ));
                                ?>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <!-- Icon -->
                            <span class="help" data-container="body" data-bs-toggle="tooltip" title="<?php echo app_lang('business_account_id_description') ?>"><i data-feather="help-circle" class="icon-16"></i></span>
                             <!-- Over -->
                            <label for="whatsapp_business_account_id" class=""><?php echo app_lang('whatsapp_business_account_id'); ?></label>
                            <div class="">
                                <?php
                                    echo form_input(array(
                                        "id" => "whatsapp_business_account_id",
                                        "name" => "whatsapp_business_account_id",
                                        "value" => get_setting('whatsapp_business_account_id'),
                                        "class" => "form-control",
                                        "data-rule-required" => true,
                                        "data-msg-required" => app_lang("field_required"),
                                    ));
                                ?>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <!-- Icon -->
                            <span class="help" data-container="body" data-bs-toggle="tooltip" title="<?php echo app_lang('access_token_description') ?>"><i data-feather="help-circle" class="icon-16"></i></span>
                             <!-- Over -->
                            <label for="whatsapp_access_token" class=""><?php echo app_lang('whatsapp_access_token'); ?></label>
                            <div class="">
                                <?php
                                    echo form_input(array(
                                        "id" => "whatsapp_access_token",
                                        "name" => "whatsapp_access_token",
                                        "value" => get_setting('whatsapp_access_token'),
                                        "class" => "form-control",
                                        "data-rule-required" => true,
                                        "data-msg-required" => app_lang("field_required"),
                                    ));
                                ?>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="alert alert-warning">
                              <strong class="alert-heading fs-4"><?php echo app_lang('template_edit_note'); ?>!</strong>
                              <hr>
                              <p><?php echo app_lang('whatsapp_settings_access_token_note_description') ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
                </div>
            </div>

            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#whatsapp-settings-form").appForm({
            isModal: false,
            onSuccess: function (result) {
                if (result.success) {
                    appAlert.success(result.message, {duration: 10000});
                } else {
                    appAlert.error(result.message);
                }
            }
        });
    });
</script>