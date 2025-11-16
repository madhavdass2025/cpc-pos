<?php
class Stock {
    private $conn;
    private $stock_batches_table = "stock_batches";
    private $stock_ledger_table = "stock_ledger";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function adjust_stock($product_id, $batch_id, $quantity, $transaction_type, $reference_id = null) {
        try {
            $this->conn->beginTransaction();

            // 1. Update stock_batches
            $query = "UPDATE " . $this->stock_batches_table . " SET current_qty = current_qty + :quantity WHERE id = :batch_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':quantity', $quantity);
            $stmt->bindParam(':batch_id', $batch_id);
            if (!$stmt->execute()) {
                throw new Exception("Failed to update stock batch.");
            }

            // 2. Insert into stock_ledger
            $ledger_query = "INSERT INTO " . $this->stock_ledger_table . " SET product_id=:product_id, batch_id=:batch_id, transaction_type=:transaction_type, quantity=:quantity, reference_id=:reference_id";
            $ledger_stmt = $this->conn->prepare($ledger_query);
            $ledger_stmt->bindParam(":product_id", $product_id);
            $ledger_stmt->bindParam(":batch_id", $batch_id);
            $ledger_stmt->bindParam(":transaction_type", $transaction_type);
            $ledger_stmt->bindParam(":quantity", $quantity);
            $ledger_stmt->bindParam(":reference_id", $reference_id);

            if (!$ledger_stmt->execute()) {
                throw new Exception("Failed to create stock ledger entry.");
            }

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }

    public function get_current_stock() {
        $query = "SELECT p.product_name, sb.batch_number, sb.current_qty, sb.expiry_date, sb.purchase_price, p.mrp FROM " . $this->stock_batches_table . " sb JOIN products p ON sb.product_id = p.id WHERE sb.current_qty > 0 ORDER BY p.product_name, sb.expiry_date";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function get_stock_ledger($product_id) {
        $query = "SELECT sl.transaction_date, sl.transaction_type, sb.batch_number, sl.quantity, sl.reference_id FROM " . $this->stock_ledger_table . " sl JOIN stock_batches sb ON sl.batch_id = sb.id WHERE sl.product_id = :product_id ORDER BY sl.transaction_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
        return $stmt;
    }

    public function get_low_stock() {
        $query = "SELECT p.product_name, SUM(sb.current_qty) as total_qty, p.reorder_level FROM " . $this->stock_batches_table . " sb JOIN products p ON sb.product_id = p.id GROUP BY p.id HAVING total_qty < p.reorder_level";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function get_expiring_soon($days = 30) {
        $query = "SELECT p.product_name, sb.batch_number, sb.current_qty, sb.expiry_date FROM " . $this->stock_batches_table . " sb JOIN products p ON sb.product_id = p.id WHERE sb.expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL :days DAY) ORDER BY sb.expiry_date ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':days', $days);
        $stmt->execute();
        return $stmt;
    }

    public function get_batches_for_product($product_id) {
        $query = "SELECT id, batch_number, current_qty FROM " . $this->stock_batches_table . " WHERE product_id = :product_id AND current_qty > 0 ORDER BY expiry_date ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
        return $stmt;
    }
}
?>
