<?php
require_once __DIR__ . '/db.php';
date_default_timezone_set('Asia/Manila');
$getDateToday = date('Y-m-d H:i:s'); 


class global_class extends db_connect
{
    public function __construct()
    {
        $this->connect();
    }



    public function updateOrderStatus($orderId, $newStatus) {
        // Use a parameterized query to prevent SQL injection
        $stmt = $this->conn->prepare("UPDATE `centralize_request` SET `cr_status` = ? WHERE `cr_id` = ?");
        
        // Bind the parameters to the query
        $stmt->bind_param("si", $newStatus, $orderId); // "si" means string and integer
        
        // Execute the query and return the result
        return $stmt->execute();
    }


    public function totalRequestbarangayID() {
        // Use a parameterized query to prevent SQL injection
        $stmt = $this->conn->prepare("
        SELECT 
            cr_formtype,
            COUNT(*) AS total
        FROM 
            centralize_request
        WHERE
            cr_formtype IN ('Barangay ID', 'Barangay Clearance', 'Barangay Residency', 'Barangay Indigency')
            AND (cr_status != 'Canceled' AND cr_status != 'Rejected')
        GROUP BY 
            cr_formtype
    ");
    
      
        // Execute the query
        if ($stmt->execute()) {
            // Fetch the results
            $result = $stmt->get_result();
    
            // Format the results into an associative array
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[$row['cr_formtype']] = (int)$row['total'];
            }
    
            // Return the formatted data
            return $data;
        } else {
            // Handle errors (e.g., log them or throw an exception)
            return false;
        }
    }
    





    public function viewOrderDetails($cr_id)
    {
        // Prepare the query with sorting by order_date in descending order
        $query = "SELECT * FROM centralize_request
                  LEFT JOIN resident ON resident.r_id = centralize_request.cr_r_id
                  WHERE cr_id = ?
                  ORDER BY centralize_request.cr_request_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $cr_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Check if there are any results
        if ($result->num_rows > 0) {
            // Fetch the results and return them as an associative array
            $order = [];
            while ($row = $result->fetch_assoc()) {
                $order[] = $row;
            }
            $stmt->close();
            return $order;
        } else {
            $stmt->close();
            return false;
        }
    }





    public function GetAllOrders()
    {
        // Prepare the query with sorting by order_date in descending order
        $query = "SELECT * FROM centralize_request
                  LEFT JOIN resident ON resident.r_id = centralize_request.cr_r_id
                  ORDER BY centralize_request.cr_request_date DESC"; 
    
        $result = $this->conn->query($query);
        
        // Check if the query was successful
        if ($result === false) {
            // Log or handle the error
            error_log("Query execution failed: " . $this->conn->error);
            return false;
        }
    
        // Check if there are any results
        if ($result->num_rows > 0) {
            // Fetch the results and return them as an associative array
            $order = [];
            while ($row = $result->fetch_assoc()) {
                $order[] = $row;
            }
            return $order;
        } else {
            return false;
        }
    }



    public function checkResidentEmailExists($email, $excludeId = 0) {
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

    public function getResidentStats() {
        $stats = [
            'total' => 0,
            'male' => 0,
            'female' => 0,
            'verified' => 0
        ];

        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN LOWER(r_gender) = 'male' THEN 1 ELSE 0 END) as male,
                    SUM(CASE WHEN LOWER(r_gender) = 'female' THEN 1 ELSE 0 END) as female,
                    SUM(CASE WHEN r_status = '1' THEN 1 ELSE 0 END) as verified
                  FROM `resident` 
                  WHERE `r_status` = '1'";

        $result = $this->conn->query($query);
        if ($result && $row = $result->fetch_assoc()) {
            $stats['total'] = (int)($row['total'] ?? 0);
            $stats['male'] = (int)($row['male'] ?? 0);
            $stats['female'] = (int)($row['female'] ?? 0);
            $stats['verified'] = (int)($row['verified'] ?? 0);
        }
        return $stats;
    }

