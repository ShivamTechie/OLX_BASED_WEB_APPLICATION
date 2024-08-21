<?php
require_once 'Database.php';

// Create a global database connection
function getDatabaseConnection()
{
    static $conn = null;

    if ($conn === null) {
        $db = Database::getInstance();
        $conn = $db->getConnection();
    }

    return $conn;
}

// Prepare a query and bind parameters
function executeQuery(
    $query,
    $types = "",
    $params = []
) {
    $conn = getDatabaseConnection();
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    if (!empty($params)) {
        if (empty($types)) {
            // Determine the parameter types if not provided
            $types = '';
            foreach ($params as $param) {
                if (is_int($param)) {
                    $types .= 'i';
                } elseif (is_double($param)) {
                    $types .= 'd';
                } elseif (is_string($param)) {
                    $types .= 's';
                } elseif (is_null($param)) {
                    $types .= 's';
                } else {
                    die("Unsupported parameter type");
                }
            }
        }

        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();

    if ($stmt->error) {
        die("Execute failed: " . $stmt->error);
    }

    return $stmt;
}

// Example function to get all users
function getAllUsers()
{
    $query = "SELECT * FROM users";
    $stmt = executeQuery($query);

    $result = $stmt->get_result();

    if ($result === false) {
        die("Query failed: " . $stmt->error);
    }

    return $result->fetch_all(MYSQLI_ASSOC);
}

// Example function to get a user by ID
function getUserById($id)
{
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = executeQuery($query, "i", [$id]);

    $result = $stmt->get_result();

    if ($result === false) {
        die("Query failed: " . $stmt->error);
    }

    return $result->fetch_assoc();
}

// Example function to insert a new user
function insertUser($name, $email)
{
    $query = "INSERT INTO users (name, email) VALUES (?, ?)";
    $stmt = executeQuery($query, "ss", [$name, $email]);

    return $stmt->insert_id;
}

// Add other functions as needed























