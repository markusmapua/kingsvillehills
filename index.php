<?php
session_start();
require 'db_connect.php';

// Checks if there are announcements
$ann_query = "SELECT * FROM announcements ORDER BY date_posted DESC";
$ann_result = $conn->query(query: $ann_query);

$has_announcements = $ann_result->num_rows > 0;

// Is the user logged in? If not, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header(header: "location: login.php");
    exit();
}
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

                    <!-- Dynamic card row to show pending stuff -->
                    <div class="row">
                        <!-- Pending Complaints / Notices -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                <a href="view_complaint.php">Pending Complaints</a></div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">1</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-comments fa-2x text-gray-300"></i>
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
                                    <?php if ($_SESSION['role'] === 'admin') {
                                        echo '<a href="#" data-toggle="modal" data-target="#createAnnouncementModal" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                                        <i class="fas fa-bullhorn"></i>Create Announcement</a>';
                                    } ?>
                                </div>
                                
                                <div class="card-body p-0" style="min-height: 20rem; overflow-x: hidden;">
                                    <?php if ($has_announcements): ?>
                                        <div class="list-group list-group-flush">
                                            <?php while ($ann_query = $ann_result->fetch_assoc()): ?>
                                                
                                                <div class="list-group-item list-group-item-action p-4 border-left-primary">
                                                    <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                                                        <h6 class="font-weight-bold text-primary mb-0 text-truncate" style="max-width: 70%;">
                                                            <?php echo htmlspecialchars(string: $ann_query['title']); ?>
                                                        </h6>
                                                        <small class="text-muted text-nowrap">
                                                            <i class="fas fa-calendar-day mr-1"></i>
                                                            <?php echo date(format: 'M d, Y', timestamp: strtotime(datetime: $ann_query['date_posted'])); ?>
                                                        </small>
                                                    </div>
                                                    
                                                    <p class="mb-2 text-gray-800 text-truncate">
                                                        <?php echo htmlspecialchars(string: $ann_query['message']); ?>
                                                    </p>

                                                    <div class="mt-2 d-flex justify-content-between align-items-center">
                                                        <button class="btn btn-link btn-sm p-0 text-primary font-weight-bold view-announcement"
                                                                data-toggle="modal" 
                                                                data-target="#viewAnnouncementModal"
                                                                data-title="<?php echo htmlspecialchars(string: $ann_query['title']); ?>"
                                                                data-date="<?php echo date(format: 'M d, Y', timestamp: strtotime($ann_query['date_posted'])); ?>"
                                                                data-message="<?php echo htmlspecialchars(string: $ann_query['message']); ?>">
                                                            Read More...
                                                        </button>

                                                        <?php if ($_SESSION['role'] === 'admin'): ?>
                                                            <div>
                                                                <a href="#" class="btn btn-sm btn-outline-secondary mr-2" title="Edit Announcement">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                
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
                                    <?php else: ?>
                                        <div class="d-flex flex-column justify-content-center align-items-center h-100 py-5">
                                            <i class="fas fa-bullhorn fa-3x text-gray-300 mb-3"></i>
                                            <p class="text-gray-500 mb-0"><i>No Announcements at this time.</i></p>
                                        </div>
                                    <?php endif; ?>
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

                    <!-- Content Row -->
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
            <?php include 'footer.php'; ?>

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

    <!-- Delete Announcement Modal-->
    <?php include 'includes/delete_ann_modal.php'; ?>

    <!-- Logout Modal-->
    <?php include 'logout_modal.php'; ?>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/chart-area-demo.js"></script>
    <script src="js/demo/chart-pie-demo.js"></script>

    <!-- View Announcement Modal Script -->
    <script>
    $(document).ready(function() {
        $('#viewAnnouncementModal').on('show.bs.modal', function (event) {
            // 1. Find the exact button that was clicked
            var button = $(event.relatedTarget); 
            
            // 2. Extract the data from that button's attributes
            var title = button.attr('data-title');
            var date = button.attr('data-date');
            var message = button.attr('data-message');
            
            // 3. Find the modal itself and inject the text into the correct IDs
            var modal = $(this);
            modal.find('#viewTitle').text(title);
            modal.find('#viewDate').text('Posted on: ' + date);
            modal.find('#viewMessage').text(message);
        });
    });
    </script>

    <!-- Delete Announcement Modal Script -->
    <script>
    $(document).ready(function() {
        $('#deleteAnnouncementModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); 
            var announcementId = button.attr('data-id'); 

            $('#confirmDeleteBtn').attr('href', 'includes/delete_announcement.php?id=' + announcementId);
        });
    });
    </script>

</body>

</html>