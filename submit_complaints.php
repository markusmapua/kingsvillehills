<?php
session_start();
require 'db_connect.php';
require 'includes/auth_check.php';

$timeout_duration = 30;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > ($timeout_duration * 60)) {
    session_unset();
    session_destroy();
    header("location: login.php");
    exit();
}
$_SESSION['last_activity'] = time();

$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_complaint'])) {
    $title = trim(mysqli_real_escape_string($conn, $_POST['complaint_title']));
    $description = trim(mysqli_real_escape_string($conn, $_POST['complaint_description']));
    $resident_id = $_SESSION['user_id'];

    if (empty($title) || empty($description)) {
        $error_msg = "Please fill in all required fields.";
    } else {
        $query = "INSERT INTO complaints (resident_id, title, description, complaint_status) VALUES ('$resident_id', '$title', '$description', 'Pending')";
        if ($conn->query($query)) {
            $success_msg = "Your complaint has been submitted successfully!";
        } else {
            $error_msg = "Something went wrong. Please try again.";
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
    <title>Kingsville Connect - Submit a Complaint</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="css/main.css" rel="stylesheet">
</head>

<body id="page-top">

    <div id="wrapper">

        <?php include 'sidebar.php'; ?>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">

                <?php include 'topbar.php'; ?>

                <div class="container-fluid">

                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Submit a Complaint</h1>
                        <a href="view_complaint.php" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                            <i class="fas fa-list fa-sm text-white-50"></i> View My Complaints
                        </a>
                    </div>

                    <?php if ($success_msg): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle mr-2"></i><?php echo $success_msg; ?>
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($error_msg): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle mr-2"></i><?php echo $error_msg; ?>
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        <i class="fas fa-wrench mr-2"></i>Complaint Form
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted mb-4">
                                        Please describe your concern in detail. Our management team will review it and get back to you as soon as possible.
                                    </p>
                                    <form method="POST" action="submit_complaints.php">
                                        <div class="form-group">
                                            <label for="complaint_title"><strong>Complaint Title <span class="text-danger">*</span></strong></label>
                                            <input type="text" class="form-control" id="complaint_title" name="complaint_title"
                                                placeholder="e.g. Broken streetlight on Block 3" maxlength="150" required>
                                            <small class="form-text text-muted">Keep it short and descriptive (max 150 characters).</small>
                                        </div>
                                        <div class="form-group">
                                            <label for="complaint_description"><strong>Description <span class="text-danger">*</span></strong></label>
                                            <textarea class="form-control" id="complaint_description" name="complaint_description"
                                                rows="6" placeholder="Please provide as much detail as possible — location, time, and what happened..." required></textarea>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-end">
                                            <a href="index.php" class="btn btn-secondary mr-2">Cancel</a>
                                            <button type="submit" name="submit_complaint" class="btn btn-primary">
                                                <i class="fas fa-paper-plane mr-1"></i> Submit Complaint
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="card shadow mb-4 border-left-info">
                                <div class="card-body">
                                    <h6 class="font-weight-bold text-info mb-3"><i class="fas fa-info-circle mr-2"></i>How It Works</h6>
                                    <ol class="pl-3 text-gray-700 small">
                                        <li class="mb-2">Fill out and submit the complaint form.</li>
                                        <li class="mb-2">Your complaint will be marked as <span class="badge badge-warning text-white">Pending</span>.</li>
                                        <li class="mb-2">Management will review and take action.</li>
                                        <li>Once resolved, the status will change to <span class="badge badge-success">Resolved</span>.</li>
                                    </ol>
                                    <hr>
                                    <p class="small text-muted mb-0"><i class="fas fa-shield-alt mr-1"></i> Your complaint is confidential and only visible to admins.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <?php include 'includes/footer.php'; ?>
        </div>
    </div>

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <?php include 'includes/logout_modal.php'; ?>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
</body>

</html>
