<?php
$query = http_build_query($_GET);
header('Location: ../logout.php' . ($query === '' ? '' : '?' . $query), true, 307);
exit;
