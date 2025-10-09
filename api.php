<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

const API_URL = "http://localhost:8000/api.php";
// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

class Database {
    private $host = "localhost";
    private $db_name = "TenClass";
    private $username = "root";
    private $password = "";
    public $conn;

    public function connect() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=".$this->host.";dbname=".$this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $e) {
            echo json_encode(["error" => "Koneksi DB gagal"]);
            exit;
        }
        return $this->conn;
    }
}

class Jadwal {
    private $conn;
    private $table = "schedule";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY day, start_session";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function add($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (room_id, user_id, class, day, start_session, end_session)
                  VALUES (:room_id, :user_id, :class, :day, :start_session, :end_session)";
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
} else {
    echo json_encode(["error" => "Metode tidak didukung"]);
}
