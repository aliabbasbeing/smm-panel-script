<div class="row justify-content-md-center">
  <div class="col-md-12">
    <div class="page-header">
      <h1 class="page-title">
        <a href="<?php echo cn($module . '/api_create'); ?>" class="ajaxModal">
          <span class="add-new" data-toggle="tooltip" data-placement="bottom" title="Add New API Config">
            <i class="fa fa-plus-square text-primary" aria-hidden="true"></i>
          </span>
        </a>
        WhatsApp API Configurations
      </h1>
      <div class="page-subtitle">
        <a href="<?php echo cn($module); ?>" class="btn btn-sm btn-secondary">
          <i class="fe fe-arrow-left"></i> Back to Dashboard
        </a>
      </div>
    </div>
  </div>
</div>

<div class="row" id="result_ajaxSearch">
  <?php if(!empty($api_configs)){ ?>
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">WhatsApp API Configuration List</h3>
        <div class="card-options">
          <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-hover table-vcenter card-table">
          <thead>
            <tr>
              <th class="w-1">No.</th>
              <th>Name</th>
              <th>API URL</th>
              <th>API Type</th>
              <th>Phone Number</th>
              <th>Default</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            $i = ($page - 1) * $per_page;
            foreach ($api_configs as $api) {
              $i++;
            ?>
            <tr class="tr_<?php echo $api->id; ?>">
              <td class="w-1"><?php echo $i; ?></td>
              <td><strong><?php echo htmlspecialchars($api->name); ?></strong></td>
              <td><small><?php echo htmlspecialchars($api->api_url); ?></small></td>
              <td>
                <span class="badge badge-info"><?php echo ucwords(str_replace('_', ' ', $api->api_type)); ?></span>
              </td>
              <td><?php echo htmlspecialchars($api->phone_number ? $api->phone_number : '-'); ?></td>
              <td>
                <?php if($api->is_default){ ?>
                <span class="badge badge-success">Default</span>
                <?php } else { ?>
                <span class="text-muted">-</span>
                <?php } ?>
              </td>
              <td>
                <?php if($api->status == 1){ ?>
                <span class="badge badge-success">Active</span>
                <?php } else { ?>
                <span class="badge badge-danger">Inactive</span>
                <?php } ?>
              </td>
              <td>
                <div class="btn-group">
                  <a href="<?php echo cn($module . '/api_edit/' . $api->ids); ?>" 
                    class="btn btn-sm btn-icon ajaxModal" 
                    data-toggle="tooltip" 
                    title="Edit">
                    <i class="fe fe-edit"></i>
                  </a>
                  <a href="javascript:void(0)" 
                    class="btn btn-sm btn-icon btn-danger actionItem" 
                    data-id="<?php echo $api->ids; ?>" 
                    data-action="<?php echo cn($module . '/ajax_api_delete'); ?>" 
                    data-toggle="tooltip" 
                    title="Delete" 
                    data-confirm="Are you sure you want to delete this API configuration?">
                    <i class="fe fe-trash"></i>
                  </a>
                </div>
              </td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Pagination -->
    <?php if($total > $per_page){ ?>
    <div class="col-md-12">
      <div class="text-center">
        <?php 
        $total_pages = ceil($total / $per_page);
        echo pagination($module . '/api', $page, $total_pages);
        ?>
      </div>
    </div>
    <?php } ?>
    
  </div>
  <?php } else { ?>
  <div class="col-md-12">
    <div class="card">
      <div class="card-body text-center">
        <i class="fa fa-cog fa-3x text-muted mb-3"></i>
        <p class="text-muted">No API configurations found. Click the + button to add your first WhatsApp API configuration.</p>
      </div>
    </div>
  </div>
  <?php } ?>
</div>
