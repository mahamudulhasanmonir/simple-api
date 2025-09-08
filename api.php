<?php
header("Content-Type: application/json");

// Fake "database"
$users = [
    ["id" => 1, "name" => "Mahamudul", "role" => "Software Engineer"],
    ["id" => 2, "name" => "John Doe", "role" => "Student"],
];

// Parse the URL
$request = $_SERVER['REQUEST_URI'];
$method  = $_SERVER['REQUEST_METHOD'];

// Remove query params and split into parts
$path = explode('/', parse_url($request, PHP_URL_PATH));
$path = array_values(array_filter($path)); // remove empty values

// Route handling
if (isset($path[0]) && $path[0] === "users") {
    if ($method === "GET") {
        // GET /users → all users
        if (!isset($path[1])) {
            echo json_encode(["status" => "success", "data" => $users]);
        }
        // GET /users/{id}
        else {
            $id = intval($path[1]);
            $user = array_filter($users, fn($u) => $u['id'] === $id);
            if ($user) {
                echo json_encode(["status" => "success", "data" => array_values($user)[0]]);
            } else {
                http_response_code(404);
                echo json_encode(["status" => "error", "message" => "User not found"]);
            }
        }
    }

    elseif ($method === "POST") {
        // POST /users
        $input = json_decode(file_get_contents("php://input"), true);
        if (isset($input['name']) && isset($input['role'])) {
            $newUser = [
                "id"   => count($users) + 1,
                "name" => $input['name'],
                "role" => $input['role']
            ];
            $users[] = $newUser;
            echo json_encode(["status" => "success", "data" => $newUser]);
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Invalid input"]);
        }
    }

    elseif ($method === "PUT" && isset($path[1])) {
        // PUT /users/{id}
        $id = intval($path[1]);
        $input = json_decode(file_get_contents("php://input"), true);
        $found = false;
        foreach ($users as &$u) {
            if ($u['id'] === $id) {
                $u['name'] = $input['name'] ?? $u['name'];
                $u['role'] = $input['role'] ?? $u['role'];
                $found = true;
                echo json_encode(["status" => "success", "data" => $u]);
                break;
            }
        }
        if (!$found) {
            http_response_code(404);
            echo json_encode(["status" => "error", "message" => "User not found"]);
        }
    }

    elseif ($method === "DELETE" && isset($path[1])) {
        // DELETE /users/{id}
        $id = intval($path[1]);
        $users = array_filter($users, fn($u) => $u['id'] !== $id);
        echo json_encode(["status" => "success", "message" => "User deleted"]);
    }

    else {
        http_response_code(405);
        echo json_encode(["status" => "error", "message" => "Method not allowed"]);
    }
}
else {
    http_response_code(404);
    echo json_encode(["status" => "error", "message" => "Endpoint not found"]);
}
