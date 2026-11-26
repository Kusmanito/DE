<?php
$query = http_build_query($_GET);
header('Location: ../login.php' . ($query === '' ? '' : '?' . $query), true, 307);
exit;
