<?php
require_once __DIR__ . '/helpers/db.php';
if (!isset($_SESSION['user_id']) || (int)$_SESSION['user_role'] !== 1) {
    http_response_code(403);
    exit('Доступ запрещён. Вы не администратор.');
}

$pageTitle = 'Панель администратора — Корочки.есть';
$breadcrumbs = [['title' => 'Главная', 'url' => 'index.php'], ['title' => 'Панель администратора']];
$error = '';
$filter = (int)($_GET['status'] ?? 0);
$search = trim($_GET['q'] ?? '');
$page = max(1, (int)($_GET['p'] ?? 1));
$pageUrl = static fn(int $number): string => 'admin.php?' . http_build_query(['status' => $filter, 'q' => $search, 'p' => $number]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = filter_var($_POST['status'] ?? '', FILTER_VALIDATE_INT);
    $id = filter_var($_POST['request_id'] ?? '', FILTER_VALIDATE_INT);
    if (($_POST['action'] ?? '') === 'update_status' && in_array($status, [2, 3], true) && $id > 0) {
        $stmt = $pdo->prepare('UPDATE requests SET status_id = ? WHERE id_request = ?');
        $stmt->execute([$status, $id]);
        if ($stmt->rowCount()) {
            $_SESSION['flash'] = 'Статус заявки сохранён.';
            header('Location: ' . $pageUrl($page), true, 303);
            exit;
        }
        http_response_code(404);
        $error = 'Заявка не найдена.';
    } else {
        $error = 'Выберите корректный статус.';
    }
}

$from = ' FROM requests r JOIN users u ON r.user_id = u.id_user
    JOIN courses c ON r.course_id = c.id_course JOIN payments p ON r.payment_id = p.id_payment';
$conditions = ['1 = 1'];
$params = [];
if (isset(REQUEST_STATUSES[$filter])) {
    $conditions[] = 'r.status_id = ?';
    $params[] = $filter;
}
if ($search !== '') {
    $conditions[] = '(u.fio LIKE ? OR c.name_course LIKE ?)';
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}
$where = ' WHERE ' . implode(' AND ', $conditions);
$stmt = $pdo->prepare('SELECT COUNT(*)' . $from . $where);
$stmt->execute($params);
$total = (int)$stmt->fetchColumn();
$pages = max(1, (int)ceil($total / 3));
$page = min($page, $pages);
$offset = ($page - 1) * 3;
$stmt = $pdo->prepare('SELECT r.*, u.fio, p.method, c.name_course' . $from . $where . " ORDER BY r.id_request DESC LIMIT 3 OFFSET $offset");
$stmt->execute($params);
$requests = $stmt->fetchAll();

ob_start();
?>
<h1 class="page-heading">Панель администратора</h1>
<?php if (isset($_SESSION['flash'])): ?>
    <div class="alert alert-success status-popup" role="status"><?= e($_SESSION['flash']) ?></div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger" role="alert"><?= e($error) ?></div><?php endif; ?>
<form method="get" class="request-filters">
    <label for="filter-status">Статус
        <select name="status" id="filter-status">
            <option value="0">Все статусы</option>
            <?php foreach (REQUEST_STATUSES as $id => $label): ?>
                <option value="<?= $id ?>"<?= $filter === $id ? ' selected' : '' ?>><?= e($label) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <label for="filter-q">ФИО или курс<input name="q" id="filter-q" value="<?= e($search) ?>" maxlength="100"></label>
    <button class="btn btn-primary">Показать</button>
</form>
<p>Найдено заявок: <?= $total ?></p>
<?php if ($requests): ?>
    <div class="request-table-wrap">
        <table class="request-table" aria-label="Заявки пользователей">
            <thead><tr><th scope="col">№</th><th scope="col">ФИО</th><th scope="col">Курс</th><th scope="col">Оплата</th><th scope="col">Дата начала</th><th scope="col">Статус</th></tr></thead>
            <tbody>
            <?php foreach ($requests as $req): ?>
                <?php $statusId = (int)$req['status_id']; $id = (int)$req['id_request']; ?>
                <tr class="request-status-<?= $statusId ?>">
                    <th scope="row" data-label="Заявка №"><?= $id ?></th>
                    <td data-label="ФИО"><?= e($req['fio']) ?></td>
                    <td data-label="Курс"><?= e($req['name_course']) ?></td>
                    <td data-label="Оплата"><?= e($req['method']) ?></td>
                    <td data-label="Дата начала"><?= date('d.m.Y', strtotime($req['data_nach'])) ?></td>
                    <td data-label="Статус">
                        <span class="status-tag"><?= e(REQUEST_STATUSES[$statusId] ?? 'Неизвестно') ?></span>
                        <form method="post" class="status-form">
                            <?= csrf() ?>
                            <input type="hidden" name="action" value="update_status">
                            <input type="hidden" name="request_id" value="<?= $id ?>">
                            <select class="status-select" name="status" aria-label="Новый статус заявки №<?= $id ?>">
                                <?php foreach (REQUEST_STATUSES as $option => $label): ?>
                                    <option value="<?= $option ?>"<?= $statusId === $option ? ' selected' : '' ?><?= $option === 1 ? ' disabled' : '' ?>><?= e($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-primary">Сохранить статус</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <p>Заявок пока нет.</p>
<?php endif; ?>
<?php if ($pages > 1): ?>
    <nav class="pagination" aria-label="Страницы заявок">
        <?php for ($i = max(1, $page - 2); $i <= min($pages, $page + 2); $i++): ?>
            <a class="page-btn<?= $i === $page ? ' active' : '' ?>" href="<?= e($pageUrl($i)) ?>"<?= $i === $page ? ' aria-current="page"' : '' ?>><?= $i ?></a>
        <?php endfor; ?>
    </nav>
<?php endif; ?>
<?php
$content = ob_get_clean();
require __DIR__ . '/helpers/layout.php';
