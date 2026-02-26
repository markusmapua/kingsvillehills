<?php

session_start();

session_unset();

session_destroy();

header(header: "location: login.php");
exit();