<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

class Database {
    private $host = "localhost";
    private $db_name = "TenClass";
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

$db = (new Database())->connect();

$data = json_decode(file_get_contents("php://input"), true);
$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

$stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
$stmt->execute(["username" => $username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && $password === $user['password']) {
    echo json_encode(["success" => true, "message" => "Login berhasil"]);
} else {
    echo json_encode(["success" => false, "message" => "Username atau password salah"]);
}
?>
