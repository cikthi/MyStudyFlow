<?php
session_start();
$host = "localhost";
$user = "root";
$password = "";
$dbname = "KanbanSystem";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get current user
if (!isset($_SESSION['username'])) {
    http_response_code(403);
    exit("Not logged in");
}

$user = $_SESSION['username'];

// Get user id
$stmt = $conn->prepare("SELECT id FROM userlogin WHERE username=?");
$stmt->bind_param("s", $user);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

$action = $_GET['action'] ?? '';

if ($action === 'list') {
    $stmt = $conn->prepare("SELECT * FROM tasks WHERE user_id=? ORDER BY id DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    echo json_encode($result->fetch_all(MYSQLI_ASSOC));
    exit;
}

if ($action === 'add') {
    $title = $_POST['title'] ?? '';
    $status = $_POST['status'] ?? 'todo';
    $due = $_POST['due_date'] ?? null;

    $stmt = $conn->prepare("INSERT INTO tasks (user_id,title,status,due_date) VALUES (?,?,?,?)");
    $stmt->bind_param("isss", $user_id, $title, $status, $due);
    $stmt->execute();
    echo json_encode(["success" => true, "id" => $stmt->insert_id]);
    exit;
}

if ($action === 'update') {
    $id = $_POST['id'];
    $status = $_POST['status'];
    $due = $_POST['due_date'];

    $stmt = $conn->prepare("UPDATE tasks SET status=?, due_date=? WHERE id=? AND user_id=?");
    $stmt->bind_param("ssii", $status, $due, $id, $user_id);
    $stmt->execute();
    echo json_encode(["success" => true]);
    exit;
}

if ($action === 'delete') {
    $id = $_POST['id'];
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    echo json_encode(["success" => true]);
    exit;
}

echo json_encode(["error" => "Invalid action"]);
