<?php
/**
 * Quick Add Service Form for Custom Modal
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
?>

<form id="quickAddServiceForm" class="quick-add-form">
    <div class="row">
        <div class="col-md-12 mb-3">
            <label class="form-label font-weight-bold"><?=lang("package_name")?> <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="name" placeholder="<?=lang("enter_service_name")?>" required>
        </div>
        
        <div class="col-md-6 mb-3">
            <label class="form-label font-weight-bold"><?=lang("Category")?> <span class="text-danger">*</span></label>
            <select name="category" class="form-control" required>
                <option value=""><?=lang("choose_a_category")?></option>
                <?php if(!empty($categories)):
                    foreach ($categories as $category): ?>
                <option value="<?=$category->id?>"><?=$category->name?></option>
                <?php endforeach; endif; ?>
            </select>
        </div>
        
        <div class="col-md-6 mb-3">
            <label class="form-label font-weight-bold"><?=lang("service_type")?></label>
            <select name="service_type" class="form-control">
                <option value="default"><?=lang("default")?></option>
                <option value="package"><?=lang("package")?></option>
                <option value="custom_comments"><?=lang("custom_comments")?></option>
                <option value="subscriptions"><?=lang("subscriptions")?></option>
            </select>
        </div>
        
        <div class="col-md-4 mb-3">
            <label class="form-label font-weight-bold"><?=lang("min_order")?> <span class="text-danger">*</span></label>
            <input type="number" class="form-control" name="min" placeholder="100" min="1" required>
        </div>
        
        <div class="col-md-4 mb-3">
            <label class="form-label font-weight-bold"><?=lang("max_order")?> <span class="text-danger">*</span></label>
            <input type="number" class="form-control" name="max" placeholder="10000" min="1" required>
        </div>
        
        <div class="col-md-4 mb-3">
            <label class="form-label font-weight-bold"><?=lang("price_per_1000")?> <span class="text-danger">*</span></label>
            <input type="number" class="form-control" name="price" placeholder="1.00" step="0.0001" min="0" required>
        </div>
        
        <div class="col-md-6 mb-3">
            <label class="form-label font-weight-bold"><?=lang("dripfeed")?></label>
            <select name="dripfeed" class="form-control">
                <option value="0"><?=lang("Deactive")?></option>
                <option value="1"><?=lang("Active")?></option>
            </select>
        </div>
        
        <div class="col-md-6 mb-3">
            <label class="form-label font-weight-bold"><?=lang("Status")?></label>
            <select name="status" class="form-control">
                <option value="1"><?=lang("Active")?></option>
                <option value="0"><?=lang("Inactive")?></option>
            </select>
        </div>
        
        <div class="col-md-12 mb-3">
            <label class="form-label font-weight-bold"><?=lang("Description")?></label>
            <textarea class="form-control" name="desc" rows="3" placeholder="<?=lang("service_description_placeholder")?>"></textarea>
        </div>
    </div>
    
    <input type="hidden" name="add_type" value="manual">
</form>

<style>
.quick-add-form .form-label {
    font-size: 0.875rem;
    margin-bottom: 6px;
    color: #495057;
}
.quick-add-form .form-control {
    border-radius: 8px;
    border: 2px solid #e9ecef;
    padding: 10px 15px;
    transition: all 0.3s ease;
}
.quick-add-form .form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
}
</style>
