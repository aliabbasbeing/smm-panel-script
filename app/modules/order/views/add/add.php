<!-- jQuery (Required for Select2) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    var $jq = jQuery.noConflict();  // Save jQuery in a different variable
</script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<?php
$currency_symbol = isset($currency_symbol) ? $currency_symbol : get_option('currency_symbol', "$");
$user_role = isset($user_role) ? $user_role : 'user';
$whatsapp_number_exists = isset($whatsapp_number_exists) ? $whatsapp_number_exists : false;
$dashboard_data = isset($dashboard_data) ? $dashboard_data : array();
$module = isset($module) ? $module : 'order';
$categories = isset($categories) ? $categories : array();
$services = isset($services) ? $services : array();
$service_item_default = !empty($services) ? $services[0] : null;
?>

<?php if (get_code_part('new_order','') != '') { ?>
<div class="col-sm-12">
  <div class="row">
    <div class="col-sm-12">
      <?=get_code_part('new_order','')?>
    </div>
  </div>
</div>
<?php }?>

<div class="container-cards">
<?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-<?= $_SESSION['message_type'] ?> mt-3">
        <?= $_SESSION['message'] ?>
    </div>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>

<style>
.cat-icon-filter-bar {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
  gap: 12px;
  margin: 0 0 15px 0;
  justify-content: flex-start;
  width: 100%;
}

.catf-btn {
  flex: none;
  box-sizing: border-box;
  position: relative;
  border: none;
  color: #333;
  padding: 12px 16px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
  white-space: nowrap;
  background: linear-gradient(135deg, #ffffff 0%, #f5f5f5 100%);
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08),
              0 1px 3px rgba(0, 0, 0, 0.05),
              inset 0 1px 0 rgba(255, 255, 255, 0.8);
  text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
  position: relative;
  overflow: hidden;
}

.catf-btn::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.4) 0%, rgba(255, 255, 255, 0) 50%);
  pointer-events: none;
  border-radius: 12px;
}

.catf-btn i { 
  font-size: 16px; 
  line-height: 1;
  z-index: 2;
  position: relative;
}

. catf-btn span {
  pointer-events: none;
  z-index: 2;
  position: relative;
}

.catf-btn:hover {
  background: linear-gradient(135deg, #ffffff 0%, #f0f0f0 100%);
  box-shadow: 0 8px 20px rgba(0, 86, 179, 0.15),
              0 1px 3px rgba(0, 0, 0, 0.08),
              inset 0 1px 0 rgba(255, 255, 255, 0.9);
  /* transform: translateY(-2px */
  color: #0056b3;
}

. catf-btn:active {
  box-shadow: 0 2px 8px rgba(0, 86, 179, 0.15),
              inset 0 2px 4px rgba(0, 0, 0, 0.1);
  /* transform: translateY(0); */
}

/* ACTIVE STATE - 3D EFFECT */
.catf-btn.active {
  background: linear-gradient(135deg, #0066cc 0%, #0056b3 100%);
  color: #fff;
  box-shadow: 0 12px 28px rgba(0, 86, 179, 0.35),
              0 4px 12px rgba(0, 86, 179, 0.25),
              inset 0 -2px 4px rgba(0, 0, 0, 0.15),
              inset 0 1px 0 rgba(255, 255, 255, 0.3);
  border: 1px solid rgba(0, 86, 179, 0.3);
  position: relative;
  z-index: 10;
}

.catf-btn.active::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0) 50%);
  pointer-events: none;
  border-radius: 12px;
}

.catf-btn. active i {
  color: #fff;
  filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
}

.catf-btn.active:hover {
  background: linear-gradient(135deg, #0073cc 0%, #005ba3 100%);
  box-shadow: 0 16px 32px rgba(0, 86, 179, 0.4),
              0 6px 16px rgba(0, 86, 179, 0.3),
              inset 0 -2px 4px rgba(0, 0, 0, 0.2),
              inset 0 1px 0 rgba(255, 255, 255, 0.3);
  /* transform: translateY(-3px); */
}

. catf-btn.active:active {
  box-shadow: 0 4px 12px rgba(0, 86, 179, 0.25),
              inset 0 2px 4px rgba(0, 0, 0, 0.15);
  transform: translateY(-1px);
}

.catf-btn:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(0, 86, 179, 0. 2),
              0 4px 15px rgba(0, 0, 0, 0.08);
}

/* Tablet: 2 per row */
@media (max-width: 900px) {
  . cat-icon-filter-bar {
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
  }

  . catf-btn {
    font-size: 13px;
    padding: 11px 14px;
    border-radius: 11px;
  }
}

/* Mobile: 2 per row */
@media (max-width: 600px) {
  .cat-icon-filter-bar {
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
  }

  .catf-btn {
    font-size: 12px;
    padding: 10px 12px;
    border-radius: 10px;
    gap: 6px;
  }

  .catf-btn i { 
    font-size: 15px; 
  }

  .catf-btn span {
    font-size: 12px;
  }

  .catf-btn:hover {
    /* transform: translateY(-1px); */
  }

  . catf-btn.active:hover {
    /* transform: translateY(-2px); */
  }
}

/* Extra small screens: 2 per row */
@media (max-width: 380px) {
  .cat-icon-filter-bar {
    grid-template-columns: repeat(2, 1fr);
    gap: 6px;
  }

  . catf-btn {
    font-size: 11px;
    padding: 8px 10px;
    gap: 4px;
    border-radius: 9px;
  }

  . catf-btn i { 
    font-size: 14px; 
  }

  . catf-btn:hover {
    /* transform: translateY(-1px); */
  }

  .catf-btn.active:hover {
    /* transform: translateY(-1px); */
  }
}

.announcement-container {
    margin: 20px auto !important;
    padding: 20px !important;
    color: #ffffff !important;
    border: 0.5px solid #0e4c75 !important;
    border-radius: 15px !important;
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.4) !important;
    text-align: center !important;
}

.announcement-heading {
    font-size: 1.4rem !important;
    font-weight: 800 !important;
    color: #00c4ff !important;
    text-transform: uppercase !important;
    margin-bottom: 20px !important;
}

.announcement-body {
    font-size: 1rem !important;
    font-weight: 500 !important;
    text-align: justify !important;
    word-spacing: -1px !important;
    line-height: 1.5 !important;
    padding: 10px !important;
    margin: 0 !important;
}

.emoji-highlight {
    font-size: 1.5rem !important;
    vertical-align: middle !important;
    margin-right: 10px !important;
}

.page-title h1{
    margin-bottom: 5px; 
}

