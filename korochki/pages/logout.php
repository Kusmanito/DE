<?php
session_start();
session_destroy();
header('Location: /DE/korochki/pages/index.php');
exit;