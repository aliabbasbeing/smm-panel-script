<style>
  .action-options{
    margin-left: auto;
  }  
  .dropdown-item.ajaxActionOptions{
    padding-top: 0px!important;
    padding-bottom: 0px!important;
  }
  /* Enhanced Services Page Styles */
  .services-filter-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
    margin-bottom: 20px;
  }
  .services-filter-card .card-header {
    background: transparent;
    border-bottom: 1px solid rgba(255,255,255,0.1);
  }
  .services-filter-card .card-header h3 {
    color: #fff !important;
  }
  .services-filter-card .card-body {
    padding: 20px;
  }
  .services-filter-card .form-control,
  .services-filter-card .btn {
    border-radius: 5px;
  }
  .filter-label {
    color: #fff;
    font-weight: 500;
    margin-bottom: 5px;
    font-size: 12px;
    text-transform: uppercase;
  }
  .stats-card {
    border-radius: 10px;
    transition: transform 0.2s ease;
  }
  .stats-card:hover {
    transform: translateY(-2px);
  }
  .stats-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
  }
  .per-page-select {
    width: 80px;
    display: inline-block;
  }
  .selected-count-badge {
    display: none;
    font-size: 14px;
  }
  .selected-count-badge.show {
    display: inline-block;
  }
  .services-loading {
    position: relative;
    min-height: 200px;
  }
  .services-loading::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255,255,255,0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
  }
  .services-loading .loader {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 11;
  }
  .quick-filter-btn {
    border-radius: 20px;
    padding: 5px 15px;
    font-size: 12px;
    margin-right: 5px;
    margin-bottom: 5px;
  }
  .quick-filter-btn.active {
    background: #667eea;
    border-color: #667eea;
    color: #fff;
  }
  .bulk-actions-panel {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 15px;
    display: none;
  }
  .bulk-actions-panel.show {
    display: block;
  }
  
  /* Custom Service Action Modal Styles */
  .service-action-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1060;
    overflow-x: hidden;
    overflow-y: auto;
    outline: 0;
  }
  .service-action-modal.show {
    display: block;
  }
  .service-action-modal .modal-backdrop1 {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    opacity: 0;
    transition: opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  }
  .service-action-modal.show .modal-backdrop1 {
    opacity: 1;
  }
  .service-action-modal .modal-container {
    position: relative;
    width: 100%;
    max-width: 700px;
    margin: 2rem auto;
    padding: 0 15px;
    transform: translateY(-50px) scale(0.95);
    opacity: 0;
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
  }
  .service-action-modal.show .modal-container {
    transform: translateY(0) scale(1);
    opacity: 1;
  }
  .service-action-modal .modal-content {
    position: relative;
    background: #ffffff;
    border: none;
    border-radius: 20px;
    box-shadow: 0 25px 80px rgba(0, 0, 0, 0.35), 0 10px 30px rgba(102, 126, 234, 0.3);
    overflow: hidden;
  }
  .service-action-modal .modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 25px 30px;
    border: none;
    position: relative;
    overflow: hidden;
  }
  .service-action-modal .modal-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
    animation: shimmer 3s ease-in-out infinite;
  }
  @keyframes shimmer {
    0%, 100% { transform: rotate(0deg); }
    50% { transform: rotate(5deg); }
  }
  .service-action-modal .modal-title {
    color: #ffffff;
    font-size: 1.5rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 12px;
    position: relative;
    z-index: 1;
    margin: 0;
  }
  .service-action-modal .modal-title i {
    background: rgba(255,255,255,0.2);
    padding: 10px;
    border-radius: 12px;
    font-size: 1.2rem;
  }
  .service-action-modal .modal-close {
    position: absolute;
    top: 20px;
    right: 20px;
    background: rgba(255,255,255,0.2);
    border: none;
    color: #ffffff;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    font-size: 1.3rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2;
  }
  .service-action-modal .modal-close:hover {
    background: rgba(255,255,255,0.35);
    transform: rotate(90deg) scale(1.1);
  }
  .service-action-modal .modal-body {
    padding: 30px;
    max-height: 60vh;
    overflow-y: auto;
  }
  .service-action-modal .modal-body::-webkit-scrollbar {
    width: 6px;
  }
  .service-action-modal .modal-body::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
  }
  .service-action-modal .modal-body::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 3px;
  }
  .service-action-modal .modal-footer {
    background: #f8f9fa;
    padding: 20px 30px;
    border-top: 1px solid #e9ecef;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
  }
  .service-action-modal .btn-modal-cancel {
    background: #6c757d;
    color: #ffffff;
    border: none;
    padding: 12px 28px;
    border-radius: 10px;
    font-weight: 500;
    transition: all 0.3s ease;
  }
  .service-action-modal .btn-modal-cancel:hover {
    background: #5a6268;
    transform: translateY(-2px);
  }
  .service-action-modal .btn-modal-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #ffffff;
    border: none;
    padding: 12px 28px;
    border-radius: 10px;
    font-weight: 500;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
  }
  .service-action-modal .btn-modal-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
  }
  .service-action-modal .modal-loader {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 60px 20px;
    text-align: center;
  }
  .service-action-modal .modal-loader .spinner {
    width: 50px;
    height: 50px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 20px;
  }
  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
  .service-action-modal .modal-loader p {
    color: #6c757d;
    font-size: 0.95rem;
  }
  
  /* Service Action Card Styles */
  .service-action-card {
    background: #ffffff;
    border: 2px solid #e9ecef;
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 15px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 15px;
  }
  .service-action-card:hover {
    border-color: #667eea;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
    transform: translateY(-3px);
  }
  .service-action-card.active {
    border-color: #667eea;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%);
  }
  .service-action-card .action-icon {
    width: 55px;
    height: 55px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
  }
  .service-action-card .action-icon.icon-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #ffffff;
  }
  .service-action-card .action-icon.icon-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: #ffffff;
  }
  .service-action-card .action-icon.icon-warning {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
    color: #ffffff;
  }
  .service-action-card .action-icon.icon-danger {
    background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);
    color: #ffffff;
  }
  .service-action-card .action-icon.icon-info {
    background: linear-gradient(135deg, #17a2b8 0%, #6610f2 100%);
    color: #ffffff;
  }
  .service-action-card .action-content {
    flex: 1;
  }
  .service-action-card .action-title {
    font-weight: 600;
    font-size: 1.05rem;
    color: #2d3436;
    margin-bottom: 4px;
  }
  .service-action-card .action-desc {
    color: #6c757d;
    font-size: 0.875rem;
    margin: 0;
  }
  .service-action-card .action-arrow {
    color: #dee2e6;
    font-size: 1.2rem;
    transition: all 0.3s ease;
  }
  .service-action-card:hover .action-arrow {
    color: #667eea;
    transform: translateX(5px);
  }
  
  /* Quick Action Button */
  .btn-quick-action {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #ffffff;
    border: none;
    padding: 10px 20px;
    border-radius: 10px;
    font-weight: 500;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
  }
  .btn-quick-action:hover {
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
  }
  .btn-quick-action i {
    margin-right: 8px;
  }
</style>
<br>
<?php if (get_option('services_text','') != '') { ?>
<div class="col-sm-12 col-sm-12">
  <div class="row">
    <div class="card">
      <div class="card-body">
        <?=get_option('services_text','')?>
      </div>
    </div>
  </div>
</div>
<?php }?>

<?php if (get_code_part('services','') != '') { ?>
<div class="col-sm-12">
  <div class="row">
    <div class="col-sm-12">
      <?=get_code_part('services','')?>
    </div>
  </div>
</div>
<?php }?>

<!-- Statistics Cards (Admin Only) -->
<?php if (get_role("admin") && isset($stats)): ?>
<div class="row mb-4">
  <div class="col-sm-6 col-lg-3">
    <div class="card stats-card">
      <div class="card-body d-flex align-items-center">
        <div class="stats-icon bg-primary text-white me-3">
          <i class="fe fe-package"></i>
        </div>
        <div>
          <div class="text-muted small text-uppercase"><?=lang("total_services")?></div>
          <div class="h4 mb-0"><?=number_format($stats->total)?></div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-lg-3">
    <div class="card stats-card">
      <div class="card-body d-flex align-items-center">
        <div class="stats-icon bg-success text-white me-3">
          <i class="fe fe-check-circle"></i>
        </div>
        <div>
          <div class="text-muted small text-uppercase"><?=lang("Active")?></div>
          <div class="h4 mb-0"><?=number_format($stats->active)?></div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-lg-3">
    <div class="card stats-card">
      <div class="card-body d-flex align-items-center">
        <div class="stats-icon bg-warning text-white me-3">
          <i class="fe fe-x-circle"></i>
        </div>
        <div>
          <div class="text-muted small text-uppercase"><?=lang("Inactive")?></div>
          <div class="h4 mb-0"><?=number_format($stats->inactive)?></div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-lg-3">
    <div class="card stats-card">
      <div class="card-body d-flex align-items-center">
        <div class="stats-icon bg-info text-white me-3">
          <i class="fe fe-cloud"></i>
        </div>
        <div>
          <div class="text-muted small text-uppercase"><?=lang("api_services")?></div>
          <div class="h4 mb-0"><?=number_format($stats->api)?></div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- Advanced Filters Card -->
<div class="card services-filter-card">
  <div class="card-header">
    <h3 class="card-title"><i class="fe fe-filter me-2"></i><?=lang("filters_and_search")?></h3>
    <div class="card-options">
      <a href="#" class="card-options-collapse text-white" data-bs-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
    </div>
  </div>
  <div class="card-body">
    <form id="services-filter-form">
      <div class="row">
        <!-- Search -->
        <div class="col-md-4 col-lg-3 mb-3">
          <label class="filter-label"><?=lang("search")?></label>
          <div class="input-group">
            <input type="text" class="form-control" name="search" id="filter-search" placeholder="<?=lang("search_services")?>" value="<?=isset($filters['search']) ? htmlspecialchars($filters['search']) : ''?>">
            <div class="">
              <span class="input-group-text bg-white"><i class="fe fe-search"></i></span>
            </div>
          </div>
        </div>
        
        <!-- Category Filter -->
        <div class="col-md-4 col-lg-2 mb-3">
          <label class="filter-label"><?=lang("Category")?></label>
          <select name="category" id="filter-category" class="form-control">
            <option value="all"><?=lang("all_categories")?></option>
            <?php if (!empty($categories_list)): 
              foreach ($categories_list as $cat): ?>
            <option value="<?=$cat->id?>" <?=(isset($filters['category']) && $filters['category'] == $cat->id) ? 'selected' : ''?>><?=$cat->name?></option>
            <?php endforeach; endif; ?>
          </select>
        </div>
        
        <?php if (get_role("admin")): ?>
        <!-- Status Filter -->
        <div class="col-md-4 col-lg-2 mb-3">
          <label class="filter-label"><?=lang("Status")?></label>
          <select name="status" id="filter-status" class="form-control">
            <option value="all"><?=lang("all_statuses")?></option>
            <option value="1" <?=(isset($filters['status']) && $filters['status'] === '1') ? 'selected' : ''?>><?=lang("Active")?></option>
            <option value="0" <?=(isset($filters['status']) && $filters['status'] === '0') ? 'selected' : ''?>><?=lang("Inactive")?></option>
          </select>
        </div>
        
        <!-- Provider Filter -->
        <div class="col-md-4 col-lg-2 mb-3">
          <label class="filter-label"><?=lang("provider")?></label>
          <select name="provider" id="filter-provider" class="form-control">
            <option value="all"><?=lang("all_providers")?></option>
            <option value="api" <?=(isset($filters['provider']) && $filters['provider'] === 'api') ? 'selected' : ''?>><?=lang("API")?> (<?=lang("all")?>)</option>
            <option value="manual" <?=(isset($filters['provider']) && $filters['provider'] === 'manual') ? 'selected' : ''?>><?=lang("Manual")?></option>
            <?php if (!empty($providers_list)): 
              foreach ($providers_list as $provider): ?>
            <option value="<?=$provider->id?>" <?=(isset($filters['provider']) && $filters['provider'] == $provider->id) ? 'selected' : ''?>><?=$provider->name?></option>
            <?php endforeach; endif; ?>
          </select>
        </div>
        <?php endif; ?>
        
        <!-- Price Range -->
        <div class="col-md-4 col-lg-2 mb-3">
          <label class="filter-label"><?=lang("price_range")?></label>
          <div class="d-flex">
            <input type="number" class="form-control" name="price_min" id="filter-price-min" placeholder="<?=lang("min")?>" step="0.01" value="<?=isset($filters['price_min']) ? htmlspecialchars($filters['price_min']) : ''?>" style="width: 45%;">
            <span class="mx-1 text-white d-flex align-items-center">-</span>
            <input type="number" class="form-control" name="price_max" id="filter-price-max" placeholder="<?=lang("max")?>" step="0.01" value="<?=isset($filters['price_max']) ? htmlspecialchars($filters['price_max']) : ''?>" style="width: 45%;">
          </div>
        </div>
        
        <!-- Per Page & Buttons -->
        <div class="col-md-4 col-lg-1 mb-3">
          <label class="filter-label"><?=lang("per_page")?></label>
          <select name="per_page" id="filter-per-page" class="form-control">
            <option value="25" <?=(isset($pagination['per_page']) && $pagination['per_page'] == 25) ? 'selected' : ''?>>25</option>
            <option value="50" <?=(!isset($pagination['per_page']) || $pagination['per_page'] == 50) ? 'selected' : ''?>>50</option>
            <option value="100" <?=(isset($pagination['per_page']) && $pagination['per_page'] == 100) ? 'selected' : ''?>>100</option>
          </select>
        </div>
      </div>
      
      <!-- Quick Filter Buttons -->
      <div class="row mt-2">
        <div class="col-12">
          <button type="button" class="btn btn-light quick-filter-btn" data-filter="all"><?=lang("all")?></button>
          <?php if (get_role("admin")): ?>
          <button type="button" class="btn btn-light quick-filter-btn" data-filter="active"><?=lang("Active")?> <?=lang("only")?></button>
          <button type="button" class="btn btn-light quick-filter-btn" data-filter="inactive"><?=lang("Inactive")?> <?=lang("only")?></button>
          <button type="button" class="btn btn-light quick-filter-btn" data-filter="api"><?=lang("API")?> <?=lang("only")?></button>
          <button type="button" class="btn btn-light quick-filter-btn" data-filter="manual"><?=lang("Manual")?> <?=lang("only")?></button>
          <?php endif; ?>
          
          <div class="float-end">
            <button type="button" class="btn btn-light" id="reset-filters"><i class="fe fe-x me-1"></i><?=lang("reset")?></button>
            <button type="submit" class="btn btn-primary" id="apply-filters"><i class="fe fe-search me-1"></i><?=lang("apply_filters")?></button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<form class="actionForm" id="services-action-form" method="POST">
  <section class="page-title">
    <div class="row justify-content-between align-items-center">
      <div class="col-md-6 col-lg-4 mb-2">
        <div class="d-flex align-items-center">
          <?php if(get_role("admin") || get_role("supporter")): ?>
          <a href="<?=cn("$module/update")?>" class="ajaxModal btn btn-info me-2">
            <i class="fas fa-plus"></i> <?=lang("add_new")?>
          </a>
          <button type="button" class="btn-quick-action me-2" id="openServiceActionModal">
            <i class="fe fe-zap"></i> <?=lang("quick_actions")?>
          </button>
          <?php else: ?>
          <h4 class="mb-0"><i class="fe fe-list me-2"></i><?=lang("Services")?></h4>
          <?php endif; ?>
          
          <span class="badge bg-primary selected-count-badge ms-2" id="selected-count">
            <span class="count">0</span> <?=lang("selected")?>
          </span>
        </div>
      </div>
      
      <div class="col-md-6 col-lg-8 mb-2 text-end">
        <?php if (get_option("enable_explication_service_symbol")): ?>
        <div class="btn-list d-inline-block me-3">
          <span class="btn btn-sm round btn-secondary">⭐ = <?=lang("__good_seller")?></span>
          <span class="btn btn-sm round btn-secondary">⚡️ = <?=lang("__speed_level")?></span>
          <span class="btn btn-sm round btn-secondary">🔥 = <?=lang("__hot_service")?></span>
        </div>
        <?php endif; ?>
        
        <?php if (get_role("admin")): ?>
        <div class="d-inline-block">
          <a href="<?=cn('api_provider/services')?>" class="btn btn-secondary me-1">
            <i class="fe fe-download me-1"></i><?=lang("import_services")?>
          </a>
          <a href="<?=cn($module.'/export_csv')?>?<?=http_build_query($filters)?>" class="btn btn-secondary me-1">
            <i class="fe fe-file-text me-1"></i><?=lang("export_csv")?>
          </a>
          <div class="item-action dropdown d-inline-block">
            <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown">
              <i class="fe fe-menu me-1"></i><?=lang("bulk_actions")?>
            </button>
            <div class="dropdown-menu dropdown-menu-right">
              <h6 class="dropdown-header"><?=lang("status_actions")?></h6>
              <a class="dropdown-item ajaxActionOptions" href="<?=cn($module.'/ajax_actions_option')?>" data-type="active">
                <i class="fe fe-check-square text-success me-2"></i><?=lang("activate_selected")?>
              </a>
              <a class="dropdown-item ajaxActionOptions" href="<?=cn($module.'/ajax_actions_option')?>" data-type="deactive">
                <i class="fe fe-x-square text-warning me-2"></i><?=lang("deactivate_selected")?>
              </a>
              <div class="dropdown-divider"></div>
              <h6 class="dropdown-header"><?=lang("delete_actions")?></h6>
              <a class="dropdown-item ajaxActionOptions" href="<?=cn($module.'/ajax_actions_option')?>" data-type="delete">
                <i class="fe fe-trash-2 text-danger me-2"></i><?=lang("delete_selected")?>
              </a>
              <a class="dropdown-item ajaxActionOptions" href="<?=cn($module.'/ajax_actions_option')?>" data-type="all_deactive">
                <i class="fe fe-trash-2 text-danger me-2"></i><?=lang("delete_all_inactive")?>
              </a>
              <div class="dropdown-divider"></div>
              <h6 class="dropdown-header"><?=lang("bulk_update")?></h6>
              <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulk-price-modal">
                <i class="fe fe-dollar-sign text-info me-2"></i><?=lang("update_prices")?>
              </a>
              <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulk-category-modal">
                <i class="fe fe-folder text-info me-2"></i><?=lang("change_category")?>
              </a>
            </div>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- Services Table Container -->
  <div class="row m-t-5">
    <div class="col-md-12 col-xl-12">
      <div class="card p-0 content">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h3 class="card-title" style="color:#fff !important;">
            <i class="fe fe-list me-2"></i><?=lang("Services")?> 
            <?php if (isset($pagination)): ?>
            <span class="badge bg-light text-dark ms-2"><?=number_format($pagination['total'])?></span>
            <?php endif; ?>
          </h3>
          <div class="card-options">
            <button type="button" class="btn btn-sm btn-light me-2" id="refresh-services">
              <i class="fe fe-refresh-cw"></i>
            </button>
            <a href="#" class="card-options-collapse" data-bs-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
          </div>
        </div>
        <div class="table-responsive dimmer" id="services-table-container">
          <div class="loader"></div>
          <div class="dimmer-content" id="services-content">
            <?php
            // Load the paginated services view
            $data = array(
              "module"       => $module,
              "columns"      => $columns,
              "services"     => $services,
              "pagination"   => $pagination,
              "custom_rates" => $custom_rates,
            );
            $this->load->view("ajax/paginated_services", $data);
            ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>

<?php if (get_role("admin")): ?>
<!-- Bulk Price Update Modal -->
<div class="modal fade" id="bulk-price-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="fe fe-dollar-sign me-2"></i><?=lang("bulk_update_prices")?></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="bulk-price-form">
        <div class="modal-body">
          <div class="form-group">
            <label><?=lang("change_type")?></label>
            <select name="change_type" class="form-control" id="price-change-type">
              <option value="percentage"><?=lang("percentage_change")?></option>
              <option value="fixed"><?=lang("fixed_amount_change")?></option>
              <option value="set"><?=lang("set_fixed_price")?></option>
            </select>
          </div>
          <div class="form-group">
            <label id="price-change-label"><?=lang("percentage")?> (%)</label>
            <input type="number" name="price_change" class="form-control" step="0.01" placeholder="<?=lang("enter_value")?>" required>
            <small class="text-muted" id="price-change-hint"><?=lang("positive_increase_negative_decrease")?></small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?=lang("Cancel")?></button>
          <button type="submit" class="btn btn-primary"><?=lang("update_prices")?></button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Bulk Category Update Modal -->
<div class="modal fade" id="bulk-category-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="fe fe-folder me-2"></i><?=lang("change_category")?></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="bulk-category-form">
        <div class="modal-body">
          <div class="form-group">
            <label><?=lang("select_new_category")?></label>
            <select name="new_category" class="form-control" required>
              <option value=""><?=lang("choose_a_category")?></option>
              <?php if (!empty($categories_list)): 
                foreach ($categories_list as $cat): ?>
              <option value="<?=$cat->id?>"><?=$cat->name?></option>
              <?php endforeach; endif; ?>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?=lang("Cancel")?></button>
          <button type="submit" class="btn btn-primary"><?=lang("change_category")?></button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- Custom Service Action Modal -->
<div class="service-action-modal" id="serviceActionModal">
  <div class="modal-backdrop1"></div>
  <div class="modal-container">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="serviceActionModalTitle">
          <i class="fe fe-zap"></i>
          <span><?=lang("quick_actions")?></span>
        </h4>
        <button type="button" class="modal-close" id="closeServiceActionModal">
          <i class="fe fe-x"></i>
        </button>
      </div>
      <div class="modal-body" id="serviceActionModalBody">
        <!-- Default content - Quick Actions Menu -->
        <div class="service-actions-menu">
          <?php if (get_role("admin")): ?>
          <div class="service-action-card" data-action="quick-add">
            <div class="action-icon icon-primary">
              <i class="fe fe-plus"></i>
            </div>
            <div class="action-content">
              <div class="action-title"><?=lang("add_new_service")?></div>
              <p class="action-desc"><?=lang("create_new_service_description")?></p>
            </div>
            <i class="fe fe-chevron-right action-arrow"></i>
          </div>
          
          <div class="service-action-card" data-action="sync-prices">
            <div class="action-icon icon-success">
              <i class="fe fe-refresh-cw"></i>
            </div>
            <div class="action-content">
              <div class="action-title"><?=lang("sync_api_prices")?></div>
              <p class="action-desc"><?=lang("sync_api_prices_description")?></p>
            </div>
            <i class="fe fe-chevron-right action-arrow"></i>
          </div>
          
          <div class="service-action-card" data-action="bulk-status">
            <div class="action-icon icon-warning">
              <i class="fe fe-toggle-left"></i>
            </div>
            <div class="action-content">
              <div class="action-title"><?=lang("manage_service_status")?></div>
              <p class="action-desc"><?=lang("manage_service_status_description")?></p>
            </div>
            <i class="fe fe-chevron-right action-arrow"></i>
          </div>
          
          <div class="service-action-card" data-action="duplicate-service">
            <div class="action-icon icon-info">
              <i class="fe fe-copy"></i>
            </div>
            <div class="action-content">
              <div class="action-title"><?=lang("duplicate_services")?></div>
              <p class="action-desc"><?=lang("duplicate_services_description")?></p>
            </div>
            <i class="fe fe-chevron-right action-arrow"></i>
          </div>
          
          <div class="service-action-card" data-action="cleanup">
            <div class="action-icon icon-danger">
              <i class="fe fe-trash-2"></i>
            </div>
            <div class="action-content">
              <div class="action-title"><?=lang("cleanup_services")?></div>
              <p class="action-desc"><?=lang("cleanup_services_description")?></p>
            </div>
            <i class="fe fe-chevron-right action-arrow"></i>
          </div>
          <?php endif; ?>
          
          <div class="service-action-card" data-action="service-info">
            <div class="action-icon icon-primary">
              <i class="fe fe-info"></i>
            </div>
            <div class="action-content">
              <div class="action-title"><?=lang("service_information")?></div>
              <p class="action-desc"><?=lang("service_information_description")?></p>
            </div>
            <i class="fe fe-chevron-right action-arrow"></i>
          </div>
        </div>
      </div>
      <div class="modal-footer" id="serviceActionModalFooter">
        <button type="button" class="btn-modal-cancel" id="cancelServiceAction"><?=lang("close")?></button>
      </div>
    </div>
  </div>
</div>

<script>
(function($) {
  'use strict';
  
  var ServicesManager = {
    currentPage: <?=(isset($pagination['current_page']) ? $pagination['current_page'] : 1)?>,
    perPage: <?=(isset($pagination['per_page']) ? $pagination['per_page'] : 50)?>,
    isLoading: false,
    debounceTimer: null,
    
    init: function() {
      this.bindEvents();
      this.updateSelectedCount();
      this.initServiceActionModal();
    },
    
    // Initialize the custom Service Action Modal
    initServiceActionModal: function() {
      var self = this;
      
      // Open modal
      $(document).on('click', '#openServiceActionModal, .btn-quick-action', function(e) {
        e.preventDefault();
        self.openServiceActionModal();
      });
      
      // Close modal
      $(document).on('click', '#closeServiceActionModal, #cancelServiceAction, .service-action-modal .modal-backdrop1', function(e) {
        e.preventDefault();
        self.closeServiceActionModal();
      });
      
      // ESC key to close
      $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && $('#serviceActionModal').hasClass('show')) {
          self.closeServiceActionModal();
        }
      });
      
      // Action card clicks
      $(document).on('click', '.service-action-card', function() {
        var action = $(this).data('action');
        self.handleServiceAction(action);
      });
    },
    
    openServiceActionModal: function() {
      $('body').css('overflow', 'hidden');
      $('#serviceActionModal').addClass('show');
      // Reset to default content
      this.resetModalToDefault();
    },
    
    closeServiceActionModal: function() {
      $('#serviceActionModal').removeClass('show');
      setTimeout(function() {
        $('body').css('overflow', '');
      }, 400);
    },
    
    resetModalToDefault: function() {
      $('#serviceActionModalTitle span').text('<?=lang("quick_actions")?>');
      $('#serviceActionModalTitle i').attr('class', 'fe fe-zap');
      $('#serviceActionModalFooter').html('<button type="button" class="btn-modal-cancel" id="cancelServiceAction"><?=lang("close")?></button>');
      // Restore default content
      var defaultContent = `
        <div class="service-actions-menu">
          <?php if (get_role("admin")): ?>
          <div class="service-action-card" data-action="quick-add">
            <div class="action-icon icon-primary"><i class="fe fe-plus"></i></div>
            <div class="action-content">
              <div class="action-title"><?=lang("add_new_service")?></div>
              <p class="action-desc"><?=lang("create_new_service_description")?></p>
            </div>
            <i class="fe fe-chevron-right action-arrow"></i>
          </div>
          <div class="service-action-card" data-action="sync-prices">
            <div class="action-icon icon-success"><i class="fe fe-refresh-cw"></i></div>
            <div class="action-content">
              <div class="action-title"><?=lang("sync_api_prices")?></div>
              <p class="action-desc"><?=lang("sync_api_prices_description")?></p>
            </div>
            <i class="fe fe-chevron-right action-arrow"></i>
          </div>
          <div class="service-action-card" data-action="bulk-status">
            <div class="action-icon icon-warning"><i class="fe fe-toggle-left"></i></div>
            <div class="action-content">
              <div class="action-title"><?=lang("manage_service_status")?></div>
              <p class="action-desc"><?=lang("manage_service_status_description")?></p>
            </div>
            <i class="fe fe-chevron-right action-arrow"></i>
          </div>
          <div class="service-action-card" data-action="duplicate-service">
            <div class="action-icon icon-info"><i class="fe fe-copy"></i></div>
            <div class="action-content">
              <div class="action-title"><?=lang("duplicate_services")?></div>
              <p class="action-desc"><?=lang("duplicate_services_description")?></p>
            </div>
            <i class="fe fe-chevron-right action-arrow"></i>
          </div>
          <div class="service-action-card" data-action="cleanup">
            <div class="action-icon icon-danger"><i class="fe fe-trash-2"></i></div>
            <div class="action-content">
              <div class="action-title"><?=lang("cleanup_services")?></div>
              <p class="action-desc"><?=lang("cleanup_services_description")?></p>
            </div>
            <i class="fe fe-chevron-right action-arrow"></i>
          </div>
          <?php endif; ?>
          <div class="service-action-card" data-action="service-info">
            <div class="action-icon icon-primary"><i class="fe fe-info"></i></div>
            <div class="action-content">
              <div class="action-title"><?=lang("service_information")?></div>
              <p class="action-desc"><?=lang("service_information_description")?></p>
            </div>
            <i class="fe fe-chevron-right action-arrow"></i>
          </div>
        </div>
      `;
      $('#serviceActionModalBody').html(defaultContent);
    },
    
    showModalLoader: function(message) {
      var loaderHtml = `
        <div class="modal-loader">
          <div class="spinner"></div>
          <p>${message || '<?=lang("loading")?>'}</p>
        </div>
      `;
      $('#serviceActionModalBody').html(loaderHtml);
    },
    
    handleServiceAction: function(action) {
      var self = this;
      
      switch(action) {
        case 'quick-add':
          // Load add service form via AJAX
          this.showModalLoader('<?=lang("loading_form")?>');
          $('#serviceActionModalTitle span').text('<?=lang("add_new_service")?>');
          $('#serviceActionModalTitle i').attr('class', 'fe fe-plus');
          
          $.ajax({
            url: PATH + 'services/ajax_get_quick_add_form',
            type: 'POST',
            data: { token: token },
            success: function(response) {
              $('#serviceActionModalBody').html(response);
              $('#serviceActionModalFooter').html(`
                <button type="button" class="btn-modal-cancel" onclick="ServicesManager.resetModalToDefault()"><?=lang("back")?></button>
                <button type="button" class="btn-modal-primary" onclick="ServicesManager.submitQuickAddForm()"><?=lang("add_service")?></button>
              `);
            },
            error: function() {
              notify('<?=lang("error_loading_form")?>', 'error');
              self.resetModalToDefault();
            }
          });
          break;
          
        case 'sync-prices':
          this.showModalLoader('<?=lang("syncing_prices")?>');
          $('#serviceActionModalTitle span').text('<?=lang("sync_api_prices")?>');
          $('#serviceActionModalTitle i').attr('class', 'fe fe-refresh-cw');
          
          $.ajax({
            url: PATH + 'services/ajax_get_sync_prices_content',
            type: 'POST',
            data: { token: token },
            success: function(response) {
              $('#serviceActionModalBody').html(response);
              $('#serviceActionModalFooter').html(`
                <button type="button" class="btn-modal-cancel" onclick="ServicesManager.resetModalToDefault()"><?=lang("back")?></button>
              `);
            },
            error: function() {
              notify('<?=lang("error_loading_content")?>', 'error');
              self.resetModalToDefault();
            }
          });
          break;
          
        case 'bulk-status':
          $('#serviceActionModalTitle span').text('<?=lang("manage_service_status")?>');
          $('#serviceActionModalTitle i').attr('class', 'fe fe-toggle-left');
          
          var statusContent = `
            <div class="text-center mb-4">
              <p class="text-muted"><?=lang("select_status_action")?></p>
            </div>
            <div class="row">
              <div class="col-6 mb-3">
                <div class="service-action-card" onclick="ServicesManager.bulkStatusAction('activate')">
                  <div class="action-icon icon-success"><i class="fe fe-check-circle"></i></div>
                  <div class="action-content">
                    <div class="action-title"><?=lang("activate_all")?></div>
                  </div>
                </div>
              </div>
              <div class="col-6 mb-3">
                <div class="service-action-card" onclick="ServicesManager.bulkStatusAction('deactivate')">
                  <div class="action-icon icon-warning"><i class="fe fe-x-circle"></i></div>
                  <div class="action-content">
                    <div class="action-title"><?=lang("deactivate_all")?></div>
                  </div>
                </div>
              </div>
            </div>
            <div class="alert alert-info mt-3">
              <i class="fe fe-info me-2"></i><?=lang("bulk_status_info")?>
            </div>
          `;
          $('#serviceActionModalBody').html(statusContent);
          $('#serviceActionModalFooter').html(`
            <button type="button" class="btn-modal-cancel" onclick="ServicesManager.resetModalToDefault()"><?=lang("back")?></button>
          `);
          break;
          
        case 'duplicate-service':
          this.showModalLoader('<?=lang("loading")?>');
          $('#serviceActionModalTitle span').text('<?=lang("duplicate_services")?>');
          $('#serviceActionModalTitle i').attr('class', 'fe fe-copy');
          
          $.ajax({
            url: PATH + 'services/ajax_get_duplicate_content',
            type: 'POST',
            data: { token: token },
            success: function(response) {
              $('#serviceActionModalBody').html(response);
              $('#serviceActionModalFooter').html(`
                <button type="button" class="btn-modal-cancel" onclick="ServicesManager.resetModalToDefault()"><?=lang("back")?></button>
              `);
            },
            error: function() {
              notify('<?=lang("error_loading_content")?>', 'error');
              self.resetModalToDefault();
            }
          });
          break;
          
        case 'cleanup':
          $('#serviceActionModalTitle span').text('<?=lang("cleanup_services")?>');
          $('#serviceActionModalTitle i').attr('class', 'fe fe-trash-2');
          
          var cleanupContent = `
            <div class="alert alert-warning mb-4">
              <i class="fe fe-alert-triangle me-2"></i>
              <strong><?=lang("warning")?></strong> <?=lang("cleanup_warning")?>
            </div>
            <div class="service-action-card" onclick="ServicesManager.cleanupAction('inactive')">
              <div class="action-icon icon-warning"><i class="fe fe-trash"></i></div>
              <div class="action-content">
                <div class="action-title"><?=lang("delete_inactive_services")?></div>
                <p class="action-desc"><?=lang("delete_inactive_services_desc")?></p>
              </div>
            </div>
            <div class="service-action-card" onclick="ServicesManager.cleanupAction('duplicates')">
              <div class="action-icon icon-info"><i class="fe fe-layers"></i></div>
              <div class="action-content">
                <div class="action-title"><?=lang("find_duplicate_services")?></div>
                <p class="action-desc"><?=lang("find_duplicate_services_desc")?></p>
              </div>
            </div>
          `;
          $('#serviceActionModalBody').html(cleanupContent);
          $('#serviceActionModalFooter').html(`
            <button type="button" class="btn-modal-cancel" onclick="ServicesManager.resetModalToDefault()"><?=lang("back")?></button>
          `);
          break;
          
        case 'service-info':
          this.showModalLoader('<?=lang("loading_statistics")?>');
          $('#serviceActionModalTitle span').text('<?=lang("service_information")?>');
          $('#serviceActionModalTitle i').attr('class', 'fe fe-info');
          
          $.ajax({
            url: PATH + 'services/ajax_get_service_stats',
            type: 'POST',
            data: { token: token },
            success: function(response) {
              $('#serviceActionModalBody').html(response);
              $('#serviceActionModalFooter').html(`
                <button type="button" class="btn-modal-cancel" onclick="ServicesManager.resetModalToDefault()"><?=lang("back")?></button>
              `);
            },
            error: function() {
              notify('<?=lang("error_loading_content")?>', 'error');
              self.resetModalToDefault();
            }
          });
          break;
      }
    },
    
    bulkStatusAction: function(action) {
      var self = this;
      if (!confirm(action === 'activate' ? '<?=lang("confirm_activate_all")?>' : '<?=lang("confirm_deactivate_all")?>')) {
        return;
      }
      
      self.showModalLoader(action === 'activate' ? '<?=lang("activating_services")?>' : '<?=lang("deactivating_services")?>');
      
      $.ajax({
        url: PATH + 'services/ajax_bulk_status_all',
        type: 'POST',
        data: { action: action, token: token },
        dataType: 'json',
        success: function(response) {
          notify(response.message, response.status);
          if (response.status === 'success') {
            self.loadServices();
            self.closeServiceActionModal();
          } else {
            self.resetModalToDefault();
          }
        },
        error: function() {
          notify('<?=lang("error_processing_request")?>', 'error');
          self.resetModalToDefault();
        }
      });
    },
    
    cleanupAction: function(type) {
      var self = this;
      
      if (type === 'inactive') {
        if (!confirm('<?=lang("confirm_delete_inactive")?>')) return;
        
        self.showModalLoader('<?=lang("deleting_services")?>');
        
        $.ajax({
          url: PATH + 'services/ajax_actions_option',
          type: 'POST',
          data: { type: 'all_deactive', token: token },
          dataType: 'json',
          success: function(response) {
            notify(response.message, response.status);
            if (response.status === 'success') {
              self.loadServices();
            }
            self.closeServiceActionModal();
          },
          error: function() {
            notify('<?=lang("error_processing_request")?>', 'error');
            self.resetModalToDefault();
          }
        });
      } else if (type === 'duplicates') {
        self.showModalLoader('<?=lang("scanning_duplicates")?>');
        
        $.ajax({
          url: PATH + 'services/ajax_find_duplicates',
          type: 'POST',
          data: { token: token },
          success: function(response) {
            $('#serviceActionModalBody').html(response);
          },
          error: function() {
            notify('<?=lang("error_processing_request")?>', 'error');
            self.resetModalToDefault();
          }
        });
      }
    },
    
    submitQuickAddForm: function() {
      var self = this;
      var $form = $('#quickAddServiceForm');
      
      if ($form.length === 0) {
        notify('<?=lang("form_not_found")?>', 'error');
        return;
      }
      
      self.showModalLoader('<?=lang("saving")?>');
      
      $.ajax({
        url: PATH + 'services/ajax_update',
        type: 'POST',
        data: $form.serialize() + '&token=' + token,
        dataType: 'json',
        success: function(response) {
          notify(response.message, response.status);
          if (response.status === 'success') {
            self.loadServices();
            self.closeServiceActionModal();
          } else {
            self.resetModalToDefault();
          }
        },
        error: function() {
          notify('<?=lang("error_processing_request")?>', 'error');
          self.resetModalToDefault();
        }
      });
    },
    
    bindEvents: function() {
      var self = this;
      
      // Filter form submission
      $('#services-filter-form').on('submit', function(e) {
        e.preventDefault();
        self.currentPage = 1;
        self.loadServices();
      });
      
      // Real-time search with debounce
      $('#filter-search').on('keyup', function() {
        clearTimeout(self.debounceTimer);
        self.debounceTimer = setTimeout(function() {
          self.currentPage = 1;
          self.loadServices();
        }, 500);
      });
      
      // Filter change events
      $('#filter-category, #filter-status, #filter-provider, #filter-per-page').on('change', function() {
        self.currentPage = 1;
        self.perPage = parseInt($('#filter-per-page').val()) || 50;
        self.loadServices();
      });
      
      // Price range filter with debounce
      $('#filter-price-min, #filter-price-max').on('change', function() {
        self.currentPage = 1;
        self.loadServices();
      });
      
      // Pagination clicks
      $(document).on('click', '.page-nav', function(e) {
        e.preventDefault();
        var page = parseInt($(this).data('page'));
        if (page && !$(this).parent().hasClass('disabled')) {
          self.currentPage = page;
          self.loadServices();
        }
      });
      
      // Quick filter buttons
      $('.quick-filter-btn').on('click', function() {
        var filter = $(this).data('filter');
        self.applyQuickFilter(filter);
      });
      
      // Reset filters
      $('#reset-filters').on('click', function() {
        self.resetFilters();
      });
      
      // Refresh button
      $('#refresh-services').on('click', function() {
        self.loadServices();
      });
      
      // Select all checkbox
      $(document).on('change', '#selectAllServices, .select-all-services', function() {
        var isChecked = $(this).is(':checked');
        $('.service-checkbox').prop('checked', isChecked);
        self.updateSelectedCount();
      });
      
      // Individual checkbox
      $(document).on('change', '.service-checkbox', function() {
        self.updateSelectedCount();
      });
      
      // Price change type selector
      $('#price-change-type').on('change', function() {
        var type = $(this).val();
        if (type === 'percentage') {
          $('#price-change-label').text('<?=lang("percentage")?> (%)');
          $('#price-change-hint').text('<?=lang("positive_increase_negative_decrease")?>');
        } else if (type === 'fixed') {
          $('#price-change-label').text('<?=lang("amount")?>');
          $('#price-change-hint').text('<?=lang("positive_increase_negative_decrease")?>');
        } else {
          $('#price-change-label').text('<?=lang("new_price")?>');
          $('#price-change-hint').text('<?=lang("all_selected_services_will_have_this_price")?>');
        }
      });
      
      // Bulk price form
      $('#bulk-price-form').on('submit', function(e) {
        e.preventDefault();
        self.bulkUpdatePrices();
      });
      
      // Bulk category form
      $('#bulk-category-form').on('submit', function(e) {
        e.preventDefault();
        self.bulkUpdateCategory();
      });
    },
    
    getFilters: function() {
      return {
        search: $('#filter-search').val(),
        category: $('#filter-category').val(),
        status: $('#filter-status').val(),
        provider: $('#filter-provider').val(),
        price_min: $('#filter-price-min').val(),
        price_max: $('#filter-price-max').val(),
        page: this.currentPage,
        per_page: this.perPage,
        token: token
      };
    },
    
    loadServices: function() {
      var self = this;
      if (this.isLoading) return;
      
      this.isLoading = true;
      var $container = $('#services-table-container');
      $container.addClass('active');
      
      $.ajax({
        url: PATH + 'services/ajax_get_paginated_services',
        type: 'POST',
        data: this.getFilters(),
        success: function(response) {
          $('#services-content').html(response);
          self.updateSelectedCount();
          
          // Smooth scroll to top of table
          $('html, body').animate({
            scrollTop: $('#services-table-container').offset().top - 100
          }, 300);
        },
        error: function(xhr, status, error) {
          notify('<?=lang("error_loading_services")?>', 'error');
          console.error('Error loading services:', error);
        },
        complete: function() {
          self.isLoading = false;
          $container.removeClass('active');
        }
      });
    },
    
    applyQuickFilter: function(filter) {
      $('.quick-filter-btn').removeClass('active');
      $('[data-filter="' + filter + '"]').addClass('active');
      
      // Reset all filters first
      $('#filter-search').val('');
      $('#filter-category').val('all');
      $('#filter-status').val('all');
      $('#filter-provider').val('all');
      $('#filter-price-min').val('');
      $('#filter-price-max').val('');
      
      switch (filter) {
        case 'active':
          $('#filter-status').val('1');
          break;
        case 'inactive':
          $('#filter-status').val('0');
          break;
        case 'api':
          $('#filter-provider').val('api');
          break;
        case 'manual':
          $('#filter-provider').val('manual');
          break;
      }
      
      this.currentPage = 1;
      this.loadServices();
    },
    
    resetFilters: function() {
      $('#filter-search').val('');
      $('#filter-category').val('all');
      $('#filter-status').val('all');
      $('#filter-provider').val('all');
      $('#filter-price-min').val('');
      $('#filter-price-max').val('');
      $('#filter-per-page').val('50');
      $('.quick-filter-btn').removeClass('active');
      $('[data-filter="all"]').addClass('active');
      
      this.currentPage = 1;
      this.perPage = 50;
      this.loadServices();
    },
    
    updateSelectedCount: function() {
      var count = $('.service-checkbox:checked').length;
      $('#selected-count .count').text(count);
      if (count > 0) {
        $('#selected-count').addClass('show');
      } else {
        $('#selected-count').removeClass('show');
      }
    },
    
    getSelectedIds: function() {
      var ids = [];
      $('.service-checkbox:checked').each(function() {
        ids.push($(this).val());
      });
      return ids;
    },
    
    bulkUpdatePrices: function() {
      var self = this;
      var ids = this.getSelectedIds();
      
      if (ids.length === 0) {
        notify('<?=lang("please_choose_at_least_one_item")?>', 'error');
        return;
      }
      
      var data = {
        ids: ids,
        change_type: $('#price-change-type').val(),
        price_change: $('input[name="price_change"]').val(),
        token: token
      };
      
      pageOverlay.show();
      
      $.ajax({
        url: PATH + 'services/ajax_bulk_update_prices',
        type: 'POST',
        data: data,
        dataType: 'json',
        success: function(response) {
          pageOverlay.hide();
          notify(response.message, response.status);
          if (response.status === 'success') {
            $('#bulk-price-modal').modal('hide');
            $('#bulk-price-form')[0].reset();
            self.loadServices();
          }
        },
        error: function() {
          pageOverlay.hide();
          notify('<?=lang("error_processing_request")?>', 'error');
        }
      });
    },
    
    bulkUpdateCategory: function() {
      var self = this;
      var ids = this.getSelectedIds();
      
      if (ids.length === 0) {
        notify('<?=lang("please_choose_at_least_one_item")?>', 'error');
        return;
      }
      
      var data = {
        ids: ids,
        new_category: $('select[name="new_category"]').val(),
        token: token
      };
      
      pageOverlay.show();
      
      $.ajax({
        url: PATH + 'services/ajax_bulk_update_category',
        type: 'POST',
        data: data,
        dataType: 'json',
        success: function(response) {
          pageOverlay.hide();
          notify(response.message, response.status);
          if (response.status === 'success') {
            $('#bulk-category-modal').modal('hide');
            $('#bulk-category-form')[0].reset();
            self.loadServices();
          }
        },
        error: function() {
          pageOverlay.hide();
          notify('<?=lang("error_processing_request")?>', 'error');
        }
      });
    }
  };
  
  // Expose ServicesManager to global scope for onclick handlers
  window.ServicesManager = ServicesManager;
  
  // Initialize when document is ready
  $(document).ready(function() {
    ServicesManager.init();
  });
  
})(jQuery);
</script>