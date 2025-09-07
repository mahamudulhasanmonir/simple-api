<?php
header("Content-Type: application/json");

// Simulate a database with an array
$users = [
    ["id" => 1, "name" => "Mahamudul", "role" => "Software Engineer"],
    ["id" => 2, "name" => "John Doe", "role" => "Student"],
];

// Get the request method
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Return all users
    echo json_encode([
        "status" => "success",
        "data" => $users
    ]);
} elseif ($method === 'POST') {
    // Get JSON data from request body
    $input = json_decode(file_get_contents("php://input"), true);
    
    if (isset($input['name']) && isset($input['role'])) {
        $newUser = [
            "id" => count($users) + 1,
            "name" => $input['name'],
            "role" => $input['role']
        ];
        $users[] = $newUser;

        echo json_encode([
            "status" => "success",
            "message" => "User added successfully",
            "data" => $newUser
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Invalid input"
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Method not allowed"
    ]);
}
