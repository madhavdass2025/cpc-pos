<?php
class Purchase {
    private $conn;
    private $table_name = "purchase_bills";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        $query = "SELECT p.*, s.supplier_name FROM " . $this->table_name . " p LEFT JOIN suppliers s ON p.supplier_id = s.id ORDER BY p.bill_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>
