<?php
require_once '../../App/core/Functions.php';

class AdminModel
{
  private static $instance = null;

  private function __construct()
  {
    // Prevent direct object creation
  }

  private function __clone()
  {
    // Prevent object cloning
  }

  public static function getInstance()
  {
    if (self::$instance === null) {
      self::$instance = new self();
    }
    return self::$instance;
  }
  public function getAllAdminData()
  {
    // Prepare your query and execute it
    $query = "
        SELECT 
            l.listing_id,
            l.title AS product_title,
            u.username AS owner_name,
            u.profile_picture,
            u.phone_number,
            u.user_id,
            l.price,
            l.status,
            l.date_posted,
            c.category_name,
            li.image_path AS product_image
        FROM listings l
        JOIN users u ON l.user_id = u.user_id
        JOIN categories c ON l.category_id = c.category_id
        LEFT JOIN listings_images li ON l.listing_id = li.listing_id
        WHERE li.image_id = (
            SELECT MIN(image_id) 
            FROM listings_images 
            WHERE listing_id = l.listing_id
        )
    ";

    $stmt = executeQuery($query);
    $result = $stmt->get_result();
    if ($result === false) {
      die("Result fetching failed: " . $stmt->error);
    }

    $data = $result->fetch_all(MYSQLI_ASSOC);

    foreach ($data as &$product) {
      $datePosted = new DateTime($product['date_posted']);
      $product['formatted_date'] = $datePosted->format('j F Y g:i A');
      $product['product_image'] = $product['product_image'] ? $product['product_image'] : 'assets/img/product/default.jpg';
    }

    $response = [
      'recordsTotal' => count($data),
      'recordsFiltered' => count($data),
      'data' => $data
    ];

    echo json_encode($response);
  }



  public function handleProfileInfoForAdmin($user_id)
  {
    $conn = getDatabaseConnection();

    // Sanitize input
    $user_id = intval($user_id); // Ensure it's an integer

    // Fetch user profile information
    $query = "SELECT username, location, phone_number, profile_picture FROM users WHERE user_id = ?";
    $stmt = executeQuery($query, "i", [$user_id]);
    $profileInfo = $stmt->get_result()->fetch_assoc();

    if ($profileInfo) {
      echo json_encode(['status' => 'success', 'profileInfo' => $profileInfo]);
      exit(); // Ensure no further output
    } else {
      echo json_encode(['status' => 'error', 'message' => 'Profile not found']);
      exit(); // Ensure no further output
    }
  }


  public function handleProfileUpdationForAdmin($user_id, $username, $location, $phone_number, $profile_picture)
  {
    $conn = getDatabaseConnection();

    // Sanitize input
    $username = htmlspecialchars(strip_tags(trim($username)));
    $location = htmlspecialchars(strip_tags(trim($location)));
    $phone_number = htmlspecialchars(strip_tags(trim($phone_number)));

    // Initialize variables for the update query
    $update_fields = [];
    $params = [];
    $types = "";

    // Add location to the update fields if not empty
    if (!empty($location)) {
      $update_fields[] = "location = ?";
      $params[] = $location;
      $types .= "s";
    }

    // Add phone number to the update fields if not empty
    if (!empty($phone_number)) {
      $update_fields[] = "phone_number = ?";
      $params[] = $phone_number;
      $types .= "s";
    }

    // Handle profile picture
    if (!empty($profile_picture)) {
      $update_fields[] = "profile_picture = ?";
      $params[] = $profile_picture;
      $types .= "s";
    }

    // Add username to the update fields
    $update_fields[] = "username = ?";
    $params[] = $username;
    $types .= "s";

    // Check if there are any fields to update
    if (empty($update_fields)) {
      echo json_encode(['status' => 'error', 'message' => 'No fields to update']);
      exit();
    }

    // Build the update query dynamically
    $update_fields_str = implode(", ", $update_fields);
    $query = "UPDATE users SET $update_fields_str WHERE user_id = ?";
    $params[] = $user_id; // User ID
    $types .= "i";

    // Debugging: Log the query and parameters
    error_log("Query: " . $query);
    error_log("Params: " . print_r($params, true));

    // Execute the query
    $stmt = executeQuery($query, $types, $params);

    // Log the affected rows
    error_log("Affected Rows: " . $stmt->affected_rows);

    // Check if any rows were affected
    if ($stmt->affected_rows > 0) {
      echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully']);
    } else {
      // Check if the user ID exists before reporting an error
      $check_query = "SELECT COUNT(*) AS count FROM users WHERE user_id = ?";
      $check_stmt = executeQuery($check_query, "i", [$user_id]);
      $check_result = $check_stmt->get_result()->fetch_assoc();

      if ($check_result['count'] == 0) {
        echo json_encode(['status' => 'error', 'message' => 'User ID does not exist']);
      } else {
        echo json_encode(['status' => 'error', 'message' => 'No changes were made or failed to update profile']);
      }
    }
    exit(); // Ensure no further output
  }


