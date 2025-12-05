<?php
/**
 * Duplicate Service Content for Custom Modal
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
?>

<div class="duplicate-content">
    <div class="alert alert-info mb-4">
        <i class="fe fe-info mr-2"></i>
        <?=lang("duplicate_service_info")?>
    </div>
    
    <div class="text-center p-4">
        <i class="fe fe-copy text-primary" style="font-size: 64px;"></i>
        <h5 class="mt-3"><?=lang("duplicate_service_title")?></h5>
        <p class="text-muted"><?=lang("duplicate_service_instructions")?></p>
        
        <div class="mt-4">
            <ol class="text-left" style="max-width: 400px; margin: 0 auto;">
                <li class="mb-2"><?=lang("duplicate_step_1")?></li>
                <li class="mb-2"><?=lang("duplicate_step_2")?></li>
                <li class="mb-2"><?=lang("duplicate_step_3")?></li>
            </ol>
        </div>
    </div>
</div>
