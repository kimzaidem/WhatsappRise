<?php
    use App\Controllers\Security_Controller;
    $ci = new Security_Controller(false);
    $user_info = $ci->login_user;
?>

<style type="text/css">
  .modal-content {
    background-color: transparent !important;
    -webkit-box-shadow: 0 0 0 0px rgb(0 0 0 / 0%), 0 0px 0px 0 rgb(0 0 0 / 0%) !important;
  }

  .modal-header {
    background-color: transparent !important;
    padding: 0px !important;
    border-bottom: 0px solid #e5e5e5 !important;
  }

  .header_data_text {
    font-weight: 700;
  }
</style>

<?php echo form_open(get_uri('whatsapp/save').(isset($template_info) ? "/".$template_info->id : ''),["id" => "whatsapp_form"]); ?>
<div id="page-content" class="page-wrapper clearfix">
    <?php if (isset($template_info)): ?>
        <div class="col-md-12">
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
              <strong class="alert-heading fs-4"><?php echo app_lang('template_edit_note'); ?>!</strong>
              <hr>
              <p><?php echo app_lang('template_edit_description') ?></p>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php endif ?>
    <div class="card">
        <div class="card-header">
            <h4><?php echo app_lang('add_new_template'); ?></h4>
        </div>
        <div class="card-body">
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label for="user" class="control-label"><?php echo app_lang('category'); ?></label>
                        <?php
                            echo form_dropdown([
                                "name" => "category",
                                "id"   => "category",
                                "class"=> "form-control",
                                "data-rule-required" => true,
                                "data-msg-required"  => app_lang("field_required"),
                            ], whts_get_whatsapp_triggers(),$template_info->category ?? '', (isset($template_info) ? ['disabled' => 'disabled'] : []));

                            if (isset($template_info)) {
                                echo form_hidden('category', $template_info->category);
                            }
                        ?>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="user" class="control-label"><?php echo app_lang('send_to'); ?></label>
                        <?php
                            echo form_dropdown([
                                "name" => "send_to",
                                "id"   => "send_to",
                                "class"=> "form-control",
                                "required" => true,
                                "data-rule-required" => true,
                                "data-msg-required"  => app_lang("field_required"),
                            ], whts_send_to_list(),$template_info->send_to ?? '', (isset($template_info) ? ['disabled' => 'disabled'] : []));
                        ?>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="user" class="control-label"><?php echo app_lang('template_name'); ?></label>
                        <?php
                            echo form_dropdown([
                                "name" => "template_name",
                                "id"   => "template_name",
                                "class"=> "form-control",
                                "data-rule-required" => true,
                                "data-msg-required"  => app_lang("field_required"),
                            ], whts_get_template_list(),$template_info->template_id ?? '', ['disabled' => 'disabled']);

                            if (isset($template_info)) {
                                echo form_hidden('template_name', $template_info->template_id);
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
        if (isset($template_info)) {
            echo view('Whatsapp\Views\mapping_form');
        } else {
    ?>
        <div class='alert alert-info' id="wa-info-message">
            Mobile Number of receiver should start with country code
        </div>
        <div class='alert alert-danger' id="wa-warning-message" style="display: none;">

        </div>
        <div id="template_map_info">
        </div>
    <?php
        }
    ?>

    <div class="card">
        <div class="card-body">
            <button type="button" id="preview_message" data-bs-toggle="modal" data-bs-target="#myModal" class="btn btn-warning text-white"><?php echo app_lang('preview'); ?></button>
            <input type="submit" value="Submit" class="btn btn-primary">
        </div>
    </div>
</div>

<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"></div>
            <div class="modal-body remove_padding">
                <div class="page">
                    <div class="marvel-device s5 black">
                        <div class="top-bar"></div>
                        <div class="sleep"></div>
                        <div class="volume"></div>
                        <div class="camera"></div>
                        <div class="screen">
                            <div class="screen-container">
                                <div class="status-bar">
                                    <div class="time"></div>
                                    <div class="battery">
                                        <i class="zmdi zmdi-battery"></i>
                                    </div>
                                    <div class="network">
                                        <i class="zmdi zmdi-network"></i>
                                    </div>
                                    <div class="wifi">
                                        <i class="zmdi zmdi-wifi-alt-2"></i>
                                    </div>
                                    <div class="star">
                                        <i class="zmdi zmdi-star"></i>
                                    </div>
                                </div>
                                <div class="chat">
                                    <div class="chat-container">
                                        <div class="user-bar">
                                            <div class="back">
                                                <i class="zmdi zmdi-arrow-left"></i>
                                            </div>
                                            <div class="avatar">
                                                <?php echo "<img alt='' src='".get_avatar($user_info->image)."' class='img img-responsive'>"; ?>
                                            </div>
                                            <div class="name">
                                                <span><?php echo app_lang('company_name') ?></span>
                                                <span class="status">online</span>
                                            </div>
                                            <div class="actions more">
                                                <i class="zmdi zmdi-more-vert"></i>
                                            </div>
                                            <div class="actions">
                                                <i class="zmdi zmdi-phone"></i>
                                            </div>
                                            <div class="actions">
                                                <i class="zmdi zmdi-videocam"></i>
                                            </div>
                                        </div>
                                        <div class="conversation">
                                            <div class="conversation-container">
                                                <div class="message sent">
                                                    <span class="header_data_text"><b></b></span><br><br>
                                                    <span class="body_data" style="white-space:pre-line"></span><br><br>
                                                    <span class="footer_data text-right" style="color: rgba(0, 0, 0, 0.45)"></span><br>
                                                    <span class="metadata">
                                                        <span class="time"> <?php echo date('h:i'); ?></span>
                                                        <span class="tick"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" id="msg-dblcheck-ack" x="2063" y="2076">
                                                            <path d="M15.01 3.316l-.478-.372a.365.365 0 0 0-.51.063L8.666 9.88a.32.32 0 0 1-.484.032l-.358-.325a.32.32 0 0 0-.484.032l-.378.48a.418.418 0 0 0 .036.54l1.32 1.267a.32.32 0 0 0 .484-.034l6.272-8.048a.366.366 0 0 0-.064-.512zm-4.1 0l-.478-.372a.365.365 0 0 0-.51.063L4.566 9.88a.32.32 0 0 1-.484.032L1.892 7.77a.366.366 0 0 0-.516.005l-.423.433a.364.364 0 0 0 .006.514l3.255 3.185a.32.32 0 0 0 .484-.033l6.272-8.048a.365.365 0 0 0-.063-.51z" fill="#4fc3f7" />
                                                        </svg></span>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="conversation-compose">
                                                <div class="emoji">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" id="smiley" x="3147" y="3209">
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M9.153 11.603c.795 0 1.44-.88 1.44-1.962s-.645-1.96-1.44-1.96c-.795 0-1.44.88-1.44 1.96s.645 1.965 1.44 1.965zM5.95 12.965c-.027-.307-.132 5.218 6.062 5.55 6.066-.25 6.066-5.55 6.066-5.55-6.078 1.416-12.13 0-12.13 0zm11.362 1.108s-.67 1.96-5.05 1.96c-3.506 0-5.39-1.165-5.608-1.96 0 0 5.912 1.055 10.658 0zM11.804 1.01C5.61 1.01.978 6.034.978 12.23s4.826 10.76 11.02 10.76S23.02 18.424 23.02 12.23c0-6.197-5.02-11.22-11.216-11.22zM12 21.355c-5.273 0-9.38-3.886-9.38-9.16 0-5.272 3.94-9.547 9.214-9.547a9.548 9.548 0 0 1 9.548 9.548c0 5.272-4.11 9.16-9.382 9.16zm3.108-9.75c.795 0 1.44-.88 1.44-1.963s-.645-1.96-1.44-1.96c-.795 0-1.44.878-1.44 1.96s.645 1.963 1.44 1.963z" fill="#7d8489" />
                                                    </svg>
                                                </div>
                                                <input readonly class="input-msg" name="input" placeholder="Type a message" autocomplete="off" autofocus></input>
                                                <div class="photo">
                                                    <i class="zmdi zmdi-camera"></i>
                                                </div>
                                                <button class="send">
                                                    <div class="circle">
                                                        <i class="zmdi zmdi-mail-send"></i>
                                                    </div>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo form_close(); ?>

<script type="text/javascript">

    $(document).ready(function() {

        "use strict";

        $('#category, #send_to, #template_name').select2({
            placeholder: 'Select an option'
        });

        $('body').on('change', '#category', function(event) {
            $('#send_to').find("option[value='staff']").attr('disabled', false);
            $('#send_to').find("option[value='contact']").attr('disabled', false);
            if (["client", "proposals"].indexOf($(this).val()) !== -1) {
                $('#send_to').find("option[value='staff']").attr('disabled', true);
            }
            if (["leads"].indexOf($(this).val()) !== -1) {
                $('#send_to').find("option[value='contact']").attr('disabled', true);
            }
        });

        $('body').on('change', '#category, #send_to', function(event) {
            event.preventDefault();
            if (($("#category").val() != '' && $("#send_to").val() != '') && ($('#category').prop('disabled') !== true)) {
                if($("#category").val() == "invoice" && $("#send_to").val() == "staff"){
                    $("#wa-warning-message").html("Invoice will be only sent to staff if invoice is related to project").show();
                }
                $('#template_name').attr('disabled', false);
            } else {
                $('#template_name').attr('disabled', true);
            }
        });

        $('#template_name').on('change', function(event) {
            event.preventDefault();
            var template_id = $(this).val();
            var category = $('#category').val();

            /*
             *  0 means nothing selected
             *  1 means hello world template
            */
            if (template_id == '' || template_id == 1) {
                $('#preview_message').hide();
            } else {
                $('#preview_message').show();
            }
            $.ajax({
                url: 'get_template_map',
                type: 'POST',
                dataType: 'html',
                data: {
                    'template_id': template_id,
                    'category': category
                },
            })
            .done(function(response) {

                $('#template_map_info').html(response);
                $('.card-body button, .card-body :input').prop("disabled", false);
                if($('#template_map_info').children(".alert").length){
                    $('.card-body button, .card-body :input').prop("disabled", true);
                }
                refreshTribute();
            })
        });

        $("#preview_message").click(function() {
            var header_data_text = $(".header_data_text").data("text");
            var body_data = $(".body_data").data("text");
            var footer_data = $(".footer_data").data("text");
            console.log(header_data_text);

            var count = 1;
            $(".header_param_text").each(function() {
                header_data_text = header_data_text.replace("{{" + count + "}}", ($(this).val() != "") ? $(this).val() : `{{${count}}}`)
                count++;
            });

            var count = 1;
            $(".body_param_text").each(function() {
                body_data = body_data.replace("{{" + count + "}}", ($(this).val() != "") ? $(this).val() : `{{${count}}}`)
                count++;
            });

            var count = 1;
            $(".footer_param_text").each(function() {
                footer_data = footer_data.replace("{{" + count + "}}", ($(this).val() != "") ? $(this).val() : `{{${count}}}`)
                count++;
            });

            $("#myModal .page .header_data_text").text(header_data_text);
            $("#myModal .page .body_data").text(body_data);
            $("#myModal .page .footer_data").text(footer_data);
        });

        $("#whatsapp_form").appForm({
            onSuccess: function (response) {
                window.location.href = "<?php echo get_uri('whatsapp'); ?>";
            }
        });

    });

</script>