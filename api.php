<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kepegawaian";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// CREATE (INSERT)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $nama = $data['nama'];
    $jabatan = $data['jabatan'];
    $departemen = $data['departemen'];

    // Validasi input
    if (empty($nama) || empty($jabatan) || empty($departemen)) {
        echo json_encode(["error" => "Semua field harus diisi"]);
        exit;
    }

    // Memanggil stored procedure untuk menambahkan pegawai
    $stmt = $conn->prepare("CALL tambah_pegawai(?, ?, ?)");
    $stmt->bind_param("sss", $nama, $jabatan, $departemen);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Pegawai berhasil ditambahkan"]);
    } else {
        echo json_encode(["error" => "Gagal menambahkan pegawai"]);
    }
}

// READ (SELECT)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['logs'])) {
    $sql = "SELECT * FROM pegawai";
    $result = $conn->query($sql);
    $pegawai = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $pegawai[] = $row;
        }
    }

    echo json_encode($pegawai);
}

// READ LOGS
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['logs'])) {
    $sql = "SELECT * FROM pegawai_logs ORDER BY action_date DESC";
    $result = $conn->query($sql);
    $logs = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $logs[] = $row;
        }
    }

    echo json_encode($logs);
}

// UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'];
    $nama = $data['nama'];
    $jabatan = $data['jabatan'];
    $departemen = $data['departemen'];

    // Validasi input
    if (empty($id) || empty($nama) || empty($jabatan) || empty($departemen)) {
        echo json_encode(["error" => "Semua field harus diisi"]);
        exit;
    }

    // Memanggil stored procedure untuk memperbarui pegawai
    $stmt = $conn->prepare("CALL perbarui_pegawai(?, ?, ?, ?)");
    $stmt->bind_param("isss", $id, $nama, $jabatan, $departemen);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Pegawai berhasil diperbarui"]);
    } else {
        echo json_encode(["error" => "Gagal memperbarui pegawai"]);
    }
}

// DELETE
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if ($id <= 0) {
        echo json_encode(["error" => "ID tidak valid"]);
        exit;
    }

    // Memanggil stored procedure untuk menghapus pegawai
    $stmt = $conn->prepare("CALL hapus_pegawai(?)");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Pegawai berhasil dihapus"]);
    } else {
        echo json_encode(["error" => "Gagal menghapus pegawai"]);
    }
}

$conn->close();
?>