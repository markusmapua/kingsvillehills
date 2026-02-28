<?php
session_start();
require 'db_connect.php';

// error stuff
$error_code = $_GET['code'] ?? '404';

$error_title = "Unknown Error";
$error_message = "Oops! An unknown error has occurred.";

switch ($error_code) {
    case '404':
        $error_title = "404 Not Found";
        $error_message = "The page you were trying to enter is invalid.";
        break;
    case '403':
        $error_title = "403 Forbidden";
        $error_message = "You do not have permission to access this page.";
        break;
    case '401':
        $error_title = "401 Unauthorized";
        $error_message = "You are not authorized to access this page.";
        break;
    case '500':
        $error_title = "500 Internal Server Error";
        $error_message = "An unexpected error has occurred on the server.";
        break;
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

    <title><?php echo htmlspecialchars(string: $error_title); ?></title>

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

                    <!-- 404 Error Text -->
                    <div class="text-center">
                        <div class="error mx-auto" data-text="<?php echo htmlspecialchars($error_code); ?>"><?php echo htmlspecialchars($error_code); ?></div>
                        <p class="lead text-gray-800 mb-5"><?php echo htmlspecialchars($error_title); ?></p>
                        <p class="text-gray-500 mb-0"><?php echo htmlspecialchars($error_message); ?></p>
                        <?php if ($error_code == '404'): ?>
                            <a href="index.php">&larr; Back to Dashboard</a>
                            <?php elseif ($error_code == '403' || $error_code == '401'): ?>
                            <a href="login.php">&larr; Back to Login</a>
                        <?php endif; ?>
                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <?php include 'includes/footer.php'; ?>

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <?php include 'logout_modal.php'; ?>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Sidebar State Script 'cause its annoying when the thing is opening by default lol -->
    <script src="js/sidebar_state.js"></script>

</body>

</html>