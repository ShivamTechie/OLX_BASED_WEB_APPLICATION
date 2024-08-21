<?php

require_once __DIR__ . '/../../App/core/Functions.php';
require_once __DIR__ . '/../models/admin-model.php';

function
respondWithAdminData()
{
  header('Content-Type: application/json');
  $adminModel = AdminModel::getInstance();
  $adminModel->getAllAdminData();
}

function getProfileInfoForChangeInAdmin($user_id)
{
  header('Content-Type: application/json');
  $adminModel = AdminModel::getInstance();

  try {
    $response = $adminModel->handleProfileInfoForAdmin($user_id);
    echo json_encode($response);
  } catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error fetching info: ' . $e->getMessage()]);
  }
}


function
updateProfileForAdmin($user_id, $username, $location, $phone_number, $profile_picture)
{

  header('Content-Type: application/json');
  $adminModel = AdminModel::getInstance();

  try {
    $response =
      $adminModel->handleProfileUpdationForAdmin($user_id, $username, $location, $phone_number, $profile_picture);
    echo json_encode($response);
  } catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error updating info: ' . $e->getMessage()]);
  }
}


function
handleDeleteUserAccount($user_id)
{

  header('Content-Type: application/json');
  $adminModel = AdminModel::getInstance();

  try {
    $response = $adminModel->processDeleteUserAccount($user_id);
    echo json_encode($response);
  } catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error delete User Account: ' . $e->getMessage()]);
  }
}


function handleAdminLogin($username, $password)
{
  header('Content-Type: application/json');
  $adminModel = AdminModel::getInstance();

  try {
    // Get response from processLogin
    $response = $adminModel->processAdminLogin($username, $password);
    echo json_encode($response);
  } catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Login error: ' . $e->getMessage()]);
  }
}



function GetProfileAdminInfo($user)
{
  header('Content-Type: application/json');
  $adminModel = AdminModel::getInstance();

  try {
    $profileInfoJson = $adminModel->handleGetProfileAdminInfo($user);

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
