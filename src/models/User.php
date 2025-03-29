<?php
require_once 'src/core/Model.php';

class User extends Model {
    public function authenticate($username, $password) {
        $sql = "SELECT * FROM users WHERE username = ? AND password = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$username, $password]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($username, $password, $userType) {
        $sql = "INSERT INTO users (username, password, user_type) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$username, $password, $userType]);
    }

    public function getById($id) {
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAll() {
        $sql = "SELECT * FROM users";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($id, $username, $userType) {
        $sql = "UPDATE users SET username = ?, user_type = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$username, $userType, $id]);
    }

    public function delete($id) {
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function getAllUsers() {
        $sql = "SELECT * FROM Users ORDER BY Created_At DESC";
        $stmt = $this->query($sql);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }
    
    public function createUser($username, $password, $userType) {
        $sql = "INSERT INTO Users (Username, Password, User_Type) VALUES (:username, :password, :userType)";
        return $this->query($sql, [
            ':username' => $username,
            ':password' => $password,
            ':userType' => $userType
        ]);
    }
    
    public function updateUserStatus($userId, $status) {
        $sql = "UPDATE Users SET Status = :status WHERE User_ID = :userId";
        return $this->query($sql, [
            ':status' => $status,
            ':userId' => $userId
        ]);
    }
} 