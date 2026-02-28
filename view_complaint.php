<?php
session_start();
require 'db_connect.php';

$timeout_duration = 30;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > ($timeout_duration * 60)) {
    session_unset();
    session_destroy();
    header("location: login.php");
    exit();
}
$_SESSION['last_activity'] = time();

if (!isset($_SESSION['user_id'])) {
    header("location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Admin sees all complaints; residents see only their own
if ($role === 'admin') {
    $query = "SELECT c.*, u.first_name, u.last_name, u.email 
              FROM complaints c 
              JOIN users u ON c.resident_id = u.user_id 
              ORDER BY c.complaint_id DESC";
} else {
    $query = "SELECT c.*, u.first_name, u.last_name, u.email 
              FROM complaints c 
              JOIN users u ON c.resident_id = u.user_id 
              WHERE c.resident_id = '$user_id' 
              ORDER BY c.complaint_id DESC";
}

$result = $conn->query($query);

// Handle admin status update
$update_msg = '';
if ($role === 'admin' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $complaint_id = intval($_POST['complaint_id']);
    $new_status = $_POST['new_status'];
    $remarks = trim(mysqli_real_escape_string($conn, $_POST['remarks']));
    $allowed = ['Pending', 'Resolved'];
    if (in_array($new_status, $allowed)) {
        $update_query = "UPDATE complaints SET complaint_status='$new_status', remarks='$remarks' WHERE complaint_id='$complaint_id'";
        if ($conn->query($update_query)) {
            header("location: view_complaint.php?updated=1");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Kingsville Connect - View Complaints</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="css/main.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
</head>

<body id="page-top">

    <div id="wrapper">

        <?php include 'sidebar.php'; ?>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">

                <?php include 'topbar.php'; ?>

                <div class="container-fluid">

                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">
                            <?php echo $role === 'admin' ? 'All Complaints' : 'My Complaints'; ?>
                        </h1>
                        <?php if ($role !== 'admin'): ?>
                            <a href="submit_complaints.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                                <i class="fas fa-plus fa-sm text-white-50"></i> Submit New Complaint
                            </a>
                        <?php endif; ?>
                    </div>

                    <?php if (isset($_GET['updated'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle mr-2"></i>Complaint status updated successfully.
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                    <?php endif; ?>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-list mr-2"></i>Complaint Records
                            </h6>
                        </div>
                        <div class="card-body">
                            <?php if ($result->num_rows > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover" id="complaintsTable" width="100%" cellspacing="0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>#</th>
                                                <?php if ($role === 'admin'): ?><th>Resident</th><?php endif; ?>
                                                <th>Title</th>
                                                <th>Description</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $count = 1; while ($row = $result->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo $count++; ?></td>
                                                    <?php if ($role === 'admin'): ?>
                                                        <td>
                                                            <strong><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></strong>
                                                            <br><small class="text-muted"><?php echo htmlspecialchars($row['email']); ?></small>
                                                        </td>
                                                    <?php endif; ?>
                                                    <td class="font-weight-bold"><?php echo htmlspecialchars($row['title']); ?></td>
                                                    <td style="max-width: 300px;">
                                                        <p class="mb-0 text-truncate" style="max-width: 280px;" title="<?php echo htmlspecialchars($row['description']); ?>">
                                                            <?php echo htmlspecialchars($row['description']); ?>
                                                        </p>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php if ($row['complaint_status'] === 'Resolved'): ?>
                                                            <span class="badge badge-success px-3 py-2">Resolved</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-warning px-3 py-2 text-white">Pending</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <button class="btn btn-info btn-sm view-complaint-btn"
                                                            data-toggle="modal"
                                                            data-target="#viewComplaintModal"
                                                            data-id="<?php echo $row['complaint_id']; ?>"
                                                            data-title="<?php echo htmlspecialchars($row['title']); ?>"
                                                            data-description="<?php echo htmlspecialchars($row['description']); ?>"
                                                            data-status="<?php echo $row['complaint_status']; ?>"
                                                            data-remarks="<?php echo htmlspecialchars($row['remarks'] ?? ''); ?>"
                                                            <?php if ($role === 'admin'): ?>
                                                            data-resident="<?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?>"
                                                            data-email="<?php echo htmlspecialchars($row['email']); ?>"
                                                            <?php endif; ?>
                                                            title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-clipboard fa-4x text-gray-300 mb-3"></i>
                                    <p class="text-gray-500">
                                        <?php echo $role === 'admin' ? 'No complaints have been submitted yet.' : 'You have not submitted any complaints yet.'; ?>
                                    </p>
                                    <?php if ($role !== 'admin'): ?>
                                        <a href="submit_complaints.php" class="btn btn-primary btn-sm">Submit a Complaint</a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>

            <?php include 'includes/footer.php'; ?>
        </div>
    </div>

    <!-- View / Manage Complaint Modal -->
    <div class="modal fade" id="viewComplaintModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-wrench mr-2"></i>Complaint Details</h5>
                    <button class="close text-white" type="button" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <?php if ($role === 'admin'): ?>
                        <div class="mb-3 p-3 bg-light rounded border">
                            <strong><i class="fas fa-user mr-1 text-muted"></i> Submitted by:</strong>
                            <span id="modal-resident" class="ml-2"></span>
                            <br>
                            <strong><i class="fas fa-envelope mr-1 text-muted"></i> Email:</strong>
                            <span id="modal-email" class="ml-2"></span>
                        </div>
                    <?php endif; ?>
                    <div class="mb-3">
                        <label class="font-weight-bold text-uppercase text-xs text-muted">Title</label>
                        <p id="modal-title" class="font-weight-bold text-gray-800 mb-0"></p>
                    </div>
                    <div class="mb-3">
                        <label class="font-weight-bold text-uppercase text-xs text-muted">Description</label>
                        <p id="modal-description" class="text-gray-700 mb-0" style="white-space: pre-wrap;"></p>
                    </div>
                    <div class="mb-3">
                        <label class="font-weight-bold text-uppercase text-xs text-muted">Current Status</label>
                        <div id="modal-status-badge"></div>
                    </div>
                    <?php if ($role !== 'admin'): ?>
                        <div id="remarks-section" class="mb-0">
                            <label class="font-weight-bold text-uppercase text-xs text-muted">Remarks from Management</label>
                            <p id="modal-remarks" class="text-gray-700 fst-italic mb-0"></p>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($role === 'admin'): ?>
                        <hr>
                        <form method="POST" action="view_complaint.php" id="updateComplaintForm">
                            <input type="hidden" name="complaint_id" id="form-complaint-id">
                            <div class="form-group">
                                <label class="font-weight-bold">Update Status</label>
                                <select class="form-control" name="new_status" id="form-new-status">
                                    <option value="Pending">Pending</option>
                                    <option value="Resolved">Resolved</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">Remarks / Notes <small class="text-muted font-weight-normal">(optional — visible to resident)</small></label>
                                <textarea class="form-control" name="remarks" id="form-remarks" rows="3" placeholder="e.g. Issue has been forwarded to maintenance..."></textarea>
                            </div>
                            <div class="text-right">
                                <button type="button" class="btn btn-secondary mr-2" data-dismiss="modal">Close</button>
                                <button type="submit" name="update_status" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
                <?php if ($role !== 'admin'): ?>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <?php include 'logout_modal.php'; ?>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <script>
    $(document).ready(function() {
        $('#complaintsTable').DataTable({
            order: [[0, 'desc']],
            pageLength: 10
        });

        $('.view-complaint-btn').on('click', function() {
            var btn = $(this);
            $('#modal-title').text(btn.data('title'));
            $('#modal-description').text(btn.data('description'));

            var status = btn.data('status');
            var badgeHtml = status === 'Resolved'
                ? '<span class="badge badge-success px-3 py-2">Resolved</span>'
                : '<span class="badge badge-warning px-3 py-2 text-white">Pending</span>';
            $('#modal-status-badge').html(badgeHtml);

            <?php if ($role === 'admin'): ?>
                $('#modal-resident').text(btn.data('resident'));
                $('#modal-email').text(btn.data('email'));
                $('#form-complaint-id').val(btn.data('id'));
                $('#form-new-status').val(status);
                $('#form-remarks').val(btn.data('remarks'));
            <?php else: ?>
                var remarks = btn.data('remarks');
                if (remarks) {
                    $('#modal-remarks').text('"' + remarks + '"');
                } else {
                    $('#modal-remarks').text('No remarks yet.');
                }
            <?php endif; ?>
        });
    });
    </script>

</body>
</html>
