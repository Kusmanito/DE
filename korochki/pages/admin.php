<?php
$query = http_build_query($_GET);
header('Location: ../admin.php' . ($query === '' ? '' : '?' . $query), true, 307);
exit;
