<?php
require_once __DIR__ . '/db.php';
date_default_timezone_set('Asia/Manila');

class global_class extends db_connect
{
    public function __construct()
    {
        $this->connect();
    }

    public function checkEmailExists($email, $excludeId = 0) {
        if ($excludeId > 0) {
            $stmt = $this->conn->prepare("SELECT `r_id` FROM `resident` WHERE `r_email` = ? AND `r_id` != ? LIMIT 1");
            $stmt->bind_param("si", $email, $excludeId);
        } else {
            $stmt = $this->conn->prepare("SELECT `r_id` FROM `resident` WHERE `r_email` = ? LIMIT 1");
            $stmt->bind_param("s", $email);
        }
        if ($stmt) {
            $stmt->execute();
            $stmt->store_result();
            $exists = $stmt->num_rows > 0;
            $stmt->close();
            return $exists;
        }
        return false;
    }

    public function getResidentStats($r_id) {
        $stats = [
            'total' => 0,
            'pending' => 0,
            'completed' => 0
        ];

        $stmt = $this->conn->prepare("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN cr_status = 'Pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN cr_status IN ('Approved', 'Shipped', 'Delivered') THEN 1 ELSE 0 END) as completed
            FROM `centralize_request` 
            WHERE `cr_r_id` = ?
        ");

        if ($stmt) {
            $stmt->bind_param("i", $r_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result && $row = $result->fetch_assoc()) {
                $stats['total'] = (int)($row['total'] ?? 0);
                $stats['pending'] = (int)($row['pending'] ?? 0);
                $stats['completed'] = (int)($row['completed'] ?? 0);
            }
            $stmt->close();
        }
        return $stats;
    }

    public function getRecentRequests($r_id, $limit = 5) {
        $limit = max(1, min(20, (int)$limit));
        $stmt = $this->conn->prepare("SELECT * FROM `centralize_request` WHERE `cr_r_id` = ? ORDER BY `cr_id` DESC LIMIT ?");
        $items = [];
        if ($stmt) {
            $stmt->bind_param("ii", $r_id, $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }
            $stmt->close();
        }
        return $items;
    }

    public function CancelRequest($requestId, $residentId = 0) {
        if ($residentId > 0) {
            $query = "UPDATE `centralize_request` SET `cr_status` = 'Canceled' WHERE `cr_id` = ? AND `cr_r_id` = ? AND `cr_status` = 'Pending'";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ii", $requestId, $residentId);
        } else {
            $query = "UPDATE `centralize_request` SET `cr_status` = 'Canceled' WHERE `cr_id` = ? AND `cr_status` = 'Pending'";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $requestId);
        }
        
        if ($stmt && $stmt->execute()) {
            $affected = $stmt->affected_rows;
            $stmt->close();
            if ($affected > 0) {
                return ['status' => 'success', 'message' => 'Request canceled successfully.'];
            }
            return ['status' => 'error', 'message' => 'Request cannot be canceled (it may already be processed or not found).'];
        }
        return ['status' => 'error', 'message' => 'Failed to cancel the request.'];
    }
    
    public function updateResident(
        $r_id, $fname, $mname, $lname, $r_suffix, $r_gender, $r_civil_status, 
        $r_citizenship, $r_bday, $r_contact_number, $region, $province, $city, $barangay, 
        $r_street, $r_email, $newPassword, $profileImgName
    ) {
        if ($this->checkEmailExists($r_email, $r_id)) {
            return "Email address is already in use by another resident.";
        }

        $citizenship = !empty($r_citizenship) ? $r_citizenship : 'Filipino';

        $query = "UPDATE `resident` 
                  SET `r_fname` = ?, `r_mname` = ?, `r_lname` = ?, `r_suffix` = ?, `r_gender` = ?, 
                      `r_civil_status` = ?, `r_citizenship` = ?, `r_bday` = ?, `r_contact_number` = ?, `r_region` = ?, 
                      `r_province` = ?, `r_municipality` = ?, `r_barangay` = ?, `r_street` = ?, 
                      `r_email` = ?";
    
        $params = [$fname, $mname, $lname, $r_suffix, $r_gender, $r_civil_status, 
                   $citizenship, $r_bday, $r_contact_number, $region, $province, $city, 
                   $barangay, $r_street, $r_email];
        $types = str_repeat('s', 15);
    
        if (!empty($newPassword)) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $query .= ", `r_password` = ?";
            $params[] = $hashedPassword;
            $types .= 's';
        }
    
