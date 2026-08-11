<?php
/**
 * controller.php — Resident Request Handler
 */

$projectRoot = dirname(__DIR__, 3);
require_once $projectRoot . '/core/Response.php';
require_once $projectRoot . '/core/Csrf.php';
require_once $projectRoot . '/core/FileUploader.php';
require_once dirname(__DIR__) . '/class.php';

$db = new global_class();

$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($requestMethod === 'POST') {

    if (!isset($_POST['requestType'])) {
        json_error('Access Denied! No Request Type.', 400);
    }

    // Validate CSRF token for all POST requests
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!validate_csrf($csrfToken)) {
        json_error('Invalid or expired CSRF token.', 403);
    }

    switch ($_POST['requestType']) {

        case 'RequestClearance': {
            $shippingFee = floatval($_POST['shippingFee'] ?? 0);
            $documentPrice = floatval($_POST['documentPrice'] ?? 0);
            $totalPrice = floatval($_POST['totalPrice'] ?? 0);
            
            $purpose = trim($_POST['purpose'] ?? '');
            $address = trim($_POST['addressForm'] ?? '');
            $payment = trim($_POST['payment'] ?? '');
            $r_id = intval($_POST['r_id'] ?? 0);
            
            $validId = $_FILES['validId'] ?? null;
            $proofResidency = $_FILES['proofResidency'] ?? null;
            
            if (empty($purpose) || empty($address) || empty($payment) || $r_id <= 0 || !$validId || !$proofResidency) {
                json_error('Please fill all required fields and upload the required documents.', 422);
            }
            
            try {
                $uploadDir = $projectRoot . '/uploads/clearance/';
                
                $uploadValidId = FileUploader::upload($validId, $uploadDir, 'validId_');
                if (!$uploadValidId['status']) {
                    json_error('Valid ID upload error: ' . $uploadValidId['error'], 422);
                }

                $uploadProof = FileUploader::upload($proofResidency, $uploadDir, 'proofResidency_');
                if (!$uploadProof['status']) {
                    json_error('Proof of residency error: ' . $uploadProof['error'], 422);
                }
                
                $message = $db->RequestClearance(
                    $purpose, $address, $payment, $uploadValidId['filename'],
                    $uploadProof['filename'], $r_id, $documentPrice, $shippingFee, $totalPrice
                );
                
                json_success($message);
            } catch (Exception $e) {
                json_error('Error: ' . $e->getMessage(), 500);
            }
            break;
        }

        case 'RequestBarangayID': {
            $shippingFee = floatval($_POST['shippingFee'] ?? 0);
            $documentPrice = floatval($_POST['documentPrice'] ?? 0);
            $totalPrice = floatval($_POST['totalPrice'] ?? 0);
            
            $purpose = trim($_POST['purpose_BrgyId'] ?? '');
            $address = trim($_POST['addressForm_BrgyId'] ?? '');
            $payment = trim($_POST['payment_BrgyId'] ?? '');
            $r_id = intval($_POST['r_id'] ?? 0);
            
            $pic_BrgyId = $_FILES['1x1pic_BrgyId'] ?? null;
            $signature_BrgyId = $_FILES['signature_BrgyId'] ?? null;
            $validId = $_FILES['validId_BrgyId'] ?? null;
            $proofResidency = $_FILES['proofResidency_BrgyId'] ?? null;
            
            if (empty($purpose) || empty($address) || empty($payment) || $r_id <= 0 || !$pic_BrgyId || !$signature_BrgyId || !$validId || !$proofResidency) {
                json_error('Please upload all required files and fill out the details.', 422);
            }
            
            try {
                $uploadDir = $projectRoot . '/uploads/id/';
                
                $uploadPic = FileUploader::upload($pic_BrgyId, $uploadDir, 'pic_');
                if (!$uploadPic['status']) {
                    json_error('1x1 Photo upload error: ' . $uploadPic['error'], 422);
                }

                $uploadSig = FileUploader::upload($signature_BrgyId, $uploadDir, 'signature_');
                if (!$uploadSig['status']) {
                    json_error('Signature upload error: ' . $uploadSig['error'], 422);
                }

                $uploadValidId = FileUploader::upload($validId, $uploadDir, 'validId_');
                if (!$uploadValidId['status']) {
                    json_error('Valid ID upload error: ' . $uploadValidId['error'], 422);
                }

                $uploadProof = FileUploader::upload($proofResidency, $uploadDir, 'proofResidency_');
                if (!$uploadProof['status']) {
                    json_error('Proof of residency error: ' . $uploadProof['error'], 422);
                }
                
                $message = $db->RequestBarangayID(
                    $purpose, $address, $payment, $uploadValidId['filename'],
                    $uploadProof['filename'], $uploadPic['filename'], $uploadSig['filename'],
                    $r_id, $documentPrice, $shippingFee, $totalPrice
                );
                
                json_success($message);
            } catch (Exception $e) {
                json_error('Error: ' . $e->getMessage(), 500);
            }
            break;
        }

        case 'RequestResidency': {
            $shippingFee = floatval($_POST['shippingFee'] ?? 0);
            $documentPrice = floatval($_POST['documentPrice'] ?? 0);
            $totalPrice = floatval($_POST['totalPrice'] ?? 0);
            
            $purpose = trim($_POST['purpose'] ?? '');
            $address = trim($_POST['addressForm'] ?? '');
            $payment = trim($_POST['payment'] ?? '');
            $r_id = intval($_POST['r_id'] ?? 0);
            
            $validId = $_FILES['validId'] ?? null;
            
            if (empty($purpose) || empty($address) || empty($payment) || $r_id <= 0 || !$validId) {
                json_error('Please upload a valid ID and complete the form.', 422);
            }
            
            try {
                $uploadDir = $projectRoot . '/uploads/residency/';
                
                $uploadValidId = FileUploader::upload($validId, $uploadDir, 'validId_');
                if (!$uploadValidId['status']) {
                    json_error('Valid ID upload error: ' . $uploadValidId['error'], 422);
                }
                
                $message = $db->RequestResidency(
                    $purpose, $address, $payment, $uploadValidId['filename'],
                    $r_id, $documentPrice, $shippingFee, $totalPrice
                );
                
                json_success($message);
            } catch (Exception $e) {
                json_error('Error: ' . $e->getMessage(), 500);
            }
            break;
        }

        case 'RequestIndigency': {
            $shippingFee = floatval($_POST['shippingFee'] ?? 0);
            $documentPrice = floatval($_POST['documentPrice'] ?? 0);
            $totalPrice = floatval($_POST['totalPrice'] ?? 0);
            
            $purpose = trim($_POST['purpose'] ?? '');
            $address = trim($_POST['addressForm'] ?? '');
            $payment = trim($_POST['payment'] ?? '');
            $r_id = intval($_POST['r_id'] ?? 0);
            
            $validId = $_FILES['validId'] ?? null;
            
            if (empty($purpose) || empty($address) || empty($payment) || $r_id <= 0 || !$validId) {
                json_error('Please upload a valid ID and complete the form.', 422);
            }
            
            try {
                $uploadDir = $projectRoot . '/uploads/residency/';
                
                $uploadValidId = FileUploader::upload($validId, $uploadDir, 'validId_');
                if (!$uploadValidId['status']) {
                    json_error('Valid ID upload error: ' . $uploadValidId['error'], 422);
                }
                
                $message = $db->RequestIndigency(
                    $purpose, $address, $payment, $uploadValidId['filename'],
                    $r_id, $documentPrice, $shippingFee, $totalPrice
                );
                
                json_success($message);
            } catch (Exception $e) {
                json_error('Error: ' . $e->getMessage(), 500);
            }
            break;
        }

        case 'UpdateAccountSetting': {
            $r_id = intval($_POST['r_id'] ?? 0);
            $fname = trim($_POST['r_fname'] ?? '');
            $mname = trim($_POST['r_mname'] ?? '');
            $lname = trim($_POST['r_lname'] ?? '');
            $r_suffix = trim($_POST['r_suffix'] ?? '');
            $r_gender = trim($_POST['r_gender'] ?? '');
            $r_civil_status = trim($_POST['r_civil_status'] ?? '');
            $r_citizenship = trim($_POST['r_citizenship'] ?? 'Filipino');
            $r_bday = trim($_POST['r_bday'] ?? '');
            $r_contact_number = trim($_POST['r_contact_number'] ?? '');
            $regionId = trim($_POST['r_region'] ?? '');
            $provinceId = trim($_POST['r_province'] ?? '');
            $cityId = trim($_POST['r_municipality'] ?? $_POST['r_city'] ?? '');
            $barangayId = trim($_POST['r_barangay'] ?? '');
            $r_street = trim($_POST['r_street'] ?? '');
            $r_email = trim($_POST['r_email'] ?? '');
            $newPassword = trim($_POST['newPassword'] ?? '');
            $confirmNewPassword = trim($_POST['confirmNewPassword'] ?? '');

            if ($r_id <= 0 || empty($fname) || empty($lname) || empty($r_email)) {
                json_error('First name, last name, and email are required.', 422);
            }

            if (!filter_var($r_email, FILTER_VALIDATE_EMAIL)) {
                json_error('Please enter a valid email address.', 422);
            }

            if (!empty($newPassword)) {
                if ($newPassword !== $confirmNewPassword) {
                    json_error('New passwords do not match.', 422);
                }
                if (strlen($newPassword) < 6) {
                    json_error('New password must be at least 6 characters long.', 422);
                }
            }

            if (!empty($r_contact_number) && !preg_match('/^(09|\+639)\d{9}$/', $r_contact_number)) {
                json_error('Please provide a valid 11-digit Philippine mobile number.', 422);
            }

            // Fetch location names from JSON files if codes were provided
            $dataPath = $projectRoot . '/assets/data/';
            $regionData = json_decode(@file_get_contents($dataPath . 'region.json') ?: '{"data":[]}', true);
            $provinceData = json_decode(@file_get_contents($dataPath . 'province.json') ?: '{"data":[]}', true);
            $cityData = json_decode(@file_get_contents($dataPath . 'city.json') ?: '{"data":[]}', true);
            $barangayData = json_decode(@file_get_contents($dataPath . 'barangay.json') ?: '{"data":[]}', true);

            $region = $regionId;
            $province = $provinceId;
            $city = $cityId;
            $barangay = $barangayId;

            if (!empty($regionData['data'])) {
                foreach ($regionData['data'] as $item) {
                    if (($item['region_code'] ?? '') === $regionId) {
                        $region = $item['region_name'];
                        break;
                    }
                }
            }
            if (!empty($provinceData['data'])) {
                foreach ($provinceData['data'] as $item) {
                    if (($item['province_code'] ?? '') === $provinceId) {
                        $province = $item['province_name'];
                        break;
                    }
                }
            }
            if (!empty($cityData['data'])) {
                foreach ($cityData['data'] as $item) {
                    if (($item['city_code'] ?? '') === $cityId) {
                        $city = $item['city_name'];
                        break;
                    }
                }
            }
            if (!empty($barangayData['data'])) {
                foreach ($barangayData['data'] as $item) {
                    if (($item['brgy_code'] ?? '') === $barangayId) {
                        $barangay = $item['brgy_name'];
                        break;
                    }
                }
            }

            $uploadDirForResident = $projectRoot . '/uploads/resident/';

            $currentResident = $db->check_account($r_id);
            if (empty($currentResident)) {
                json_error('Account not found.', 404);
            }

            $currentProfileImg = $currentResident[0]['r_profile'] ?? '';
            $profileImgPathDb = '';

            if (isset($_FILES['r_profile']) && $_FILES['r_profile']['error'] === UPLOAD_ERR_OK) {
                $uploadRes = FileUploader::upload($_FILES['r_profile'], $uploadDirForResident, 'resident_');
                if (!$uploadRes['status']) {
                    json_error('Profile image: ' . $uploadRes['error'], 422);
                }
                if ($currentProfileImg && file_exists($uploadDirForResident . $currentProfileImg)) {
                    @unlink($uploadDirForResident . $currentProfileImg);
                }
                $profileImgPathDb = $uploadRes['filename'];
            }

            try {
                $message = $db->updateResident(
                    $r_id, $fname, $mname, $lname, $r_suffix, $r_gender, $r_civil_status,
                    $r_citizenship, $r_bday, $r_contact_number, $region, $province, $city, $barangay,
                    $r_street, $r_email, $newPassword, $profileImgPathDb
                );
                if (str_contains(strtolower($message), 'error') || str_contains(strtolower($message), 'already in use')) {
                    json_error($message, 422);
                }
                // Update session name if changed
                if (session_status() === PHP_SESSION_ACTIVE) {
                    $_SESSION['r_fname'] = $fname;
                }
                json_success($message);
            } catch (Exception $e) {
                json_error('Error updating resident: ' . $e->getMessage(), 500);
            }
            break;
        }

        case 'CancelRequest': {
            $requestId = intval($_POST['requestId'] ?? 0);
            if ($requestId <= 0) {
                json_error('Invalid request ID.', 422);
            }
            $currentResidentId = intval($_SESSION['r_id'] ?? 0);
            $response = $db->CancelRequest($requestId, $currentResidentId);
            
            if (($response['status'] ?? '') === 'success') {
                json_success($response['message'] ?? 'Request cancelled.');
            } else {
                json_error($response['message'] ?? 'Failed to cancel request.', 422);
            }
            break;
        }

        case 'GetAnnouncementDetails': {
            $announcement_id = intval($_POST['announcement_id'] ?? 0);
            if ($announcement_id <= 0) {
                json_error('Invalid announcement ID.', 422);
            }

            $announcement = $db->getAnnouncementById($announcement_id);
            if (!$announcement) {
                json_error('Announcement not found or no longer available.', 404);
            }
            json_success('Announcement retrieved successfully.', $announcement);
            break;
        }

        default: {
            json_error('Invalid request type', 400);
        }
    }

} elseif ($requestMethod === 'GET') {
    if (isset($_GET['requestType']) && $_GET['requestType'] == 'GetAnnouncementDetails') {
        $announcement_id = intval($_GET['announcement_id'] ?? 0);
        if ($announcement_id <= 0) {
            json_error('Invalid announcement ID.', 422);
        }
        $announcement = $db->getAnnouncementById($announcement_id);
        if (!$announcement) {
            json_error('Announcement not found or no longer available.', 404);
        }
        json_success('Announcement retrieved successfully.', $announcement);
    } else {
        json_error('GET requests are not supported for this action.', 400);
    }
} else {
    json_error('Method not allowed.', 405);
}