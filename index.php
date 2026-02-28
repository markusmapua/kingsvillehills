<?php
session_start();
require 'db_connect.php';
require 'includes/auth_check.php';

$timeout_duration = 30; // 30 minutes
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > ($timeout_duration * 60)) {
    session_unset();
    session_destroy();
    header(header: "location: login.php");
    exit();
}
$_SESSION['last_activity'] = time();

// Checks if there are announcements
$ann_query = "SELECT * FROM announcements ORDER BY date_posted DESC";
$ann_result = $conn->query(query: $ann_query);
$has_announcements = $ann_result->num_rows > 0;

// Start of quick card stuff

// Initialize counts to zero
$pending_complaints_count = 0;
$resolved_complaints_count = 0;
$pending_notices_count = 0;
$resolved_notices_count = 0;

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Bases queries on role
if ($role === 'admin') {
    // ADMIN = Count everything in the subdivision
    $complaint_sql = "SELECT 
                    SUM(CASE WHEN complaint_status = 'pending' THEN 1 ELSE 0 END) AS pending_comp,
                    SUM(CASE WHEN complaint_status = 'resolved' THEN 1 ELSE 0 END) AS resolved_comp
                 FROM complaints";
                 
    $notice_sql = "SELECT 
                    SUM(CASE WHEN notice_status = 'pending' THEN 1 ELSE 0 END) AS pending_not,
                    SUM(CASE WHEN notice_status = 'resolved' THEN 1 ELSE 0 END) AS resolved_not
                   FROM notices";
                   
    $complaint_stmt = $conn->prepare($complaint_sql);
    $notice_stmt = $conn->prepare($notice_sql);

} else {
    // RESIDENT = Count ONLY items matching their user_id
    $complaint_sql = "SELECT 
                    SUM(CASE WHEN complaint_status = 'pending' THEN 1 ELSE 0 END) AS pending_comp,
                    SUM(CASE WHEN complaint_status = 'resolved' THEN 1 ELSE 0 END) AS resolved_comp
                 FROM complaints WHERE resident_id = ?";
                 
    $notice_sql = "SELECT 
                    SUM(CASE WHEN notice_status = 'pending' THEN 1 ELSE 0 END) AS pending_not,
                    SUM(CASE WHEN notice_status = 'resolved' THEN 1 ELSE 0 END) AS resolved_not
                   FROM notices WHERE resident_id = ?";
                   
    $complaint_stmt = $conn->prepare($complaint_sql);
    $complaint_stmt->bind_param("i", $user_id);
    
    $notice_stmt = $conn->prepare($notice_sql);
    $notice_stmt->bind_param("i", $user_id);
}

$complaint_stmt->execute();
$comp_result = $complaint_stmt->get_result();
if ($comp_row = $comp_result->fetch_assoc()) {
    $pending_complaints_count = (int)$comp_row['pending_comp'];
    $resolved_complaints_count = (int)$comp_row['resolved_comp'];
}
$complaint_stmt->close();

