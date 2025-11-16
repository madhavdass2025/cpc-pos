<?php
class Purchase {
    private $conn;
    private $table_name = "purchase_bills";

    public $id;
    public $bill_number;
    public $bill_date;
    public $supplier_id;
    public $total_amount;
    public $products; // This will be an array of products in the purchase

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        try {
            $this->conn->beginTransaction();

            // 1. Create the purchase bill
            $query = "INSERT INTO " . $this->table_name . " SET bill_number=:bill_number, bill_date=:bill_date, supplier_id=:supplier_id, total_amount=:total_amount";
            $stmt = $this->conn->prepare($query);

            // Sanitize and bind
            $this->bill_number=htmlspecialchars(strip_tags($this->bill_number));
            $this->bill_date=htmlspecialchars(strip_tags($this->bill_date));
            $this->supplier_id=htmlspecialchars(strip_tags($this->supplier_id));
            $this->total_amount=htmlspecialchars(strip_tags($this->total_amount));

            $stmt->bindParam(":bill_number", $this->bill_number);
            $stmt->bindParam(":bill_date", $this->bill_date);
            $stmt->bindParam(":supplier_id", $this->supplier_id);
            $stmt->bindParam(":total_amount", $this->total_amount);

            if (!$stmt->execute()) {
                throw new Exception("Failed to create purchase bill.");
            }

            $this->id = $this->conn->lastInsertId();

            // 2. Update stock
            foreach ($this->products as $product) {
                // a. Insert into stock_batches
                $batch_query = "INSERT INTO stock_batches SET product_id=:product_id, batch_number=:batch_number, current_qty=:current_qty, expiry_date=:expiry_date, purchase_price=:purchase_price, tax_rate=0"; // Assuming tax rate for now
                $batch_stmt = $this->conn->prepare($batch_query);

                $batch_stmt->bindParam(":product_id", $product['product_id']);
                $batch_stmt->bindParam(":batch_number", $product['batch_number']);
                $batch_stmt->bindParam(":current_qty", $product['quantity']);
                $batch_stmt->bindParam(":expiry_date", $product['expiry_date']);
                $batch_stmt->bindParam(":purchase_price", $product['purchase_price']);

                if(!$batch_stmt->execute()){
                    throw new Exception("Failed to create stock batch.");
                }

                $batch_id = $this->conn->lastInsertId();

                // b. Insert into stock_ledger
                $ledger_query = "INSERT INTO stock_ledger SET product_id=:product_id, batch_id=:batch_id, transaction_type='IN', quantity=:quantity, reference_id=:reference_id";
                $ledger_stmt = $this->conn->prepare($ledger_query);

                $ledger_stmt->bindParam(":product_id", $product['product_id']);
                $ledger_stmt->bindParam(":batch_id", $batch_id);
                $ledger_stmt->bindParam(":quantity", $product['quantity']);
                $ledger_stmt->bindParam(":reference_id", $this->id);

                if(!$ledger_stmt->execute()){
                    throw new Exception("Failed to create stock ledger entry.");
                }
            }

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }

    public function read() {
        $query = "SELECT p.*, s.supplier_name FROM " . $this->table_name . " p LEFT JOIN suppliers s ON p.supplier_id = s.id ORDER BY p.bill_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readOneWithDetails() {
        $query = "SELECT p.*, s.supplier_name FROM " . $this->table_name . " p LEFT JOIN suppliers s ON p.supplier_id = s.id WHERE p.id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->bill_number = $row['bill_number'];
        $this->bill_date = $row['bill_date'];
        $this->supplier_id = $row['supplier_id'];
        $this->supplier_name = $row['supplier_name'];
        $this->total_amount = $row['total_amount'];

        // Get the products for this purchase
        $products_query = "SELECT sb.batch_number, sb.expiry_date, sb.purchase_price, sl.quantity, p.product_name FROM stock_ledger sl JOIN stock_batches sb ON sl.batch_id = sb.id JOIN products p ON sl.product_id = p.id WHERE sl.reference_id = ? AND sl.transaction_type = 'IN'";
        $products_stmt = $this->conn->prepare($products_query);
        $products_stmt->bindParam(1, $this->id);
        $products_stmt->execute();
        $this->products = $products_stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
