<?php

require_once __DIR__ . '/../core/Functions.php';
require_once __DIR__ . '/../models/user-model.php';

require '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

date_default_timezone_set('Asia/Kolkata');
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start the session if it hasn't been started already
}
$productModel = ProductModel::getInstance();

function respondWithCategories()
{
    header('Content-Type: application/json');
    $productModel = ProductModel::getInstance();
    $categories = $productModel->getAllCategories();
    echo json_encode(['status' => 'success', 'categories' => $categories]);
}

function respondWithLatestProducts()
{
    header('Content-Type: application/json');
    $productModel = ProductModel::getInstance();
    try {
        $latestProducts = $productModel->getLatestProductsFromRandomCategories();
        echo json_encode(['status' => 'success', 'products' => $latestProducts]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to fetch products: ' . $e->getMessage()]);
    }
}

function respondWithFeaturedProducts()
{
    header('Content-Type: application/json');
    $productModel = ProductModel::getInstance();
    try {
        $featuredProducts = $productModel->getRandomFeaturedProducts();
        echo json_encode(['status' => 'success', 'products' => $featuredProducts]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to fetch products: ' . $e->getMessage()]);
    }
}

function respondWithProductDetails($productId)
{
    header('Content-Type: application/json');
    $productModel = ProductModel::getInstance();
    $product = $productModel->getProductDetails($productId);

    if ($product) {
        // Fetch random products from the same seller
        $randomProducts = $productModel->getRandomProductsFromSeller($product['user_id'], $productId);
        echo json_encode([
            'status' => 'success',
            'product' => $product,
            'randomProducts' => $randomProducts
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Product not found']);
    }
}

function respondWithCategoriesWithCount()
{
    header('Content-Type: application/json');
    $productModel = ProductModel::getInstance();
    try {
        $categories = $productModel->getCategoriesWithCounts();
        echo json_encode(['status' => 'success', 'categories' => $categories]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to fetch categories: ' . $e->getMessage()]);
    }
}

function respondWithAllProducts($page, $limit)
{
    $productModel = ProductModel::getInstance();
    $result = $productModel->getAllProducts($page, $limit);

    echo json_encode([
        'status' => 'success',
        'currentPage' => $result['currentPage'],
        'totalPages' => $result['totalPages'],
        'totalCount' => $result['totalCount'],
        'products' => $result['products']
    ]);
}

function respondWithProductsByCategory($categoryId, $page, $limit)
{
    $productModel = ProductModel::getInstance();
    $result = $productModel->getProductsByCategory($categoryId, $page, $limit);

    echo json_encode([
        'status' => 'success',
        'currentPage' => $result['currentPage'],
        'totalPages' => $result['totalPages'],
        'totalCount' => $result['totalCount'],
        'products' => $result['products']
    ]);
}

function respondWithSearchResults($searchQuery, $page, $limit)
{
    $productModel = ProductModel::getInstance();
    try {
        $result = $productModel->searchProducts($searchQuery, $page, $limit);
        echo json_encode([
            'status' => 'success',
            'currentPage' => $result['currentPage'],
            'totalPages' => $result['totalPages'],
            'totalCount' => $result['totalCount'],
            'products' => $result['products']
        ]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to fetch products: ' . $e->getMessage()]);
    }
}

function handleRegistration($registrationData, $profilePicture)
{
    header('Content-Type: application/json');
    $productModel = ProductModel::getInstance();

    try {
        // Get response from registerUser
        $response = $productModel->registerUser($registrationData, $profilePicture);
        echo json_encode($response); // Send the response back as JSON
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Registration error: ' . $e->getMessage()]);
    }
}

function handleLogin($username, $password)
{
    header('Content-Type: application/json');
    $productModel = ProductModel::getInstance();

    try {
        // Get response from processLogin
        $response = $productModel->processLogin($username, $password);
        echo json_encode($response);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Login error: ' . $e->getMessage()]);
    }
}

function handleSendOtp($username)
{
    header('Content-Type: application/json');
    $productModel = ProductModel::getInstance();
    $conn = getDatabaseConnection();

    // Fetch the user's email from the database
    $query = "SELECT email FROM users WHERE username = ?";
    $stmt = executeQuery($query, "s", [$username]);
    $user = $stmt->get_result()->fetch_assoc();

    if (!$user) {
        echo json_encode(['status' => 'error', 'message' => 'User not found']);
        return;
    }

    $email = $user['email'];

    // Generate OTP and its expiration time
    $otp = rand(100000, 999999);
    $expires = (new DateTime())->modify('+2 minutes')->format('Y-m-d H:i:s');

    // Insert OTP into the permanent table
    $insertQuery = "INSERT INTO otp_verification (email, otp, otp_expires) VALUES (?, ?, ?)";
    $stmt = executeQuery($insertQuery, "sss", [$email, $otp, $expires]);

    if ($stmt) {
        // Send OTP via email
        if ($productModel->sendOtpEmail($email, $otp)) {
            echo json_encode(['status' => 'success', 'message' => 'OTP sent to your email']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to send OTP']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to store OTP']);
    }
}

function handleOtpVerification($username, $otp)
{
    header('Content-Type: application/json');
    $productModel = ProductModel::getInstance();

    try {
        $response = $productModel->processOtpVerification($username, $otp);
        echo json_encode($response);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Verification Error : ' . $e->getMessage()]);
    }
}

function handleChangePassword($newPassword, $confirmPassword)
{
    header('Content-Type: application/json');
    $productModel = ProductModel::getInstance();

    try {
        $response = $productModel->processChangePassword($newPassword, $confirmPassword);
        echo json_encode($response);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error in Password change : ' . $e->getMessage()]);
    }
}

function handleSendForgotOtp($email)
{
    header('Content-Type: application/json');
    $productModel = ProductModel::getInstance();
    $conn = getDatabaseConnection();

    try {
        // Check if email exists in users table
        $checkEmailQuery = "SELECT * FROM users WHERE email = ?";
        $result = executeQuery($checkEmailQuery, "s", [$email]);

        if ($result) {
            $user = $result->get_result()->fetch_assoc();

            if ($user) {
                $_SESSION['forgot_password_email'] = $email;
                // Generate OTP and its expiration time
                $otp = rand(100000, 999999);
                $expires = (new DateTime())->modify('+2 minutes')->format('Y-m-d H:i:s');

                // Insert OTP into the permanent table
                $insertQuery = "INSERT INTO otp_verification (email, otp, otp_expires) VALUES (?, ?, ?)";
                $stmt = executeQuery($insertQuery, "sss", [$email, $otp, $expires]);

                if ($stmt) {
                    // Send OTP via email
                    if ($productModel->sendOtpEmail($email, $otp)) {
                        echo json_encode(['status' => 'success', 'message' => 'OTP sent to your email']);
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Failed to send OTP']);
                    }
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to store OTP']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'User not found']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database query failed']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

function handleVerifyForgotOtp($otp)
{
    header('Content-Type: application/json');
    $productModel = ProductModel::getInstance();

    try {
        $response = $productModel->verifyOtp($otp);
        echo json_encode($response);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Verification Error: ' . $e->getMessage()]);
    }
}

function handleForgotChangePass($newPassword, $confirmPassword)
{
    header('Content-Type: application/json');
    $productModel = ProductModel::getInstance();

    try {
        $response = $productModel->changeForgotPassword($newPassword, $confirmPassword);
        echo json_encode($response);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error in Password change: ' . $e->getMessage()]);
    }
}

// New functions
function GetProfileInfo($user)
{
    header('Content-Type: application/json');
    $productModel = ProductModel::getInstance();

    try {
        $profileInfoJson = $productModel->handleGetProfileInfo($user);

        // Decode and re-encode the JSON to ensure it's properly formatted
        $profileInfo = json_decode($profileInfoJson, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Failed to decode profile info JSON.');
        }

        echo json_encode([
            'status' => 'success',
            'profileInfo' => $profileInfo
        ]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to fetch profile info: ' . $e->getMessage()]);
    }

    exit;
}


function handleFormSubmission($formData, $files)
{
    header('Content-Type: application/json');
    $productModel = ProductModel::getInstance();

    try {
        $response = $productModel->handlePostAd($formData, $files);
        echo json_encode($response);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Form submission error: ' . $e->getMessage()]);
    }
}


function getProfileInfoForChange($username)
{
    header('Content-Type: application/json');
    $productModel = ProductModel::getInstance();

    try {
        $response = $productModel->handleProfileInfo($username);
        echo json_encode($response);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error fetching info: ' . $e->getMessage()]);
    }
}

function
updateProfile($username, $location, $phone_number, $profile_picture)
{

    header('Content-Type: application/json');
    $productModel = ProductModel::getInstance();

    try {
        $response = $productModel->handleProfileUpdation($username, $location, $phone_number, $profile_picture);
        echo json_encode($response);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error updating info: ' . $e->getMessage()]);
    }
}

function
getUserAds($username)
{

    header('Content-Type: application/json');
    $productModel = ProductModel::getInstance();

    try {
        $response = $productModel->processUserAds($username);
        echo json_encode($response);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error fetching Ads: ' . $e->getMessage()]);
    }
}

function
getProductDetailsForEdit($listing_id)
{

    header('Content-Type: application/json');
    $productModel = ProductModel::getInstance();

    try {
        $response = $productModel->processProductDetailsForEdit($listing_id);
        echo json_encode($response);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error fetching details: ' . $e->getMessage()]);
    }
}

function updateProduct($listing_id, $title, $price, $description, $specifications, $condition, $brand, $status, $location, $category_id, $files)
{
    header('Content-Type: application/json');
    $productModel = ProductModel::getInstance();

    try {
        $response = $productModel->processUpdateProduct($listing_id, $title, $price, $description, $specifications, $condition, $brand, $status, $location, $category_id, $files);
        echo json_encode($response);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error updating details: ' . $e->getMessage()]);
    }
}

function
deleteProducts($listing_id)
{
    header('Content-Type: application/json');
    $productModel = ProductModel::getInstance();

    try {
        $response = $productModel->processDeleteProducts($listing_id);
        echo json_encode($response);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error deleting product: ' . $e->getMessage()]);
    }
}

function handleSendRegisterOtp($email)
{
    // Start the session
    if (session_status() == PHP_SESSION_NONE) {
        session_start(); // Start the session if it hasn't been started already
    }

    // Generate a 6-digit OTP
    $otp = rand(100000, 999999);

    // Store OTP, email, and timestamp in session
    $_SESSION['otp'] = $otp;
    $_SESSION['otp_email'] = $email;
    $_SESSION['otp_generated_at'] = time();
    $_SESSION['otp_expiry'] = time() + 300; // OTP valid for 5 minutes

    // Create a new PHPMailer instance
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

        // Return success status
        return json_encode(['status' => 'success', 'message' => 'OTP sent to your email']);
    } catch (Exception $e) {
        // Return failure status
        return json_encode(['status' => 'error', 'message' => 'Failed to send OTP: ' . $e->getMessage()]);
    }
}
function handleVerifyRegisterOtp($otp)
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start(); // Start the session if it hasn't been started already
    }

    // Check if OTP exists in session and is within the validity period
    if (isset($_SESSION['otp']) && isset($_SESSION['otp_expiry'])) {
        if (time() <= $_SESSION['otp_expiry']) {
            // Validate the provided OTP
            if ($_SESSION['otp'] == $otp) {
                return json_encode(['status' => 'success', 'message' => 'OTP verified successfully']);
            } else {
                return json_encode(['status' => 'error', 'message' => 'Invalid OTP']);
            }
        } else {
            return json_encode(['status' => 'error', 'message' => 'OTP has expired']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'OTP not found']);
    }
}

function handleUserRegisterWithVerify($data)
{
    $conn = getDatabaseConnection();

    // Sanitize inputs
    $username = htmlspecialchars(strip_tags(trim($data['username'])));
    $email = htmlspecialchars(strip_tags(trim($data['email'])));
    $password = htmlspecialchars(strip_tags(trim($data['password'])));
    $location = htmlspecialchars(strip_tags(trim($data['location'])));
    $phoneNumber = htmlspecialchars(strip_tags(trim($data['phone'])));
    $profilePicture = isset($data['profilePicture']) ? $data['profilePicture'] : null;

    // Check if username already exists
    $query = "SELECT user_id FROM users WHERE username = ?";
    $stmt = executeQuery($query, "s", [$username]);
    if ($stmt->get_result()->num_rows > 0) {
        return ['status' => 'error', 'message' => 'Username already registered'];
    }

    // Check if email already exists
    $query = "SELECT user_id FROM users WHERE email = ?";
    $stmt = executeQuery($query, "s", [$email]);
    if ($stmt->get_result()->num_rows > 0) {
        return ['status' => 'error', 'message' => 'Email already registered'];
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // If profile picture is uploaded and data is available
    $profilePictureName = $profilePicture ? htmlspecialchars(strip_tags(trim($profilePicture))) : null;

    // Prepare to insert user into the database
    $query = "INSERT INTO users (username, email, password_hash, profile_picture, location, phone_number, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())";
    $stmt = executeQuery($query, "ssssss", [$username, $email, $hashedPassword, $profilePictureName, $location, $phoneNumber]);

    if ($stmt) {
        return ['status' => 'success', 'message' => 'Registration successful'];
    } else {
        return ['status' => 'error', 'message' => 'Failed to register user'];
    }
}
function
processSendMessage($productId)
{
    header('Content-Type: application/json');
    $productModel = ProductModel::getInstance();

    try {
        $response = $productModel->handleSendMessage($productId);
        echo json_encode($response);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error sending message: ' . $e->getMessage()]);
    }
}

function processContactForm($name, $email, $subject, $message)
{
    header('Content-Type: application/json');
    $productModel = ProductModel::getInstance();

    try {
        $response = $productModel->handleSendContactForm($name, $email, $subject, $message);
        echo json_encode($response);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error sending form: ' . $e->getMessage()]);
    }
}


function
processLikedProduct($productId)
{
    header('Content-Type: application/json');
    $productModel = ProductModel::getInstance();

    try {
        $response = $productModel->handleLikedProduct($productId);
        echo json_encode($response);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error likedProduct: ' . $e->getMessage()]);
    }
}


function
processSavedProduct($productId)
{
    header('Content-Type: application/json');
    $productModel = ProductModel::getInstance();

    try {
        $response = $productModel->handleSavedProduct($productId);
        echo json_encode($response);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error SavedProduct: ' . $e->getMessage()]);
    }
}


function
getLikedUserAds($username)
{

    header('Content-Type: application/json');
    $productModel = ProductModel::getInstance();

    try {
        $response = $productModel->processUserLikedAds($username);
        echo json_encode($response);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error fetching Ads: ' . $e->getMessage()]);
    }
}

function
getSavedUserAds($username)
{

    header('Content-Type: application/json');
    $productModel = ProductModel::getInstance();

    try {
        $response = $productModel->processUserSavedAds($username);
        echo json_encode($response);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error fetching Ads: ' . $e->getMessage()]);
    }
}


function
deleteLikedProducts($listing_id)
{
    header('Content-Type: application/json');
    $productModel = ProductModel::getInstance();

    try {
        $response = $productModel->processDeleteLikedProducts($listing_id);
        echo json_encode($response);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error deleting product: ' . $e->getMessage()]);
    }
}


function
deleteSavedProducts($listing_id)
{
    header('Content-Type: application/json');
    $productModel = ProductModel::getInstance();

    try {
        $response = $productModel->processDeleteSavedProducts($listing_id);
        echo json_encode($response);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error deleting product: ' . $e->getMessage()]);
    }
}
