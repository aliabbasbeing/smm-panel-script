<?php
/**
 * AJAX Paginated Services View
 * Displays services table with pagination info
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
?>

<?php if (!empty($services)): ?>
<div class="paginated-services-container">
    <table class="table table-hover table-bordered table-outline table-vcenter card-table" id="services-table">
        <thead>
            <tr>
                <?php if (get_role("admin")): ?>
                <th class="text-center w-1">
                    <div class="custom-controls-stacked">
                        <label class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input select-all-services" id="selectAllServices">
                            <span class="custom-control-label"></span>
                        </label>
                    </div>
                </th>
                <?php endif; ?>
                <th class="text-center w-1">ID</th>
                <th><?php echo lang("Name"); ?></th>
                <th class="text-center"><?php echo lang("Category"); ?></th>
                <?php if (!empty($columns)): 
                    foreach ($columns as $key => $row): ?>
                <th class="text-center"><?=$row?></th>
                <?php endforeach; endif; ?>
                <?php if (get_role("admin") || get_role("supporter")): ?>
                <th class="text-center"><?=lang("Action")?></th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($services as $row): ?>
            <tr class="tr_<?php echo (get_role('admin')) ? $row->ids : $row->id; ?>" data-service-id="<?=$row->id?>" data-service-ids="<?=$row->ids?>">
                <?php if (get_role("admin")): ?>
                <td class="text-center w-1">
                    <div class="custom-controls-stacked">
                        <label class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input service-checkbox" name="ids[]" value="<?=$row->ids?>">
                            <span class="custom-control-label"></span>
                        </label>
                    </div>
                </td>
                <?php endif; ?>
                <td class="text-center text-muted"><?=$row->id?></td>
                <td>
                    <div class="title"><?=$row->name?></div>
                </td>
                <td class="text-center">
                    <span class="badge badge-secondary"><?=isset($row->category_name) ? $row->category_name : '-'?></span>
                </td>
                
                <?php if (get_role("admin") || get_role("supporter")): ?>
                <td style="width: 10%;">
                    <div class="title">
                        <?php
                        if (!empty($row->add_type) && $row->add_type == "api") {
                            echo isset($row->api_name) ? truncate_string($row->api_name, 13) : '-';
                        } else {
                            echo lang('Manual');
                        }
                        ?>
                    </div>
                    <div class="text-muted small">
                        <?=(!empty($row->api_service_id)) ? $row->api_service_id : ""?>
                    </div>
                </td>
                <?php endif; ?>
                
                <td class="text-center" style="width: 8%;">
                    <div>
                        <?php
                        $service_price = $row->price;
                        if (!get_role('admin') && isset($custom_rates[$row->id])) {
                            $service_price = $custom_rates[$row->id]['service_price'];
                        }
                        ?>
                        <?php echo (double)$service_price; ?>
                    </div>
                    <?php 
                    if (get_role("admin") && isset($row->original_price)) {
                        $text_color = $row->original_price > $row->price ? "text-danger" : "text-muted";
                        echo '<small class="'.$text_color.'">'.(double)$row->original_price.'</small>';
                    }
                    ?>
                </td>
                
                <td class="text-center" style="width: 8%;"><?=$row->min?> / <?=$row->max?></td>
                
                <td style="width: 6%;">
                    <button class="btn btn-info btn-sm" type="button" data-toggle="modal" data-target="#desc_<?=$row->ids?>"><?=lang("Details")?></button>
                    <div id="desc_<?=$row->ids?>" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                        <?php $this->load->view('descriptions', ['service' => $row]); ?>
                    </div>
                </td>
                
                <?php if (get_role("admin") || get_role("supporter")): ?>
                <td class="w-1 text-center">
                    <?php if(!empty($row->dripfeed) && $row->dripfeed == 1): ?>
                    <span class="badge badge-info"><?=lang("Active")?></span>
                    <?php else: ?>
                    <span class="badge badge-warning"><?=lang("Deactive")?></span>
                    <?php endif; ?>
                </td>
                
                <td class="w-1 text-center">
                    <label class="custom-switch">
                        <input type="checkbox" name="item_status" data-id="<?=$row->id?>" data-action="<?=cn($module.'/ajax_toggle_item_status/')?>" class="custom-switch-input ajaxToggleItemStatus" <?php if(!empty($row->status) && $row->status == 1) echo 'checked'; ?>>
                        <span class="custom-switch-indicator"></span>
                    </label>
                </td>
                
                <td class="text-center" style="width: 5%;">
                    <a href="<?=cn("$module/update/".$row->ids)?>" class="ajaxModal"><i class="btn btn-info fe fe-edit btn-sm"> <?=lang('Edit')?></i></a>
                    <?php if (get_role("admin")): ?>
                    <br><br>
                    <a href="<?=cn("$module/ajax_delete_item/".$row->ids)?>" class="ajaxDeleteItem"><i class="btn btn-danger fe fe-trash btn-sm"> <?=lang('Delete')?></i></a>
                    <?php endif; ?>
                </td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <!-- Pagination Info -->
    <?php if (isset($pagination)): ?>
    <div class="pagination-info d-flex justify-content-between align-items-center mt-3 p-3 bg-light rounded">
        <div class="text-muted">
            <?php echo sprintf(lang("showing_x_to_y_of_z_entries"), $pagination['from'], $pagination['to'], $pagination['total']); ?>
        </div>
        <div class="pagination-controls">
            <?php if ($pagination['pages'] > 1): ?>
            <nav aria-label="Services pagination">
                <ul class="pagination pagination-sm mb-0">
                    <!-- Previous -->
                    <li class="page-item <?php echo $pagination['current_page'] <= 1 ? 'disabled' : ''; ?>">
                        <a class="page-link page-nav" href="#" data-page="<?php echo $pagination['current_page'] - 1; ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    
                    <?php
                    $start_page = max(1, $pagination['current_page'] - 2);
                    $end_page = min($pagination['pages'], $pagination['current_page'] + 2);
                    
                    if ($start_page > 1): ?>
                    <li class="page-item">
                        <a class="page-link page-nav" href="#" data-page="1">1</a>
                    </li>
                    <?php if ($start_page > 2): ?>
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                    <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                    <li class="page-item <?php echo $i == $pagination['current_page'] ? 'active' : ''; ?>">
                        <a class="page-link page-nav" href="#" data-page="<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                    <?php endfor; ?>
                    
                    <?php if ($end_page < $pagination['pages']): 
                        if ($end_page < $pagination['pages'] - 1): ?>
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                    <?php endif; ?>
                    <li class="page-item">
                        <a class="page-link page-nav" href="#" data-page="<?php echo $pagination['pages']; ?>"><?php echo $pagination['pages']; ?></a>
                    </li>
                    <?php endif; ?>
                    
                    <!-- Next -->
                    <li class="page-item <?php echo $pagination['current_page'] >= $pagination['pages'] ? 'disabled' : ''; ?>">
                        <a class="page-link page-nav" href="#" data-page="<?php echo $pagination['current_page'] + 1; ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php else: ?>
<div class="text-center p-5">
    <i class="fe fe-inbox text-muted" style="font-size: 48px;"></i>
    <p class="text-muted mt-3"><?php echo lang("no_services_found"); ?></p>
</div>
<?php endif; ?>
