<?php
class Product {
    private $conn;
    private $table_name = "products";

    public $id;
    public $product_name;
    public $generic_name;
    public $description;
    public $hsn_code;
    public $mrp;
    public $tax_excluded_price;
    public $tax_amount;
    public $taxable;
    public $itax_rate;
    public $cess_rate;
    public $reorder_level;
    public $is_active;
    public $is_favorite;
    public $submitted_by;
    public $submitted_date;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET product_name=:product_name, generic_name=:generic_name, mrp=:mrp, reorder_level=:reorder_level, submitted_by=:submitted_by";
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->product_name=htmlspecialchars(strip_tags($this->product_name));
        $this->generic_name=htmlspecialchars(strip_tags($this->generic_name));
        $this->mrp=htmlspecialchars(strip_tags($this->mrp));
        $this->reorder_level=htmlspecialchars(strip_tags($this->reorder_level));
        $this->submitted_by=htmlspecialchars(strip_tags($this->submitted_by));

        // bind values
        $stmt->bindParam(":product_name", $this->product_name);
        $stmt->bindParam(":generic_name", $this->generic_name);
        $stmt->bindParam(":mrp", $this->mrp);
        $stmt->bindParam(":reorder_level", $this->reorder_level);
        $stmt->bindParam(":submitted_by", $this->submitted_by);

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

        $this->product_name = $row['product_name'];
        $this->generic_name = $row['generic_name'];
        $this->mrp = $row['mrp'];
        $this->reorder_level = $row['reorder_level'];
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " SET product_name = :product_name, generic_name = :generic_name, mrp = :mrp, reorder_level = :reorder_level WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->product_name=htmlspecialchars(strip_tags($this->product_name));
        $this->generic_name=htmlspecialchars(strip_tags($this->generic_name));
        $this->mrp=htmlspecialchars(strip_tags($this->mrp));
        $this->reorder_level=htmlspecialchars(strip_tags($this->reorder_level));
        $this->id=htmlspecialchars(strip_tags($this->id));

        // bind values
        $stmt->bindParam(':product_name', $this->product_name);
        $stmt->bindParam(':generic_name', $this->generic_name);
        $stmt->bindParam(':mrp', $this->mrp);
        $stmt->bindParam(':reorder_level', $this->reorder_level);
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
