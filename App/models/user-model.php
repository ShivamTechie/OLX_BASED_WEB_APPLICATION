<?php
require_once __DIR__ . '/../core/Functions.php';

require '../../vendor/autoload.php';
date_default_timezone_set('Asia/Kolkata');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class ProductModel
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

    public function getAllCategories()
    {
        $query = "SELECT category_name,category_id FROM categories";
        $stmt = executeQuery($query);
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // private function fetchRandomCategories()
    // {
    //     $query = "SELECT category_id, category_name FROM categories ORDER BY RAND() LIMIT 10"; // Increased limit to ensure enough categories
    //     $stmt = executeQuery($query);
    //     return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    // }

    // private function fetchProductCountByCategory($categoryId)
    // {
    //     $query = "SELECT COUNT(*) as product_count FROM listings WHERE category_id = ? AND status = 'active'";
    //     $stmt = executeQuery($query, "i", [$categoryId]);
    //     return $stmt->get_result()->fetch_assoc()['product_count'];
    // }

    // private function fetchLatestProductByCategory($categoryId)
    // {
    //     $query = "
    //     SELECT 
    //         l.listing_id, l.title, l.description, l.price, l.location, l.date_posted,
    //         u.username,
    //         COALESCE(li.image_path, 'default-img.jpg') AS image_path,
    //         c.category_name
    //     FROM listings l
    //     JOIN users u ON l.user_id = u.user_id
    //     LEFT JOIN (
    //         SELECT listing_id, image_path
    //         FROM listings_images
    //         ORDER BY uploaded_at DESC
    //     ) li ON l.listing_id = li.listing_id
    //     JOIN categories c ON l.category_id = c.category_id
    //     WHERE l.category_id = ? AND l.status = 'active'
    //     ORDER BY l.date_posted DESC
    //     LIMIT 1
    // ";

    //     $stmt = executeQuery($query, "i", [$categoryId]);
    //     $result = $stmt->get_result()->fetch_assoc();
    //     if ($result) {
    //         $result['image_path'] = 'assets/img/product/' . $result['image_path'];
    //     }
    //     return $result;
    // }

    // public function getLatestProductsFromRandomCategories()
    // {
    //     $requiredCount = 6; // The number of categories/products you want
    //     $initialFetchCount = 10; // Initial number of categories to fetch

    //     $categories = $this->fetchRandomCategories();
    //     $validCategories = [];

    //     // Check if each category has products
    //     foreach ($categories as $category) {
    //         $productCount = $this->fetchProductCountByCategory($category['category_id']);
    //         if ($productCount > 0) {
    //             $validCategories[] = $category['category_id'];
    //         }
    //         // Stop if we have enough categories with products
    //         if (count($validCategories) >= $requiredCount) {
    //             break;
    //         }
    //     }

    //     // If not enough valid categories, continue fetching additional categories
    //     while (count($validCategories) < $requiredCount) {
    //         $additionalCategories = $this->fetchRandomCategories();
    //         foreach ($additionalCategories as $category) {
    //             if (!in_array($category['category_id'], $validCategories) && $this->fetchProductCountByCategory($category['category_id']) > 0) {
    //                 $validCategories[] = $category['category_id'];
    //                 if (count($validCategories) >= $requiredCount) {
    //                     break 2; // Break out of both loops
    //                 }
    //             }
    //         }
    //     }

    //     // Now fetch the latest product for each valid category
    //     $latestProducts = [];
    //     foreach ($validCategories as $categoryId) {
    //         $latestProduct = $this->fetchLatestProductByCategory($categoryId);
    //         if ($latestProduct) {
    //             $latestProducts[] = $latestProduct;
    //         }
    //     }

    //     // If we still don't have enough products, try fetching more from the valid categories
    //     if (count($latestProducts) < $requiredCount) {
    //         $remainingCount = $requiredCount - count($latestProducts);
    //         foreach ($validCategories as $categoryId) {
    //             $additionalProducts = $this->fetchLatestProductByCategory($categoryId);
    //             if ($additionalProducts) {
    //                 $latestProducts[] = $additionalProducts;
    //                 if (count($latestProducts) >= $requiredCount) {
    //                     break;
    //                 }
    //             }
    //         }
    //     }

    //     return $latestProducts;
    // }
    private function fetchLatestProducts($limit = 6)
    {
        $query = "
        SELECT 
            l.listing_id, l.title, l.description, l.price, l.location, l.date_posted,
            u.username,
            c.category_name,
            COALESCE(
                (SELECT li.image_path 
                 FROM listings_images li 
                 WHERE li.listing_id = l.listing_id 
                 ORDER BY li.uploaded_at DESC LIMIT 1), 
                'default-img.jpg'
            ) AS image_path
        FROM listings l
        JOIN users u ON l.user_id = u.user_id
        JOIN categories c ON l.category_id = c.category_id
        WHERE l.status = 'active'
        ORDER BY l.date_posted DESC
        LIMIT ?
    ";

        $stmt = executeQuery($query, "i", [$limit]);
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Append the correct image path
        foreach ($result as &$product) {
            $product['image_path'] = 'assets/img/product/' . $product['image_path'];
        }

        return $result;
    }

    public function getLatestProductsFromRandomCategories()
    {
        return $this->fetchLatestProducts(6);
    }



    private function getRandomProductIds()
    {
        $query = "
        SELECT listing_id
        FROM listings
        WHERE status = 'active'
        ORDER BY RAND()
        LIMIT 6
    ";

        $stmt = executeQuery($query);
        $ids = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        return array_column($ids, 'listing_id');
    }

    public function getRandomFeaturedProducts()
    {
        $ids = $this->getRandomProductIds();
        if (empty($ids)) {
            return [];
        }

        $idsPlaceholder = implode(',', array_fill(0, count($ids), '?'));

        $query = "
        SELECT 
            l.listing_id, l.title, l.price, l.location,
            c.category_name,
            COALESCE(
                (SELECT li.image_path 
                 FROM listings_images li 
                 WHERE li.listing_id = l.listing_id 
                 ORDER BY li.uploaded_at DESC LIMIT 1), 
                'assets/img/default-img.jpg'
            ) AS image_path
        FROM listings l
        JOIN categories c ON l.category_id = c.category_id
        WHERE l.listing_id IN ($idsPlaceholder)
        AND l.status = 'active'
        ORDER BY l.listing_id
    ";

        $stmt = executeQuery($query, str_repeat('i', count($ids)), $ids);
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }



    public function getProductDetails($productId)
    {
        $conn = getDatabaseConnection();

        // Retrieve product details and images
        $query = "SELECT l.*, u.username, u.phone_number, u.profile_picture, c.category_name 
              FROM listings l
              JOIN users u ON l.user_id = u.user_id
              JOIN categories c ON l.category_id = c.category_id
              WHERE l.listing_id = ?";
        $stmt = executeQuery($query, "i", [$productId]);
        $product = $stmt->get_result()->fetch_assoc();

        if ($product) {
            // Fetch all images for the product
            $imageQuery = "SELECT image_path FROM listings_images WHERE listing_id = ?";
            $imageStmt = executeQuery($imageQuery, "i", [$productId]);
            $images = $imageStmt->get_result()->fetch_all(MYSQLI_ASSOC);

            // Collect image paths into an array
            $product['images'] = array_column($images, 'image_path');

            // Format the date
            $datePosted = new DateTime($product['date_posted']);
            $product['formatted_date'] = $datePosted->format('j F Y g:i A');
        }

        return $product;
    }


    public function getRandomProductsFromSeller($userId, $currentListingId)
    {
        $query = "SELECT l.*, li.image_path, u.username, c.category_name 
              FROM listings l
              JOIN (
                  SELECT listing_id, MIN(image_path) AS image_path
                  FROM listings_images
                  GROUP BY listing_id
              ) li ON l.listing_id = li.listing_id
              JOIN users u ON l.user_id = u.user_id
              JOIN categories c ON l.category_id = c.category_id
              WHERE l.user_id = ? 
              AND l.listing_id != ? 
              ORDER BY RAND() 
              LIMIT 5";
        $stmt = executeQuery($query, "ii", [$userId, $currentListingId]);
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }


    public function getCategoriesWithCounts()
    {
        $query = "
        SELECT c.category_id, c.category_name, COUNT(l.listing_id) as count
        FROM categories c
        LEFT JOIN listings l ON c.category_id = l.category_id AND l.status = 'Active'
        GROUP BY c.category_id, c.category_name
        ORDER BY c.category_name
    ";
        $stmt = executeQuery($query);
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }


    public function getAllProducts($page = 1, $perPage = 10)
    {
        $offset = ($page - 1) * $perPage;

        // Get the total number of products
        $totalQuery = "SELECT COUNT(*) AS total FROM listings WHERE status = 'active'";
        $totalResult = executeQuery($totalQuery);
        if (!$totalResult) {
            return ['error' => 'Failed to retrieve total count'];
        }
        $totalCount = $totalResult->get_result()->fetch_assoc()['total'];

        // Get the products for the current page
        $query = "
    SELECT 
        l.listing_id, 
        l.title, 
        l.description, 
        l.price, 
        l.location, 
        l.date_posted,
        l.brand,
        c.category_name,
        COALESCE(li.image_path, 'default-img.jpg') AS image_path,
        u.username  
    FROM listings l
    JOIN categories c ON l.category_id = c.category_id
    LEFT JOIN (
        SELECT listing_id, image_path
        FROM listings_images
        ORDER BY uploaded_at ASC
    ) li ON l.listing_id = li.listing_id
    JOIN users u ON l.user_id = u.user_id  
    WHERE l.status = 'active'
    GROUP BY l.listing_id
    ORDER BY l.date_posted DESC
    LIMIT ? OFFSET ?
    ";

        $stmt = executeQuery($query, "ii", [$perPage, $offset]);
        if (!$stmt) {
            return ['error' => 'Failed to retrieve products'];
        }
        $products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Calculate the total number of pages
        $totalPages = ceil($totalCount / $perPage);

        return [
            'products' => $products,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalCount' => $totalCount
        ];
    }


    public function getProductsByCategory($categoryId, $page = 1, $perPage = 10)
    {
        $offset = ($page - 1) * $perPage;

        // Get the total number of products in the category
        $totalQuery = "SELECT COUNT(*) AS total FROM listings WHERE category_id = ? AND status = 'active'";
        $totalResult = executeQuery($totalQuery, "i", [$categoryId]);
        $totalCount = $totalResult->get_result()->fetch_assoc()['total'];

        // Get the products for the current page
        $query = "
        SELECT 
            l.listing_id, 
            l.title, 
            l.description, 
            l.price, 
            l.location, 
            l.date_posted,
            l.brand,
            c.category_name,
            COALESCE(li.image_path, 'default-img.jpg') AS image_path,
            u.username  
        FROM listings l
        JOIN categories c ON l.category_id = c.category_id
        LEFT JOIN (
            SELECT listing_id, image_path
            FROM listings_images
            ORDER BY uploaded_at ASC
        ) li ON l.listing_id = li.listing_id
        JOIN users u ON l.user_id = u.user_id  
        WHERE l.category_id = ? AND l.status = 'active'
        GROUP BY l.listing_id
        ORDER BY l.date_posted DESC
        LIMIT ? OFFSET ?
    ";

        $stmt = executeQuery($query, "iii", [$categoryId, $perPage, $offset]);
        $products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Calculate the total number of pages
        $totalPages = ceil($totalCount / $perPage);

        return [
            'products' => $products,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalCount' => $totalCount
        ];
    }



    public function searchProducts($searchQuery, $page = 1, $perPage = 10)
    {
        $offset = ($page - 1) * $perPage;

        // Split the search query into parts
        $parts = explode(',', $searchQuery);
        $keyword = isset($parts[0]) ? $parts[0] : '';
        $location = isset($parts[1]) ? $parts[1] : '';
        $categoryId = isset($parts[2]) ? $parts[2] : '';

        // Escape the keyword and location to prevent SQL injection
        $escapedKeyword = '%' . $keyword . '%';
        $escapedLocation = '%' . $location . '%';

        // Initialize query parts
        $conditions = ["l.status = 'active'"];
        $params = [];
        $paramTypes = '';

        // Add keyword condition
        if (!empty($keyword)) {
            $keywordCondition = "(l.title LIKE ? OR l.description LIKE ? OR l.price LIKE ? OR l.brand LIKE ? OR u.username LIKE ?)";
            $conditions[] = $keywordCondition;
            $params = array_merge($params, array_fill(0, 5, $escapedKeyword));
            $paramTypes .= str_repeat('s', 5);
        }

        // Add location condition
        if (!empty($location)) {
            $locationCondition = "l.location LIKE ?";
            $conditions[] = $locationCondition;
            $params[] = $escapedLocation;
            $paramTypes .= 's';
        }

        // Add category condition
        if (!empty($categoryId)) {
            // Get the category name from the category ID
            $categoryQuery = "SELECT category_name FROM categories WHERE category_id = ?";
            $categoryStmt = executeQuery($categoryQuery, 'i', [$categoryId]);
            $categoryName = $categoryStmt->get_result()->fetch_assoc()['category_name'];

            $categoryCondition = "c.category_name LIKE ?";
            $conditions[] = $categoryCondition;
            $params[] = '%' . $categoryName . '%';
            $paramTypes .= 's';
        }

        // Build the WHERE clause
        $whereClause = implode(' AND ', $conditions);

        // Get the total number of products matching the search query
        $totalQuery = "
        SELECT COUNT(*) AS total
        FROM listings l
        JOIN users u ON l.user_id = u.user_id
        LEFT JOIN categories c ON l.category_id = c.category_id
        WHERE $whereClause
    ";

        $totalStmt = executeQuery($totalQuery, $paramTypes, $params);
        $totalCount = $totalStmt->get_result()->fetch_assoc()['total'];

        // Get the products for the current page
        $query = "
        SELECT 
            l.listing_id, 
            l.title, 
            l.description, 
            l.price, 
            l.location, 
            l.date_posted,
            l.brand,
            c.category_name,
            COALESCE(li.image_path, 'default-img.jpg') AS image_path,
            u.username  
        FROM listings l
        JOIN categories c ON l.category_id = c.category_id
        LEFT JOIN (
            SELECT listing_id, image_path
            FROM listings_images
            ORDER BY uploaded_at ASC
        ) li ON l.listing_id = li.listing_id
        JOIN users u ON l.user_id = u.user_id  
        WHERE $whereClause
        GROUP BY l.listing_id
        ORDER BY l.date_posted DESC
        LIMIT ? OFFSET ?
    ";

        $params[] = $perPage;
        $params[] = $offset;
        $paramTypes .= 'ii';

        $stmt = executeQuery($query, $paramTypes, $params);
        $products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Calculate the total number of pages
        $totalPages = ceil($totalCount / $perPage);

        return [
            'products' => $products,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalCount' => $totalCount
        ];
    }




    public function registerUser($data, $profilePicture)
    {
        $conn = getDatabaseConnection();

        // Sanitize inputs
        $username = htmlspecialchars(strip_tags(trim($data['username'])));
        $email = htmlspecialchars(strip_tags(trim($data['email'])));
        $password = htmlspecialchars(strip_tags(trim($data['password'])));
        $location = htmlspecialchars(strip_tags(trim($data['location'])));
        $phoneNumber = htmlspecialchars(strip_tags(trim($data['phone'])));

        // Check if username already exists
        $query = "SELECT user_id FROM users WHERE username = ?";
        $stmt = executeQuery($query, "s", [$username]);
        if ($stmt->get_result()->num_rows > 0) {
            return ['status' => 'error', 'message' => 'Username already registered'];
            exit();
        }

        // Check if email already exists
        $query = "SELECT user_id FROM users WHERE email = ?";
        $stmt = executeQuery($query, "s", [$email]);
        if ($stmt->get_result()->num_rows > 0) {
            return ['status' => 'error', 'message' => 'Email already registered'];
            exit();
        }

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Handle profile picture upload
        if ($profilePicture && $profilePicture['error'] === UPLOAD_ERR_OK) {
            $imageExtension = pathinfo($profilePicture['name'], PATHINFO_EXTENSION);
            $uniqueImageName = time() . '_' . uniqid() . '.' . $imageExtension; // Create a unique name
            $targetDirectory = __DIR__ . '/../../assets/img/author/'; // Define target directory

            // Ensure target directory exists
            if (!is_dir($targetDirectory)) {
                if (!mkdir($targetDirectory, 0755, true)) {
                    return ['status' => 'error', 'message' => 'Failed to create target directory'];
                    exit();
                }
            }

            // Move the uploaded file
            if (move_uploaded_file($profilePicture['tmp_name'], $targetDirectory . $uniqueImageName)) {
                // Prepare to insert user into the database
                $query = "INSERT INTO users (username, email, password_hash, profile_picture, location, phone_number, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())";
                $stmt = executeQuery($query, "ssssss", [$username, $email, $hashedPassword, $uniqueImageName, $location, $phoneNumber]);

                if ($stmt) {
                    return ['status' => 'success', 'message' => 'Registration successful'];
                    exit();
                } else {
                    return ['status' => 'error', 'message' => 'Failed to register user'];
                    exit();
                }
            } else {
                return ['status' => 'error', 'message' => 'Failed to upload profile picture'];
                exit();
            }
        } else {
            return ['status' => 'error', 'message' => 'No profile picture uploaded or upload error'];
            exit();
        }
    }




    public function processLogin($username, $password)
    {
        $conn = getDatabaseConnection();

        // Sanitize input
        $username = htmlspecialchars(strip_tags(trim($username)));
        $password = htmlspecialchars(strip_tags(trim($password)));

        // Check if username exists
        $query = "SELECT user_id, password_hash FROM users WHERE username = ?";
        $stmt = executeQuery($query, "s", [$username]);
        $user = $stmt->get_result()->fetch_assoc();

        if (!$user) {
            // Username not found
            return ['status' => 'error', 'message' => 'Username not found'];
        }

        // Verify password
        if (!password_verify($password, $user['password_hash'])) {
            // Password incorrect
            return ['status' => 'error', 'message' => 'Incorrect password'];
        }

        // Password correct, set session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['username'] = $username;

        return ['status' => 'success', 'message' => 'Login successful'];
    }


    public function sendOtpEmail($email, $otp)
    {
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; // Set the SMTP server to send through
            $mail->SMTPAuth   = true;
            $mail->Username   = 'shivamkk2001@gmail.com'; // SMTP username
            $mail->Password   = 'cdss kdvz bcnk skej'; // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            //Recipients
            $mail->setFrom('shivamkk2001@gmail.com', 'OLX');
            $mail->addAddress($email);

            //Content
            $mail->isHTML(true);
            $mail->Subject = 'Your OTP Code';
            $mail->Body    = "Here is your OTP code: <b>$otp</b><br>This OTP is valid for 5 minutes.";

            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }



    function processOtpVerification($username, $otp)
    {
        $productModel = ProductModel::getInstance();
        $conn = getDatabaseConnection();

        // Log the provided OTP for debugging
        error_log("Provided OTP: " . $otp);

        // Fetch the user's email from the database
        $query = "SELECT email FROM users WHERE username = ?";
        $stmt = executeQuery($query, "s", [$username]);

        if (!$stmt) {
            error_log("Error executing query to fetch user email: " . $conn->error);
            return json_encode(['status' => 'error', 'message' => 'Database query error']);
            exit();
        }

        $user = $stmt->get_result()->fetch_assoc();

        if (!$user) {
            return json_encode(['status' => 'error', 'message' => 'User not found']);
        }

        $email = $user['email'];
        error_log("User Email: " . $email);

        // Fetch the latest OTP from the database
        $query = "SELECT otp FROM otp_verification WHERE email = ? ORDER BY created_at DESC LIMIT 1";
        $stmt = executeQuery($query, "s", [$email]);

        if (!$stmt) {
            error_log("Error executing query to fetch OTP: " . $conn->error);
            // Delete all OTP records for this email in case of error
            $deleteQuery = "DELETE FROM otp_verification WHERE email = ?";
            executeQuery($deleteQuery, "s", [$email]);
            return json_encode(['status' => 'error', 'message' => 'Database query error']);
        }

        $result = $stmt->get_result()->fetch_assoc();

        if (!$result) {
            return json_encode(['status' => 'error', 'message' => 'OTP not found for this email']);
        }

        error_log("Stored OTP: " . $result['otp']);

        // Check if the OTP matches
        if ($result['otp'] !== $otp) {
            // Delete all OTP records for this email in case of invalid OTP
            $deleteQuery = "DELETE FROM otp_verification WHERE email = ?";
            executeQuery($deleteQuery, "s", [$email]);
            return json_encode(['status' => 'error', 'message' => 'Invalid OTP']);
        }

        // Delete the OTP record after successful verification
        $deleteQuery = "DELETE FROM otp_verification WHERE email = ?";
        if (executeQuery($deleteQuery, "s", [$email]) === false) {
            error_log("Error deleting OTP record: " . $conn->error);
            return json_encode(['status' => 'error', 'message' => 'Failed to remove OTP record']);
        }

        return json_encode(['status' => 'success', 'message' => 'OTP verified successfully']);
    }


    function processChangePassword($newPassword, $confirmPassword)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $username = $_SESSION['username'];

        // Fetch the user's email and current hashed password from the database
        $query = "SELECT email, password_hash FROM users WHERE username = ?";
        $stmt = executeQuery($query, "s", [$username]);
        $user = $stmt->get_result()->fetch_assoc();

        if (!$user) {
            return json_encode(['status' => 'error', 'message' => 'User not found']);
            exit();
        }

        $email = $user['email'];
        $currentHashedPassword = $user['password_hash'];

        // Check if new password matches confirm password
        if ($newPassword !== $confirmPassword) {
            return json_encode(['status' => 'error', 'message' => 'Passwords do not match']);
        }

        // Verify that the new password is not the same as the old password
        if (password_verify($newPassword, $currentHashedPassword)) {
            return json_encode(['status' => 'error', 'message' => 'New password cannot be the same as the old password']);
        }

        // Hash the new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update password in the database
        $query = "UPDATE users SET password_hash = ? WHERE email = ?";
        $stmt = executeQuery($query, "ss", [$hashedPassword, $email]);

        if ($stmt) {
            return json_encode(['status' => 'success', 'message' => 'Password changed successfully']);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Failed to change password']);
        }
    }

    public function verifyOtp($otp)
    {
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $email = $_SESSION['forgot_password_email'];

        // Check if the email is stored in the session
        if (!isset($_SESSION['forgot_password_email'])) {
            return json_encode(['status' => 'error', 'message' => 'Email not found in session']);
        }



        // Log the provided OTP for debugging
        error_log("Provided OTP: " . $otp);

        $conn = getDatabaseConnection();

        // Fetch the latest OTP $email = $_SESSION['forgot_password_email'];from the database
        $query = "SELECT otp FROM otp_verification WHERE email = ? ORDER BY created_at DESC LIMIT 1";
        $stmt = executeQuery($query, "s", [$email]);

        if (!$stmt) {
            error_log("Error executing query to fetch OTP: " . $conn->error);
            // Delete all OTP records for this email in case of error
            $deleteQuery = "DELETE FROM otp_verification WHERE email = ?";
            executeQuery($deleteQuery, "s", [$email]);
            return json_encode(['status' => 'error', 'message' => 'Database query error']);
        }

        $result = $stmt->get_result()->fetch_assoc();

        if (!$result) {
            return json_encode(['status' => 'error', 'message' => 'OTP not found for this email']);
        }

        error_log("Stored OTP: " . $result['otp']);

        // Check if the OTP matches
        if ($result['otp'] !== $otp) {
            // Delete all OTP records for this email in case of invalid OTP
            $deleteQuery = "DELETE FROM otp_verification WHERE email = ?";
            executeQuery($deleteQuery, "s", [$email]);
            return json_encode(['status' => 'error', 'message' => 'Invalid OTP']);
        }

        // Delete the OTP record after successful verification
        $deleteQuery = "DELETE FROM otp_verification WHERE email = ?";
        if (executeQuery($deleteQuery, "s", [$email]) === false) {
            error_log("Error deleting OTP record: " . $conn->error);
            return json_encode(['status' => 'error', 'message' => 'Failed to remove OTP record']);
        }

        return json_encode(['status' => 'success', 'message' => 'OTP verified successfully']);
    }




    public function changeForgotPassword($newPassword, $confirmPassword)
    {
        header('Content-Type: application/json');

        // Verify if the new and confirm passwords match
        if ($newPassword !== $confirmPassword) {
            return json_encode(['status' => 'error', 'message' => 'New password and confirm password do not match']);
        }

        if (session_status() == PHP_SESSION_NONE) {
            session_start(); // Start the session if it hasn't been started already
        }

        // Get the email from session
        $email = $_SESSION['forgot_password_email'];

        // Check if the email is valid
        if (empty($email)) {
            return json_encode(['status' => 'error', 'message' => 'Session has expired or email is missing']);
        }

        // Check if new password is the same as the old one
        $query = "SELECT password_hash FROM users WHERE email = ?";
        $stmt = executeQuery($query, "s", [$email]);

        if (!$stmt) {
            return json_encode(['status' => 'error', 'message' => 'Database query failed']);
        }

        $result = $stmt->get_result()->fetch_assoc();

        if ($result) {
            $hashedOldPassword = $result['password_hash'];
            if (password_verify($newPassword, $hashedOldPassword)) {
                return json_encode(['status' => 'error', 'message' => 'New password cannot be the same as the old password']);
            }
        }

        // Hash the new password
        $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update password
        $query = "UPDATE users SET password_hash = ? WHERE email = ?";
        $stmt = executeQuery($query, "ss", [$hashedNewPassword, $email]);

        if (!$stmt) {
            return json_encode(['status' => 'error', 'message' => 'Database query failed']);
        }

        if ($stmt->affected_rows > 0) {
            // Remove OTP entry after successful password change
            $query = "DELETE FROM otp_verification WHERE email = ?";
            executeQuery($query, "s", [$email]);

            return json_encode(['status' => 'success', 'message' => 'Password successfully changed']);
        }

        return json_encode(['status' => 'error', 'message' => 'Error changing password']);
    }


    public function handleGetProfileInfo($username)
    {
        // Get the database connection
        $conn = getDatabaseConnection();

        // Sanitize the input
        $username = htmlspecialchars(strip_tags(trim($username)));

        // Prepare the query to fetch the profile picture
        $query = "SELECT profile_picture FROM users WHERE username = ?";
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

    function handlePostAd($formData, $files)
    {
        $conn = getDatabaseConnection();

        // Start the session to get the username
        $username = $_SESSION['username'];

        // Retrieve user ID based on the session username
        $userIdQuery = "SELECT user_id FROM users WHERE username = ?";
        $stmt = executeQuery($userIdQuery, "s", [$username]);
        $userIdResult = $stmt->get_result()->fetch_assoc();
        $userId = $userIdResult['user_id'];

        // Retrieve category ID based on category ID (as provided in formData)
        $categoryId = $formData['category'];

        // Log the raw specifications data
        error_log("Raw Specifications: " . htmlspecialchars($formData['specifications']));

        // Convert <br> tags to commas for specifications
        $specifications = str_replace('<br>', ',', $formData['specifications']);

        // Debugging: Log the received specifications and converted specifications
        error_log("Original Specifications: " . $formData['specifications']);
        error_log("Converted Specifications: " . $specifications);

        // Set default status to 'Active' if not provided
        $status = isset($formData['status']) ? $formData['status'] : 'active';

        // Insert listing into `listings` table
        $insertListingQuery = "INSERT INTO listings (user_id, category_id, title, description, price, location, `condition`, brand, specifications, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $params = [
            $userId,
            $categoryId,
            $formData['title'],
            $formData['description'],
            $formData['price'],
            $formData['address'], // Assuming location field is address
            $formData['condition'],
            $formData['brand'],
            $specifications,
            $status
        ];
        $stmt = executeQuery($insertListingQuery, "iissdsssss", $params);
        if ($stmt->errno) {
            error_log("Failed to insert listing: " . $stmt->error);
            return ['status' => 'error', 'message' => 'Failed to insert listing'];
        }

        $listingId = $conn->insert_id; // Get the last inserted listing ID

        // Handle image upload
        if (isset($files['tmp_name']) && !empty($files['tmp_name'])) {
            foreach ($files['tmp_name'] as $index => $tmpName) {
                if ($files['error'][$index] === UPLOAD_ERR_OK) {
                    $originalFileName = basename($files['name'][$index]);
                    $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);
                    $newFileName = time() . '_' . $originalFileName; // Add current time and original name to the filename
                    $filePath = '../../assets/img/product/' . $newFileName;

                    // Log file details
                    error_log("Processing file: " . $originalFileName . " as " . $newFileName);

                    // Move uploaded file to desired directory
                    if (move_uploaded_file($tmpName, $filePath)) {
                        // Insert image path into `listings_images` table
                        $insertImageQuery = "INSERT INTO listings_images (listing_id, image_path) VALUES (?, ?)";
                        $imageStmt = executeQuery($insertImageQuery, "is", [$listingId, $newFileName]);
                        if ($imageStmt->errno) {
                            error_log("Failed to insert image path: " . $imageStmt->error);
                        }
                    } else {
                        error_log("Failed to move uploaded file: " . $originalFileName);
                    }
                } else {
                    error_log("File upload error: " . $files['error'][$index]);
                }
            }
        } else {
            error_log("No files found in the form submission.");
        }

        return ['status' => 'success', 'message' => 'Ad posted successfully'];
    }


    public function handleProfileInfo($username)
    {
        $conn = getDatabaseConnection();

        // Sanitize input
        $username = htmlspecialchars(strip_tags(trim($username)));

        // Fetch user profile information
        $query = "SELECT username, location, phone_number, profile_picture FROM users WHERE username = ?";
        $stmt = executeQuery($query, "s", [$username]);
        $profileInfo = $stmt->get_result()->fetch_assoc();

        if ($profileInfo) {
            echo json_encode(['status' => 'success', 'profileInfo' => $profileInfo]);
            exit(); // Ensure no further output
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Profile not found']);
            exit(); // Ensure no further output
        }
    }


    public function handleProfileUpdation($username, $location, $phone_number, $profile_picture)
    {

        $sesuser = $_SESSION['username']; // Get session username

        $conn = getDatabaseConnection();

        // Print all parameters for debugging
        // error_log("Debug Info:");
        // error_log("Username: " . print_r($username, true));
        // error_log("Location: " . print_r($location, true));
        // error_log("Phone Number: " . print_r($phone_number, true));
        // error_log("Profile Picture: " . print_r($profile_picture, true));
        // error_log("Session Username: " . print_r($sesuser, true));

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

        // Check if there are any fields to update
        if (empty($update_fields)) {
            echo json_encode(['status' => 'error', 'message' => 'No fields to update']);
            exit();
        }

        // Build the update query dynamically
        $update_fields_str = implode(", ", $update_fields);
        $query = "UPDATE users SET $update_fields_str, username = ? WHERE username = ?";
        $params[] = $username; // New username
        $params[] = $sesuser; // Old username
        $types .= "ss";

        // Debugging: Log the query and parameters
        error_log("Query: " . $query);
        error_log("Params: " . print_r($params, true));

        // Execute the query
        $stmt = executeQuery($query, $types, $params);

        // Log the affected rows
        error_log("Affected Rows: " . $stmt->affected_rows);

        // Check if any rows were affected
        if ($stmt->affected_rows > 0) {
            // Update session username if it has changed
            if ($username !== $sesuser) {
                $_SESSION['username'] = $username;
            }
            echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully']);
        } else {
            // Check if the username exists before reporting an error
            $check_query = "SELECT COUNT(*) AS count FROM users WHERE username = ?";
            $check_stmt = executeQuery($check_query, "s", [$username]);
            $check_result = $check_stmt->get_result()->fetch_assoc();

            if ($check_result['count'] == 0) {
                echo json_encode(['status' => 'error', 'message' => 'Username does not exist']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No changes were made or failed to update profile']);
            }
        }
        exit(); // Ensure no further output
    }

    public function processUserAds($username)
    {
        $conn = getDatabaseConnection();

        // Sanitize input
        $username = htmlspecialchars(strip_tags(trim($username)));

        // Fetch user ID from users table
        $query = "SELECT user_id FROM users WHERE username = ?";
        $stmt = executeQuery($query, "s", [$username]);
        $user = $stmt->get_result()->fetch_assoc();

        if ($user) {
            $userId = $user['user_id'];

            // Count total number of listings for the user
            $query = "SELECT COUNT(*) AS total_count FROM listings WHERE user_id = ?";
            $stmt = executeQuery($query, "i", [$userId]);
            $countResult = $stmt->get_result()->fetch_assoc();
            $recordsTotal = $countResult['total_count'];

            // Fetch filtered listings count (if needed for search/filter)
            $recordsFiltered = $recordsTotal; // Assuming no filtering is done in this example

            // Fetch all listings with category names and the first image for each listing
            $query = "
        SELECT l.listing_id, l.title, l.description, l.price, l.location, l.date_posted, l.status, l.`condition`, l.brand, l.specifications, c.category_name,
               li.image_path AS image
        FROM listings l
        LEFT JOIN categories c ON l.category_id = c.category_id
        LEFT JOIN (
            SELECT listing_id, image_path
            FROM listings_images
            WHERE image_id IN (
                SELECT MIN(image_id)
                FROM listings_images
                GROUP BY listing_id
            )
        ) li ON l.listing_id = li.listing_id
        WHERE l.user_id = ?";
            $stmt = executeQuery($query, "i", [$userId]);
            $listings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            // Prepare the data array
            $data = [];
            foreach ($listings as $listing) {
                $data[] = [
                    'listing_id' => $listing['listing_id'],
                    'title' => $listing['title'],
                    'category_name' => $listing['category_name'],
                    'price' => $listing['price'],
                    'location' => $listing['location'],
                    'date_posted' => $listing['date_posted'],
                    'status' => $listing['status'],
                    'image' => $listing['image']
                ];
            }

            // Safely retrieve the draw parameter, defaulting to 0 if not set
            $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 0;

            echo json_encode([
                'draw' => $draw, // For DataTables
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $data
            ]);
        } else {
            // Safely retrieve the draw parameter, defaulting to 0 if not set
            $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 0;

            echo json_encode([
                'draw' => $draw, // For DataTables
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'status' => 'error',
                'message' => 'User not found'
            ]);
        }

        exit(); // Ensure no further output
    }





    public function processProductDetailsForEdit($listing_id)
    {
        $conn = getDatabaseConnection();

        // Sanitize input
        $listing_id = intval($listing_id);

        // Log the received listing_id for debugging
        error_log("Received listing_id: $listing_id");

        // Fetch the listing details with category name, user details, and the first image
        $query = "
    SELECT 
        l.listing_id, 
        l.title, 
        l.description, 
        l.price, 
        l.`condition`, 
        l.location,
        l.brand, 
        l.specifications, 
        l.status, 
        c.category_name,
        u.username AS user_name,
        u.phone_number AS user_phone,
        li.image_path AS image
    FROM listings l
    LEFT JOIN categories c ON l.category_id = c.category_id
    LEFT JOIN users u ON l.user_id = u.user_id
    LEFT JOIN (
        SELECT listing_id, image_path
        FROM listings_images
        WHERE image_id IN (
            SELECT MIN(image_id)
            FROM listings_images
            GROUP BY listing_id
        )
    ) li ON l.listing_id = li.listing_id
    WHERE l.listing_id = ?";

        // Execute the query
        $stmt = executeQuery($query, "i", [$listing_id]);

        // Log query execution status
        if (!$stmt) {
            error_log("Query execution failed: " . $conn->error);
            echo json_encode(['status' => 'error', 'message' => 'Query execution failed']);
            exit();
        }

        $listing = $stmt->get_result()->fetch_assoc();

        // Log fetched listing data
        error_log("Fetched listing data: " . print_r($listing, true));

        // Prepare the response
        if ($listing) {
            // Fetch all images for the current listing
            $imageQuery = "
        SELECT image_path
        FROM listings_images
        WHERE listing_id = ?
        ORDER BY image_id ASC
        LIMIT 3";
            $imageStmt = executeQuery($imageQuery, "i", [$listing_id]);

            // Log image fetching status
            if (!$imageStmt) {
                error_log("Image query execution failed: " . $conn->error);
                echo json_encode(['status' => 'error', 'message' => 'Image query execution failed']);
                exit();
            }

            $images = $imageStmt->get_result()->fetch_all(MYSQLI_ASSOC);

            // Format specifications for display
            $listing['specifications'] = str_replace('<br>', "\n", $listing['specifications']);

            echo json_encode([
                'status' => 'success',
                'product' => $listing,
                'images' => $images
            ]);
        } else {
            error_log("No listings found for listing_id: $listing_id");
            echo json_encode(['status' => 'error', 'message' => 'No listings found']);
        }

        exit(); // Ensure no further output
    }



    function processUpdateProduct($listing_id, $title, $price, $description, $specifications, $condition, $brand, $status, $location, $category_id, $files)
    {
        $conn = getDatabaseConnection();

        // Log received parameters
        error_log("Received parameters: " . print_r(func_get_args(), true));

        // Build the SQL query
        $updateFields = [];
        $params = [];
        $types = '';

        // Always include these fields
        $updateFields[] = 'title = ?';
        $params[] = $title;
        $types .= 's';

        $updateFields[] = 'price = ?';
        $params[] = $price;
        $types .= 'd';

        $updateFields[] = 'description = ?';
        $params[] = $description;
        $types .= 's';

        $updateFields[] = 'specifications = ?';
        $params[] = $specifications;
        $types .= 's';

        $updateFields[] = '`condition` = ?'; // Use backticks to avoid SQL keyword issues
        $params[] = $condition;
        $types .= 's';

        $updateFields[] = 'brand = ?';
        $params[] = $brand;
        $types .= 's';

        $updateFields[] = 'status = ?';
        $params[] = $status;
        $types .= 's';

        $updateFields[] = 'location = ?';
        $params[] = $location;
        $types .= 's';

        // Check for category_id
        if ($category_id > 0) {
            $updateFields[] = 'category_id = ?';
            $params[] = $category_id;
            $types .= 'i';
        }

        // Prepare the final SQL query
        $sql = "UPDATE listings SET " . implode(', ', $updateFields) . " WHERE listing_id = ?";
        $params[] = $listing_id;
        $types .= 'i';

        // Log final SQL and parameters
        error_log("Final SQL: $sql");
        error_log("Parameters: " . print_r($params, true));

        // Execute the statement
        $stmt = executeQuery($sql, $types, $params);

        if (!$stmt) {
            error_log("Query execution failed: " . $conn->error); // Log SQL error
            echo json_encode(['status' => 'error', 'message' => 'Query execution failed']);
            exit();
        }

        // Handle image updates if new images are provided
        if ($files && is_array($files['tmp_name']) && count($files['tmp_name']) > 0) {
            $uploadDir = '../../assets/img/product/'; // Ensure this directory exists and is writable

            // Prepare to delete old images if necessary
            $deleteOldImagesQuery = "DELETE FROM listings_images WHERE listing_id = ?";
            $deleteStmt = executeQuery($deleteOldImagesQuery, 'i', [$listing_id]);
            if (!$deleteStmt) {
                echo json_encode(['status' => 'error', 'message' => 'Failed to delete old images']);
                exit();
            }

            // Insert new images
            $insertImageQuery = "INSERT INTO listings_images (listing_id, image_path) VALUES (?, ?)";
            foreach ($files['tmp_name'] as $index => $tmpName) {
                // Generate unique filename
                $fileExtension = pathinfo($files['name'][$index], PATHINFO_EXTENSION);
                $uniqueFileName = time() . '_' . $index . '.' . $fileExtension;
                $targetPath = $uploadDir . $uniqueFileName;

                // Move the uploaded file to the server directory
                if (move_uploaded_file($tmpName, $targetPath)) {
                    // Insert new image path into database
                    $params = [$listing_id, $uniqueFileName];
                    $types = 'is';
                    $imageStmt = executeQuery($insertImageQuery, $types, $params);

                    if (!$imageStmt) {
                        echo json_encode(['status' => 'error', 'message' => 'Image update failed']);
                        exit();
                    }
                } else {
                    error_log("Failed to move uploaded file: $tmpName");
                }
            }
        }

        echo json_encode(['status' => 'success', 'message' => 'Product updated successfully']);
        exit();
    }


    public function processDeleteProducts($listing_id)
    {
        // Start a transaction
        $conn = getDatabaseConnection();
        $conn->begin_transaction();

        try {
            // Delete the product images from the listings_images table
            $queryImages = "DELETE FROM listings_images WHERE listing_id = ?";
            $stmtImages = executeQuery($queryImages, "i", [$listing_id]);

            // Check if the deletion was successful
            if ($stmtImages->affected_rows === 0) {
                throw new Exception("Failed to delete product images or no images found.");
            }

            // Delete the product from the listings table
            $queryProduct = "DELETE FROM listings WHERE listing_id = ?";
            $stmtProduct = executeQuery($queryProduct, "i", [$listing_id]);

            // Check if the deletion was successful
            if ($stmtProduct->affected_rows === 0) {
                throw new Exception("Failed to delete product or no product found.");
            }

            // Commit the transaction
            $conn->commit();

            echo json_encode(['status' => 'success', 'message' => 'Product deleted successfully']);
            exit();
        } catch (Exception $e) {
            // Rollback the transaction
            $conn->rollback();

            echo json_encode(['status' => 'error', 'message' => 'Error deleting product: ' . $e->getMessage()]);
            exit();
        }
    }


    function handleSendMessage($productId)
    {
        // Ensure session is started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $username = $_SESSION['username'] ?? null;

        if ($username) {
            // User is logged in
            $status = 'loggedIn';

            // Prepare to retrieve user details from the database
            $queryUser = "
            SELECT phone_number, location
            FROM users
            WHERE username = ?
        ";

            // Execute the query to get user details
            $stmtUser = executeQuery($queryUser, 's', [$username]);
            $userData = $stmtUser->get_result()->fetch_assoc();

            // Prepare to retrieve the email and title of the listing
            $queryListing = "
            SELECT u.email, l.title
            FROM listings l
            JOIN users u ON l.user_id = u.user_id
            WHERE l.listing_id = ?
        ";

            // Execute the query to get the email and title
            $stmtListing = executeQuery($queryListing, 'i', [$productId]);
            $listingData = $stmtListing->get_result()->fetch_assoc();

            if ($userData && $listingData) {
                // Extract data from the query results
                $phoneNumber = $userData['phone_number'] ?? 'N/A';
                $location = $userData['location'] ?? 'N/A';
                $recipientEmail = $listingData['email'];
                $productTitle = $listingData['title'];

                // Return the data in JSON format
                return [
                    'status' => $status,
                    'username' => $username,
                    'phoneNumber' => $phoneNumber,
                    'location' => $location,
                    'recipientEmail' => $recipientEmail,
                    'productTitle' => $productTitle
                ];
            } else {
                // Return error if product or user details are not found
                return [
                    'status' => 'error',
                    'message' => 'Product or user details not found'
                ];
            }
        } else {
            // User is not logged in
            return [
                'status' => 'loggedOut',
                'message' => 'User not logged in'
            ];
        }
    }



    function handleSendContactForm($name, $email, $subject, $message)
    {
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; // Set the SMTP server to send through
            $mail->SMTPAuth   = true;
            $mail->Username   = 'shivamkk2001@gmail.com'; // SMTP username
            $mail->Password   = 'cdss kdvz bcnk skej'; // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            //Recipients
            $mail->setFrom('shivamkk2001@gmail.com', 'OLX');
            $mail->addAddress('skmwebworks@gmail.com'); // Set recipient to the desired email

            //Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = "
            <h3>Contact Form Submission Details</h3>
            <p><strong>Name:</strong> $name</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Message:</strong></p>
            <p>$message</p>
        ";

            $mail->send();
            return json_encode(['status' => 'success', 'message' => 'Your message has been sent successfully.']);
        } catch (Exception $e) {
            return json_encode(['status' => 'error', 'message' => 'Failed to send your message. Please try again later.']);
        }
    }

    function handleLikedProduct($productId)
    {
        // Ensure session is started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $username = $_SESSION['username'] ?? null;

        if ($username) {
            // User is logged in
            $status = 'loggedIn';

            // Prepare to retrieve user_id from the database
            $queryUser = "SELECT user_id FROM users WHERE username = ?";
            $stmtUser = executeQuery($queryUser, 's', [$username]);
            $userResult = $stmtUser->get_result()->fetch_assoc();

            if ($userResult) {
                $userId = $userResult['user_id'];

                if ($productId) {
                    // Check if the product is already liked by the user
                    $checkQuery = "SELECT id FROM product_likes WHERE user_id = ? AND product_id = ?";
                    $stmtCheck = executeQuery($checkQuery, 'ii', [$userId, $productId]);
                    $checkResult = $stmtCheck->get_result()->fetch_assoc();

                    if (!$checkResult) {
                        // Insert new like
                        $insertQuery = "INSERT INTO product_likes (user_id, product_id) VALUES (?, ?)";
                        $result = executeQuery($insertQuery, 'ii', [$userId, $productId]);

                        if ($result) {
                            return [
                                'status' => 'success',
                                'message' => 'Product liked successfully'
                            ];
                        } else {
                            return [
                                'status' => 'error',
                                'message' => 'Failed to like product'
                            ];
                        }
                    } else {
                        return [
                            'status' => 'error',
                            'message' => 'Product already liked'
                        ];
                    }
                } else {
                    return [
                        'status' => 'error',
                        'message' => 'Product ID not specified'
                    ];
                }
            } else {
                return [
                    'status' => 'error',
                    'message' => 'User not found'
                ];
            }
        } else {
            // User is not logged in
            return [
                'status' => 'loggedOut',
                'message' => 'User not logged in'
            ];
        }
    }

    
    function handleSavedProduct($productId)
    {
        // Ensure session is started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $username = $_SESSION['username'] ?? null;

        if ($username) {
            // User is logged in
            $status = 'loggedIn';

            // Prepare to retrieve user_id from the database
            $queryUser = "SELECT user_id FROM users WHERE username = ?";
            $stmtUser = executeQuery($queryUser, 's', [$username]);
            $userResult = $stmtUser->get_result()->fetch_assoc();

            if ($userResult) {
                $userId = $userResult['user_id'];

                if ($productId) {
                    // Check if the product is already saved by the user
                    $checkQuery = "SELECT id FROM saved_products WHERE user_id = ? AND product_id = ?";
                    $stmtCheck = executeQuery($checkQuery, 'ii', [$userId, $productId]);
                    $checkResult = $stmtCheck->get_result()->fetch_assoc();

                    if (!$checkResult) {
                        // Insert new save
                        $insertQuery = "INSERT INTO saved_products (user_id, product_id) VALUES (?, ?)";
                        $result = executeQuery($insertQuery, 'ii', [$userId, $productId]);

                        if ($result) {
                            return [
                                'status' => 'success',
                                'message' => 'Product saved successfully'
                            ];
                        } else {
                            return [
                                'status' => 'error',
                                'message' => 'Failed to save product'
                            ];
                        }
                    } else {
                        return [
                            'status' => 'error',
                            'message' => 'Product already saved'
                        ];
                    }
                } else {
                    return [
                        'status' => 'error',
                        'message' => 'Product ID not specified'
                    ];
                }
            } else {
                return [
                    'status' => 'error',
                    'message' => 'User not found'
                ];
            }
        } else {
            // User is not logged in
            return [
                'status' => 'loggedOut',
                'message' => 'User not logged in'
            ];
        }
    }




    public function processUserLikedAds($username)
    {
        $conn = getDatabaseConnection();

        // Sanitize input
        $username = htmlspecialchars(strip_tags(trim($username)));

        // Fetch user ID from users table
        $query = "SELECT user_id FROM users WHERE username = ?";
        $stmt = executeQuery($query, "s", [$username]);
        $user = $stmt->get_result()->fetch_assoc();

        if ($user) {
            $userId = $user['user_id'];

            // Get liked product IDs for the user
            $query = "SELECT product_id FROM product_likes WHERE user_id = ?";
            $stmt = executeQuery($query, "i", [$userId]);
            $likedProducts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            if ($likedProducts) {
                $productIds = array_column($likedProducts, 'product_id');

                // Count total number of liked products
                $recordsTotal = count($productIds);

                // Fetch all liked listings with category names and the first image for each listing
                $inClause = implode(',', array_fill(0, count($productIds), '?'));
                $query = "
            SELECT l.listing_id, l.title, l.description, l.price, l.location, l.date_posted, l.status, l.`condition`, l.brand, l.specifications, c.category_name,
                   li.image_path AS image
            FROM listings l
            LEFT JOIN categories c ON l.category_id = c.category_id
            LEFT JOIN (
                SELECT listing_id, image_path
                FROM listings_images
                WHERE image_id IN (
                    SELECT MIN(image_id)
                    FROM listings_images
                    GROUP BY listing_id
                )
            ) li ON l.listing_id = li.listing_id
            WHERE l.listing_id IN ($inClause)";
                $stmt = executeQuery($query, str_repeat("i", count($productIds)), $productIds);
                $listings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

                // Prepare the data array
                $data = [];
                foreach ($listings as $listing) {
                    $data[] = [
                        'listing_id' => $listing['listing_id'],
                        'title' => $listing['title'],
                        'category_name' => $listing['category_name'],
                        'price' => $listing['price'],
                        'location' => $listing['location'],
                        'date_posted' => $listing['date_posted'],
                        'status' => $listing['status'],
                        'image' => $listing['image']
                    ];
                }

                // Safely retrieve the draw parameter, defaulting to 0 if not set
                $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 0;

                echo json_encode([
                    'draw' => $draw, // For DataTables
                    'recordsTotal' => $recordsTotal,
                    'recordsFiltered' => $recordsTotal, // Assuming no filtering is done
                    'data' => $data
                ]);
            } else {
                // Safely retrieve the draw parameter, defaulting to 0 if not set
                $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 0;

                echo json_encode([
                    'draw' => $draw, // For DataTables
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'status' => 'error',
                    'message' => 'No liked products found'
                ]);
            }
        } else {
            // Safely retrieve the draw parameter, defaulting to 0 if not set
            $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 0;

            echo json_encode([
                'draw' => $draw, // For DataTables
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'status' => 'error',
                'message' => 'User not found'
            ]);
        }

        exit(); // Ensure no further output
    }
    public function processUserSavedAds($username)
    {
        $conn = getDatabaseConnection();

        // Sanitize input
        $username = htmlspecialchars(strip_tags(trim($username)));

        // Fetch user ID from users table
        $query = "SELECT user_id FROM users WHERE username = ?";
        $stmt = executeQuery($query, "s", [$username]);
        $user = $stmt->get_result()->fetch_assoc();

        if ($user) {
            $userId = $user['user_id'];

            // Get saved product IDs for the user
            $query = "SELECT product_id FROM saved_products WHERE user_id = ?";
            $stmt = executeQuery($query, "i", [$userId]);
            $savedProducts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            if ($savedProducts) {
                $productIds = array_column($savedProducts, 'product_id');

                // Count total number of saved products
                $recordsTotal = count($productIds);

                // Fetch all saved listings with category names and the first image for each listing
                $inClause = implode(',', array_fill(0, count($productIds), '?'));
                $query = "
            SELECT l.listing_id, l.title, l.description, l.price, l.location, l.date_posted, l.status, l.`condition`, l.brand, l.specifications, c.category_name,
                   li.image_path AS image
            FROM listings l
            LEFT JOIN categories c ON l.category_id = c.category_id
            LEFT JOIN (
                SELECT listing_id, image_path
                FROM listings_images
                WHERE image_id IN (
                    SELECT MIN(image_id)
                    FROM listings_images
                    GROUP BY listing_id
                )
            ) li ON l.listing_id = li.listing_id
            WHERE l.listing_id IN ($inClause)";
                $stmt = executeQuery($query, str_repeat("i", count($productIds)), $productIds);
                $listings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

                // Prepare the data array
                $data = [];
                foreach ($listings as $listing) {
                    $data[] = [
                        'listing_id' => $listing['listing_id'],
                        'title' => $listing['title'],
                        'category_name' => $listing['category_name'],
                        'price' => $listing['price'],
                        'location' => $listing['location'],
                        'date_posted' => $listing['date_posted'],
                        'status' => $listing['status'],
                        'image' => $listing['image']
                    ];
                }

                // Safely retrieve the draw parameter, defaulting to 0 if not set
                $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 0;

                echo json_encode([
                    'draw' => $draw, // For DataTables
                    'recordsTotal' => $recordsTotal,
                    'recordsFiltered' => $recordsTotal, // Assuming no filtering is done
                    'data' => $data
                ]);
            } else {
                // Safely retrieve the draw parameter, defaulting to 0 if not set
                $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 0;

                echo json_encode([
                    'draw' => $draw, // For DataTables
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'status' => 'error',
                    'message' => 'No saved products found'
                ]);
            }
        } else {
            // Safely retrieve the draw parameter, defaulting to 0 if not set
            $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 0;

            echo json_encode([
                'draw' => $draw, // For DataTables
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'status' => 'error',
                'message' => 'User not found'
            ]);
        }

        exit(); // Ensure no further output
    }


    // Function to delete a liked product from the liked_products table
    // Function to delete a liked product from the product_likes table
    public function processDeleteLikedProducts($product_id)
    {
        // Start a transaction
        $conn = getDatabaseConnection();
        $conn->begin_transaction();

        try {
            // Delete the liked product from the product_likes table
            $queryLiked = "DELETE FROM product_likes WHERE product_id = ?";
            $stmtLiked = executeQuery($queryLiked, "i", [$product_id]);

            // Check if the deletion was successful
            if ($stmtLiked->affected_rows === 0) {
                throw new Exception("Failed to delete liked product or no liked product found.");
            }

            // Commit the transaction
            $conn->commit();

            echo json_encode(['status' => 'success', 'message' => 'Liked product deleted successfully']);
            exit();
        } catch (Exception $e) {
            // Rollback the transaction
            $conn->rollback();

            echo json_encode(['status' => 'error', 'message' => 'Error deleting liked product: ' . $e->getMessage()]);
            exit();
        }
    }

    // Function to delete a saved product from the saved_products table
    public function processDeleteSavedProducts($product_id)
    {
        // Start a transaction
        $conn = getDatabaseConnection();
        $conn->begin_transaction();

        try {
            // Delete the saved product from the saved_products table
            $querySaved = "DELETE FROM saved_products WHERE product_id = ?";
            $stmtSaved = executeQuery($querySaved, "i", [$product_id]);

            // Check if the deletion was successful
            if ($stmtSaved->affected_rows === 0) {
                throw new Exception("Failed to delete saved product or no saved product found.");
            }

            // Commit the transaction
            $conn->commit();

            echo json_encode(['status' => 'success', 'message' => 'Saved product deleted successfully']);
            exit();
        } catch (Exception $e) {
            // Rollback the transaction
            $conn->rollback();

            echo json_encode(['status' => 'error', 'message' => 'Error deleting saved product: ' . $e->getMessage()]);
            exit();
        }
    }
}