$notice_stmt->execute();
$notice_result = $notice_stmt->get_result();
if ($notice_row = $notice_result->fetch_assoc()) {
    $pending_notices_count = (int)$notice_row['pending_not'];
    $resolved_notices_count = (int)$notice_row['resolved_not'];
}
$notice_stmt->close();
// End of quick card stuff
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Kingsville Connect - Dashboard</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="css/main.css" rel="stylesheet">

    <!-- Calendar stuff / FullCalendar -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.20/index.global.min.js'></script>

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar Module -->
        <?php include 'sidebar.php'; ?>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <?php include 'topbar.php'; ?>

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <?php if ($_SESSION['role'] === 'admin') {
                            echo '<h1 class="h3 mb-0 text-gray-800">Admin Dashboard</h1>';
                        } else {
                            echo '<h1 class="h3 mb-0 text-gray-800">Resident Dashboard</h1>';
                        } ?>
                    </div>

                    <!-- Dynamic card row to serve as quick access -->
                    <div class="row">

                        <!-- Pending Complaints / Notices -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                <a href="view_complaint.php">Pending Complaints</a></div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $pending_complaints_count; ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-comments fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Resolved Complaints -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                <a href="view_complaint.php">Resolved Complaints</a></div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $resolved_complaints_count; ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-check fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Notices -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                <a href="view_visitor_notice.php">Pending Notices</a></div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $pending_notices_count; ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-bell fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Resolved Notices -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                <a href="view_visitor_notice.php">Resolved Notices</a></div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $resolved_notices_count; ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-bell fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content Row -->
                    <div class="row">

                        <!-- Announcements Card -->
                        <div class="col-xl-8 col-lg-7 mb-4">
                            <div class="card shadow h-100">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Announcements</h6>
                                    <?php if ($_SESSION['role'] === 'admin'): ?>
                                        <a href="#" data-toggle="modal" data-target="#createAnnouncementModal" class="btn btn-sm btn-primary shadow-sm">
                                        <i class="fas fa-bullhorn mr-1"></i>
                                        <span class="d-none d-sm-inline">Create Announcement</a></span>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- PHP stuff that dynamically displays announcements -->
                                <div class="card-body p-0" style="min-height: 20rem; overflow-x: hidden;">
                                    <?php if ($has_announcements): ?>
                                        <div style="max-height: 350px; overflow-y: auto;">
                                            <div class="list-group list-group-flush">
                                                <?php while ($ann_query = $ann_result->fetch_assoc()): ?>               
                                                    
                                                    <div class="list-group-item list-group-item-action p-4">
                                                        <div class="d-flex w-100 flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3">
                                                            <h6 class="font-weight-bold text-primary mb-2 mb-md-0" style="max-width: 70%;">
                                                                <?php echo htmlspecialchars($ann_query['title']); ?>
                                                            </h6>
                                                            <small class="text-muted">
                                                                <i class="fas fa-calendar-day mr-1"></i>
                                                                <?php echo date('M d, Y g:i A', strtotime($ann_query['date_posted'])); ?>
                                                                <?php if (!empty($ann_query['updated_at'])): ?>
                                                                    <small class="text-warning ml-2 font-italic">(Edited on: <?php echo date('M d, Y g:i A', strtotime($ann_query['updated_at'])); ?>)</small>
                                                                <?php endif; ?>
                                                            </small>
                                                        </div>
                                                        
                                                        <p class="mb-2 text-gray-800" style="white-space: pre-wrap; word-break: break-word;"><?php echo htmlspecialchars($ann_query['message']); ?></p>

                                                        <div class="mt-2 d-flex justify-content-between align-items-center">
                                                            <button class="btn btn-link btn-sm p-0 text-primary font-weight-bold view-announcement"
                                                                    data-toggle="modal" 
                                                                    data-target="#viewAnnouncementModal"
                                                                    data-title="<?php echo htmlspecialchars($ann_query['title']); ?>"
                                                                    data-updated-at="<?php echo !empty($ann_query['updated_at']) ? date('M d, Y g:i A', strtotime($ann_query['updated_at'])) : ''; ?>"
                                                                    data-date="<?php echo date('M d, Y g:i A', strtotime($ann_query['date_posted'])); ?>"
                                                                    data-message="<?php echo htmlspecialchars($ann_query['message']); ?>">
                                                                Read More...
                                                            </button>

                                                            <?php if ($_SESSION['role'] === 'admin'): ?>
                                                                <div>
                                                                    <!-- Edit Announcement -->
                                                                    <button type="button" 
                                                                            class="btn btn-sm btn-outline-warning" 
                                                                            data-toggle="modal" 
                                                                            data-target="#editAnnouncementModal"
                                                                            data-id="<?php echo $ann_query['announcement_id']; ?>"
                                                                            data-title="<?php echo htmlspecialchars($ann_query['title']); ?>"
                                                                            data-message="<?php echo htmlspecialchars($ann_query['message']); ?>">
                                                                        <i class="fas fa-edit"></i>
                                                                    </button>
                                                                    
                                                                    <!-- Delete Announcement -->
                                                                    <button type="button" 
                                                                            class="btn btn-sm btn-outline-danger" 
                                                                            data-toggle="modal" 
                                                                            data-target="#deleteAnnouncementModal"
                                                                            data-id="<?php echo $ann_query['announcement_id']; ?>">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>

                                                <?php endwhile; ?>
                                            </div>
                                        </div> <?php else: ?>
                                        <div class="d-flex flex-column justify-content-center align-items-center h-100 py-5">
                                            <i class="fas fa-bullhorn fa-3x text-gray-300 mb-3"></i>
                                            <p class="text-gray-500 mb-0"><i>No Announcements at this time.</i></p>
                                        </div>
                                    <?php endif; ?>
                                </div> <div class="card-footer text-center bg-white border-top py-3">
                                    <a href="all_announcements.php" class="font-weight-bold text-primary text-uppercase text-xs text-decoration-none">
                                        View All Announcements <i class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Schedule Card -->
                        <div class="col-xl-4 col-lg-5 mb-4">
                            <div class="card shadow h-100">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Schedules</h6>
                                </div>
                                <div id="calendar"></div>
                            </div>
                        </div>

                    </div>
                    <!-- End of Content Row 1 -->

                    <!-- Content Row 2 -->
                    <div class="row">

                        <!-- Content Column -->
                        <div class="col-lg-6 mb-4">

                            <!-- Emergency Contacts -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Emergency Contacts</h6>
                                </div>
                                <div class="card-body">
                                    <p>Contact numbers in case of an emergency:</p>
                                    <ul>
                                        <li><strong>PNP Antipolo:</strong>
                                            <ul>
                                                <li>0917 157 7627</li>
                                                <li>0998 589 5717</li>
                                            </ul>
                                        </li>
                                        <li><strong>BFP Antipolo:</strong>
                                            <ul>
                                                <li>(02) 8871 2865</li>
                                                <li>(02) 8533 8591</li>
                                                <li>0945 155 6015</li>
                                            </ul>
                                        </li>
                                        <li><strong>CDRRMO / Rescue:</strong>
                                            <ul>
                                                <li>(02) 8689 4576</li>
                                                <li>(02) 8689 4564</li>
                                            </ul>
                                        </li>
                                        <li><strong>Medical Emergency:</strong>
                                            <ul>
                                                <li>Rizal Provincial Hospital Annex 1: (02) 8639 8453 / (02) 8251 1559</li>
                                                <li>Rizal Provincial Hospital Annex 2: (02) 8941 8518 / (02) 8997 9401</li>
                                                <li>Antipolo Doctors Hospital: (02) 8650 8269</li>
                                            </ul>
                                        </li>
                                    </ul>
                            </div>

                        </div>

                        <div class="col-lg-6 mb-4">

                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <?php include 'includes/footer.php'; ?>

            <!-- Calendar script -->
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var calendarEl = document.getElementById('calendar');
                    var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    themeSystem: 'bootstrap',

                    headerToolbar: {
                        left: 'prev,next',
                        center: 'title',
                        right: 'today'
                    }
                });
                calendar.render();
            });
            </script>

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Create Announcement Modal-->
    <?php include 'includes/create_announcement.php'; ?>

    <!-- View Announcement Modal-->
    <?php include 'includes/announcement_details.php'; ?>

    <!-- Edit Announcement Modal-->
    <?php include 'includes/edit_ann_modal.php'; ?>

    <!-- Delete Announcement Modal-->
    <?php include 'includes/delete_ann_modal.php'; ?>

    <!-- Logout Modal-->
    <?php include 'includes/logout_modal.php'; ?>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Announcements Modal Script -->
    <script src="js/announcements.js"></script>
    
    <!-- Sidebar State Script 'cause its annoying when the thing is opening by default lol -->
    <!-- <script src="js/sidebar_state.js"></script> forget it it's more annoying when it flashes :/ -->

</body>

</html>