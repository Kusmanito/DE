<?php
declare(strict_types=1);

final class Repository
{
    private PDO $db;

    public function __construct(array $config)
    {
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset=utf8mb4";
        $this->db = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_FOUND_ROWS => true,
        ]);
    }

    public function findUser(string $login): ?array
    {
        return $this->query(
            'SELECT u.*, r.code AS role FROM users u JOIN roles r ON r.id = u.role_id WHERE login = ?', [$login]
        )->fetch() ?: null;
    }

    public function register(array $field, string $password): bool
    {
        try {
            $this->query(
                'INSERT INTO users (login, password_hash, full_name, phone, email) VALUES (?, ?, ?, ?, ?)',
                [$field['login'], password_hash($password, PASSWORD_DEFAULT), $field['full_name'], $field['phone'], $field['email']]
            );
            return true;
        } catch (PDOException $error) {
            if ((int)($error->errorInfo[1] ?? 0) === 1062) {
                return false;
            }
            throw $error;
        }
    }

    public function types(): array
    {
        return $this->query('SELECT id, name FROM achievement_types ORDER BY id')->fetchAll();
    }

    public function levels(): array
    {
        return $this->query('SELECT id, name FROM event_levels ORDER BY id')->fetchAll();
    }

    public function submit(int $student, int $type, int $level, string $event, string $date, string $stored, string $name): void
    {
        $this->query(
            'INSERT INTO achievements (user_id, type_id, level_id, event_name, event_date, stored_file, original_file)
             VALUES (?, ?, ?, ?, ?, ?, ?)', [$student, $type, $level, $event, $date, $stored, $name]
        );
    }

    public function addReview(int $id, int $student, string $comment): bool
    {
        $this->db->beginTransaction();
        try {
            $eligible = $this->query(
                'SELECT id FROM achievements WHERE id = ? AND user_id = ? AND status_id = 2 FOR UPDATE', [$id, $student]
            )->fetchColumn();
            if (!$eligible || $this->query('SELECT 1 FROM reviews WHERE achievement_id = ?', [$id])->fetchColumn()) {
                $this->db->rollBack();
                return false;
            }
            $this->query('INSERT INTO reviews (achievement_id, comment) VALUES (?, ?)', [$id, $comment]);
            $this->db->commit();
            return true;
        } catch (Throwable $error) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $error;
        }
    }

    public function changeStatus(int $id, int $status): bool
    {
        return $this->query('UPDATE achievements SET status_id = ? WHERE id = ?', [$status, $id])->rowCount() > 0;
    }

    public function document(int $id, array $user): ?array
    {
        $sql = 'SELECT stored_file, original_file FROM achievements WHERE id = ?';
        return ($user['role'] === 'admin' ? $this->query($sql, [$id])
            : $this->query($sql . ' AND user_id = ?', [$id, $user['id']]))->fetch() ?: null;
    }

    public function achievements(?int $student, int $type, int $status, int $page): array
    {
        $conditions = [];
        $params = [];
        foreach (['a.user_id' => $student, 'a.type_id' => $type, 'a.status_id' => $status] as $column => $value) {
            if ($value) {
                $conditions[] = "$column = ?";
                $params[] = $value;
            }
        }
        $where = $conditions ? ' WHERE ' . implode(' AND ', $conditions) : '';
        $total = (int)$this->query('SELECT COUNT(*) FROM achievements a' . $where, $params)->fetchColumn();
        $limit = 6;
        $pages = max(1, (int)ceil($total / $limit));
        $page = min(max(1, $page), $pages);
        $offset = ($page - 1) * $limit;
        $rows = $this->query(
            'SELECT a.*, t.name AS type, l.name AS level, s.name AS status, u.full_name AS student, r.comment
             FROM achievements a JOIN achievement_types t ON t.id = a.type_id JOIN event_levels l ON l.id = a.level_id
             JOIN statuses s ON s.id = a.status_id JOIN users u ON u.id = a.user_id
             LEFT JOIN reviews r ON r.achievement_id = a.id' . $where
             . " ORDER BY a.id DESC LIMIT $limit OFFSET $offset", $params
        )->fetchAll();
        return [$rows, $page, $pages, $total];
    }

    private function query(string $sql, array $params = []): PDOStatement
    {
        $statement = $this->db->prepare($sql);
        $statement->execute($params);
        return $statement;
    }
}
