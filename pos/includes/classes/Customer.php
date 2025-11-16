<?php
class Customer {
    private $conn;
    private $table_name = "customers";

    public $RegID;
    public $ownnam;
    public $ownmob;
    public $petnam;
    public $Pettyp;
    public $petsp;
    public $year;
    public $month;
    public $gram;
    public $kg;
    public $custType;
    public $RegDt;
    public $RegNo;


    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE RegID = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->RegID);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->ownnam = $row['ownnam'];
        $this->ownmob = $row['ownmob'];
        $this->petnam = $row['petnam'];
        $this->Pettyp = $row['Pettyp'];
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET custType=:custType, RegDt=:RegDt, RegNo=:RegNo, Pettyp=:Pettyp, petnam=:petnam, petsp=:petsp, year=:year, month=:month, gram=:gram, kg=:kg, ownnam=:ownnam, ownmob=:ownmob";
        $stmt = $this->conn->prepare($query);

        // Sanitize and bind
        $this->custType = htmlspecialchars(strip_tags($this->custType));
        $this->RegDt = htmlspecialchars(strip_tags($this->RegDt));
        $this->RegNo = htmlspecialchars(strip_tags($this->RegNo));
        $this->Pettyp = htmlspecialchars(strip_tags($this->Pettyp));
        $this->petnam = htmlspecialchars(strip_tags($this->petnam));
        $this->petsp = htmlspecialchars(strip_tags($this->petsp));
        $this->year = htmlspecialchars(strip_tags($this->year));
        $this->month = htmlspecialchars(strip_tags($this->month));
        $this->gram = htmlspecialchars(strip_tags($this->gram));
        $this->kg = htmlspecialchars(strip_tags($this->kg));
        $this->ownnam = htmlspecialchars(strip_tags($this->ownnam));
        $this->ownmob = htmlspecialchars(strip_tags($this->ownmob));

        $stmt->bindParam(":custType", $this->custType);
        $stmt->bindParam(":RegDt", $this->RegDt);
        $stmt->bindParam(":RegNo", $this->RegNo);
        $stmt->bindParam(":Pettyp", $this->Pettyp);
        $stmt->bindParam(":petnam", $this->petnam);
        $stmt->bindParam(":petsp", $this->petsp);
        $stmt->bindParam(":year", $this->year);
        $stmt->bindParam(":month", $this->month);
        $stmt->bindParam(":gram", $this->gram);
        $stmt->bindParam(":kg", $this->kg);
        $stmt->bindParam(":ownnam", $this->ownnam);
        $stmt->bindParam(":ownmob", $this->ownmob);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " SET ownnam = :ownnam, ownmob = :ownmob, petnam = :petnam, Pettyp = :Pettyp WHERE RegID = :RegID";
        $stmt = $this->conn->prepare($query);

        // Sanitize and bind
        $this->ownnam = htmlspecialchars(strip_tags($this->ownnam));
        $this->ownmob = htmlspecialchars(strip_tags($this->ownmob));
        $this->petnam = htmlspecialchars(strip_tags($this->petnam));
        $this->Pettyp = htmlspecialchars(strip_tags($this->Pettyp));
        $this->RegID = htmlspecialchars(strip_tags($this->RegID));

        $stmt->bindParam(":ownnam", $this->ownnam);
        $stmt->bindParam(":ownmob", $this->ownmob);
        $stmt->bindParam(":petnam", $this->petnam);
        $stmt->bindParam(":Pettyp", $this->Pettyp);
        $stmt->bindParam(":RegID", $this->RegID);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE RegID = :RegID";
        $stmt = $this->conn->prepare($query);
        $this->RegID=htmlspecialchars(strip_tags($this->RegID));
        $stmt->bindParam(":RegID", $this->RegID);
        if($stmt->execute()){
            return true;
        }
        return false;
    }
}
?>
