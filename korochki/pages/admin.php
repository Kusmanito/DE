<?php
session_start();
require_once __DIR__ . '/../helpers/db.php';

// Только для админа (role_id = 1)
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
    die("Доступ запрещён. Вы не администратор.");
}

$pageTitle = 'Панель администратора — Корочки.Есть';
$breadcrumbs = [
    ['title' => 'Главная', 'url' => '/DE/korochki/pages/index.php'],
    ['title' => 'Панель Администратора', 'url' => '#']
];

$activeTab = $_GET['tab'] ?? 'requests';

// --- Обработка действий ---

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $stmt = $pdo->prepare("UPDATE requests SET status_id = ? WHERE id_request = ?");
    $stmt->execute([(int)$_POST['status'], (int)$_POST['request_id']]);
    header('Location: admin.php?tab=requests');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_request') {
    $stmt = $pdo->prepare("DELETE FROM requests WHERE id_request = ?");
    $stmt->execute([(int)$_POST['request_id']]);
    header('Location: admin.php?tab=requests');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_role') {
    $stmt = $pdo->prepare("UPDATE users SET role_id = ? WHERE id_user = ?");
    $stmt->execute([(int)$_POST['role_id'], (int)$_POST['user_id']]);
    header('Location: admin.php?tab=users');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_review') {
    $reviewId = (int)$_POST['review_id'];
    $stmt = $pdo->prepare("UPDATE requests SET review_id = NULL WHERE review_id = ?");
    $stmt->execute([$reviewId]);
    $stmt = $pdo->prepare("DELETE FROM reviews WHERE id_review = ?");
    $stmt->execute([$reviewId]);
    header('Location: admin.php?tab=reviews');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_course') {
    $name = trim($_POST['name_course'] ?? '');
    if (!empty($name)) {
        $stmt = $pdo->prepare("INSERT INTO courses (name_course) VALUES (?)");
        $stmt->execute([$name]);
    }
    header('Location: admin.php?tab=courses');
    exit;
}

// --- Данные ---

