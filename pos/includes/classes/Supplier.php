<?php
class Supplier {
    private $conn;
    private $table_name = "suppliers";

    public $id;
    public $supplier_name;
    public $phone;
    public $gstin;
    public $address;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET supplier_name=:supplier_name, phone=:phone, gstin=:gstin, address=:address";
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->supplier_name=htmlspecialchars(strip_tags($this->supplier_name));
        $this->phone=htmlspecialchars(strip_tags($this->phone));
        $this->gstin=htmlspecialchars(strip_tags($this->gstin));
        $this->address=htmlspecialchars(strip_tags($this->address));

        // bind values
        $stmt->bindParam(":supplier_name", $this->supplier_name);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":gstin", $this->gstin);
        $stmt->bindParam(":address", $this->address);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    public function read() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->supplier_name = $row['supplier_name'];
        $this->phone = $row['phone'];
        $this->gstin = $row['gstin'];
        $this->address = $row['address'];
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " SET supplier_name = :supplier_name, phone = :phone, gstin = :gstin, address = :address WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->supplier_name=htmlspecialchars(strip_tags($this->supplier_name));
        $this->phone=htmlspecialchars(strip_tags($this->phone));
        $this->gstin=htmlspecialchars(strip_tags($this->gstin));
        $this->address=htmlspecialchars(strip_tags($this->address));
        $this->id=htmlspecialchars(strip_tags($this->id));

        // bind values
        $stmt->bindParam(':supplier_name', $this->supplier_name);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':gstin', $this->gstin);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':id', $this->id);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $this->id=htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);

        if($stmt->execute()){
            return true;
        }
        return false;
    }
}
?>
