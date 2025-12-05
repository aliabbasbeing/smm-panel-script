<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-file-text"></i> WhatsApp Message Templates</h3>
            <a href="<?php echo cn($module . '/template_create'); ?>" class="btn btn-primary float-right ajaxModal">
                <i class="fas fa-plus"></i> Create Template
            </a>
        </div>
        <div class="card-body">
            <?php if(!empty($templates)): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Message Preview</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($templates as $template): ?>
                    <tr>
                        <td><?php echo $template->name; ?></td>
                        <td><?php echo $template->description; ?></td>
                        <td><?php echo substr($template->message, 0, 100); ?>...</td>
                        <td><?php echo $template->created_at; ?></td>
                        <td>
                            <a href="<?php echo cn($module . '/template_edit/' . $template->ids); ?>" class="btn btn-sm btn-warning ajaxModal">Edit</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p class="text-center">No templates found. <a href="<?php echo cn($module . '/template_create'); ?>" class="ajaxModal">Create your first template</a></p>
            <?php endif; ?>
        </div>
    </div>
</div>
