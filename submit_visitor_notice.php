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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_notice'])) {
    $title       = trim(mysqli_real_escape_string($conn, $_POST['notice_title']));
    $visitor_type = $_POST['visitor_type'];
    $arrival     = $_POST['arrival'];
    $departure   = $_POST['departure'];
    $description = trim(mysqli_real_escape_string($conn, $_POST['description']));
    $resident_id = $_SESSION['user_id'];

    $allowed_types = ['Visitor', 'Delivery', 'Other(s)'];

    if (empty($title) || empty($visitor_type) || empty($arrival) || empty($departure) || empty($description)) {
        $error_msg = "Please fill in all required fields.";
    } elseif (!in_array($visitor_type, $allowed_types)) {
        $error_msg = "Invalid visitor type selected.";
    } elseif ($departure <= $arrival) {
        $error_msg = "Departure time must be after arrival time.";
    } else {
        // Store the visitor/purpose title in the title column
        $title_escaped = mysqli_real_escape_string($conn, $title);
        $description_escaped = mysqli_real_escape_string($conn, $description);
        $query = "INSERT INTO notices (resident_id, title, visitor_type, arrival, departure, description, notice_status) 
                  VALUES ('$resident_id', '$title_escaped', '$visitor_type', '$arrival', '$departure', '$description_escaped', 'Pending')";
        if ($conn->query($query)) {
            $success_msg = "Your visitor notice has been submitted successfully!";
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
    <title>Kingsville Connect - Submit Visitor Notice</title>
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
                        <h1 class="h3 mb-0 text-gray-800">Submit a Visitor Notice</h1>
                        <a href="view_visitor_notice.php" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                            <i class="fas fa-list fa-sm text-white-50"></i> View My Notices
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
                                        <i class="fas fa-users mr-2"></i>Visitor Notice Form
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted mb-4">
                                        Inform the management of any expected visitors, deliveries, or guests. Notices are subject to admin approval.
                                    </p>
                                    <form method="POST" action="submit_visitor_notice.php">
                                        <div class="form-group">
                                            <label for="notice_title"><strong>Visitor / Purpose Name <span class="text-danger">*</span></strong></label>
                                            <input type="text" class="form-control" id="notice_title" name="notice_title"
                                                placeholder="e.g. Juan Dela Cruz or LBC Package Delivery" maxlength="255" required>
                                            <small class="form-text text-muted">Name of the visitor or purpose of entry.</small>
                                        </div>

                                        <div class="form-group">
                                            <label for="visitor_type"><strong>Visitor Type <span class="text-danger">*</span></strong></label>
                                            <select class="form-control" id="visitor_type" name="visitor_type" required>
                                                <option value="" disabled selected>-- Select Type --</option>
                                                <option value="Visitor">Visitor</option>
                                                <option value="Delivery">Delivery</option>
                                                <option value="Other(s)">Other(s)</option>
                                            </select>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="arrival"><strong>Expected Arrival <span class="text-danger">*</span></strong></label>
                                                <input type="datetime-local" class="form-control" id="arrival" name="arrival" required>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="departure"><strong>Expected Departure <span class="text-danger">*</span></strong></label>
                                                <input type="datetime-local" class="form-control" id="departure" name="departure" required>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="description"><strong>Additional Details <span class="text-danger">*</span></strong></label>
                                            <textarea class="form-control" id="description" name="description" rows="4"
                                                placeholder="e.g. My cousin will be visiting for a family gathering. Expected vehicle: Toyota Vios, plate ABC 123." required></textarea>
                                        </div>

                                        <hr>
                                        <div class="d-flex justify-content-end">
                                            <a href="index.php" class="btn btn-secondary mr-2">Cancel</a>
                                            <button type="submit" name="submit_notice" class="btn btn-primary">
                                                <i class="fas fa-paper-plane mr-1"></i> Submit Notice
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
                                        <li class="mb-2">Submit a notice for your expected visitor or delivery.</li>
                                        <li class="mb-2">Your notice will be marked as <span class="badge badge-warning text-white">Pending</span>.</li>
                                        <li class="mb-2">Management will review and approve or reject it.</li>
                                        <li>The guard will be informed of approved notices.</li>
                                    </ol>
                                    <hr>
                                    <h6 class="font-weight-bold text-primary mb-2 small">Visitor Types</h6>
                                    <ul class="pl-3 text-gray-700 small mb-0">
                                        <li class="mb-1"><strong>Visitor</strong> — Personal guests, family, friends</li>
                                        <li class="mb-1"><strong>Delivery</strong> — Packages, food, couriers</li>
                                        <li><strong>Other(s)</strong> — Contractors, service workers, etc.</li>
                                    </ul>
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

    <script>
    // Set min datetime for arrival/departure to now
    document.addEventListener('DOMContentLoaded', function() {
        var now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        var minVal = now.toISOString().slice(0, 16);
        document.getElementById('arrival').setAttribute('min', minVal);
        document.getElementById('departure').setAttribute('min', minVal);

        document.getElementById('arrival').addEventListener('change', function() {
            document.getElementById('departure').setAttribute('min', this.value);
        });
    });
    </script>

</body>
</html>
