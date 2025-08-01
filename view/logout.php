<?php
include '../lib/session.php';
Session::destroy();
header('Location: ../index.php');
exit();