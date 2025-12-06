<?php
/**
 * Service Statistics Content for Custom Modal
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
?>

<div class="service-stats-content">
    <!-- Overview Stats -->
    <div class="row mb-4">
        <div class="col-6 col-md-3 mb-3">
            <div class="text-center p-3 rounded" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);">
                <div class="h3 mb-0 text-primary"><?=number_format($stats->total)?></div>
                <div class="text-muted small"><?=lang("total_services")?></div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="text-center p-3 rounded" style="background: rgba(40, 167, 69, 0.1);">
                <div class="h3 mb-0 text-success"><?=number_format($stats->active)?></div>
                <div class="text-muted small"><?=lang("Active")?></div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="text-center p-3 rounded" style="background: rgba(255, 193, 7, 0.1);">
                <div class="h3 mb-0 text-warning"><?=number_format($stats->inactive)?></div>
                <div class="text-muted small"><?=lang("Inactive")?></div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="text-center p-3 rounded" style="background: rgba(23, 162, 184, 0.1);">
                <div class="h3 mb-0 text-info"><?=number_format($stats->api)?></div>
                <div class="text-muted small"><?=lang("API")?></div>
            </div>
        </div>
    </div>
    
    <!-- Category Breakdown -->
    <?php if(!empty($category_stats)): ?>
    <h6 class="font-weight-bold mb-3"><?=lang("services_by_category")?></h6>
    <div class="category-breakdown">
        <?php 
        // Use fallback value of 1 to prevent division by zero when calculating percentages
        $total_services = $stats->total ?: 1;
        foreach ($category_stats as $cat): 
            $percentage = ($cat['count'] / $total_services) * 100;
        ?>
        <div class="d-flex align-items-center mb-2">
            <div class="flex-fill">
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-sm"><?=$cat['name']?></span>
                    <span class="text-muted text-sm"><?=$cat['count']?> (<?=number_format($percentage, 1)?>%)</span>
                </div>
                <div class="progress" style="height: 8px; border-radius: 4px;">
                    <div class="progress-bar" role="progressbar" style="width: <?=$percentage?>%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    
    <!-- Quick Info -->
    <div class="alert alert-light mt-4 mb-0" style="border-left: 4px solid #667eea;">
        <div class="d-flex align-items-center">
            <i class="fe fe-pie-chart text-primary me-3" style="font-size: 24px;"></i>
            <div>
                <strong><?=lang("manual_vs_api")?></strong>
                <div class="text-muted small">
                    <?=lang("Manual")?>: <?=number_format($stats->manual)?> | <?=lang("API")?>: <?=number_format($stats->api)?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.service-stats-content .progress {
    background-color: #e9ecef;
}
</style>
