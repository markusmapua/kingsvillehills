<?php
session_start();
require 'db_connect.php';
require 'includes/auth_check.php';

$limit = 8; // Max number of announcements per page

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

$offset = ($page - 1) * $limit;

// Math shit that calculates how many pages
$count_sql = "SELECT COUNT(*) AS total FROM announcements";
$count_result = $conn->query($count_sql);
$count_row = $count_result->fetch_assoc();
$total_announcements = $count_row['total'];

$total_pages = ceil($total_announcements / $limit);

$ann_sql = "SELECT * FROM announcements ORDER BY date_posted DESC LIMIT $limit OFFSET $offset";
$ann_result = $conn->query($ann_sql);

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Kingsville Connect - All Announcements</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="css/main.css" rel="stylesheet">

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <?php include 'topbar.php'; ?>

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Announcement Page -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">All Announcements</h1>
                        <a href="index.php" class="btn btn-primary btn-sm">Back to Home</a>
                    </div>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">Announcement History</h6>
                            <?php if ($_SESSION['role'] === 'admin'): ?>
                                <a href="#" data-toggle="modal" data-target="#createAnnouncementModal" class="btn btn-sm btn-primary shadow-sm">
                                <i class="fas fa-bullhorn mr-1"></i>
                                <span class="d-none d-sm-inline">Create Announcement</a></span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- The thing where the announcements are displayed -->
                        <?php if ($ann_result->num_rows > 0): ?>
                        <div class="list-group list-group-flush">
                            <?php while ($ann_query = $ann_result->fetch_assoc()): ?>               
                                
                                <div class="list-group-item p-4">
                                    <div class="d-flex w-100 flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3">
                                        <h5 class="font-weight-bold text-primary mb-0">
                                            <?php echo htmlspecialchars($ann_query['title']); ?>
                                        </h5>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar-day mr-1"></i>
                                            <?php echo date('M d, Y g:i A', strtotime($ann_query['date_posted'])); 
                                            if (!empty($ann_query['updated_at'])) {
                                                echo '<small class="text-warning ml-2 font-italic">(Edited on: ' . date('M d, Y g:i A', strtotime($ann_query['updated_at'])) . ')</small>';
                                            }
                                            ?>
                                        </small>
                                    </div>
                                    <p class="mb-0 text-gray-800" style="white-space: pre-wrap; word-break: break-word;"><?php echo htmlspecialchars($ann_query['message']); ?></p>
                                    
                                    <!-- Edit/Delete Buttons -->
                                    <?php if ($_SESSION['role'] === 'admin'): ?>
                                        <div class ="mt-3">
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

                            <?php endwhile; ?>
                        </div>
                        
                    <?php else: ?>
                        <div class="p-5 text-center">
                            <i class="fas fa-box-open fa-3x text-gray-300 mb-3"></i>
                            <p class="text-gray-500 mb-0">No announcements found on this page.</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <?php if ($total_pages > 1): ?>
                    <div class="card-footer bg-white py-4 border-top">
                        <nav aria-label="Announcements Pagination">
                            <ul class="pagination justify-content-center mb-0">
                                
                                <li class="page-item <?php if($page <= 1){ echo 'disabled'; } ?>">
                                    <a class="page-link" href="<?php if($page <= 1){ echo '#'; } else { echo "?page=".($page - 1); } ?>">Previous</a>
                                </li>

                                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?php if($page == $i){ echo 'active'; } ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>

                                <li class="page-item <?php if($page >= $total_pages){ echo 'disabled'; } ?>">
                                    <a class="page-link" href="<?php if($page >= $total_pages){ echo '#'; } else { echo "?page=".($page + 1); } ?>">Next</a>
                                </li>
                                
                            </ul>
                        </nav>
                    </div>
                <?php endif; ?>
            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <?php include 'includes/footer.php'; ?>
            <!-- End of Footer -->

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

    <!-- Announcement Modals -->
    <script src="js/announcements.js"></script>

    <!-- Sidebar State Script 'cause its annoying when the thing is opening by default lol -->
    <!-- <script src="js/sidebar_state.js"></script> forget it it's more annoying when it flashes :/ -->

</body>
</html>