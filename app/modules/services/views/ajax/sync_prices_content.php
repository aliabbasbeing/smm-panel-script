<?php
/**
 * Sync Prices Content for Custom Modal
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
?>

<div class="sync-prices-content">
    <div class="alert alert-info mb-4">
        <i class="fe fe-info mr-2"></i>
        <?=lang("sync_prices_info")?>
    </div>
    
    <?php if(!empty($api_providers)): ?>
    <div class="provider-list">
        <?php foreach ($api_providers as $provider): ?>
        <div class="service-action-card" onclick="window.location.href='<?=cn('api_provider/services/'.$provider->ids)?>'">
            <div class="action-icon icon-primary">
                <i class="fe fe-cloud"></i>
            </div>
            <div class="action-content">
                <div class="action-title"><?=$provider->name?></div>
                <p class="action-desc"><?=lang("click_to_sync_from")?> <?=$provider->name?></p>
            </div>
            <i class="fe fe-external-link action-arrow"></i>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="text-center p-4">
        <i class="fe fe-alert-circle text-muted" style="font-size: 48px;"></i>
        <p class="text-muted mt-3"><?=lang("no_api_providers_found")?></p>
        <a href="<?=cn('api_provider')?>" class="btn btn-primary mt-2">
            <i class="fe fe-plus mr-1"></i><?=lang("add_api_provider")?>
        </a>
    </div>
    <?php endif; ?>
</div>