$requests = $pdo->query("
    SELECT r.*, u.fio, c.name_course, p.method, s.name_status
    FROM requests r
    LEFT JOIN users u ON r.user_id = u.id_user
    LEFT JOIN courses c ON r.course_id = c.id_course
    LEFT JOIN payments p ON r.payment_id = p.id_payment
    LEFT JOIN statuses s ON r.status_id = s.id_status
    ORDER BY r.id_request DESC
")->fetchAll();
$allStatuses = $pdo->query("SELECT * FROM statuses")->fetchAll();

$users = $pdo->query("
    SELECT u.*, r.name_role
    FROM users u
    LEFT JOIN roles r ON u.role_id = r.id_role
    ORDER BY u.id_user DESC
")->fetchAll();
$allRoles = $pdo->query("SELECT * FROM roles")->fetchAll();

$reviews = $pdo->query("
    SELECT rv.*, r.id_request, u.fio, c.name_course
    FROM reviews rv
    LEFT JOIN requests r ON r.review_id = rv.id_review
    LEFT JOIN users u ON r.user_id = u.id_user
    LEFT JOIN courses c ON r.course_id = c.id_course
    ORDER BY rv.id_review DESC
")->fetchAll();

$courses = $pdo->query("SELECT * FROM courses ORDER BY id_course DESC")->fetchAll();

ob_start();
?>

<h2 style="margin-bottom: 20px;">Панель администратора</h2>

<div style="display: flex; gap: 10px; margin-bottom: 25px; border-bottom: 2px solid #ddd; padding-bottom: 10px; flex-wrap: wrap;">
    <a href="?tab=requests" style="padding: 8px 16px; text-decoration: none; border-radius: 4px; <?= $activeTab === 'requests' ? 'background: #0056b3; color: white;' : 'background: #eee; color: #333;' ?>">Заявки</a>
    <a href="?tab=users" style="padding: 8px 16px; text-decoration: none; border-radius: 4px; <?= $activeTab === 'users' ? 'background: #0056b3; color: white;' : 'background: #eee; color: #333;' ?>">Пользователи</a>
    <a href="?tab=reviews" style="padding: 8px 16px; text-decoration: none; border-radius: 4px; <?= $activeTab === 'reviews' ? 'background: #0056b3; color: white;' : 'background: #eee; color: #333;' ?>">Отзывы</a>
    <a href="?tab=courses" style="padding: 8px 16px; text-decoration: none; border-radius: 4px; <?= $activeTab === 'courses' ? 'background: #0056b3; color: white;' : 'background: #eee; color: #333;' ?>">Курсы</a>
</div>

<?php if ($activeTab === 'requests'): ?>
    <h3 style="margin-bottom: 15px;">Все заявки</h3>
    <div class="requests-grid">
        <?php if (empty($requests)): ?>
            <p style="grid-column: 1 / -1;">Заявок пока нет.</p>
        <?php else: ?>
            <?php foreach ($requests as $req): ?>
            <div class="request-card">
                <div class="request-card-header">Заявка №<?= $req['id_request'] ?></div>
                <div class="request-card-body">
                    <p><strong>ФИО:</strong> <?= htmlspecialchars($req['fio'] ?? 'Неизвестно') ?></p>
                    <p><strong>Курс:</strong> <?= htmlspecialchars($req['name_course'] ?? 'Неизвестно') ?></p>
                    <p><strong>Оплата:</strong> <?= htmlspecialchars($req['method'] ?? 'Неизвестно') ?></p>
                    <p><strong>Дата начала:</strong> <?= date('d.m.Y', strtotime($req['data_nach'])) ?></p>

                    <form method="POST" style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee;">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="request_id" value="<?= $req['id_request'] ?>">
                        <label style="font-size: 14px; font-weight: bold;">Статус:</label>
                        <select name="status" class="status-select">
                            <?php foreach ($allStatuses as $st): ?>
                                <option value="<?= $st['id_status'] ?>" <?= ($st['id_status'] == $req['status_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($st['name_status']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-primary" style="margin-top: 10px; width: 100%; font-size: 12px;">Сохранить статус</button>
                    </form>

                    <form method="POST" onsubmit="return confirm('Удалить заявку?');" style="margin-top: 10px;">
                        <input type="hidden" name="action" value="delete_request">
                        <input type="hidden" name="request_id" value="<?= $req['id_request'] ?>">
                        <button type="submit" style="background: #dc3545; color: white; border: none; padding: 8px; width: 100%; border-radius: 4px; cursor: pointer; font-size: 12px;">Удалить заявку</button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

<?php elseif ($activeTab === 'users'): ?>
    <h3 style="margin-bottom: 15px;">Пользователи</h3>
    <div class="requests-grid">
        <?php foreach ($users as $u): ?>
        <div class="request-card">
            <div class="request-card-header"><?= htmlspecialchars($u['login']) ?></div>
            <div class="request-card-body">
                <p><strong>ФИО:</strong> <?= htmlspecialchars($u['fio']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($u['email']) ?></p>
                <p><strong>Телефон:</strong> <?= htmlspecialchars($u['phone']) ?></p>
                <p><strong>Роль:</strong> <?= htmlspecialchars($u['name_role']) ?></p>

                <form method="POST" style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee;">
                    <input type="hidden" name="action" value="update_role">
                    <input type="hidden" name="user_id" value="<?= $u['id_user'] ?>">
                    <label style="font-size: 14px; font-weight: bold;">Изменить роль:</label>
                    <select name="role_id" class="status-select">
                        <?php foreach ($allRoles as $role): ?>
                            <option value="<?= $role['id_role'] ?>" <?= ($role['id_role'] == $u['role_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($role['name_role']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-primary" style="margin-top: 10px; width: 100%; font-size: 12px;">Сохранить роль</button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

<?php elseif ($activeTab === 'reviews'): ?>
    <h3 style="margin-bottom: 15px;">Отзывы</h3>
    <div class="requests-grid">
        <?php if (empty($reviews)): ?>
            <p style="grid-column: 1 / -1;">Отзывов пока нет.</p>
        <?php else: ?>
            <?php foreach ($reviews as $rev): ?>
            <div class="request-card">
                <div class="request-card-header">Отзыв №<?= $rev['id_review'] ?></div>
                <div class="request-card-body">
                    <p><strong>Оценка:</strong> <?= $rev['stars'] ?> ★</p>
                    <p><strong>Комментарий:</strong> <?= htmlspecialchars($rev['comment']) ?></p>
                    <?php if ($rev['fio']): ?>
                        <p><strong>Пользователь:</strong> <?= htmlspecialchars($rev['fio']) ?></p>
                        <p><strong>Курс:</strong> <?= htmlspecialchars($rev['name_course']) ?></p>
                    <?php else: ?>
                        <p><em>Отзыв не привязан к заявке</em></p>
                    <?php endif; ?>

                    <form method="POST" onsubmit="return confirm('Удалить отзыв?');" style="margin-top: 15px;">
                        <input type="hidden" name="action" value="delete_review">
                        <input type="hidden" name="review_id" value="<?= $rev['id_review'] ?>">
                        <button type="submit" style="background: #dc3545; color: white; border: none; padding: 8px; width: 100%; border-radius: 4px; cursor: pointer; font-size: 12px;">Удалить отзыв</button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

<?php elseif ($activeTab === 'courses'): ?>
    <h3 style="margin-bottom: 15px;">Курсы</h3>

    <div class="form-container" style="margin-bottom: 20px; max-width: 100%;">
        <h4>Добавить новый курс</h4>
        <form method="POST">
            <input type="hidden" name="action" value="add_course">
            <div class="form-group">
                <label>Название курса</label>
                <input type="text" name="name_course" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: auto;">Добавить курс</button>
        </form>
    </div>

    <div class="requests-grid">
        <?php foreach ($courses as $c): ?>
        <div class="request-card">
            <div class="request-card-header">Курс №<?= $c['id_course'] ?></div>
            <div class="request-card-body">
                <p><?= htmlspecialchars($c['name_course']) ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';