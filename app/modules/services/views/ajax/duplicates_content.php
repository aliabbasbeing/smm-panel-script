<?php
/**
 * Duplicates Content for Custom Modal
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
?>

<div class="duplicates-content">
    <?php if(!empty($duplicates)): ?>
    <div class="alert alert-warning mb-4">
        <i class="fe fe-alert-triangle mr-2"></i>
        <?=sprintf(lang("found_x_duplicate_service_names"), count($duplicates))?>
    </div>
    
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th><?=lang("service_name")?></th>
                    <th class="text-center"><?=lang("count")?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($duplicates as $dup): ?>
                <tr>
                    <td>
                        <span class="text-truncate d-inline-block" style="max-width: 400px;" title="<?=htmlspecialchars($dup->name)?>">
                            <?=htmlspecialchars($dup->name)?>
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-warning"><?=$dup->count?></span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div class="text-muted small mt-3">
        <i class="fe fe-info mr-1"></i>
        <?=lang("duplicate_services_tip")?>
    </div>
    <?php else: ?>
    <div class="text-center p-5">
        <i class="fe fe-check-circle text-success" style="font-size: 64px;"></i>
        <h5 class="mt-3 text-success"><?=lang("no_duplicates_found")?></h5>
        <p class="text-muted"><?=lang("no_duplicates_found_desc")?></p>
    </div>
    <?php endif; ?>
</div>
