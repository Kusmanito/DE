<?php
$query = http_build_query($_GET);
header('Location: ../register.php' . ($query === '' ? '' : '?' . $query), true, 307);
exit;
