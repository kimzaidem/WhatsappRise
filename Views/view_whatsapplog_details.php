<style type="text/css">
    table { white-space: nowrap; }
</style>
<div id="page-content" class="page-wrapper clearfix">
   <div class="container">
       <div class="row">
           <div class="col-md-6">
               <div>
                    <div class="card">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="page-title clearfix rounded">
                                    <h1><?php echo app_lang('request_details') ?></h1>
                                </div>                            
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12" style="overflow-x: hidden;overflow: scroll;">
                                    <?php if (!is_null($log_data)): ?>
                                        <table class="table table-striped table-condensed table-hover" style="overflow-x: hidden;overflow: scroll;">
                                            <tr>
                                                <td><?= app_lang('action'); ?></td>
                                                <td><?= $log_data->message_category ?></td>
                                            </tr>
                                            <tr>
                                                <td><?= strtoupper(app_lang('date')); ?></td>
                                                <td><?= $log_data->recorded_at ?></td>
                                            </tr>
                                            <tr>
                                                <td><?= app_lang('total_parameters'); ?></td>
                                                <td><?= $log_data->category_params ?></td>
                                            </tr>
                                        </table>
                                    <?php endif ?>
                                </div>
                            </div>    
                        </div>
                    </div>
               </div>
           </div>
           <div class="col-md-6">
              <div>
                  <div class="card">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="page-title clearfix rounded">
                                <h1><?php echo app_lang('headers') ?></h1>
                            </div>                            
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12" style="overflow-x: hidden;overflow: scroll;">
                               <?php if (null !== $log_data) { ?>
                                    <table class="table table-striped table-condensed table-hover" style="overflow-x: hidden;overflow: scroll;">
                                        <tr>
                                            <td><?php echo app_lang('phone_number_id'); ?></td>
                                            <td><?php echo $log_data->phone_number_id; ?></td>
                                        </tr>
                                        <tr>
                                            <td><?php echo app_lang('whatsapp_business_account_id'); ?></td>
                                            <td><?php echo $log_data->business_account_id; ?></td>
                                        </tr>
                                        <tr>
                                            <td><?php echo app_lang('whatsapp_access_token'); ?></td>
                                            <td><?php echo $log_data->access_token; ?></td>
                                        </tr>
                                    </table>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
              </div> 
           </div>
       </div>
       <div class="row">
            <div class="col-md-6">
               <div>
                    <div class="card">
                        <div class="page-title clearfix rounded">
                            <div class="row">
                                <div class="col-md-6">
                                    <h1><?php echo app_lang('raw_content') ?></h1>    
                                </div>
                                <div class="col-md-6 text-right">
                                    <?php if (!is_null($log_data)): ?>
                                        <span class="badge bg-primary me-5 mt-4"><?= app_lang('format_type')." : JSON"?></span>
                                    <?php endif ?>
                                </div>                          
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <?php if (null !== $log_data) { ?>
                                    <p>
                                        <pre><code class="language-json"><?php echo json_encode(json_decode(html_entity_decode($log_data->send_json)), \JSON_PRETTY_PRINT); ?></code></pre>
                                    </p>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
               </div>
            </div>
            <div class="col-md-6">
                <div>
                    <div class="card">

                    <div class="page-title clearfix rounded">
                        <div class="row">
                            <div class="col-md-6">
                                <h1><?php echo app_lang('Response') ?></h1>
                            </div>
                            <div class="col-md-6 text-right">
                                <?php if (!is_null($log_data)): ?>
                                    <span class="badge bg-primary me-5 mt-4"><?= app_lang('response_code')." : ".$log_data->response_code ?></span>
                                <?php endif ?>
                            </div>                          
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <?php if (!is_null($log_data)): ?>
                                <p>
                                    <?php if ((isset($log_data->response_data)) && (isJson(html_entity_decode($log_data->response_data)))): ?>
                                        <pre><code class="language-json"><?php echo json_encode(json_decode(html_entity_decode($log_data->response_data)), JSON_PRETTY_PRINT); ?></code></pre>
                                    <?php endif ?>

                                    <?php if (isset($log_data->response_data) && isXml(html_entity_decode($log_data->response_data))) : ?>
                                        <pre><code class="language-xml"><?php  print_r($log_data->response_data); ?></code></pre>
                                    <?php endif ?>
                                </p>
                            <?php endif ?>
                        </div>
                    </div>

                </div>
                </div>
            </div>
       </div>
   </div> 
</div>