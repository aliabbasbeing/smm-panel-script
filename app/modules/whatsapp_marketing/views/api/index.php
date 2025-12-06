<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-cog"></i> WhatsApp API Configurations</h3>
            <a href="<?php echo cn($module . '/api_create'); ?>" class="btn btn-primary float-end ajaxModal">
                <i class="fas fa-plus"></i> Add API Config
            </a>
        </div>
        <div class="card-body">
            <?php if(!empty($api_configs)): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>API URL</th>
                        <th>Default</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($api_configs as $api): ?>
                    <tr>
                        <td><?php echo $api->name; ?></td>
                        <td><?php echo $api->api_url; ?></td>
                        <td><?php echo $api->is_default ? '<span class="badge bg-primary">Yes</span>' : 'No'; ?></td>
                        <td><?php echo $api->status ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>'; ?></td>
                        <td><?php echo $api->created_at; ?></td>
                        <td>
                            <a href="<?php echo cn($module . '/api_edit/' . $api->ids); ?>" class="btn btn-sm btn-warning ajaxModal">Edit</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p class="text-center">No API configurations found. <a href="<?php echo cn($module . '/api_create'); ?>" class="ajaxModal">Create your first API config</a></p>
            <?php endif; ?>
        </div>
    </div>
</div>
