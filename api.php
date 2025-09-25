<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

class Database {
    private $host = "localhost";
    private $db_name = "tenclass";
    private $username = "root";
    private $password = "";
    public $conn;

    public function connect() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=".$this->host.";dbname=".$this->db_name,
                                  $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch(PDOException $e) {
            echo json_encode(["error" => $e->getMessage()]);
            exit;
        }
        return $this->conn;
    }
}

class Jadwal {
    private $conn;
    private $table = "ruang_kelas";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY tanggal, jam_mulai";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function add($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (ruang, pemakai, kegiatan, tanggal, jam_mulai, jam_selesai)
                  VALUES (:ruang, :pemakai, :kegiatan, :tanggal, :jam_mulai, :jam_selesai)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($data);
    }
}

// === MAIN ===
$db = (new Database())->connect();
$jadwal = new Jadwal($db);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === "GET") {
    echo json_encode($jadwal->getAll());
} elseif ($method === "POST") {
    $input = json_decode(file_get_contents("php://input"), true);
    if ($jadwal->add($input)) {
        echo json_encode(["message" => "Jadwal berhasil ditambahkan"]);
    } else {
        echo json_encode(["error" => "Gagal menambah jadwal"]);
    }
}