        if (!empty($profileImgName)) {
            $query .= ", `r_profile` = ?";
            $params[] = $profileImgName;
            $types .= 's';
        }
    
        $query .= " WHERE `r_id` = ?";
        $types .= 'i';
        $params[] = $r_id;
    
        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param($types, ...$params);
    
            if ($stmt->execute()) {
                $stmt->close();
                return "Resident updated successfully.";
            } else {
                $error = $stmt->error;
                $stmt->close();
                return "Error executing the query: " . $error;
            }
        } else {
            return "Error preparing the statement: " . $this->conn->error;
        }
    }

    public function check_account($r_id) {
        $query = "SELECT * FROM resident WHERE r_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $r_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $items = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }
        }
        $stmt->close();
        return $items; 
    }

    public function RequestClearance($purpose, $address, $payment, $validId, $proofResidency, $r_id, $documentPrice, $shippingFee, $totalPrice) {
        $uniqueCode = uniqid('CR-', true);
    
        $query = "INSERT INTO `centralize_request` 
                  (`cr_code`, `cr_purpose`, `cr_address`, `cr_payment`, `cr_validId`, `cr_proofResidency`, `cr_r_id`, `cr_price`, `cr_shipping_fee`, `cr_total`, `cr_formtype`) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Database error: " . $this->conn->error);
        }
    
        $formType = 'Barangay Clearance';
        $stmt->bind_param("ssssssiddds", $uniqueCode, $purpose, $address, $payment, $validId, $proofResidency, $r_id, $documentPrice, $shippingFee, $totalPrice, $formType);
    
        if ($stmt->execute()) {
            $stmt->close();
            return "Barangay Clearance request submitted successfully.";
        } else {
            $err = $stmt->error;
            $stmt->close();
            throw new Exception("Failed to submit clearance request: " . $err);
        }
    }

    public function RequestBarangayID($purpose, $address, $payment, $validId, $proofResidency, $pic, $signature, $r_id, $documentPrice, $shippingFee, $totalPrice) {
        $uniqueCode = uniqid('CR-', true);
    
        $query = "INSERT INTO `centralize_request` 
                  (`cr_code`, `cr_purpose`, `cr_address`, `cr_payment`, `cr_validId`, `cr_proofResidency`, `cr_1X1_pic`, `cr_Signature`, `cr_r_id`, `cr_price`, `cr_shipping_fee`, `cr_total`, `cr_formtype`) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Database error: " . $this->conn->error);
        }
    
        $formType = 'Barangay ID';
        $stmt->bind_param("ssssssssiddds", $uniqueCode, $purpose, $address, $payment, $validId, $proofResidency, $pic, $signature, $r_id, $documentPrice, $shippingFee, $totalPrice, $formType);
    
        if ($stmt->execute()) {
            $stmt->close();
            return "Barangay ID request submitted successfully.";
        } else {
            $err = $stmt->error;
            $stmt->close();
            throw new Exception("Failed to submit Barangay ID request: " . $err);
        }
    }

    public function RequestResidency(
        $purpose, 
        $address, 
        $payment, 
        $validIdFilename,
        $r_id,
        $documentPrice,
        $shippingFee,
        $totalPrice) {
        $uniqueCode = uniqid('CR-', true);
    
        $query = "INSERT INTO `centralize_request` 
                  (`cr_code`, `cr_purpose`, `cr_address`, `cr_payment`, `cr_validId`, `cr_r_id`, `cr_price`, `cr_shipping_fee`, `cr_total`, `cr_formtype`) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Database error: " . $this->conn->error);
        }
    
        $formType = 'Barangay Residency';
        $stmt->bind_param("sssssiddds", $uniqueCode, $purpose, $address, $payment, $validIdFilename, $r_id, $documentPrice, $shippingFee, $totalPrice, $formType);
    
        if ($stmt->execute()) {
            $stmt->close();
            return "Certificate of Residency request submitted successfully.";
        } else {
            $err = $stmt->error;
            $stmt->close();
            throw new Exception("Failed to submit residency request: " . $err);
        }
    }

    public function RequestIndigency(
        $purpose, 
        $address, 
        $payment, 
        $validIdFilename,
        $r_id,
        $documentPrice,
        $shippingFee,
        $totalPrice) {
        $uniqueCode = uniqid('CR-', true); 
    
        $query = "INSERT INTO `centralize_request` 
                  (`cr_code`, `cr_purpose`, `cr_address`, `cr_payment`, `cr_validId`, `cr_r_id`, `cr_price`, `cr_shipping_fee`, `cr_total`, `cr_formtype`) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Database error: " . $this->conn->error);
        }
    
        $formType = 'Barangay Indigency';
        $stmt->bind_param("sssssiddds", $uniqueCode, $purpose, $address, $payment, $validIdFilename, $r_id, $documentPrice, $shippingFee, $totalPrice, $formType);
    
        if ($stmt->execute()) {
            $stmt->close();
            return "Certificate of Indigency request submitted successfully.";
        } else {
            $err = $stmt->error;
            $stmt->close();
            throw new Exception("Failed to submit indigency request: " . $err);
        }
    }
    
    public function fetch_all_clearance_request($r_id){
        $query = "SELECT * FROM `centralize_request` WHERE `cr_r_id` = ? ORDER BY `cr_id` DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $r_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $items = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }
        }
        $stmt->close();
        return $items; 
    }

    public function getPublishedAnnouncements($limit = null, $category = null, $search = null) {
        $query = "SELECT a.*, u.user_fname, u.user_lname 
                  FROM `announcements` a 
                  LEFT JOIN `user` u ON a.user_id = u.user_id 
                  WHERE a.status = 'Published'";

        $params = [];
        $types = "";

        if (!empty($category) && $category !== 'All') {
            $query .= " AND a.category = ?";
            $params[] = $category;
            $types .= "s";
        }

        if (!empty($search)) {
            $searchTerm = '%' . $search . '%';
            $query .= " AND (a.title LIKE ? OR a.content LIKE ? OR a.category LIKE ?)";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= "sss";
        }

        $query .= " ORDER BY a.is_pinned DESC, a.created_at DESC";

        if ($limit !== null && (int)$limit > 0) {
            $query .= " LIMIT ?";
            $params[] = (int)$limit;
            $types .= "i";
        }

        if (!empty($params)) {
            $stmt = $this->conn->prepare($query);
            if (!$stmt) return [];
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $this->conn->query($query);
        }

        $items = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }
        }
        if (isset($stmt)) {
            $stmt->close();
        }
        return $items;
    }

    public function getPinnedAnnouncements($limit = 3) {
        $limit = max(1, (int)$limit);
        $query = "SELECT a.*, u.user_fname, u.user_lname 
                  FROM `announcements` a 
                  LEFT JOIN `user` u ON a.user_id = u.user_id 
                  WHERE a.status = 'Published' AND a.is_pinned = 1 
                  ORDER BY a.created_at DESC LIMIT ?";
        
        $stmt = $this->conn->prepare($query);
        if (!$stmt) return [];
        
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $items = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }
        }
        $stmt->close();
        return $items;
    }

    public function getAnnouncementById($announcement_id) {
        $query = "SELECT a.*, u.user_fname, u.user_lname 
                  FROM `announcements` a 
                  LEFT JOIN `user` u ON a.user_id = u.user_id 
                  WHERE a.announcement_id = ? AND a.status = 'Published' LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        if (!$stmt) return null;
        
        $stmt->bind_param("i", $announcement_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $announcement = null;
        if ($result && $row = $result->fetch_assoc()) {
            $announcement = $row;
        }
        $stmt->close();
        return $announcement;
    }

    public function getAnnouncementCategories() {
        $query = "SELECT DISTINCT `category` FROM `announcements` WHERE `status` = 'Published' ORDER BY `category` ASC";
        $result = $this->conn->query($query);
        $categories = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $categories[] = $row['category'];
            }
        }
        return $categories;
    }
}