    public function updateResident(
        $r_id, $fname, $mname, $lname, $r_suffix, $r_gender, $r_civil_status, 
        $r_citizenship, $r_bday, $r_contact_number, $region, $province, $city, $barangay, 
        $r_street, $r_email, $newPassword, $profileImgPathDb, $IdImgPathDb
    ) {
        if ($this->checkResidentEmailExists($r_email, $r_id)) {
            return "Email address is already in use by another resident.";
        }

        $citizenship = !empty($r_citizenship) ? $r_citizenship : 'Filipino';

        $query = "UPDATE `resident` SET 
            `r_fname` = ?, `r_mname` = ?, `r_lname` = ?, `r_suffix` = ?, 
            `r_gender` = ?, `r_civil_status` = ?, `r_citizenship` = ?, `r_bday` = ?, 
            `r_contact_number` = ?, `r_region` = ?, `r_province` = ?, 
            `r_municipality` = ?, `r_barangay` = ?, `r_street` = ?, 
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
    
        if (!empty($profileImgPathDb)) {
            $query .= ", `r_profile` = ?";
            $params[] = $profileImgPathDb;
            $types .= 's';
        }
    
        if (!empty($IdImgPathDb)) {
            $query .= ", `r_valid_ids` = ?";
            $params[] = $IdImgPathDb;
            $types .= 's';
        }
    
        $query .= " WHERE `r_id` = ?";
        $types .= 'i';
        $params[] = $r_id;
    
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return "Error preparing statement: " . $this->conn->error;
        }
        
        $stmt->bind_param($types, ...$params);
        
