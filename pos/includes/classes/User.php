<?php
class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $name;
    public $email;
    public $role_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getById($id) {
        $query = "SELECT id, name, email, role_id FROM " . $this->table_name . " WHERE id = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function read() {
        $query = "SELECT u.id, u.name, u.email, r.role_name FROM " . $this->table_name . " u LEFT JOIN roles r ON u.role_id = r.id ORDER BY u.name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET name=:name, email=:email, password_hash=:password_hash, role_id=:role_id";
        $stmt = $this->conn->prepare($query);
        // sanitize and bind
        $this->name=htmlspecialchars(strip_tags($this->name));
        $this->email=htmlspecialchars(strip_tags($this->email));
        $this->password_hash=htmlspecialchars(strip_tags($this->password_hash));
        $this->role_id=htmlspecialchars(strip_tags($this->role_id));

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password_hash", $this->password_hash);
        $stmt->bindParam(":role_id", $this->role_id);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " SET name=:name, email=:email, role_id=:role_id WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        // sanitize and bind
        $this->name=htmlspecialchars(strip_tags($this->name));
        $this->email=htmlspecialchars(strip_tags($this->email));
        $this->role_id=htmlspecialchars(strip_tags($this->role_id));
        $this->id=htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":role_id", $this->role_id);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $this->id=htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(":id", $this->id);
        if($stmt->execute()){
            return true;
        }
        return false;
    }
}
