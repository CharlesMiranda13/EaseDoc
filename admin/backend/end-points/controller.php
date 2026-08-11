<?php
/**
 * controller.php — Admin Request Handler
 */

$projectRoot = dirname(__DIR__, 3);
require_once $projectRoot . '/core/Auth.php';
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
        json_error('Invalid or expired CSRF token', 403);
    }

    switch ($_POST['requestType']) {

        case 'UpdateOrderStatus': {
            $orderId = intval($_POST['orderId'] ?? 0);
            $orderStatus = trim($_POST['orderStatus'] ?? '');
            
            if ($orderId <= 0 || empty($orderStatus)) {
                json_error('Invalid order parameters.', 422);
            }

            if ($db->updateOrderStatus($orderId, $orderStatus)) {
                json_success('Order status updated successfully.');
            } else {
                json_error('Failed to update order in the database.', 500);
            }
            break;
        }

        case 'UpdateAdminInfo': {
            $user_fname = trim($_POST['user_fname'] ?? '');
            $user_mname = trim($_POST['user_mname'] ?? '');
            $user_lname = trim($_POST['user_lname'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $user_id = intval($_POST['user_id'] ?? 0);
            
            if (empty($user_fname) || empty($user_lname) || empty($email) || $user_id <= 0) {
                json_error('All required fields must be provided.', 422);
            }

            $result = $db->UpdateAdminInfo($user_fname, $user_mname, $user_lname, $email, $user_id);
            
            if (str_contains($result, 'successfully')) {
                json_success($result);
            } else {
                json_error($result, 500);
            }
            break;
        }

        case 'UpdatePassword': {
            $current_password = $_POST['current_password'] ?? '';
            $new_password = $_POST['new_password'] ?? '';
            $user_id = intval($_POST['user_id'] ?? 0);
            
            if (empty($current_password) || empty($new_password) || $user_id <= 0) {
                json_error('Please fill in both current and new password.', 422);
            }

            $result = $db->UpdateAdminPassword($current_password, $new_password, $user_id);
            
            if ($result === "Password updated successfully.") {
                json_success($result);
            } else {
                json_error($result, 400);
            }
            break;
        }

        case 'addResident': {
            $fname = trim($_POST['fname'] ?? '');
            $mname = trim($_POST['mname'] ?? '');
            $lname = trim($_POST['lname'] ?? '');
            $r_suffix = trim($_POST['r_suffix'] ?? '');
            $Gender = trim($_POST['Gender'] ?? '');
            $r_civil_status = trim($_POST['r_civil_status'] ?? '');
            $r_citizenship = trim($_POST['r_citizenship'] ?? 'Filipino');
            $r_bday = trim($_POST['r_bday'] ?? '');
            $r_contact_number = trim($_POST['r_contact_number'] ?? '');
            $regionId = trim($_POST['r_region'] ?? '');
            $provinceId = trim($_POST['r_province'] ?? '');
            $cityId = trim($_POST['r_city'] ?? '');
            $barangayId = trim($_POST['r_barangay'] ?? '');
            $r_street = trim($_POST['r_street'] ?? '');
            $r_email = trim($_POST['r_email'] ?? '');
            $r_password = $_POST['r_password'] ?? '';
            $c_Password = $_POST['c_Password'] ?? '';

            if (empty($fname) || empty($lname) || empty($r_email) || empty($r_password)) {
                json_error('First name, last name, email, and password are required.', 422);
            }

            if (!filter_var($r_email, FILTER_VALIDATE_EMAIL)) {
                json_error('Please enter a valid email address.', 422);
            }

            if (!empty($c_Password) && $r_password !== $c_Password) {
                json_error('Passwords do not match.', 422);
            }

            if (strlen($r_password) < 6) {
                json_error('Password must be at least 6 characters long.', 422);
            }

            if (!empty($r_contact_number) && !preg_match('/^(09|\+639)\d{9}$/', $r_contact_number)) {
                json_error('Please provide a valid 11-digit Philippine contact number (e.g., 09123456789).', 422);
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

            // Upload handling using secure FileUploader
            $uploadDirResident = $projectRoot . '/uploads/resident/';
            $uploadDirId = $projectRoot . '/uploads/resident_id/';

            $profileImgPathDb = '';
            $validIdPathDb = '';

            if (isset($_FILES['profileImg']) && $_FILES['profileImg']['error'] === UPLOAD_ERR_OK) {
                $uploadRes = FileUploader::upload($_FILES['profileImg'], $uploadDirResident, 'resident_');
                if (!$uploadRes['status']) {
                    json_error('Profile image: ' . $uploadRes['error'], 422);
                }
                $profileImgPathDb = $uploadRes['filename'];
            }

            if (isset($_FILES['validId']) && $_FILES['validId']['error'] === UPLOAD_ERR_OK) {
                $uploadRes = FileUploader::upload($_FILES['validId'], $uploadDirId, 'valid_id_');
                if (!$uploadRes['status']) {
                    json_error('Valid ID: ' . $uploadRes['error'], 422);
                }
                $validIdPathDb = $uploadRes['filename'];
            }

            try {
                $message = $db->addResident(
                    $fname, $mname, $lname, $r_suffix, $Gender, $r_civil_status, $r_citizenship,
                    $r_bday, $r_contact_number, $region, $province, $city, $barangay,
                    $r_street, $r_email, $r_password, $profileImgPathDb, $validIdPathDb
                );
                if (str_contains(strtolower($message), 'error') || str_contains(strtolower($message), 'already in use')) {
                    json_error($message, 422);
                }
                json_success($message);
            } catch (Exception $e) {
                json_error('Error adding resident: ' . $e->getMessage(), 500);
            }
            break;
        }

        case 'EditResident': {
            $r_id = intval($_POST['r_id'] ?? 0);
            $fname = trim($_POST['fname'] ?? '');
            $mname = trim($_POST['mname'] ?? '');
            $lname = trim($_POST['lname'] ?? '');
            $r_suffix = trim($_POST['r_suffix'] ?? '');
            $r_gender = trim($_POST['Gender'] ?? '');
            $r_civil_status = trim($_POST['r_civil_status'] ?? '');
            $r_citizenship = trim($_POST['r_citizenship'] ?? 'Filipino');
            $r_bday = trim($_POST['r_bday'] ?? '');
            $r_contact_number = trim($_POST['r_contact_number'] ?? '');
            $regionId = trim($_POST['r_region'] ?? '');
            $provinceId = trim($_POST['r_province'] ?? '');
            $cityId = trim($_POST['r_city'] ?? '');
            $barangayId = trim($_POST['r_barangay'] ?? '');
            $r_street = trim($_POST['r_street'] ?? '');
            $r_email = trim($_POST['r_email'] ?? '');
            $newPassword = trim($_POST['r_password'] ?? $_POST['newPassword'] ?? '');
            $c_Password = trim($_POST['c_Password'] ?? $_POST['confirmNewPassword'] ?? '');

            if ($r_id <= 0 || empty($fname) || empty($lname) || empty($r_email)) {
                json_error('First name, last name, and email are required.', 422);
            }

            if (!filter_var($r_email, FILTER_VALIDATE_EMAIL)) {
                json_error('Please enter a valid email address.', 422);
            }

            if (!empty($newPassword)) {
                if (!empty($c_Password) && $newPassword !== $c_Password) {
                    json_error('New passwords do not match.', 422);
                }
                if (strlen($newPassword) < 6) {
                    json_error('New password must be at least 6 characters long.', 422);
                }
            }

            if (!empty($r_contact_number) && !preg_match('/^(09|\+639)\d{9}$/', $r_contact_number)) {
                json_error('Please provide a valid 11-digit Philippine contact number (e.g., 09123456789).', 422);
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

            $uploadDirResident = $projectRoot . '/uploads/resident/';
            $uploadDirId = $projectRoot . '/uploads/resident_id/';

            $currentResident = $db->check_resident($r_id);
            if (empty($currentResident)) {
                json_error('Resident record not found.', 404);
            }

            $currentProfileImg = $currentResident[0]['r_profile'] ?? '';
            $currentIdImg = $currentResident[0]['r_valid_ids'] ?? '';

            $profileImgPathDb = '';
            $IdImgPathDb = '';

            if (isset($_FILES['profileImg']) && $_FILES['profileImg']['error'] === UPLOAD_ERR_OK) {
                $uploadRes = FileUploader::upload($_FILES['profileImg'], $uploadDirResident, 'resident_');
                if (!$uploadRes['status']) {
                    json_error('Profile image: ' . $uploadRes['error'], 422);
                }
                if ($currentProfileImg && file_exists($uploadDirResident . $currentProfileImg)) {
                    @unlink($uploadDirResident . $currentProfileImg);
                }
                $profileImgPathDb = $uploadRes['filename'];
            }

            if (isset($_FILES['validId']) && $_FILES['validId']['error'] === UPLOAD_ERR_OK) {
                $uploadRes = FileUploader::upload($_FILES['validId'], $uploadDirId, 'valid_id_');
                if (!$uploadRes['status']) {
                    json_error('Valid ID: ' . $uploadRes['error'], 422);
                }
                if ($currentIdImg && file_exists($uploadDirId . $currentIdImg)) {
                    @unlink($uploadDirId . $currentIdImg);
                }
                $IdImgPathDb = $uploadRes['filename'];
            }

            try {
                $message = $db->updateResident(
                    $r_id, $fname, $mname, $lname, $r_suffix, $r_gender, $r_civil_status,
                    $r_citizenship, $r_bday, $r_contact_number, $region, $province, $city, $barangay,
                    $r_street, $r_email, $newPassword, $profileImgPathDb, $IdImgPathDb
                );
                if (str_contains(strtolower($message), 'error') || str_contains(strtolower($message), 'already in use')) {
                    json_error($message, 422);
                }
                json_success($message);
            } catch (Exception $e) {
                json_error('Error updating resident: ' . $e->getMessage(), 500);
            }
            break;
        }

        case 'DeleteResident': {
            $residentId = intval($_POST['residentId'] ?? 0);
            if ($residentId <= 0) {
                json_error('Invalid resident ID.', 422);
            }
            $response = $db->DeleteResident($residentId);
            if (str_contains(strtolower($response), 'error')) {
                json_error($response, 500);
            }
            json_success($response);
            break;
        }

        case 'CreateAnnouncement': {
            if (!is_admin()) {
                json_error('Unauthorized access.', 403);
            }

            $title = trim($_POST['title'] ?? '');
            $content = trim($_POST['content'] ?? '');
            $category = trim($_POST['category'] ?? 'General');
            $status = trim($_POST['status'] ?? 'Published');
            $is_pinned = !empty($_POST['is_pinned']) ? 1 : 0;
            $user_id = get_admin_id();

            if (empty($title)) {
                json_error('Announcement title is required.', 422);
            }
            if (empty($content)) {
                json_error('Announcement content cannot be empty.', 422);
            }

            $imageFilename = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = $projectRoot . '/uploads/announcements/';
                $uploadRes = FileUploader::upload($_FILES['image'], $uploadDir, 'announcement_');
                if (!$uploadRes['status']) {
                    json_error('Image upload failed: ' . $uploadRes['error'], 422);
                }
                $imageFilename = $uploadRes['filename'];
            }

            try {
                $result = $db->createAnnouncement($user_id, $title, $content, $category, $status, $is_pinned, $imageFilename);
                if (str_contains(strtolower($result), 'error')) {
                    if ($imageFilename && file_exists($projectRoot . '/uploads/announcements/' . $imageFilename)) {
                        @unlink($projectRoot . '/uploads/announcements/' . $imageFilename);
                    }
                    json_error($result, 500);
                }
                json_success($result);
            } catch (Exception $e) {
                if ($imageFilename && file_exists($projectRoot . '/uploads/announcements/' . $imageFilename)) {
                    @unlink($projectRoot . '/uploads/announcements/' . $imageFilename);
                }
                json_error('Error creating announcement: ' . $e->getMessage(), 500);
            }
            break;
        }

        case 'UpdateAnnouncement': {
            if (!is_admin()) {
                json_error('Unauthorized access.', 403);
            }

            $announcement_id = intval($_POST['announcement_id'] ?? 0);
            $title = trim($_POST['title'] ?? '');
            $content = trim($_POST['content'] ?? '');
            $category = trim($_POST['category'] ?? 'General');
            $status = trim($_POST['status'] ?? 'Published');
            $is_pinned = !empty($_POST['is_pinned']) ? 1 : 0;
            $remove_image = !empty($_POST['remove_image']) ? true : false;

            if ($announcement_id <= 0) {
                json_error('Invalid announcement ID.', 422);
            }
            if (empty($title)) {
                json_error('Announcement title is required.', 422);
            }
            if (empty($content)) {
                json_error('Announcement content cannot be empty.', 422);
            }

            $current = $db->get_announcement_by_id($announcement_id);
            if (!$current) {
                json_error('Announcement not found.', 404);
            }

            $imageFilename = null;
            $uploadDir = $projectRoot . '/uploads/announcements/';

            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadRes = FileUploader::upload($_FILES['image'], $uploadDir, 'announcement_');
                if (!$uploadRes['status']) {
                    json_error('Image upload failed: ' . $uploadRes['error'], 422);
                }
                $imageFilename = $uploadRes['filename'];

                // Remove old image if one existed
                if (!empty($current['image']) && file_exists($uploadDir . $current['image'])) {
                    @unlink($uploadDir . $current['image']);
                }
            } elseif ($remove_image) {
                if (!empty($current['image']) && file_exists($uploadDir . $current['image'])) {
                    @unlink($uploadDir . $current['image']);
                }
            }

            try {
                $result = $db->updateAnnouncement($announcement_id, $title, $content, $category, $status, $is_pinned, $imageFilename, $remove_image);
                if (str_contains(strtolower($result), 'error')) {
                    json_error($result, 500);
                }
                json_success($result);
            } catch (Exception $e) {
                json_error('Error updating announcement: ' . $e->getMessage(), 500);
            }
            break;
        }

        case 'UpdateAnnouncementStatus': {
            if (!is_admin()) {
                json_error('Unauthorized access.', 403);
            }

            $announcement_id = intval($_POST['announcement_id'] ?? 0);
            $status = trim($_POST['status'] ?? '');

            if ($announcement_id <= 0 || empty($status)) {
                json_error('Invalid parameters.', 422);
            }

            $result = $db->updateAnnouncementStatus($announcement_id, $status);
            if (str_contains(strtolower($result), 'error') || str_contains(strtolower($result), 'invalid')) {
                json_error($result, 422);
            }
            json_success($result);
            break;
        }

        case 'ToggleAnnouncementPin': {
            if (!is_admin()) {
                json_error('Unauthorized access.', 403);
            }

            $announcement_id = intval($_POST['announcement_id'] ?? 0);
            $is_pinned = !empty($_POST['is_pinned']) ? 1 : 0;

            if ($announcement_id <= 0) {
                json_error('Invalid announcement ID.', 422);
            }

            $result = $db->toggleAnnouncementPin($announcement_id, $is_pinned);
            if (str_contains(strtolower($result), 'error')) {
                json_error($result, 500);
            }
            json_success($result);
            break;
        }

        case 'DeleteAnnouncement': {
            if (!is_admin()) {
                json_error('Unauthorized access.', 403);
            }

            $announcement_id = intval($_POST['announcement_id'] ?? 0);
            if ($announcement_id <= 0) {
                json_error('Invalid announcement ID.', 422);
            }

            $result = $db->deleteAnnouncement($announcement_id);
            if (str_contains(strtolower($result), 'error') || str_contains(strtolower($result), 'not found')) {
                json_error($result, 500);
            }
            json_success($result);
            break;
        }

        case 'GetAnnouncementDetails': {
            $announcement_id = intval($_POST['announcement_id'] ?? 0);
            if ($announcement_id <= 0) {
                json_error('Invalid announcement ID.', 422);
            }

            $announcement = $db->get_announcement_by_id($announcement_id);
            if (!$announcement) {
                json_error('Announcement not found.', 404);
            }
            json_success('Announcement retrieved successfully.', $announcement);
            break;
        }

        default: {
            json_error('Invalid request type', 400);
        }
    }

} elseif ($requestMethod === 'GET') {
    
    if (isset($_GET['requestType']) && $_GET['requestType'] == 'GetAllOrders') {
        $orders = $db->GetAllOrders();
        json_success('Orders retrieved successfully.', $orders ?: []);
    } elseif (isset($_GET['requestType']) && $_GET['requestType'] == 'GetAnnouncementDetails') {
        $announcement_id = intval($_GET['announcement_id'] ?? 0);
        if ($announcement_id <= 0) {
            json_error('Invalid announcement ID.', 422);
        }
        $announcement = $db->get_announcement_by_id($announcement_id);
        if (!$announcement) {
            json_error('Announcement not found.', 404);
        }
        json_success('Announcement retrieved successfully.', $announcement);
    } else {
        json_error('Invalid GET request.', 400);
    }
} else {
    json_error('Method not allowed.', 405);
}