  public function processDeleteUserAccount($user_id)
  {
    $conn = getDatabaseConnection();
    $response = [];

    // Start a transaction
    $conn->begin_transaction();

    try {
      // Step 1: Fetch all listing IDs for this user
      $stmt = executeQuery("SELECT listing_id FROM listings WHERE user_id = ?", "i", [$user_id]);
      $listings = $stmt->get_result();

      // Prepare to delete images associated with the listings
      while ($listing = $listings->fetch_assoc()) {
        $listing_id = $listing['listing_id'];

        // Delete images related to each listing from the database
        executeQuery("DELETE FROM listings_images WHERE listing_id = ?", "i", [$listing_id]);
      }

      // Step 2: Delete all listings associated with the user
      executeQuery("DELETE FROM listings WHERE user_id = ?", "i", [$user_id]);

      // Step 3: Delete the user record
      executeQuery("DELETE FROM users WHERE user_id = ?", "i", [$user_id]);

      // Commit transaction
      $conn->commit();

      // Set success response
      $response = ['status' => 'success', 'message' => 'User account and associated data deleted successfully'];
    } catch (Exception $e) {
      // Rollback transaction in case of error
      $conn->rollback();

      // Set error response
      $response = ['status' => 'error', 'message' => 'Failed to delete user account: ' . $e->getMessage()];
    }

    return $response;
  }



  public function processAdminLogin($username, $password)
  {
    $conn = getDatabaseConnection();

    // Sanitize input
    $username = htmlspecialchars(strip_tags(trim($username)));
    $password = htmlspecialchars(strip_tags(trim($password)));

    // Check if username exists
    $query = "SELECT  username, password_hash, full_name FROM admins WHERE username = ?";
    $stmt = executeQuery($query, "s", [$username]);
    $user = $stmt->get_result()->fetch_assoc();

    if (!$user) {
      // Username not found
      return ['status' => 'error', 'message' => 'Username not found'];
    }

    // Compare password directly if stored as plain text
    if ($password === $user['password_hash']) {
      // Password correct, set session
      if (session_status() === PHP_SESSION_NONE) {
        session_start();
      }

      // Store username and full_name in session

      $_SESSION['full_name'] = $user['full_name'];

      return ['status' => 'success', 'message' => 'Login successful'];
    } else {
      // Password incorrect
      return ['status' => 'error', 'message' => 'Incorrect password'];
    }
  }



  public function handleGetProfileAdminInfo($username)
  {
    // Get the database connection
    $conn = getDatabaseConnection();

    // Sanitize the input
    $username = htmlspecialchars(strip_tags(trim($username)));

    // Prepare the query to fetch the profile picture
    $query = "SELECT profile_picture FROM admins WHERE full_name = ?";
    $stmt = executeQuery($query, "s", [$username]);

    // Check for query execution errors
    if (!$stmt) {
      return json_encode(['status' => 'error', 'message' => 'Database query failed']);
    }

    // Fetch the result
    $user = $stmt->get_result()->fetch_assoc();

    // Check if user exists
    if (!$user) {
      return json_encode(['status' => 'error', 'message' => 'User not found']);
    }

    // Return the result in JSON format
    return json_encode(['status' => 'success', 'profile_picture' => $user['profile_picture']]);
  }
}
