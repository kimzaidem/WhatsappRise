<?php if (!empty($template_info)) { ?>

<pre class="header_data_text d-none" data-text="<?php echo $template_info->header_data_text; ?>"><?php echo $template_info->header_data_text; ?></pre>
<pre class="body_data d-none" data-text="<?php echo $template_info->body_data; ?>"><?php echo $template_info->body_data; ?></pre>
<pre class="footer_data d-none" data-text="<?php echo $template_info->footer_data; ?>"><?php echo $template_info->footer_data; ?></pre>

<div class="card">
	<div class="card-body">
		<div class="row">
			<div class="col-md-6"><b><?php echo app_lang("template_category") ?>:</b> <?php echo $template_info->category; ?></div>
			<div class="col-md-6"><b><?php echo app_lang("template_type") ?>:</b> <?php echo (empty($template_info->header_data_format)) ? "TEXT" : $template_info->header_data_format; ?></div>
		</div>
	</div>
</div>

<!-- Header parameters template -->
<?php if ($template_info->header_params_count > 0) { ?>
	<div class="card">
		<div class="card-body">
			<div class="row">
				<div class="col-md-12">
					<span class="help" data-container="body" data-bs-toggle="tooltip" title="<?php echo app_lang('tootltip_template_headers') ?>"><i data-feather="help-circle" class="icon-16"></i></span>
					<label for="header_params" class="control-label" data-bs-toggle="tooltip" data-bs-title="Default tooltip">
						<b><?php echo app_lang('template_header_params'); ?></b>
					</label>
				</div>
			</div>
			<div class="row">
				<?php
	                if (isset($template_info->header_params)) {
	                    $header_params = (array) (json_decode($template_info->header_params));
	                }
	                ?>
					<?php for ($i = 1; $i <= $template_info->header_params_count; $i++) { ?>
					<div class="col-md-4 col-xs-6 mt-3">
						<label for="header_params[<?=$i?>][key]" class="col-md-2"><?php echo app_lang('key'); ?></label>
						<?php
							echo form_input(array(
	                            "id" => "header_params[".$i."][key]",
	                            "name" => "header_params[".$i."][key]",
	                            "value" => "{{".$i."}}",
	                            "class" => "form-control",
	                            "readonly" => 'true',
	                            "data-rule-required" => true,
	                            "data-msg-required" => app_lang("field_required"),
	                        ));
						?>
					</div>
					<div class="col-md-8 col-xs-6 mt-3">
						<label for="header_params[<?=$i?>][value]" class="col-md-2"><?php echo app_lang('value'); ?></label>
						<?php
							echo form_input(array(
	                            "id" => "header_params[".$i."][value]",
	                            "name" => "header_params[".$i."][value]",
	                            "value" => $header_params[$i]->value ?? "",
	                            "class" => "form-control header_param_text mentionable",
	                            "data-rule-required" => true,
	                            "data-msg-required" => app_lang("field_required"),
	                        ));
						?>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
<?php } ?>

<!-- Body parameters template  -->
<?php if ($template_info->body_params_count > 0) { ?>
	<div class="card">
		<div class="card-body">
			<div class="row">
				<div class="col-md-12">
					<span class="help" data-container="body" data-bs-toggle="tooltip" title="<?php echo app_lang('tootltip_template_body') ?>"><i data-feather="help-circle" class="icon-16"></i></span>
					<label for="body_params" class="control-label">
						<b><?php echo app_lang('template_body_params'); ?></b>
					</label>
				</div>
			</div>
			<div class="row">
				<?php
	                if (isset($template_info->body_params)) {
	                    $body_params = (array) (json_decode($template_info->body_params));
	                }
	                ?>
					<?php for ($i = 1; $i <= $template_info->body_params_count; $i++) { ?>
					<div class="col-md-4 col-xs-6 mt-3">
						<label for="body_params[<?=$i?>][key]" class="col-md-2"><?php echo app_lang('key'); ?></label>
						<?php
							echo form_input(array(
	                            "id" => "body_params[".$i."][key]",
	                            "name" => "body_params[".$i."][key]",
	                            "value" => "{{".$i."}}",
	                            "class" => "form-control",
	                            "readonly" => 'true',
	                            "data-rule-required" => true,
	                            "data-msg-required" => app_lang("field_required"),
	                        ));
						?>
					</div>
					<div class="col-md-8 col-xs-6 mt-3">
						<label for="body_params[<?=$i?>][value]" class="col-md-2"><?php echo app_lang('value'); ?></label>
						<?php
							echo form_input(array(
	                            "id" => "body_params[".$i."][value]",
	                            "name" => "body_params[".$i."][value]",
	                            "value" => $body_params[$i]->value ?? "",
	                            "class" => "form-control body_param_text mentionable",
	                            "data-rule-required" => true,
	                            "data-msg-required" => app_lang("field_required"),
	                        ));
						?>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
<?php } ?>

<!-- Footer parameters template  -->
<?php if ($template_info->footer_params_count > 0) { ?>
	<div class="card">
		<div class="card-body">
			<div class="row">
				<div class="col-md-12">
					<span class="help" data-container="body" data-bs-toggle="tooltip" title="<?php echo app_lang('tootltip_template_footer') ?>"><i data-feather="help-circle" class="icon-16"></i></span>
					<label for="footer_params" class="control-label">
						<i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo app_lang('tootltip_template_headers'); ?>" data-original-title="" title=""></i>
						<b><?php echo app_lang('template_footer_params'); ?></b>
					</label>
				</div>
			</div>
			<div class="row">
				<?php
	                if (isset($template_info->footer_params)) {
	                    $footer_params = (array) (json_decode($template_info->footer_params));
	                }
	                ?>
					<?php for ($i = 1; $i <= $template_info->footer_params_count; $i++) { ?>
					<div class="col-md-4 col-xs-6 mt-3">
						<label for="footer_params[<?=$i?>][key]" class="col-md-2"><?php echo app_lang('key'); ?></label>
						<?php
							echo form_input(array(
	                            "id" => "footer_params[".$i."][key]",
	                            "name" => "footer_params[".$i."][key]",
	                            "value" => "{{".$i."}}",
	                            "class" => "form-control",
	                            "readonly" => 'true',
	                            "data-rule-required" => true,
	                            "data-msg-required" => app_lang("field_required"),
	                        ));
						?>
					</div>
					<div class="col-md-8 col-xs-6 mt-3">
						<label for="footer_params[<?=$i?>][value]" class="col-md-2"><?php echo app_lang('value'); ?></label>
						<?php
							echo form_input(array(
	                            "id" => "footer_params[".$i."][value]",
	                            "name" => "footer_params[".$i."][value]",
	                            "value" => $footer_params[$i]->value ?? "",
	                            "class" => "form-control footer_param_text mentionable",
	                            "data-rule-required" => true,
	                            "data-msg-required" => app_lang("field_required"),
	                        ));
						?>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
<?php } ?>
<?php } ?>
