<div class="row justify-content-md-center">
  <div class="col-md-12">
    <div class="page-header">
      <h1 class="page-title">
        <a href="<?php echo cn($module . '/template_create'); ?>" class="ajaxModal">
          <span class="add-new" data-toggle="tooltip" data-placement="bottom" title="Add New Template">
            <i class="fas fa-plus-square text-primary" aria-hidden="true"></i>
          </span>
        </a>
        Email Templates
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
  <?php if(!empty($templates)){ ?>
  <div class="col-md-12">
    <div class="card p-0 content">
      <div class="card-header">
        <h3 class="card-title" style="color:#fff !important;">Template List</h3>
        <div class="card-options">
          <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-hover table-vcenter card-table">
          <thead>
            <tr>
              <th class="w-1">No.</th>
              <th>Template Name</th>
              <th>Subject</th>
              <th>Description</th>
              <th>Created</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            $i = ($page - 1) * $per_page;
            foreach ($templates as $template) {
              $i++;
            ?>
            <tr class="tr_<?php echo $template->id; ?>">
              <td class="w-1"><?php echo $i; ?></td>
              <td><strong><?php echo htmlspecialchars($template->name); ?></strong></td>
              <td><?php echo htmlspecialchars($template->subject); ?></td>
              <td><?php echo htmlspecialchars(substr($template->description ?: '', 0, 50)); ?><?php echo strlen($template->description) > 50 ? '...' : ''; ?></td>
              <td><?php echo date('M d, Y', strtotime($template->created_at)); ?></td>
              <td>
                <div class="btn-group">
                  <a href="<?php echo cn($module . '/template_edit/' . $template->ids); ?>" 
                    class="btn btn-sm btn-icon ajaxModal" 
                    data-toggle="tooltip" 
                    title="Edit">
                    <i class="fe fe-edit"></i>
                  </a>
                  <a href="javascript:void(0)" 
                    class="btn btn-sm btn-icon btn-danger actionItem" 
                    data-id="<?php echo $template->ids; ?>" 
                    data-action="<?php echo cn($module . '/ajax_template_delete'); ?>" 
                    data-toggle="tooltip" 
                    title="Delete" 
                    data-confirm="Are you sure you want to delete this template?">
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
    
    <?php if($total > $per_page){ ?>
    <div class="card-footer">
      <ul class="pagination">
        <?php
        $total_pages = ceil($total / $per_page);
        for($p = 1; $p <= $total_pages; $p++){
          $active = ($p == $page) ? 'active' : '';
        ?>
        <li class="page-item <?php echo $active; ?>">
          <a class="page-link" href="<?php echo cn($module . '/templates/' . $p); ?>"><?php echo $p; ?></a>
        </li>
        <?php } ?>
      </ul>
    </div>
    <?php } ?>
  </div>
  <?php } else { ?>
    <div class="col-md-12">
      <?php echo Modules::run("blocks/empty_data"); ?>
      <div class="text-center mt-3">
        <a href="<?php echo cn($module . '/template_create'); ?>" class="btn btn-primary ajaxModal">
          <i class="fe fe-plus"></i> Create Your First Template
        </a>
      </div>
    </div>
  <?php } ?>
</div>

<script>
$(document).ready(function(){
  $('.actionItem').on('click', function(e){
    e.preventDefault();
    var $this = $(this);
    var ids = $this.data('id');
    var action = $this.data('action');
    var confirm_msg = $this.data('confirm');
    
    if(confirm_msg && !confirm(confirm_msg)){
      return;
    }
    
    $.ajax({
      url: action,
      type: 'POST',
      dataType: 'json',
      data: {ids: ids, csrf_test_name: $('input[name="csrf_test_name"]').val()},
      success: function(response){
        if(response.status == 'success'){
          show_message(response.message, 'success');
          setTimeout(function(){
            location.reload();
          }, 1000);
        } else {
          show_message(response.message, 'error');
        }
      }
    });
  });
});
</script>
