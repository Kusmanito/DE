<?php
$query = http_build_query($_GET);
header('Location: ../dashboard.php' . ($query === '' ? '' : '?' . $query), true, 307);
exit;
