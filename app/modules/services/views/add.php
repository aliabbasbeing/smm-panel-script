<div class="page-header">
  <h1 class="page-title">
    <i class="fa fa-plus"></i> <?=lang("add_new_service")?>
  </h1>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="card p-0 content">
      <div class="card-header">
        <h3 class="card-title" style="color:#fff !important;"><?=lang("add_new_service")?></h3>
        <div class="card-options">
          <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
        </div>
      </div>
      <div class="card-body">
        <form class="form actionForm" action="<?=cn($module."/ajax_add")?>" data-redirect="<?=cn($module)?>" method="POST">
          <div class="form-body" id="add_service_form">
            <div class="row justify-content-md-center">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group emoji-picker-container">
                  <label ><?=lang("package_name")?></label>
                  <input type="text" data-emojiable="true" class="form-control square" name="name" value="">
                </div>
              </div>
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group">
                  <label><?=lang("choose_a_category")?></label>
                  <select  name="category" class="form-control square">
                    <?php if(!empty($categories)){
                      foreach ($categories as $key => $category) {
                    ?>
                    <option value="<?=$category->id?>"><?=$category->name?></option>
                   <?php }}?>
                  </select>
                </div>
              </div>
              
              <div class="col-md-12">
                <div class="form-group">
                  <div class="form-label"><?php echo lang("Type"); ?></div>
                  <div class="custom-controls-stacked">
                    <label class="custom-control custom-radio custom-control-inline">
                      <input type="radio" class="custom-control-input" name="add_type" value="manual" checked>
                      <span class="custom-control-label"><?php echo lang('Manual'); ?></span>
                    </label>
                    <label class="custom-control custom-radio custom-control-inline">
                      <input type="radio" class="custom-control-input" name="add_type" value="api">
                      <span class="custom-control-label"><?php echo lang('API'); ?></span>
                    </label>
                    
                  </div>
                </div>
              </div>

              <!-- API mode -->
              <div class="col-md-12 add-service-type d-none">
                <fieldset class="form-fieldset">
                  <div class="form-group">
                    <label><?php echo lang("api_provider_name"); ?></label>
                    <select name="api_provider_id" class="form-control square ajaxGetServicesFromAPIAdd" data-url="<?php echo cn($module.'/ajax_get_services_from_api/'); ?>">
                      <option value="0"> <?php echo lang('choose_a_api_provider'); ?></option>
                      <?php
                        if (!empty($api_providers)) {
                        foreach ($api_providers as $type => $api_provider) {
                      ?>
                      <option value="<?php echo strip_tags($api_provider->id)?>"><?php echo strip_tags($api_provider->name)?></option>
                      <?php }} ?>
                    </select>
                  </div>

                  <div class="form-group result-api-service-lists-add d-none">
                    <div class="dimmer">
                      <div class="loader"></div>
                      <div class="dimmer-content">
                        <label><?php echo lang('list_of_api_services'); ?></label>
                        <select name="api_service_id" class="form-control square">
                          <option value="0"> <?php echo lang('choose_a_service'); ?></option>
                        </select>
                        <input type="hidden" class="form-control square" name="api_service_id" value="">
                      </div>
                    </div>
                  </div>

                  <div class="row api-service-details-add d-none">
                    <div class="col-md-12">
                      <div class="form-group">
                        <label> Original Rate per 1000</label>
                        <input type="text" class="form-control square" name="original_price" value="" disabled>
                        <input type="hidden" class="form-control square" name="original_price" value="">

                        <input type="hidden" class="form-control square" name="api_service_type" value="default">

                        <input type="hidden" class="form-control square" name="api_service_dripfeed" value="">
                      </div>
                    </div>
                  </div>
                </fieldset>
              </div>
              <!-- Manual Mode -->
              <div class="col-md-12 add-service-manual-type">
                <fieldset class="form-fieldset">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Service type</label>
                        <select name="service_type" class="form-control square ajaxChangeServiceTypeAdd">
                          <?php
                            $service_type_array = all_services_type();
                            foreach ($service_type_array as $type => $service_type) {
                          ?>
                          <option value="<?=$type?>"><?=$service_type?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label><?=lang("dripfeed")?></label>
                        <select name="dripfeed" class="form-control square">
                          <option value="0"><?=lang("Deactive")?></option>
                          <option value="1"><?=lang("Active")?></option>
                        </select>
                      </div>
                    </div>
                  </div>
                </fieldset>
              </div>

              <div class="col-md-4 col-sm-6 col-xs-6">
                <div class="form-group">
                  <label><?=lang("minimum_amount")?></label>
                  <input type="number" class="form-control square" name="min" value="<?=get_option('default_min_order',"")?>">
                </div>
              </div>

              <div class="col-md-4 col-sm-6 col-xs-6">
                <div class="form-group">
                  <label><?=lang("maximum_amount")?></label>
                  <input type="number" class="form-control square" name="max" value="<?=get_option('default_max_order',"")?>">
                </div>
              </div>

              <div class="col-md-4 col-sm-6 col-xs-6">
                <div class="form-group">
                  <label><?=lang("rate_per_1000")?></label>
                  <input type="text" class="form-control square" name="price" value="<?=currency_format(get_option('default_price_per_1k',"0.80"),2)?>">
                </div>
              </div>

              <div class="col-md-12 col-sm-6 col-xs-6">
                <div class="form-group">
                  <label><?=lang("Status")?></label>
                  <select name="status" class="form-control square">
                    <option value="1"><?=lang("Active")?></option>
                    <option value="0"><?=lang("Deactive")?></option>
                  </select>
                </div>
              </div>

              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group">
                  <label><?=lang("Description")?></label>
                  <textarea rows="10" class="form-control square text-emoji-add" id="text-emoji-add" name="desc"></textarea>
                </div>
              </div>

              <div class="col-md-12 col-sm-12 col-xs-12 form-actions">
                <div class="p-l-10">
                  <a href="<?=cn("api_provider/services")?>" class="btn round btn-info btn-min-width mr-1 mb-1"><?=lang("add_new_service_via_api")?></a>
                  <button type="submit" class="btn round btn-primary btn-min-width mr-1 mb-1"><?=lang("Submit")?></button>
                  <a href="<?=cn($module)?>" class="btn round btn-default btn-min-width mr-1 mb-1"><?=lang("Cancel")?></a>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  // Check post type for Add Service page
  $(document).on("change", "#add_service_form input[type=radio][name=add_type]", function(){
    _that = $(this);
    _type = _that.val();
    if(_type == 'api'){
      $('.add-service-type').removeClass('d-none');
      $('.add-service-manual-type').addClass('d-none');
    }else{
      $('.add-service-manual-type').removeClass('d-none');
      $('.add-service-type').addClass('d-none');
    }
  });

  /*----------  Get Services list from API for Add Service  ----------*/
  $(document).on("change", ".ajaxGetServicesFromAPIAdd" , function(){

    event.preventDefault();
    $('.result-api-service-lists-add').removeClass('d-none');
    $('.result-api-service-lists-add .dimmer').addClass('active');
    var _that       = $(this),
        _id         = _that.val();
    if (_id == "" || _id == 0) {
        return;
    }
    var _action     = _that.data("url"),
        _token      = '<?php echo strip_tags($this->security->get_csrf_hash()); ?>',
        _data       = $.param({token:_token, api_id:_id});
    $.post( _action, _data,function(_result){
      setTimeout(function () {
        $('.api-service-details-add').removeClass('d-none');
        $(".api-service-details-add input[name=original_price]").val('');
        $(".api-service-details-add input[name=api_service_type]").val('');
        $(".api-service-details-add input[name=api_service_dripfeed]").val('');

        $('.result-api-service-lists-add .dimmer').removeClass('active');
        $(".result-api-service-lists-add .dimmer-content").html(_result);
      }, 100);
    });
  })  

  /*----------  Choose a service for Add Service  ----------*/
  $(document).on("change", "#add_service_form .ajaxGetServiceDetail", function(){
    
    $(".api-service-details-add input[name=original_price]").val('');
    $("#add_service_form input[name=min]").val('');
    $("#add_service_form input[name=max]").val('');

    var _that      = $('option:selected', this),
        _name      = _that.attr('data-name'),
        _min       = _that.attr('data-min'),
        _max       = _that.attr("data-max"),
        _rate      = _that.attr("data-rate"),
        _type      = _that.attr("data-type"),
        _dripfeed  = _that.attr("data-dripfeed");

    $(".api-service-details-add input[name=original_price]").val(_rate);
    $(".api-service-details-add input[name=api_service_type]").val(_type);
    $(".api-service-details-add input[name=api_service_dripfeed]").val(_dripfeed);

    $("#add_service_form input[name=min]").val(_min);
    $("#add_service_form input[name=max]").val(_max);
  })

  /*----------  Change service type for Add Service  ----------*/
  $(document).on("change", ".ajaxChangeServiceTypeAdd", function(){
    event.preventDefault();
    _that   = $(this);
    _type    = _that.val();
    switch(_type) {
      case "default":
        $("#add_service_form .dripfeed-form").removeClass("d-none");
        break;  
      default:
        $("#add_service_form .dripfeed-form").addClass("d-none");
        break;
    }
  })
</script>

<script>
  $(function() {
    window.emojiPickerAdd = new EmojiPicker({
      emojiable_selector: '[data-emojiable=true]',
      assetsPath: "<?=BASE?>assets/plugins/emoji-picker/lib/img/",
      popupButtonClasses: 'fa fa-smile-o'
    });
    window.emojiPickerAdd.discover();
  });
</script>

<script type="text/javascript">
  $(document).ready(function() {
    $(".text-emoji-add").emojioneArea({
      pickerPosition: "top",
      tonesStyle: "bullet"
    });
  });
</script>
