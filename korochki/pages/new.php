<?php
$query = http_build_query($_GET);
header('Location: ../add_request.php' . ($query === '' ? '' : '?' . $query), true, 307);
exit;
