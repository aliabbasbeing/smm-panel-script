<div class="container-fluid">
    <div class="row justify-content-center mt-5">
        <div class="col-md-8">
            <div class="card border-warning">
                <div class="card-header bg-warning text-white">
                    <h3 class="card-title mb-0">
                        <i class="<?php echo $module_icon; ?>"></i> <?php echo $module_name; ?> - Installation Required
                    </h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning" role="alert">
                        <h4 class="alert-heading"><i class="fe fe-alert-triangle"></i> Database Tables Not Found</h4>
                        <p>The required database tables for Email Marketing are not installed in your database.</p>
                    </div>
                    
                    <h5 class="mt-4"><i class="fe fe-tool"></i> Installation Instructions</h5>
                    <p>To enable the Email Marketing module, you need to import the database schema:</p>
                    
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="font-weight-bold">Option 1: Using phpMyAdmin</h6>
                            <ol>
                                <li>Login to your cPanel and open phpMyAdmin</li>
                                <li>Select your database</li>
                                <li>Click on the "Import" tab</li>
                                <li>Upload the file: <code><?php echo $sql_file; ?></code></li>
                                <li>Click "Go" to import the database schema</li>
                            </ol>
                        </div>
                    </div>
                    
                    <div class="card bg-light mt-3">
                        <div class="card-body">
                            <h6 class="font-weight-bold">Option 2: Using MySQL Command Line</h6>
                            <pre class="bg-dark text-white p-3 rounded"><code>mysql -u your_username -p your_database < <?php echo $sql_file; ?></code></pre>
                            <small class="text-muted">Replace <code>your_username</code> and <code>your_database</code> with your actual database credentials.</small>
                        </div>
                    </div>
                    
                    <div class="card bg-light mt-3">
                        <div class="card-body">
                            <h6 class="font-weight-bold">Required Tables</h6>
                            <ul>
                                <li><code>email_campaigns</code> - Store marketing campaigns</li>
                                <li><code>email_templates</code> - Store email templates</li>
                                <li><code>email_smtp_configs</code> - Store SMTP configurations</li>
                                <li><code>email_recipients</code> - Store campaign recipients</li>
                                <li><code>email_logs</code> - Store activity logs</li>
                                <li><code>email_settings</code> - Store module settings</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="alert alert-info mt-4" role="alert">
                        <i class="fe fe-info"></i> <strong>Note:</strong> After importing the database schema, refresh this page to access the Email Marketing module.
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="<?php echo cn($module); ?>" class="btn btn-primary">
                            <i class="fe fe-refresh-cw"></i> Refresh Page
                        </a>
                        <a href="<?php echo cn(); ?>" class="btn btn-secondary">
                            <i class="fe fe-home"></i> Go to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
