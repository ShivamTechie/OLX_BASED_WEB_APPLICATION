<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
error_log("Received parameters: " . print_r($_REQUEST, true));

require_once __DIR__ . '/../controllers/user-controller.php';
require_once '../../Admin/controllers/admin-controller.php';

// Sanitize inputs function
function sanitizeInput($input)
{
    return htmlspecialchars(strip_tags(trim($input)));
}

// Check if action is set
if (isset($_REQUEST['action'])) {
    $action = sanitizeInput($_REQUEST['action']);

    switch ($action) {
        case 'getCategories':
            respondWithCategories();
            break;

        case 'getLatestProductsFromRandomCategories':
            respondWithLatestProducts();
            break;

        case 'getFeaturedProducts':
            respondWithFeaturedProducts();
            break;

        case 'getProductDetails':
            if (isset($_REQUEST['id'])) {
                $productId = intval(sanitizeInput($_REQUEST['id']));
                respondWithProductDetails($productId);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Product ID not specified']);
            }
            break;

        case 'getCategorieswithcount':
            respondWithCategoriesWithCount();
            break;

        case 'getAllProducts':
            $page = isset($_REQUEST['page']) ? intval(sanitizeInput($_REQUEST['page'])) : 1;
            $limit = 10; // Define the limit per page
            respondWithAllProducts($page, $limit);
            break;

        case 'getProductsByCategory':
            if (isset($_REQUEST['categoryId'])) {
                $categoryId = intval(sanitizeInput($_REQUEST['categoryId']));
                $page = isset($_REQUEST['page']) ? intval(sanitizeInput($_REQUEST['page'])) : 1;
                $limit = 10; // Define the limit per page
                respondWithProductsByCategory($categoryId, $page, $limit);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Category ID not specified']);
            }
            break;

        case 'searchProducts':
            if (isset($_REQUEST['query'])) {
                $searchQuery = sanitizeInput($_REQUEST['query']);
                $page = isset($_REQUEST['page']) ? intval(sanitizeInput($_REQUEST['page'])) : 1;
                $limit = 10; // Define the limit per page
                respondWithSearchResults($searchQuery, $page, $limit);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Search query not specified']);
            }
            break;

        case 'register':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $registrationData = $_POST;
                $profilePicture = $_FILES['profilePicture'] ?? null;
                handleRegistration($registrationData, $profilePicture);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
            }
            break;

        case 'login':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $username = sanitizeInput($_POST['username']);
                $password = sanitizeInput($_POST['password']);
                handleLogin($username, $password);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
            }
            break;

        case 'sendOtp':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $username = sanitizeInput($_SESSION['username']); // Fetch username from session
                handleSendOtp($username);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
            }
            break;

        case 'otpVerify':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $otp = sanitizeInput($_POST['otp']);
                $username = sanitizeInput($_SESSION['username']); // Fetch username from session
                handleOtpVerification($username, $otp);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
            }
            break;

        case 'changePass':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $newPassword = sanitizeInput($_POST['newPassword']);
                $confirmPassword = sanitizeInput($_POST['confirmPassword']);
                handleChangePassword($newPassword, $confirmPassword);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
            }
            break;

        case 'sendForgotOtp':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $email = sanitizeInput($_POST['email']);
                handleSendForgotOtp($email);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
            }
            break;

        case 'verifyForgotOtp':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $otp = sanitizeInput($_POST['otp']);
                handleVerifyForgotOtp($otp);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
            }
            break;

        case 'forgotChangePass':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $newPassword = sanitizeInput($_POST['newPassword']);
                $confirmPassword = sanitizeInput($_POST['confirmPassword']);
                handleForgotChangePass($newPassword, $confirmPassword);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
            }
            break;

        case 'getProfileInfo':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                $username = $_SESSION['username'];
                GetProfileInfo($username);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
            }
            break;

        case 'post-ad':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $formData = [
                    'title' => sanitizeInput($_POST['Title']),
                    'category' => sanitizeInput($_POST['category']),
                    'price' => sanitizeInput($_POST['price']),
                    'description' => sanitizeInput($_POST['description']),
                    'specifications' => $_POST['specifications'],
                    'condition' => sanitizeInput($_POST['condition']),
                    'brand' => sanitizeInput($_POST['brand']),
                    'location' => sanitizeInput($_POST['address']),
                    'name' => sanitizeInput($_POST['name']),
                    'phone' => sanitizeInput($_POST['phone']),
                    'address' => sanitizeInput($_POST['address'])
                ];

                $files = $_FILES['files'] ?? null;
                error_log("Form Data: " . print_r($formData, true));
                if ($files) {
                    foreach ($files['name'] as $fileName) {
                        error_log("File: " . $fileName);
                    }
                } else {
                    error_log("No files uploaded.");
                }

                handleFormSubmission($formData, $files);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
            }
            break;

        case 'getProfileInfoForChange':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                $username = $_SESSION['username'];
                getProfileInfoForChange($username);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
                exit();
            }
            break;

        case 'updateProfile':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $username = sanitizeInput($_POST['username']);
                $location = sanitizeInput($_POST['location']);
                $phone_number = sanitizeInput($_POST['phone_number']);
                $profile_picture = null;

                if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
                    $originalFileName = basename($_FILES['profile_picture']['name']);
                    $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);
                    $newFileName = time() . '_' . $originalFileName;
                    $filePath = '../../assets/img/author/' . $newFileName;

                    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $filePath)) {
                        $profile_picture = $newFileName;
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Failed to upload profile picture']);
                        exit();
                    }
                }

                updateProfile($username, $location, $phone_number, $profile_picture);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
            }
            break;

        case 'getUserProducts':
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $username = $_SESSION['username'] ?? null;
            if ($username) {
                $ads = getUserAds($username);
                echo json_encode(['status' => 'success', 'ads' => $ads]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
            }
            break;

        case 'getProductDetaislForEdit':
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $listing_id = sanitizeInput($_GET['listing_id']);

                if ($listing_id) {
                    getProductDetailsForEdit($listing_id);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Product not found']);
                }
            }
            break;

        case 'updateProductDetails':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $listing_id = intval($_POST['listing_id']);
                $title = sanitizeInput($_POST['title']);
                $price = sanitizeInput($_POST['price']);
                $description = sanitizeInput($_POST['description']);
                $specifications = sanitizeInput($_POST['specifications']);
                $condition = sanitizeInput($_POST['condition']);
                $brand = sanitizeInput($_POST['brand']);
                $status = sanitizeInput($_POST['status']);
                $location = sanitizeInput($_POST['location']);
                $category_id = intval($_POST['category']);

                $specifications = str_replace("\n", ",", $specifications);

                $files = $_FILES['images'] ?? null;
                updateProduct($listing_id, $title, $price, $description, $specifications, $condition, $brand, $status, $location, $category_id, $files);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
            }
            break;

        case 'deleteProduct':
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['listing_id'])) {
                $listing_id = intval($_POST['listing_id']);
                deleteProducts($listing_id);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid request method or missing listing ID']);
            }
            break;
        case 'sendRegisterOtp':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $email = sanitizeInput($_POST['email']);
                echo handleSendRegisterOtp($email);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
            }
            break;


        case 'verifyOtp':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $otp = sanitizeInput($_POST['otp']);
                echo handleVerifyRegisterOtp($otp);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
            }
            break;


        case 'registerUser':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Sanitize input data
                $registrationData = [
                    'username' => sanitizeInput($_POST['username']),
                    'email' => sanitizeInput($_POST['email']),
                    'password' => sanitizeInput($_POST['password']),
                    'location' => sanitizeInput($_POST['location']),
                    'phone' => sanitizeInput($_POST['phone']),
                ];

                // Handle profile picture if uploaded
                if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] == UPLOAD_ERR_OK) {
                    $profilePicture = $_FILES['profilePicture'];
                    $profilePictureName = time() . '_' . basename($profilePicture['name']);
                    $targetFilePath = __DIR__ . '/../../assets/img/author/' . $profilePictureName;

                    if (move_uploaded_file($profilePicture['tmp_name'], $targetFilePath)) {
                        $registrationData['profilePicture'] = $profilePictureName;
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Profile picture upload failed']);
                        exit();
                    }
                } else {
                    $registrationData['profilePicture'] = null; // No profile picture uploaded
                }

                // Hash the password separately
                $registrationData['password'] = password_hash($registrationData['password'], PASSWORD_DEFAULT);

                // Pass the sanitized data to handleUserRegisterWithVerify
                echo json_encode(handleUserRegisterWithVerify($registrationData));
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
            }
            break;

        case 'fetchAdminData':
            respondWithAdminData();
            break;

        case 'getProfileInfoForChangeInAdmin':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {


                // Retrieve user_id from POST data
                if (isset($_POST['user_id']) && !empty($_POST['user_id'])) {
                    $user_id = intval($_POST['user_id']); // Ensure it's an integer
                    getProfileInfoForChangeInAdmin($user_id);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'User ID not provided']);
                    exit();
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
                exit();
            }
            break;

        case 'updateProfileInAdmin':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $username = sanitizeInput($_POST['username']);
                $location = sanitizeInput($_POST['location']);
                $phone_number = sanitizeInput($_POST['phone_number']);
                $profile_picture = null;
                $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0; // Get user ID from POST data

                if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
                    $originalFileName = basename($_FILES['profile_picture']['name']);
                    $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);
                    $newFileName = time() . '_' . $originalFileName;
                    $filePath = '../../assets/img/author/' . $newFileName;

                    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $filePath)) {
                        $profile_picture = $newFileName;
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Failed to upload profile picture']);
                        exit();
                    }
                }

                updateProfileForAdmin($user_id, $username, $location, $phone_number, $profile_picture); // Pass user ID to the function
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
            }
            break;



        case 'deleteUserAccount':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {


                // Retrieve user_id from POST data
                if (isset($_POST['user_id']) && !empty($_POST['user_id'])) {
                    $user_id = intval($_POST['user_id']); // Ensure it's an integer
                    handleDeleteUserAccount($user_id);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'User ID not found']);
                    exit();
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
                exit();
            }
            break;
        case 'admin-login':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $username = sanitizeInput($_POST['username']);
                $password = sanitizeInput($_POST['password']);
                handleAdminLogin($username, $password);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
            }
            break;

        case 'getProfileInfoAdmin':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                $full_name = $_SESSION['full_name'];
                GetProfileAdminInfo($full_name);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
            }
            break;

        case 'sendMessageToUser':
            if (isset($_REQUEST['productId'])) {
                $productId = intval(sanitizeInput($_REQUEST['productId']));
                processSendMessage($productId);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Product ID not specified']);
            }
            break;

        case 'submitContactForm':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Collect form data
                $name = sanitizeInput($_POST['name']);
                $email = sanitizeInput($_POST['email']);
                $subject = sanitizeInput($_POST['subject']);
                $message = sanitizeInput($_POST['message']);

                // Pass the data to the processing function
                processContactForm($name, $email, $subject, $message);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
            }
            break;

        case 'likedProduct':
            if (isset($_POST['product_id'])) {
                $productId = intval(sanitizeInput($_POST['product_id']));
                processLikedProduct($productId);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Product ID not specified']);
            }
            break;

        case 'savedProduct':
            if (isset($_POST['product_id'])) {
                $productId = intval(sanitizeInput($_POST['product_id']));
                processSavedProduct($productId);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Product ID not specified']);
            }
            break;
        case 'getLikedProducts':
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $username = $_SESSION['username'] ?? null;
            if ($username) {
                $ads = getLikedUserAds($username);
                echo json_encode(['status' => 'success', 'ads' => $ads]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
            }
            break;

        case 'getSavedProducts':
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $username = $_SESSION['username'] ?? null;
            if ($username) {
                $ads = getSavedUserAds($username);
                echo json_encode(['status' => 'success', 'ads' => $ads]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
            }
            break;

        case 'unlikeProduct':
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['listing_id'])) {
                $listing_id = intval($_POST['listing_id']);
                deleteLikedProducts($listing_id);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid request method or missing listing ID']);
            }
            break;

        case 'unsaveProduct':
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['listing_id'])) {
                $listing_id = intval($_POST['listing_id']);
                deleteSavedProducts($listing_id);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid request method or missing listing ID']);
            }
            break;









        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
            break;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No action specified']);
}
