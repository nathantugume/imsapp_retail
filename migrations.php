<?php
require_once("init/init.php");

// Check if user is logged in and is Master
if(!isset($_SESSION['LOGGEDIN']) || $_SESSION['LOGGEDIN']['role'] !== 'Master'){
    header("location:login.php?unauth=unauthorized access");
    exit();
}

require_once("migrations/DatabaseMigration.php");
$migration = new DatabaseMigration();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Database Migrations - IMS</title>
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.js"></script>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/custom.css">
    <link rel="stylesheet" href="css/sweetalert2.min.css">
    <script src="js/sweetalert2.min.js"></script>
    <style>
        .migration-card {
            border-left: 4px solid #007bff;
            margin-bottom: 15px;
        }
        .migration-applied {
            border-left-color: #28a745;
            background-color: #f8fff9;
        }
        .migration-pending {
            border-left-color: #ffc107;
            background-color: #fffdf5;
        }
        .migration-error {
            border-left-color: #dc3545;
            background-color: #fff5f5;
        }
        .status-badge {
            font-size: 0.8em;
            padding: 4px 8px;
        }
        .migration-actions {
            margin-top: 10px;
        }
        .migration-description {
            color: #666;
            font-size: 0.9em;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <?php include("common/navbar.php"); ?>
    
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fa fa-database"></i> Database Migrations
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <button class="btn btn-success btn-lg" onclick="runAllMigrations()">
                                    <i class="fa fa-play"></i> Run All Migrations
                                </button>
                                <button class="btn btn-info btn-lg ml-2" onclick="refreshStatus()">
                                    <i class="fa fa-refresh"></i> Refresh Status
                                </button>
                            </div>
                            <div class="col-md-6 text-right">
                                <div class="alert alert-info mb-0">
                                    <strong>Note:</strong> Migrations will apply database schema changes. 
                                    Make sure to backup your database before running migrations.
                                </div>
                            </div>
                        </div>
                        
                        <div id="migration-results" class="mb-4"></div>
                        
                        <h5>Migration Status</h5>
                        <div id="migration-status">
                            <?php
                            $status = $migration->getMigrationStatus();
                            foreach($status as $migrationInfo):
                                $applied = $migrationInfo['applied'];
                                $cardClass = $applied ? 'migration-applied' : 'migration-pending';
                                $badgeClass = $applied ? 'badge-success' : 'badge-warning';
                                $badgeText = $applied ? 'Applied' : 'Pending';
                            ?>
                            <div class="card migration-card <?php echo $cardClass; ?>">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h6 class="mb-1">
                                                <span class="badge <?php echo $badgeClass; ?> status-badge"><?php echo $badgeText; ?></span>
                                                <?php echo $migrationInfo['id']; ?>
                                            </h6>
                                            <div class="migration-description">
                                                <?php echo $migrationInfo['description']; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-right">
                                            <div class="migration-actions">
                                                <?php if($applied): ?>
                                                    <button class="btn btn-sm btn-outline-danger" onclick="rollbackMigration('<?php echo $migrationInfo['id']; ?>')">
                                                        <i class="fa fa-undo"></i> Rollback
                                                    </button>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-outline-primary" onclick="runMigration('<?php echo $migrationInfo['id']; ?>')">
                                                        <i class="fa fa-play"></i> Run
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <h5 class="mt-4">Applied Migrations History</h5>
                        <div class="table-responsive">
                            <table class="table table-striped" id="migration-history">
                                <thead>
                                    <tr>
                                        <th>Migration ID</th>
                                        <th>Description</th>
                                        <th>Applied At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $appliedMigrations = $migration->getAppliedMigrations();
                                    foreach($appliedMigrations as $appliedMigration):
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($appliedMigration['id']); ?></td>
                                        <td><?php echo htmlspecialchars($appliedMigration['description']); ?></td>
                                        <td><?php echo date('Y-m-d H:i:s', strtotime($appliedMigration['applied_at'])); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function runAllMigrations() {
            Swal.fire({
                title: 'Run All Migrations?',
                text: 'This will apply all pending database migrations. Make sure you have a backup!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, run all migrations!'
            }).then((result) => {
                if (result.isConfirmed) {
                    runMigrationRequest('all');
                }
            });
        }
        
        function runMigration(migrationId) {
            Swal.fire({
                title: 'Run Migration?',
                text: 'This will apply the database migration: ' + migrationId,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#007bff',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, run migration!'
            }).then((result) => {
                if (result.isConfirmed) {
                    runMigrationRequest(migrationId);
                }
            });
        }
        
        function rollbackMigration(migrationId) {
            Swal.fire({
                title: 'Rollback Migration?',
                text: 'This will rollback the migration: ' + migrationId + '. This may cause data loss!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, rollback!'
            }).then((result) => {
                if (result.isConfirmed) {
                    rollbackMigrationRequest(migrationId);
                }
            });
        }
        
        function runMigrationRequest(migrationId) {
            $.ajax({
                url: 'migrations/run-migration.php',
                type: 'POST',
                data: { migration_id: migrationId },
                dataType: 'json',
                beforeSend: function() {
                    Swal.fire({
                        title: 'Running Migration...',
                        text: 'Please wait while the migration is being applied.',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(response) {
                    Swal.close();
                    if (response.success) {
                        if (response.skipped) {
                            Swal.fire('Info', response.message, 'info');
                        } else {
                            Swal.fire('Success', response.message, 'success');
                        }
                        refreshStatus();
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.close();
                    Swal.fire('Error', 'An error occurred while running the migration.', 'error');
                }
            });
        }
        
        function rollbackMigrationRequest(migrationId) {
            $.ajax({
                url: 'migrations/rollback-migration.php',
                type: 'POST',
                data: { migration_id: migrationId },
                dataType: 'json',
                beforeSend: function() {
                    Swal.fire({
                        title: 'Rolling Back Migration...',
                        text: 'Please wait while the migration is being rolled back.',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(response) {
                    Swal.close();
                    if (response.success) {
                        if (response.skipped) {
                            Swal.fire('Info', response.message, 'info');
                        } else {
                            Swal.fire('Success', response.message, 'success');
                        }
                        refreshStatus();
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.close();
                    Swal.fire('Error', 'An error occurred while rolling back the migration.', 'error');
                }
            });
        }
        
        function refreshStatus() {
            location.reload();
        }
    </script>
</body>
</html>