        if ($stmt->execute()) {
            $stmt->close();
            return "Resident updated successfully.";
        } else {
            $error = $stmt->error;
            $stmt->close();
            return "Error executing the query: " . $error;
        }
    }

    public function DeleteResident($residentId) {
        $query = "UPDATE `resident` SET `r_status` = '0' WHERE `r_id` = ?";
        
        if ($stmt = $this->conn->prepare($query)) {
            $stmt->bind_param("i", $residentId);
            
            if ($stmt->execute()) {
                $stmt->close();
                return "Resident removed successfully.";
            } else {
                $error = $stmt->error;
                $stmt->close();
                return "Error executing the query: " . $error;
            }
        } else {
            return "Error preparing the query: " . $this->conn->error;
        }
    }

    public function check_resident($user_id) {
        $query = "SELECT * FROM resident WHERE r_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
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

    public function check_account($user_id) {
        $query = "SELECT * FROM user WHERE user_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
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

    public function fetch_all_resident() {
        $query = "SELECT * FROM `resident` WHERE `r_status` = '1' ORDER BY `r_id` DESC";
    
        $result = $this->conn->query($query);

        $items = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }
        }
        return $items; 
    }

    public function UpdateAdminInfo($user_fname, $user_mname, $user_lname, $email, $user_id){
        $query = "UPDATE `user` SET `user_fname`=?, `user_mname`=?, `user_lname`=?, `user_email`=? WHERE `user_id`=?";
        
        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param("ssssi", $user_fname, $user_mname, $user_lname, $email, $user_id);
            
            if ($stmt->execute()) {
                $stmt->close();
                return "Admin info updated successfully.";
            } else {
                $error = $stmt->error;
                $stmt->close();
                return "Error executing the statement: " . $error;
            }
        } else {
            return "Error preparing the statement: " . $this->conn->error;
        }
    }

    public function UpdateAdminPassword($current_password, $new_password, $user_id){
        $query = "SELECT `user_password` FROM `user` WHERE `user_id`=?";
        
        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->store_result();
            
            if ($stmt->num_rows > 0) {
                $stmt->bind_result($stored_password);
                $stmt->fetch();
                
                if (password_verify($current_password, $stored_password)) {
                    $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                    $update_query = "UPDATE `user` SET `user_password`=? WHERE `user_id`=?";
                    
                    $update_stmt = $this->conn->prepare($update_query);
                    if ($update_stmt) {
                        $update_stmt->bind_param("si", $new_password_hash, $user_id);
                        $update_stmt->execute();
                        $update_stmt->close();
                        $stmt->close();
                        return "Password updated successfully.";
                    } else {
                        $stmt->close();
                        return "Error updating password.";
                    }
                } else {
                    $stmt->close();
                    return "Current password is incorrect.";
                }
            } else {
                $stmt->close();
                return "User not found.";
            }
        } else {
            return "Error preparing the statement.";
        }
    }
    
    public function addResident(
        $fname, $mname, $lname, $r_suffix, $Gender, $r_civil_status, $r_citizenship, $r_bday, 
        $r_contact_number, $region, $province, $city, $barangay, $r_street, 
        $r_email, $r_password, $profileImgPathDb, $validIdPathDb
    ) {
        if ($this->checkResidentEmailExists($r_email)) {
            return "Email address is already in use by another resident.";
        }

        $citizenship = !empty($r_citizenship) ? $r_citizenship : 'Filipino';
        $hashedPassword = password_hash($r_password, PASSWORD_DEFAULT);
    
        $query = "INSERT INTO `resident` 
                  (`r_fname`, `r_mname`, `r_lname`, `r_suffix`, `r_gender`, `r_civil_status`, `r_citizenship`, `r_bday`, 
                   `r_contact_number`, `r_region`, `r_province`, `r_municipality`, `r_barangay`, `r_street`, 
                   `r_email`, `r_password`, `r_profile`, `r_valid_ids`, `r_status`) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";
        
        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param(
                "ssssssssssssssssss", 
                $fname, $mname, $lname, $r_suffix, $Gender, $r_civil_status, $citizenship, $r_bday, 
                $r_contact_number, $region, $province, $city, $barangay, $r_street, 
                $r_email, $hashedPassword, $profileImgPathDb, $validIdPathDb
            );
        
            if ($stmt->execute()) {
                $stmt->close();
                return "Resident added successfully.";
            } else {
                $error = $stmt->error;
                $stmt->close();
                return "Error: " . $error;
            }
        } else {
            return "Error preparing the statement: " . $this->conn->error;
        }
    }

    public function getAnnouncementStats() {
        $stats = [
            'total' => 0,
            'published' => 0,
            'draft' => 0,
            'archived' => 0,
            'pinned' => 0
        ];

        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN `status` = 'Published' THEN 1 ELSE 0 END) as published,
                    SUM(CASE WHEN `status` = 'Draft' THEN 1 ELSE 0 END) as draft,
                    SUM(CASE WHEN `status` = 'Archived' THEN 1 ELSE 0 END) as archived,
                    SUM(CASE WHEN `is_pinned` = 1 AND `status` = 'Published' THEN 1 ELSE 0 END) as pinned
                  FROM `announcements`";

        $result = $this->conn->query($query);
        if ($result && $row = $result->fetch_assoc()) {
            $stats['total'] = (int)($row['total'] ?? 0);
            $stats['published'] = (int)($row['published'] ?? 0);
            $stats['draft'] = (int)($row['draft'] ?? 0);
            $stats['archived'] = (int)($row['archived'] ?? 0);
            $stats['pinned'] = (int)($row['pinned'] ?? 0);
        }
        return $stats;
    }

    public function fetch_all_announcements($status = null, $category = null, $search = null) {
        $query = "SELECT a.*, u.user_fname, u.user_lname 
                  FROM `announcements` a 
                  LEFT JOIN `user` u ON a.user_id = u.user_id 
                  WHERE 1=1";
        
        $params = [];
        $types = "";

        if (!empty($status) && in_array($status, ['Published', 'Draft', 'Archived'], true)) {
            $query .= " AND a.status = ?";
            $params[] = $status;
            $types .= "s";
        }

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

    public function get_announcement_by_id($announcement_id) {
        $query = "SELECT a.*, u.user_fname, u.user_lname 
                  FROM `announcements` a 
                  LEFT JOIN `user` u ON a.user_id = u.user_id 
                  WHERE a.announcement_id = ? LIMIT 1";
        
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

    public function createAnnouncement($user_id, $title, $content, $category, $status, $is_pinned, $image = null) {
        $validStatuses = ['Draft', 'Published', 'Archived'];
        if (!in_array($status, $validStatuses, true)) {
            $status = 'Published';
        }
        $is_pinned = $is_pinned ? 1 : 0;
        $category = !empty($category) ? $category : 'General';

        $query = "INSERT INTO `announcements` (`user_id`, `title`, `content`, `category`, `image`, `status`, `is_pinned`) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return "Error preparing the statement: " . $this->conn->error;
        }

        $stmt->bind_param("isssssi", $user_id, $title, $content, $category, $image, $status, $is_pinned);

        if ($stmt->execute()) {
            $stmt->close();
            return "Announcement created successfully.";
        } else {
            $error = $stmt->error;
            $stmt->close();
            return "Error creating announcement: " . $error;
        }
    }

    public function updateAnnouncement($announcement_id, $title, $content, $category, $status, $is_pinned, $image = null, $remove_image = false) {
        $validStatuses = ['Draft', 'Published', 'Archived'];
        if (!in_array($status, $validStatuses, true)) {
            $status = 'Published';
        }
        $is_pinned = $is_pinned ? 1 : 0;
        $category = !empty($category) ? $category : 'General';

        $query = "UPDATE `announcements` SET `title` = ?, `content` = ?, `category` = ?, `status` = ?, `is_pinned` = ?";
        $params = [$title, $content, $category, $status, $is_pinned];
        $types = "ssssi";

        if ($image !== null) {
            $query .= ", `image` = ?";
            $params[] = $image;
            $types .= "s";
        } elseif ($remove_image) {
            $query .= ", `image` = NULL";
        }

        $query .= " WHERE `announcement_id` = ?";
        $params[] = $announcement_id;
        $types .= "i";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return "Error preparing statement: " . $this->conn->error;
        }

        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            $stmt->close();
            return "Announcement updated successfully.";
        } else {
            $error = $stmt->error;
            $stmt->close();
            return "Error updating announcement: " . $error;
        }
    }

    public function updateAnnouncementStatus($announcement_id, $status) {
        $validStatuses = ['Draft', 'Published', 'Archived'];
        if (!in_array($status, $validStatuses, true)) {
            return "Invalid status specified.";
        }

        $query = "UPDATE `announcements` SET `status` = ? WHERE `announcement_id` = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return "Error preparing statement: " . $this->conn->error;
        }

        $stmt->bind_param("si", $status, $announcement_id);

        if ($stmt->execute()) {
            $stmt->close();
            return "Announcement status updated successfully.";
        } else {
            $error = $stmt->error;
            $stmt->close();
            return "Error updating status: " . $error;
        }
    }

    public function toggleAnnouncementPin($announcement_id, $is_pinned) {
        $pinVal = $is_pinned ? 1 : 0;
        $query = "UPDATE `announcements` SET `is_pinned` = ? WHERE `announcement_id` = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return "Error preparing statement: " . $this->conn->error;
        }

        $stmt->bind_param("ii", $pinVal, $announcement_id);

        if ($stmt->execute()) {
            $stmt->close();
            return $pinVal ? "Announcement pinned successfully." : "Announcement unpinned successfully.";
        } else {
            $error = $stmt->error;
            $stmt->close();
            return "Error toggling pin status: " . $error;
        }
    }

    public function deleteAnnouncement($announcement_id) {
        $announcement = $this->get_announcement_by_id($announcement_id);
        if (!$announcement) {
            return "Announcement not found.";
        }

        $query = "DELETE FROM `announcements` WHERE `announcement_id` = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return "Error preparing statement: " . $this->conn->error;
        }

        $stmt->bind_param("i", $announcement_id);

        if ($stmt->execute()) {
            $stmt->close();
            if (!empty($announcement['image'])) {
                $imagePath = dirname(__DIR__, 2) . '/uploads/announcements/' . $announcement['image'];
                if (file_exists($imagePath)) {
                    @unlink($imagePath);
                }
            }
            return "Announcement deleted successfully.";
        } else {
            $error = $stmt->error;
            $stmt->close();
            return "Error deleting announcement: " . $error;
        }
    }

}

?>