.page-title .border-line {
  height: 5px;
  width: 250px;
  background: #eca28d;
  background: -webkit-linear-gradient(45deg, #eca28d, #f98c6b) !important;
  background: -moz-linear-gradient(45deg, #eca28d, #f98c6b) !important;
  background: -o-linear-gradient(45deg, #eca28d, #f98c6b) !important;
  background: linear-gradient(45deg, #eca28d, #f98c6b) !important;
  position: relative;
  border-radius: 30px; 
}

.page-title .border-line::before {
  content: '';
  position: absolute;
  left: 0;
  top: -2.7px;
  height: 10px;
  width: 10px;
  border-radius: 50%;
  background: #fa6d7e;
  -webkit-animation-duration: 6s;
  animation-duration: 6s;
  -webkit-animation-timing-function: linear;
  animation-timing-function: linear;
  -webkit-animation-iteration-count: infinite;
  animation-iteration-count: infinite;
  -webkit-animation-name: moveIcon;
  animation-name: moveIcon; 
}

@-webkit-keyframes moveIcon {
  from {
    -webkit-transform: translateX(0);
  }
  to { 
    -webkit-transform: translateX(250px);
  }
}

.text-lbl{
  margin-top: 0px;
  margin-bottom: 2px;
  font-weight: 550;
  color: #fff;
}

label{
  color: #fff;
}
</style>

<!-- Announcement from Database -->
<?php 
$announcement_text = get_option('new_order_text', '');
if (!empty($announcement_text)): 
?>
<div class="col-sm-12">
  <div class="row">
    <div class="card announcement-container">
      <h1 class="announcement-heading">
        Announcement <span class="emoji-highlight">📢</span>
      </h1>
      <div class="announcement-body">
        <?= $announcement_text ?>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>



<!-- Modal for Updating WhatsApp Number -->
<div id="whatsappUpdateModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title">Update WhatsApp Number</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <!-- <span aria-hidden="true">&times;</span> -->
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <h5 class="card-title"></h5>
                    <p class="card-text">We noticed you haven't added a WhatsApp number. Please update it.</p>
                    
                    <form id="whatsappUpdateForm" action="<?= cn("$module/update_whatsapp_number") ?>" method="POST">
                        <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
                        
                        <div class="form-group">
                            <label for="whatsapp_number">WhatsApp Number</label>
                            <input type="text" id="whatsapp_number" name="whatsapp_number" class="form-control" placeholder="+923XXXXXXXXX" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


</div>

<!-- MAIN ORDER FORM SECTION -->
<div class="row m-t-5">
  <div class="col-sm-12 col-lg-12">
    <div class="row">
      <div class="col-sm-7 col-lg-7 item">
        <div class="card" style="border: none; border-radius: 12px; background: #003a74; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15); overflow: hidden;">
           <!-- Card Header -->
          <div class="card-header1" style="padding: 20px 30px; border-bottom: 2px solid #04a9f4; display: flex; align-items: center; justify-content: space-between;">
            <h4 style="color: #fff !important; margin: 0; font-weight: 700; font-size: 18px; letter-spacing: 0.5px;">
              <i class="fa fa-shopping-cart" style="margin-right: 10px; color: #04a9f4;"></i><?=lang("place_a_new_order")?>
            </h4>
          </div>

          <div class="d-flex align-items-center">
            <div class="tabs-list"></div>
          </div>
          <div class="card-body" style="padding: 30px;">
            <div class="tab-content">
              <div id="new_order" class="tab-pane fade in active show">
                <form class="form actionForm" action="<?=cn($module."/ajax_add_order")?>" data-redirect="<?=cn($module."/order/add")?>" method="POST">

                  <div class="row">
                    <div class="">
                      <div class="form-group">
                        <!-- Platform Filter Bar -->
                        <div id="category-icon-filters" class="cat-icon-filter-bar">
                          <button type="button" class="catf-btn active" data-platform="all">
                            <i class="fa fa-bars"></i><span>All</span>
                          </button>
                          <button type="button" class="catf-btn" data-platform="tiktok">
                            <i class="fa-brands fa-tiktok"></i><span>TikTok</span>
                          </button>
                          <button type="button" class="catf-btn" data-platform="youtube">
                            <i class="fa-brands fa-youtube"></i><span>Youtube</span>
                          </button>
                          <button type="button" class="catf-btn" data-platform="instagram">
                            <i class="fa-brands fa-instagram"></i><span>Instagram</span>
                          </button>
                          <button type="button" class="catf-btn" data-platform="telegram">
                            <i class="fa-brands fa-telegram"></i><span>Telegram</span>
                          </button>
                          <button type="button" class="catf-btn" data-platform="facebook">
                            <i class="fa-brands fa-facebook"></i><span>Facebook</span>
                          </button>
                          <button type="button" class="catf-btn" data-platform="twitter">
                            <i class="fa-brands fa-x-twitter"></i><span>Twitter</span>
                          </button>
                          <button type="button" class="catf-btn" data-platform="whatsapp">
                            <i class="fa-brands fa-whatsapp"></i><span>Whatsapp</span>
                          </button>
                          <button type="button" class="catf-btn" data-platform="snapchat">
                            <i class="fa-brands fa-snapchat"></i><span>Snapchat</span>
                          </button>
                          <button type="button" class="catf-btn" data-platform="linkedin">
                            <i class="fa-brands fa-linkedin"></i><span>Linkedin</span>
                          </button>
                          <button type="button" class="catf-btn" data-platform="other">
                            <i class="fa fa-plus"></i><span>Other</span>
                          </button>
                        </div>

                        <!-- Category Select -->
                        <div class="form-group category-select-wrapper" style="margin-bottom: 20px;">
                          <label for="dropdowncategories" style="color: #fff; font-weight: 600; margin-bottom: 8px; display: block;"><?=lang("Category")?></label>
                          <select id="dropdowncategories"
                                  name="category_id"
                                  class="form-control square ajaxChangeCategory"
                                  data-url="<?=cn($module."/get_services/")?>"
                                  style="border-radius: 6px; border: 1px solid #ddd; padding: 10px; background-color: #fff; color: #333;">
                            <option value="" disabled selected hidden><?=lang("choose_a_category")?></option>
                            <?php if (!empty($categories)):
                              foreach ($categories as $c): ?>
                                <option value="<?=$c->id?>"><?=$c->name?></option>
                              <?php endforeach; 
                            endif; ?>
                          </select>
                        </div>

                        <!-- Service Select -->
                        <div class="form-group" id="result_onChange" style="margin-bottom: 20px;">
                          <label style="color: #fff; font-weight: 600; margin-bottom: 8px; display: block;"><?=lang("order_service")?></label>
                          <select id="dropdownservices" name="service_id" class="form-control square ajaxChangeService" data-url="<?=cn($module."/get_service/")?>" style="border-radius: 6px; border: 1px solid #ddd; padding: 10px; background-color: #fff; color: #333;">
                            <option><?=lang("choose_a_service")?></option>
                            <?php
                              if (!empty($services)) {
                                foreach ($services as $key => $service) {
                            ?>
                            <option value="<?=$service->id?>"><?=$service->name?></option>
                            <?php }}?>
                          </select>
                        </div>

                        <!-- Order Resume Section -->
                        <div id="order_resume" style="background: rgba(255, 255, 255, 0.08); padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                          <div class="row" id="result_onChangeService">
                            
                            <!-- Service Name -->
                            <div class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom: 15px;">
                              <div class="form-group" style="margin: 0;">
                                <input type="hidden" name="service_id" id="service_id" value="<?=(!empty($service_item_default->id))? $service_item_default->id :''?>">
                                <input class="form-control223344 square" name="service_name" type="text" readonly style="border-radius: 6px; border: 1px solid #ddd; padding: 10px; background-color: #fff; color: #333;">
                              </div>
                            </div>   

                            <!-- Description -->
                            <div class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom: 15px;">
                              <div class="form-group" style="margin: 0;">
                                <label for="userinput8" style="color: #fff; font-weight: 600; margin-bottom: 8px; display: block;"><?=lang("Description")?></label>
                                <textarea 
                                  name="service_desc" 
                                  class="form-control square" 
                                  readonly
                                  style="padding: 10px; 
                                         min-height: 120px; 
                                         max-height: 300px; 
                                         overflow-y: auto; 
                                         border: 1px solid #ddd; 
                                         border-radius: 6px; 
                                         background: #fff;
                                         font-size: 14px; 
                                         color: #333; 
                                         line-height: 1.5;
                                         width: 100%; 
                                         resize: vertical;">
                                </textarea>
                              </div>
                            </div>

                            <!-- Min/Max/Price Table -->
                            <div class="col-md-12" style="margin-bottom: 15px;">
                              <div class="row" style="margin: -10px;">
                                <div class="col-md-6" style="padding: 10px;">
                                  <label style="color: #fff; font-weight: 600; margin-bottom: 8px; display: block;"><?=lang("minimum")?></label>
                                  <input class="form-control2233 square" name="service_min" type="text" readonly style="border-radius: 6px; border: 1px solid #ddd; padding: 10px; background-color: #fff; color: #333; width: 100%;">
                                </div>
                                <div class="col-md-6" style="padding: 10px;">
                                  <label style="color: #fff; font-weight: 600; margin-bottom: 8px; display: block;"><?=lang("maximum")?></label>
                                  <input class="form-control2233 square" name="service_max" type="text" readonly style="border-radius: 6px; border: 1px solid #ddd; padding: 10px; background-color: #fff; color: #333; width: 100%;">
                                </div>
                              </div>
                            </div>
                            
                            <!-- Price -->
                            <div class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom: 15px;">
                              <label style="color: #fff; font-weight: 600; margin-bottom: 8px; display: block;"><?=lang("price_per_1000")?></label>
                              <input class="form-control223344 square" name="service_price" type="text" readonly style="border-radius: 6px; border: 1px solid #ddd; padding: 10px; background-color: #fff; color: #333; width: 100%;">
                            </div>

                          </div>
                        </div>
                        
                        <!-- Link Input -->
                        <div class="form-group order-default-link" style="margin-bottom: 20px;">
                          <label style="color: #fff; font-weight: 600; margin-bottom: 8px; display: block;"><?=lang("Link")?></label>
                          <input class="form-control square" type="text" name="link" placeholder="https://" id="" style="border-radius: 6px; border: 1px solid #ddd; padding: 10px; background-color: #fff; color: #333;">
                        </div>

                        <!-- Quantity Input -->
                        <div class="form-group order-default-quantity" style="margin-bottom: 20px;">
                          <label style="color: #fff; font-weight: 600; margin-bottom: 8px; display: block;"><?=lang("Quantity")?></label>
                          <input class="form-control square ajaxQuantity" name="quantity" type="number" style="border-radius: 6px; border: 1px solid #ddd; padding: 10px; background-color: #fff; color: #333;">
                        </div>
                        
                        <!-- Comments -->
                        <div class="form-group order-comments d-none" style="margin-bottom: 20px;">
                          <label for="" style="color: #fff; font-weight: 600; margin-bottom: 8px; display: block;"><?=lang("Comments")?> <?php lang('1_per_line')?></label>
                          <textarea rows="8" name="comments" class="form-control square ajax_custom_comments" style="border-radius: 6px; border: 1px solid #ddd; padding: 10px; background-color: #fff; color: #333; resize: vertical;"></textarea>
                        </div> 

                        <!-- Comments Custom Package -->
                        <div class="form-group order-comments-custom-package d-none" style="margin-bottom: 20px;">
                          <label for="" style="color: #fff; font-weight: 600; margin-bottom: 8px; display: block;"><?=lang("Comments")?> <?php lang('1_per_line')?></label>
                          <textarea rows="8" name="comments_custom_package" class="form-control square" style="border-radius: 6px; border: 1px solid #ddd; padding: 10px; background-color: #fff; color: #333; resize: vertical;"></textarea>
                        </div>

                        <!-- Usernames -->
                        <div class="form-group order-usernames d-none" style="margin-bottom: 20px;">
                          <label for="" style="color: #fff; font-weight: 600; margin-bottom: 8px; display: block;"><?=lang("Usernames")?></label>
                          <input type="text" class="form-control input-tags" name="usernames" value="usenameA,usenameB,usenameC,usenameD" style="border-radius: 6px; border: 1px solid #ddd; padding: 10px; background-color: #fff; color: #333;">
                        </div>

                        <!-- Usernames Custom -->
                        <div class="form-group order-usernames-custom d-none" style="margin-bottom: 20px;">
                          <label for="" style="color: #fff; font-weight: 600; margin-bottom: 8px; display: block;"><?=lang("Usernames")?> <?php lang('1_per_line')?></label>
                          <textarea rows="8" name="usernames_custom" class="form-control square ajax_custom_lists" style="border-radius: 6px; border: 1px solid #ddd; padding: 10px; background-color: #fff; color: #333; resize: vertical;"></textarea>
                        </div>

                        <!-- Hashtags -->
                        <div class="form-group order-hashtags d-none" style="margin-bottom: 20px;">
                          <label for="" style="color: #fff; font-weight: 600; margin-bottom: 8px; display: block;"><?=lang("hashtags_format_hashtag")?></label>
                          <input type="text" class="form-control input-tags" name="hashtags" value="#goodphoto,#love,#nice,#sunny" style="border-radius: 6px; border: 1px solid #ddd; padding: 10px; background-color: #fff; color: #333;">
                        </div>

                        <!-- Single Hashtag -->
                        <div class="form-group order-hashtag d-none" style="margin-bottom: 20px;">
                          <label for="" style="color: #fff; font-weight: 600; margin-bottom: 8px; display: block;"><?=lang("Hashtag")?></label>
                          <input class="form-control square" type="text" name="hashtag" style="border-radius: 6px; border: 1px solid #ddd; padding: 10px; background-color: #fff; color: #333;">
                        </div>

                        <!-- Username -->
                        <div class="form-group order-username d-none" style="margin-bottom: 20px;">
                          <label for="" style="color: #fff; font-weight: 600; margin-bottom: 8px; display: block;"><?=lang("Username")?></label>
                          <input class="form-control square" name="username" type="text" style="border-radius: 6px; border: 1px solid #ddd; padding: 10px; background-color: #fff; color: #333;">
                        </div>   
                        
                        <!-- Mentions Media Likers -->
                        <div class="form-group order-media d-none" style="margin-bottom: 20px;">
                          <label for="" style="color: #fff; font-weight: 600; margin-bottom: 8px; display: block;"><?=lang("Media_Url")?></label>
                          <input class="form-control square" name="media_url" type="link" style="border-radius: 6px; border: 1px solid #ddd; padding: 10px; background-color: #fff; color: #333;">
                        </div>

                        <!-- Subscriptions Section -->
                        <div class="row order-subscriptions d-none" style="margin-bottom: 20px;">

                          <div class="col-md-6" style="margin-bottom: 15px;">
                            <div class="form-group" style="margin: 0;">
                              <label style="color: #fff; font-weight: 600; margin-bottom: 8px; display: block;"><?=lang("Username")?></label>
                              <input class="form-control square" type="text" name="sub_username" style="border-radius: 6px; border: 1px solid #ddd; padding: 10px; background-color: #fff; color: #333;">
                            </div>
                          </div>

                          <div class="col-md-6" style="margin-bottom: 15px;">
                            <div class="form-group" style="margin: 0;">
                              <label style="color: #fff; font-weight: 600; margin-bottom: 8px; display: block;"><?=lang("New_posts")?></label>
                              <input class="form-control square" type="number" placeholder="<?=lang("minimum_1_post")?>" name="sub_posts" style="border-radius: 6px; border: 1px solid #ddd; padding: 10px; background-color: #fff; color: #333;">
                            </div>
                          </div>

                          <div class="col-md-6" style="margin-bottom: 15px;">
                            <div class="form-group" style="margin: 0;">
                              <label style="color: #fff; font-weight: 600; margin-bottom: 8px; display: block;"><?=lang("Quantity")?></label>
                              <input class="form-control square" type="number" name="sub_min" placeholder="<?=lang("min")?>" style="border-radius: 6px; border: 1px solid #ddd; padding: 10px; background-color: #fff; color: #333;">
                            </div>
                          </div>
                          
                          <div class="col-md-6" style="margin-bottom: 15px;">
                            <div class="form-group" style="margin: 0;">
                              <label style="color: #fff; font-weight: 600; margin-bottom: 8px; display: block;">&nbsp;</label>
                              <input class="form-control square" type="number" name="sub_max" placeholder="<?=lang("max")?>" style="border-radius: 6px; border: 1px solid #ddd; padding: 10px; background-color: #fff; color: #333;">
                            </div>
                          </div>

                          <div class="col-md-6" style="margin-bottom: 15px;">
                            <div class="form-group" style="margin: 0;">
                              <label style="color: #fff; font-weight: 600; margin-bottom: 8px; display: block;"><?=lang("Delay")?> (<?=lang("minutes")?>)</label>
                              <select name="sub_delay" class="form-control square" style="border-radius: 6px; border: 1px solid #ddd; padding: 10px; background-color: #fff; color: #333;">
                                <option value="0"><?=lang("")?><?=lang("No_delay")?></option>
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="15">15</option>
                                <option value="30">30</option>
                                <option value="60">60</option>
                                <option value="90">90</option>
                              </select>
                            </div>
                          </div>

                          <div class="col-md-6" style="margin-bottom: 15px;">
                            <div class="form-group" style="margin: 0;">
                              <label style="color: #fff; font-weight: 600; margin-bottom: 8px; display: block;"><?=lang("Expiry")?></label>
                              <div class="input-group">
                                <input type="text" class="form-control datepicker" name="sub_expiry" onkeydown="return false" placeholder="" id="expiry" style="border-radius: 6px; border: 1px solid #ddd; padding: 10px; background-color: #fff; color: #333;">
                                <span class="input-group-append">
                                  <button class="btn btn-info" type="button" onclick="document.getElementById('expiry').value = ''" style="border-radius: 0 6px 6px 0; padding: 8px 12px;"><i class="fe fe-trash-2"></i></button>
                                </span>
                              </div>
                            </div>
                          </div>

                        </div>

                        <!-- Drip Feed Option -->
                        <?php
                          if (get_option("enable_drip_feed","") == 1) {
                        ?>
                        <div class="row drip-feed-option d-none" style="margin-bottom: 20px;">
                          <div class="col-md-12">
                            <div class="form-group" style="margin: 0;">
                              <div class="form-label" style="color: #fff; font-weight: 600; margin-bottom: 12px; display: flex; align-items: center; justify-content: space-between;">
                                <span><?=lang("dripfeed")?></span>
                                <label class="custom-switch">
                                  <span class="custom-switch-description m-r-20"><i class="fa fa-question-circle" data-toggle="popover" data-trigger="hover" data-placement="right" data-content="<?=lang("drip_feed_desc")?>" data-title="<?=lang("what_is_dripfeed")?>" style="cursor: help;"></i></span>

                                  <input type="checkbox" name="is_drip_feed" class="is_drip_feed custom-switch-input" data-toggle="collapse" data-target="#drip-feed" aria-expanded="false" aria-controls="drip-feed">
                                  <span class="custom-switch-indicator"></span>
                                </label>
                              </div>

                              <div class="row collapse" id="drip-feed" style="margin: -10px;">
                                <div class="col-md-6" style="padding: 10px;">
                                  <div class="form-group" style="margin: 0;">
                                    <label style="color: #fff; font-weight: 600; margin-bottom: 8px; display: block;"><?=lang("Runs")?></label>
                                    <input class="form-control square ajaxDripFeedRuns" type="number" name="runs" value="<?=get_option("default_drip_feed_runs", "")?>" style="border-radius: 6px; border: 1px solid #ddd; padding: 10px; background-color: #fff; color: #333;">
                                  </div>
                                </div>
                                <div class="col-md-6" style="padding: 10px;">
                                  <div class="form-group" style="margin: 0;">
                                    <label style="color: #fff; font-weight: 600; margin-bottom: 8px; display: block;"><?=lang("interval_in_minutes")?></label>
                                    <select name="interval" class="form-control square" style="border-radius: 6px; border: 1px solid #ddd; padding: 10px; background-color: #fff; color: #333;">
                                      <?php
                                        for ($i = 1; $i <= 60; $i++) {
                                          if ($i%10 == 0) {
                                      ?>
                                      <option value="<?=$i?>" <?=(get_option("default_drip_feed_interval", "") == $i)? "selected" : ''?>><?=$i?></option>
                                      <?php }} ?>
                                    </select>
                                  </div>
                                </div>
                                <div class="col-md-12" style="padding: 10px;">
                                  <div class="form-group" style="margin: 0;">
                                    <label style="color: #fff; font-weight: 600; margin-bottom: 8px; display: block;"><?=lang("total_quantity")?></label>
                                    <input class="form-control square" name="total_quantity" type="number" disabled style="border-radius: 6px; border: 1px solid #ddd; padding: 10px; background-color: #f5f5f5; color: #999;">
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <?php }?>

                        <!-- Total Charge -->
                        <div class="form-group" id="result_total_charge" style="margin-bottom: 20px;">
                          <input type="hidden" name="total_charge" value="0.00">
                          <input type="hidden" name="currency_symbol" value="<?=$currency_symbol?>">
                          <br>
                          <center>
                            <p class="btn btn-info2 total_charge" style="display: flex;
    justify-content: space-between;
    align-items: center;
    background: linear-gradient(135deg, #0056b3 0%, #003d82 100%);
    padding: 14px 20px;
    border-radius: 10px;
    box-shadow: 
      0 6px 16px rgba(0, 86, 179, 0.25),
      0 2px 6px rgba(0, 0, 0, 0.08),
      inset 0 1px 0 rgba(255, 255, 255, 0.15);
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.12);
    position: relative;
    overflow: hidden;">
                              <?=lang("total_charge")?> <span class="charge_number" style="color: #ffffffff; font-weight: 700;"><?=$currency_symbol?> 0</span>
                            </p>
                          </center>
                          
                          <?php
                            $user = $this->model->get("balance, custom_rate", USERS, ['id' => session('uid')]);
                            if ($user && $user->custom_rate > 0) {
                          ?>
                          <p class="small text-muted" style="color: rgba(255, 255, 255, 0.7); margin-top: 10px; margin-bottom: 0; font-size: 12px;"><?=lang("custom_rate")?>: <span class="charge_number" style="color: #fff;"><?=$user->custom_rate?>%</span></p>
                          <?php }?>
                          <div class="alert alert-icon alert-danger d-none" role="alert" style="border-radius: 6px; margin-top: 10px; padding: 12px; border: 1px solid #dc3545;">
                            <i class="fe fe-alert-triangle mr-2" aria-hidden="true"></i><?=lang("order_amount_exceeds_available_funds")?>
                          </div>
                        </div>

                        <!-- Agreement Checkbox -->
                        <div class="form-group" style="margin-bottom: 20px;">
                          <label class="custom-control custom-checkbox" style="display: flex; align-items: center; cursor: pointer;">
                            <input type="checkbox" class="custom-control-input" name="agree" style="cursor: pointer;">
                            <span class="custom-control-label text-uppercase" style="color: #fff; margin-left: 8px; font-size: 13px; font-weight: 500;"><?=lang("yes_i_have_confirmed_the_order")?></span>
                          </label>
                        </div>

                        <!-- Submit Button -->
                        <div class="form-actions left">
                          <button type="submit" class="btn round btn-primary btn-min-width mr-1 mb-1" style="border-radius: 8px; background: linear-gradient(135deg, #04a9f4 0%, #0288d1 100%); color: #fff; min-width: 140px; margin-right: 5px; margin-top: 10px; margin-bottom: 5px; padding: 12px 24px; border: none; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 8px rgba(4, 169, 244, 0.3);">
                            <?=lang("place_order")?>
                          </button>
                        </div>

                      </div>
                    </div>
                  </div>
                  </div>
                

                        <!-- Mass Order Tab -->
                        <div id="rules" class="tab-pane fade">
                            <form class="form actionForm" action="<?=cn($module."/ajax_mass_order")?>" data-redirect="<?=cn($module."/log")?>" method="POST">
                                <div class="x_content row">
                                    <?=get_option('order_rules','')?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>

      <!-- Right Column - Updates -->
<div class="col-sm-5 col-lg-5 item">
    <div class="card" style="border: none; border-radius: 12px; background: #003a74; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15); overflow: hidden;">
        
        <!-- Card Header -->
        <div class="card-header1" style="padding: 20px 30px; border-bottom: 2px solid #04a9f4; display: flex; align-items: center; justify-content: space-between;">
            <h4 style="color: #fff !important; margin: 0; font-weight: 700; font-size: 18px; letter-spacing: 0.5px;">
                <i class="fa fa-bell" style="margin-right: 10px; color: #04a9f4;"></i>Updates
            </h4>
            <span class="badge" style="background: #04a9f4; color: #fff; padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 600;">NEW</span>
        </div>

        <div class="card-body video-section">
  <div class="tabs-list">
    <ul class="nav nav-tabs video-tabs">
      <li class="video-item">
        <!-- YouTube Embed Section -->
        <div class="video-wrapper">
          <!-- Video Title -->
          <h5 class="video-title">
            <i class="fa fa-play-circle"></i>HOW TO PLACE ORDER ON BEAST SMM PANEL? 
          </h5>

          <!-- YouTube Embed with Enhanced Styling -->
          <div class="video-container">
            <iframe
              src="https://www.youtube.com/embed/Y_nv63RWNXQ"
              title="YouTube Shorts - How to Place Order on Beast SMM Panel"
              frameborder="0"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
              allowfullscreen
              class="video-iframe"
            ></iframe>
          </div>

          <!-- Watch Button -->
          <div class="video-button-wrapper">
            <a href="https://www.youtube.com/embed/Y_nv63RWNXQ" target="_blank" class="btn btn-watch">
              <i class="fa fa-youtube"></i>Watch on YouTube
            </a>
          </div>
        </div>
      </li>
    </ul>
  </div>
</div>

<style>
  .video-section {
    padding: 30px 20px;
    /* background-color: #ffffffff; */
  }

  .tabs-list {
    width: 100%;
  }

  .video-tabs {
    border: none;
    margin-bottom: 20px;
    list-style: none;
    padding: 0;
    display: flex;
    justify-content: center;
  }

  .video-item {
    list-style: none;
    width: 100%;
  }

  .video-wrapper {
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  /* Video Title */
  .video-title {
    color: #ffffffff !important;
    margin-bottom: 20px;
    font-weight: 600;
    font-size: 16px;
    letter-spacing: 0.3px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
  }

  . video-title i {
    color: #04a9f4;
    font-size: 18px;
  }

  /* Video Container - Gradient Border */
  .video-container {
    display: inline-block;
    padding: 8px;
    border-radius: 24px;
    background: linear-gradient(135deg, #05cbfd 0%, #203d9d 100%);
    box-shadow: 0 8px 20px rgba(4, 169, 244, 0. 3);
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    width: 100%;
    max-width: 400px;
  }

  .video-container:hover {
    box-shadow: 0 12px 28px rgba(4, 169, 244, 0.4);
    transform: translateY(-4px);
  }

  /* Video iFrame */
  .video-iframe {
    border-radius: 20px;
    background: #fff;
    border: none;
    display: block;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    width: 100%;
    max-width: 100%;
    aspect-ratio: 9 / 16;
  }

  /* Watch Button Wrapper */
  .video-button-wrapper {
    margin-top: 25px;
    display: flex;
    justify-content: center;
    width: 100%;
  }

  /* Watch Button */
  .btn-watch {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    background: linear-gradient(135deg, #04a9f4 0%, #0288d1 100%);
    color: #fff;
    padding: 12px 30px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(4, 169, 244, 0.35);
    white-space: nowrap;
  }

  .btn-watch:hover {
    background: linear-gradient(135deg, #0288d1 0%, #01579b 100%);
    box-shadow: 0 6px 16px rgba(4, 169, 244, 0.45);
    transform: translateY(-2px);
  }

  .  btn-watch:active {
    transform: translateY(0);
    box-shadow: 0 2px 8px rgba(4, 169, 244, 0.3);
  }

  . btn-watch i {
    font-size: 16px;
  }

  /* Tablet Responsive */
  @media (max-width: 768px) {
    .video-section {
      padding: 25px 15px;
    }

    .video-title {
      font-size: 15px;
      margin-bottom: 18px;
    }

    . video-container {
      padding: 6px;
      max-width: 350px;
    }

    . video-iframe {
      aspect-ratio: 9 / 16;
    }

    .btn-watch {
      padding: 11px 25px;
      font-size: 13px;
    }
  }

  /* Mobile Responsive */
  @media (max-width: 600px) {
    .video-section {
      padding: 20px 12px;
    }

    . video-title {
      font-size: 14px;
      margin-bottom: 16px;
      gap: 8px;
    }

    .video-title i {
      font-size: 16px;
    }

    .video-container {
      padding: 5px;
      max-width: 100%;
      border-radius: 20px;
    }

    . video-iframe {
      border-radius: 18px;
      aspect-ratio: 9 / 16;
    }

    .video-button-wrapper {
      margin-top: 20px;
    }

    .btn-watch {
      padding: 10px 20px;
      font-size: 12px;
      gap: 8px;
    }

    .btn-watch i {
      font-size: 14px;
    }
  }

  /* Small Mobile */
  @media (max-width: 480px) {
    .video-section {
      padding: 18px 10px;
    }

    .video-title {
      font-size: 13px;
      margin-bottom: 14px;
    }

    .video-container {
      padding: 4px;
      max-width: 100%;
      box-shadow: 0 6px 16px rgba(4, 169, 244, 0.25);
    }

    .video-container:hover {
      box-shadow: 0 8px 20px rgba(4, 169, 244, 0.3);
      transform: translateY(-2px);
    }

    .video-iframe {
      border-radius: 16px;
    }

    .btn-watch {
      padding: 9px 18px;
      font-size: 11px;
      width: 100%;
      max-width: 280px;
    }

    . btn-watch i {
      font-size: 13px;
    }
  }

  /* Extra Small Mobile */
  @media (max-width: 380px) {
    .video-section {
      padding: 16px 8px;
    }

    .video-title {
      font-size: 12px;
      margin-bottom: 12px;
    }

    .video-container {
      padding: 3px;
    }

    .video-iframe {
      border-radius: 14px;
    }

    . btn-watch {
      padding: 8px 16px;
      font-size: 11px;
      width: 100%;
    }
  }
</style>

<!-- Add hover effect style -->
<style>
    .video-container:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 28px rgba(4, 169, 244, 0.4) !important;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(4, 169, 244, 0.4) !important;
    }
</style>          <div class="card-body" style="max-height: calc(80vh - 180px); overflow-y: scroll;">
                    <div class="tab-content">
                        <div id="Manual_Payments" class="tab-pane fade in active show">
                            <div class="content">
                                <?=get_option('updates_text')?>
                            </div> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
  </div>
</div>

<!-- ALL JAVASCRIPT -->
<script>
  // Initialize datepicker and selectize
  $(function(){
    $('.datepicker').datepicker({
      format: "dd/mm/yyyy",
      autoclose: true,
      startDate: truncateDate(new Date())
    });
    $(".datepicker").datepicker().datepicker("setDate", new Date());

    function truncateDate(date) {
      return new Date(date.getFullYear(), date.getMonth(), date.getDate());
    }

    $('.input-tags').selectize({
        delimiter: ',',
        persist: false,
        create: function (input) {
            return {
                value: input,
                text: input
            }
        }
    });
  });

  // Store order data and show confirmation modal
  $(document).ready(function() {
    $('.actionForm').on('submit', function() {
      var orderData = {
        service_name: $('select[name="service_id"] option:selected').text(),
        link: $('input[name="link"]').val(),
        quantity: $('input[name="quantity"]').val(),
        total_charge: $('input[name="total_charge"]').val()
      };
      
      localStorage.setItem('orderData', JSON.stringify(orderData));
    });

    function getShortServiceName(serviceName) {
      const lowerServiceName = serviceName.toLowerCase();
      const platforms = ['tiktok', 'instagram', 'youtube', 'facebook', 'twitter'];
      const actions = ['views', 'likes', 'followers', 'subscribers', 'comments', 'shares'];
      
      let shortServiceName = '';
      
      const foundPlatform = platforms.find(platform => lowerServiceName.includes(platform));
      const foundAction = actions.find(action => lowerServiceName.includes(action));

      if (foundPlatform && foundAction) {
        shortServiceName = `${foundPlatform.charAt(0).toUpperCase() + foundPlatform.slice(1)} ${foundAction.charAt(0).toUpperCase() + foundAction.slice(1)}`;
      } else {
        shortServiceName = serviceName;
      }

      return shortServiceName;
    }
    
    var savedOrderData = localStorage.getItem('orderData');
    if (savedOrderData) {
        savedOrderData = JSON.parse(savedOrderData);
        var shortServiceName = getShortServiceName(savedOrderData.service_name);
        
        var currencySymbol = '<?=$currency_symbol?>';
        
        var summaryContent = `
          <div class="order-summary p-4" style="background-color: ; color: #ecf0f1; animation: fadeIn 1s ease-in-out;">
            <div class="text-center mb-4">
              <i class="fa fa-check-circle bounce-icon" style="font-size: 60px; color: #2ecc71; animation: bounce 1.5s infinite;"></i>
            </div>
            <h5 class="text-center mb-3" style="color: #000000ff; font-size: 24px;">Order Confirmation</h5>
            <div class="order-details">
              <p><i class="fa fa-bell" style="color: #2ecc71; margin-right: 8px;"></i><strong style="color: #000000ff;">Service Name:</strong> <span style="color: #000000ff;">${shortServiceName}</span></p>
              <p><i class="fa fa-link" style="color: #2ecc71; margin-right: 8px;"></i><strong style="color: #000000ff;">Link:</strong> <span style="color: #000000ff;">${savedOrderData.link}</span></p>
              <p><i class="fa fa-long-arrow-up" aria-hidden="true" style="color: #2ecc71; margin-right: 8px;"></i><strong style="color: #000000ff;">Quantity:</strong> <span style="color: #000000ff;">${savedOrderData.quantity}</span></p>
              <p><i class="fa fa-usd" style="color: #2ecc71; margin-right: 8px;"></i><strong style="color: #000000ff;">Total Charge:</strong> <span style="color: #000000ff;">${currencySymbol} ${savedOrderData.total_charge}</span></p>
            </div>
          </div>

          <style>
            @keyframes fadeIn {
              from {
                opacity: 0;
                transform: translateY(20px);
              }
              to {
                opacity: 1;
                transform: translateY(0);
              }
            }

            @keyframes bounce {
              0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
              }
              40% {
                transform: translateY(-10px);
              }
              60% {
                transform: translateY(-5px);
              }
            }

            .bounce-icon {
              animation: bounce 2s infinite;
            }
          </style>
        `;

        $('#orderSummary').html(summaryContent);
        $('#orderConfirmationModal').modal('show'); 

        $('#closeModalButton').on('click', function() {
            localStorage.removeItem('orderData');
        });
    }
  });
</script>

<!-- Order Confirmation Modal -->
<div class="modal fade" id="orderConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="orderConfirmationModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="orderConfirmationModalLabel">Order Confirmation</h5>
      </div>
      <div class="modal-body">
        <div id="orderSummary"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" id="closeModalButton">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Vertical Image Modal -->
<?php
$show_vertical_image_modal = get_option('show_vertical_image_modal', 0);
$vertical_image_modal_url = get_option('vertical_image_modal_url', 'https://i.ibb.co/8LZvrpDK/file-000000006374622f80e6350155d31b37.png');
?>

<?php if ($show_vertical_image_modal && $vertical_image_modal_url): ?>
<style>
#verticalImageModal .modal-content {
  background: transparent;
  border: none;
  box-shadow: none;
  text-align: center;
  position: relative;
  padding: 0;
}

#verticalImageModal .modal-dialog {
  max-width: 95vw;
  width: auto;
  margin: 0 auto;
}

#verticalImageModal .image-wrapper {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 60vh;
  width: 100%;
  padding: 12px;
}

#verticalImageModal img {
  border-radius: 18px;
  width: 95vw;
  max-width: 100%;
  max-height: 95vh;
  height: auto;
  object-fit: cover;
  box-shadow: 0 2px 24px rgba(0,0,0,0.18);
  display: block;
  margin: 0 auto;
}

.vertical-modal-close {
  position: absolute;
  top: 12px;
  right: 12px;
  z-index: 1200;
  width: 44px;
  height: 44px;
  border-radius: 50%;
  border: none;
  background: rgba(0,0,0,0.6);
  color: #fff;
  font-size: 20px;
  line-height: 1;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  box-shadow: 0 4px 12px rgba(0,0,0,0.35);
  transition: transform .08s ease, background .08s ease;
  outline: none;
}

.vertical-modal-close:hover,
.vertical-modal-close:focus {
  transform: scale(1.04);
  background: rgba(0,0,0,0.75);
  color: #fff;
}

@media (max-width: 480px) {
  .vertical-modal-close {
    width: 48px;
    height: 48px;
    top: 10px;
    right: 10px;
  }
}
</style>

<div class="modal fade" id="verticalImageModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content p-0">
      <button type="button"
              class="vertical-modal-close"
              data-dismiss="modal"
              aria-label="Close vertical image modal">
        &times;
      </button>

      <div class="image-wrapper">
        <img src="<?= htmlspecialchars($vertical_image_modal_url) ?>" alt="Vertical Image" />
      </div>
    </div>
  </div>
</div>

<script>
  $(document).ready(function(){
    $('#verticalImageModal').modal('show');
  });
</script>
<?php endif; ?>

<?php if (!$whatsapp_number_exists): ?>
<script>
  $(document).ready(function(){
    $('#whatsappUpdateModal').modal('show');
  });
</script>
<?php endif; ?>

<!-- Platform Filter and Select2 Initialization -->
<script>
(function($){
  /* Platform Icon Helper */
  function pickPlatformIcon(text){
    if (!text) return '';
    var t = text.toLowerCase();

    if (t.includes('facebook'))  return 'img:https://storage.perfectcdn.com/etopvh/xk5ab1173935x41z.gif';
    if (t.includes('instagram')) return 'img:https://storage.perfectcdn.com/etopvh/r2726iff1gsgb78r.gif';
    if (t.includes('whatsapp'))  return 'img:https://i.ibb.co/846d9Whj/372108180-WHATSAPP-ICON-400.gif';
    if (t.includes('tiktok'))    return 'img:https://storage.perfectcdn.com/etopvh/p7pol1se08k6yc2x.gif';
    if (t.includes('youtube'))   return 'img:https://storage.perfectcdn.com/etopvh/duea6r011zfl9fo8.gif';
    if (t.includes('twitter') || t.match(/\bx\b/)) return 'img:https://storage.perfectcdn.com/etopvh/8d1btd44mgx8geie.gif';
    if (t.includes('snack'))     return 'img:https://i.ibb.co/rRzSFYtC/unnamed.png';
    if (t.includes('likee'))     return 'img:https://i.ibb.co/rRzSFYtC/unnamed.png';
    if (t.includes('linkedin'))  return 'img:https://i.ibb.co/KcX4v9Fb/372102050-LINKEDIN-ICON-TRANSPARENT-1080.gif';
    if (t.includes('snapchat'))  return 'img:https://i.ibb.co/23F0G4BY/images-7.jpg';

    if (t.includes('youtube'))   return 'fa-brands fa-youtube';
    if (t.includes('tiktok'))    return 'fa-brands fa-tiktok';
    if (t.includes('twitch'))    return 'fa-brands fa-twitch';
    if (t.includes('vimeo'))     return 'fa-brands fa-vimeo';
    if (t.includes('instagram')) return 'fa-brands fa-instagram';
    if (t.includes('twitter') || t.match(/\bx\b/)) return 'fa-brands fa-x-twitter';
    if (t.includes('linkedin'))  return 'fa-brands fa-linkedin';
    if (t.includes('snapchat'))  return 'fa-brands fa-snapchat';
    if (t.includes('pinterest')) return 'fa-brands fa-pinterest';
    if (t.includes('reddit'))    return 'fa-brands fa-reddit';
    if (t.includes('tumblr'))    return 'fa-brands fa-tumblr';
    if (t.includes('discord'))   return 'fa-brands fa-discord';
    if (t.includes('telegram'))  return 'fa-brands fa-telegram';
    if (t.includes('whatsapp')) return 'fa-brands fa-whatsapp';
    if (t.includes('messenger')) return 'fa-brands fa-facebook-messenger';
    if (t.includes('skype'))     return 'fa-brands fa-skype';
    if (t.includes('viber'))     return 'fa-brands fa-viber';
    if (t.includes('line'))      return 'fa-solid fa-comment';
    if (t.includes('slack'))     return 'fa-brands fa-slack';
    if (t.includes('teams'))     return 'fa-brands fa-microsoft';
    if (t.includes('zoom'))      return 'fa-solid fa-video';
    if (t.includes('wechat'))    return 'fa-brands fa-weixin';
    if (t.includes('weibo'))     return 'fa-brands fa-weibo';
    if (t.includes('qq'))        return 'fa-brands fa-qq';
    if (t.includes('spotify'))   return 'fa-brands fa-spotify';
    if (t.includes('soundcloud')) return 'fa-brands fa-soundcloud';
    if (t.includes('github'))    return 'fa-brands fa-github';
    if (t.includes('behance'))   return 'fa-brands fa-behance';
    if (t.includes('dribbble'))  return 'fa-brands fa-dribbble';
    if (t.includes('medium'))    return 'fa-brands fa-medium';
    if (t.includes('quora'))     return 'fa-brands fa-quora';
    if (t.includes('flickr'))    return 'fa-brands fa-flickr';
    if (t.includes('foursquare')) return 'fa-brands fa-foursquare';
    if (t.includes('tinder') || t.includes('bumble')) return 'fa-solid fa-heart';
    if (t.includes('vk') || t.includes('vkontakte')) return 'fa-brands fa-vk';
    if (t.includes('odnoklassniki')) return 'fa-brands fa-odnoklassniki';
    if (t.includes('xing'))      return 'fa-brands fa-xing';
    if (t.includes('live') || t.includes('stream')) return 'fa-solid fa-broadcast-tower';
    if (t.includes('video'))     return 'fa-solid fa-video';
    if (t.includes('photo') || t.includes('image')) return 'fa-solid fa-image';
    if (t.includes('music') || t.includes('audio')) return 'fa-solid fa-music';
    if (t.includes('podcast'))   return 'fa-solid fa-podcast';
    if (t.includes('blog'))      return 'fa-solid fa-blog';
    if (t.includes('news'))      return 'fa-solid fa-newspaper';
    if (t.includes('shopping') || t.includes('store')) return 'fa-solid fa-shopping-cart';
    if (t.includes('game') || t.includes('gaming')) return 'fa-solid fa-gamepad';

    return '';
  }

  function renderIconHtml(iconToken){
    if (!iconToken) return '';
    if (iconToken.indexOf && iconToken.indexOf('img:') === 0) {
      var url = iconToken.substring(4);
      return '<img src="' + url + '" alt="platform" class="cat-icon-img" style="width:18px;height:18px;vertical-align:middle;margin-right:8px;border-radius:3px;">';
    }
    return '<i class="' + iconToken + '" aria-hidden="true" style="margin-right:8px;"></i>';
  }

  function formatService(option) {
    if (!option.id) return option.text;
    var $opt = $(option.element);
    var name = $opt.data('name') || option.text;
    var rate = $opt.data('rate');
    var min  = $opt.data('min');
    var max  = $opt.data('max');
    var drip = ($opt.data('dripfeed') == 1);
    var meta = [];
    if (rate) meta.push('<?=$currency_symbol?> ' + rate);
    if (min)  meta.push('Min: ' + min);
    if (max)  meta.push('Max: ' + max);
    if (drip) meta.push('Drip');
    var iconToken = pickPlatformIcon(name);
    var iconHtml = renderIconHtml(iconToken);
    return $(
      '<div class="svc-item">'+
        iconHtml +
        '<strong>'+ $('<span>').text(name).html() +'</strong><br>'+
        '<span class="svc-meta">'+ meta.join(' | ') +'</span>'+
      '</div>'
    );
  }

  function formatServiceSelection (option) {
    if (!option.id) return option.text;
    var $opt = $(option.element);
    var name = $opt.data('name') || option.text;
    var rate = $opt.data('rate');
    var iconToken = pickPlatformIcon(name);
    var iconHtml = renderIconHtml(iconToken);
    var label = rate ? name + ' (<?=$currency_symbol?>' + rate + ')' : name;
    return $(
      '<span class="svc-sel">'+
        iconHtml +
        $('<span>').text(label).html()+
      '</span>'
    );
  }

  function categoryTemplate(option){
    if (!option.id) return option.text;
    var txt = option.text || '';
    var iconToken = pickPlatformIcon(txt);
    var iconHtml = renderIconHtml(iconToken);
    return $(
      '<span class="cat-opt">'+
        iconHtml +
        $('<span>').text(txt).html()+
      '</span>'
    );
  }

  function initCategorySelect() {
    var $cat = $('#dropdowncategories');
    if (!$cat.length) return;
    if ($cat.hasClass('select2-hidden-accessible')) $cat.select2('destroy');

    $cat.select2({
      width: '100%',
      templateResult: categoryTemplate,
      templateSelection: categoryTemplate,
      escapeMarkup: function(markup){ return markup; },
      dropdownParent: $cat.closest('.category-select-wrapper').length ?
                      $cat.closest('.category-select-wrapper') : $cat.parent(),
      dropdownCssClass: 'custom-dropdown-height',
      dropdownAutoWidth: false
    }).on('change', function(){
      loadServicesForCategory($(this).val());
    });
  }

  function initServiceSelect(ctx) {
    var $svc = (ctx) ? $(ctx).find('#dropdownservices') : $('#dropdownservices');
    if (!$svc.length) return;
    if ($svc.hasClass('select2-hidden-accessible')) $svc.select2('destroy');

    $svc.select2({
      width: '100%',
      dropdownParent: $svc.closest('.service-select-wrapper').length ?
                      $svc.closest('.service-select-wrapper') : $svc.parent(),
      placeholder: 'Choose a service',
      templateResult: formatService,
      templateSelection: formatServiceSelection,
      escapeMarkup: function(markup){ return markup; },
      allowClear: false,
      dropdownCssClass: 'custom-dropdown-height',
      dropdownAutoWidth: false,
      minimumResultsForSearch: 5
    }).on('change', function(){
      fetchServiceDetails($(this).val(), $(this).data('url'), $(this));
    });

    requestAnimationFrame(function(){
      $svc.next('.select2').css('width','100%');
    });
  }

  function loadServicesForCategory(categoryId){
    if (!categoryId) {
      $('#result_onChange').html(
        '<div class="service-select-wrapper">'+
          '<select id="dropdownservices" name="service_id" class="form-control square" data-url="<?=cn($module."/get_service/")?>">'+
            '<option value="">Choose a service</option>'+
          '</select>'+
        '</div>'
      );
      initServiceSelect('#result_onChange');
      resetServiceResume();
      return;
    }
    
    var url = $('#dropdowncategories').data('url');
    $.ajax({
      type: 'POST',
      url: url + categoryId,
      data: { token: (typeof token !== 'undefined') ? token : '' },
      success: function(html){
        $('#result_onChange').html(html);
        initServiceSelect('#result_onChange');
        resetServiceResume();
        
        setTimeout(function() {
          var $serviceDropdown = $('#result_onChange').find('#dropdownservices');
          if ($serviceDropdown.length) {
            var firstServiceOption = $serviceDropdown.find('option[value!=""]').first();
            if (firstServiceOption.length && firstServiceOption.val()) {
              console.log('Auto-selecting first service:', firstServiceOption.val());
              
              $serviceDropdown.val(firstServiceOption.val()).trigger('change');
              
              var serviceId = firstServiceOption.val();
              var baseUrl = $serviceDropdown.data('url');
              if (serviceId && baseUrl) {
                fetchServiceDetails(serviceId, baseUrl, $serviceDropdown);
              }
            }
          }
        }, 100);
        
      },
      error: function(xhr){
        console.error('Failed to load services', xhr.status, xhr.responseText);
        alert('Could not load services for this category.');
      }
    });
  }

  function fetchServiceDetails(serviceId, baseUrl, $select){
    if (!serviceId) { resetServiceResume(); return; }
    
       console.log('Fetching service details for ID:', serviceId);
    
    $.ajax({
      type: 'POST',
      url: baseUrl + serviceId,
      data: { token: (typeof token !== 'undefined') ? token : '' },
      success: function(fragment){
        $('#result_onChangeService').html(fragment);
        var price = $('#order_resume input[name=service_price]').val();
        var min   = $('#order_resume input[name=service_min]').val();
        var max   = $('#order_resume input[name=service_max]').val();
        if (price) $('#new_order input[name=service_price]').val(price);
        if (min)   $('#new_order input[name=service_min]').val(min);
        if (max)   $('#new_order input[name=service_max]').val(max);
        $('#service_id').val(serviceId);
        var $opt = $select.find('option:selected');
        applyServiceTypeUI($opt.data('type'), $opt.data('dripfeed'));
        
        console.log('Service details loaded successfully for ID:', serviceId);
      },
      error: function(xhr){
        console.error('Failed to fetch service details', xhr.status, xhr.responseText);
        alert('Failed to fetch service details.');
      }
    });
  }

  function resetServiceResume(){
    $('#order_resume input[name=service_name]').val('');
    $('#order_resume input[name=service_min]').val('');
    $('#order_resume input[name=service_max]').val('');
    $('#order_resume input[name=service_price]').val('');
    $('#order_resume textarea[name=service_desc]').val('');
    $('#service_id').val('');
    $('#result_onChangeService').html('');
  }

  function applyServiceTypeUI(type, drip){
    if (drip == 1) {
      $("#new_order .drip-feed-option").removeClass("d-none");
    } else {
      $("#new_order .drip-feed-option").addClass("d-none");
    }
  }

  /* =========================================================
     CATEGORY ICON FILTER BAR
  ========================================================== */
  var originalCategoryOptions = [];
  var categoryIndexed = false;

  function detectPlatform(txt){
    if(!txt) return 'other';
    var t = txt.toLowerCase();
    if (t.includes('tiktok')) return 'tiktok';
    if (t.includes('youtube') || t.includes('yt ')) return 'youtube';
    if (t.includes('insta')) return 'instagram';
    if (t.includes('telegram') || t.includes('tg ')) return 'telegram';
    if (t.includes('facebook') || t.includes('fb ')) return 'facebook';
    if (t.includes('twitter') || t.includes(' x ') || /\bx\b/.test(t)) return 'twitter';
    if (t.includes('whatsapp') || t.includes('wa ')) return 'whatsapp';
    if (t.includes('snap')) return 'snapchat';
    if (t.includes('linked')) return 'linkedin';
    return 'other';
  }

  function indexCategories(){
    if (categoryIndexed) return;
    var $cat = $('#dropdowncategories');
    originalCategoryOptions = [];
    $cat.find('option').each(function(){
      var $o = $(this);
      var val = $o.attr('value');
      if (!val) return;
      var label = $o.text();
      originalCategoryOptions.push({
        value: val,
        text: label,
        platform: detectPlatform(label)
      });
    });
    categoryIndexed = true;
  }

  function rebuildCategorySelect(platform){
    var $cat = $('#dropdowncategories');
    var dataUrl = $cat.data('url');
    var placeholderText = '<?=lang("choose_a_category")?>';

    var oldVal = $cat.val();

    $cat.empty();
    $cat.append('<option value="" disabled selected hidden>'+placeholderText+'</option>');

    var filtered = [];
    if (platform === 'all') {
      filtered = originalCategoryOptions;
    } else if (platform === 'other') {
      filtered = originalCategoryOptions.filter(o => o.platform === 'other');
    } else {
      filtered = originalCategoryOptions.filter(o => o.platform === platform);
    }

    filtered.forEach(function(o){
      $cat.append('<option value="'+o.value+'">'+o.text+'</option>');
    });

    if (dataUrl) $cat.attr('data-url', dataUrl);

    if ($cat.hasClass('select2-hidden-accessible')) {
      $cat.select2('destroy');
    }
    initCategorySelect();

    if (filtered.length){
      $cat.val(filtered[0].value).trigger('change');
    }
  }

  function attachFilterButtons(){
    var $bar = $('#category-icon-filters');
    if (!$bar.length) return;
    $bar.on('click', '.catf-btn', function(){
      var $btn = $(this);
      if ($btn.hasClass('active')) return;
      $bar.find('.catf-btn').removeClass('active');
      $btn.addClass('active');
      indexCategories();
      rebuildCategorySelect($btn.data('platform'));
    });
  }

  /* =========================================================
     BOOT
  ========================================================== */
  $(function(){
    initCategorySelect();
    initServiceSelect();
    
    var pre = $('#dropdownservices').val();
    if (pre) {
      fetchServiceDetails(pre, $('#dropdownservices').data('url'), $('#dropdownservices'));
    }

    setTimeout(function(){
      indexCategories();
      attachFilterButtons();
    }, 150);
  });

})(jQuery);
</script>