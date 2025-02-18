<?php
require 'config.php';

$method = $_SERVER['REQUEST_METHOD'];

$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));

switch ($method) {
    case 'GET':
        if (isset($request[0]) && is_numeric($request[0])) {
            getUser($request[0]);
        } else {
            getUsers();
        }
        break;
    case 'POST':  
        if (isset($request[0]) && is_numeric($request[0])) {
            loginUser($request[0],$request[1]);
        } else {
            createUser(); 
        } 
        break;
    case 'PUT':
        if (isset($request[0]) && is_numeric($request[0])) {
            updateUser($request[0]);
        } else {
            echo json_encode(['error' => 'Invalid User ID']);
        }
        break;
    case 'DELETE':
        if (isset($request[0]) && is_numeric($request[0])) {
            deleteUser($request[0]);
        } else {
            echo json_encode(['error' => 'Invalid User ID']);
        }
        break;
    default:
        echo json_encode(['error' => 'Invalid Request Method']);
}

function getUsers() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($users);
}

function getUser($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($user);
}

function createUser() {
    global $pdo;
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    if ($stmt->execute([$data['name'], $data['email'], $data['password']])) {
        echo json_encode(['success' => 'User created successfully']);
    } else {
        echo json_encode(['error' => 'Failed to create user']);
    }
}

function updateUser($id) {
    global $pdo;
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?");
    if ($stmt->execute([$data['name'], $data['email'], $data['password'], $id])) {
        echo json_encode(['success' => 'User updated successfully']);
    } else {
        echo json_encode(['error' => 'Failed to update user']);
    }
}

function deleteUser($id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    if ($stmt->execute([$id])) {
        echo json_encode(['success' => 'User deleted successfully']);
    } else {
        echo json_encode(['error' => 'Failed to delete user']);
    }
}

function loginUser($email,$password) {
    global $pdo;
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND password = ?");    
    $stmt->execute([$data['email'], $data['password']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC); 
    if ($user) {
        echo json_encode(['success' => 'User log in']);
    } else {
        echo json_encode(['error' => 'Failed log in user']);
    }
}
?>