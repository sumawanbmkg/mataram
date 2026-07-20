<?php
/**
 * User Manager Class dengan RBAC
 */

define('KHK_ADMIN', true);
require_once __DIR__ . '/../config/config.php';

class UserManager {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    /**
     * Get all users
     */
    public function getUsers($params = []) {
        $page = $params['page'] ?? 1;
        $limit = $params['limit'] ?? 10;
        $search = $params['search'] ?? null;
        $role = $params['role'] ?? null;

        $offset = ($page - 1) * $limit;
        $where = [];
        $bindings = [];

        if ($search) {
            $where[] = "(p.nama_lengkap LIKE :search OR p.username LIKE :search OR p.email LIKE :search)";
            $bindings['search'] = "%{$search}%";
        }

        if ($role) {
            $where[] = "ur.role_name = :role";
            $bindings['role'] = $role;
        }

        $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        // Count total
        $countSql = "SELECT COUNT(*) as total FROM penulis p LEFT JOIN user_roles ur ON p.role_id = ur.id {$whereClause}";
        $stmt = $this->pdo->prepare($countSql);
        $stmt->execute($bindings);
        $total = $stmt->fetch()['total'];

        // Get data
        $sql = "
            SELECT p.id_penulis, p.nama_lengkap, p.username, p.email, p.status, 
                   p.mfa_enabled, p.last_login, p.created_at,
                   ur.role_name, ur.description as role_description,
                   (SELECT COUNT(*) FROM berita WHERE id_penulis = p.id_penulis) as news_count
            FROM penulis p
            LEFT JOIN user_roles ur ON p.role_id = ur.id
            {$whereClause}
            ORDER BY p.created_at DESC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->pdo->prepare($sql);
        foreach ($bindings as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'data' => $stmt->fetchAll(),
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'total_pages' => ceil($total / $limit)
        ];
    }

    /**
     * Get user by ID
     */
    public function getUserById($id) {
        $stmt = $this->pdo->prepare("
            SELECT p.*, ur.role_name, ur.permissions
            FROM penulis p
            LEFT JOIN user_roles ur ON p.role_id = ur.id
            WHERE p.id_penulis = :id
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Create new user
     */
    public function createUser($data) {
        // Check if username or email exists
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM penulis WHERE username = :username OR email = :email");
        $stmt->execute(['username' => $data['username'], 'email' => $data['email']]);
        if ($stmt->fetchColumn() > 0) {
            return ['success' => false, 'message' => 'Username atau email sudah digunakan.'];
        }

        $stmt = $this->pdo->prepare("
            INSERT INTO penulis (nama_lengkap, username, email, password, bio, role_id, status)
            VALUES (:nama, :username, :email, :password, :bio, :role_id, :status)
        ");

        $stmt->execute([
            'nama' => Security::sanitize($data['nama_lengkap']),
            'username' => Security::sanitize($data['username']),
            'email' => filter_var($data['email'], FILTER_SANITIZE_EMAIL),
            'password' => Security::hashPassword($data['password']),
            'bio' => Security::sanitize($data['bio'] ?? ''),
            'role_id' => $data['role_id'] ?? 3, // Default: editor
            'status' => $data['status'] ?? 'aktif'
        ]);

        return ['success' => true, 'id' => $this->pdo->lastInsertId()];
    }

    /**
     * Update user
     */
    public function updateUser($id, $data) {
        // Check if username or email exists for other users
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM penulis 
            WHERE (username = :username OR email = :email) AND id_penulis != :id
        ");
        $stmt->execute(['username' => $data['username'], 'email' => $data['email'], 'id' => $id]);
        if ($stmt->fetchColumn() > 0) {
            return ['success' => false, 'message' => 'Username atau email sudah digunakan.'];
        }

        $sql = "
            UPDATE penulis SET
                nama_lengkap = :nama,
                username = :username,
                email = :email,
                bio = :bio,
                role_id = :role_id,
                status = :status
        ";
        $bindings = [
            'nama' => Security::sanitize($data['nama_lengkap']),
            'username' => Security::sanitize($data['username']),
            'email' => filter_var($data['email'], FILTER_SANITIZE_EMAIL),
            'bio' => Security::sanitize($data['bio'] ?? ''),
            'role_id' => $data['role_id'],
            'status' => $data['status'],
            'id' => $id
        ];

        // Update password if provided
        if (!empty($data['password'])) {
            $sql .= ", password = :password";
            $bindings['password'] = Security::hashPassword($data['password']);
        }

        $sql .= " WHERE id_penulis = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);

        return ['success' => true];
    }

    /**
     * Delete user
     */
    public function deleteUser($id, $currentUserId) {
        if ($id == $currentUserId) {
            return ['success' => false, 'message' => 'Tidak dapat menghapus akun sendiri.'];
        }

        // Check if user has news
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM berita WHERE id_penulis = :id");
        $stmt->execute(['id' => $id]);
        if ($stmt->fetchColumn() > 0) {
            return ['success' => false, 'message' => 'User memiliki berita. Nonaktifkan saja atau pindahkan beritanya terlebih dahulu.'];
        }

        $stmt = $this->pdo->prepare("DELETE FROM penulis WHERE id_penulis = :id");
        $stmt->execute(['id' => $id]);

        return ['success' => true];
    }

    /**
     * Get all roles
     */
    public function getRoles() {
        $stmt = $this->pdo->query("SELECT * FROM user_roles ORDER BY id");
        return $stmt->fetchAll();
    }

    /**
     * Update user profile (self)
     */
    public function updateProfile($userId, $data) {
        $sql = "UPDATE penulis SET nama_lengkap = :nama, bio = :bio";
        $bindings = [
            'nama' => Security::sanitize($data['nama_lengkap']),
            'bio' => Security::sanitize($data['bio'] ?? ''),
            'id' => $userId
        ];

        if (!empty($data['password'])) {
            $sql .= ", password = :password";
            $bindings['password'] = Security::hashPassword($data['password']);
        }

        $sql .= " WHERE id_penulis = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);

        return ['success' => true];
    }
}
