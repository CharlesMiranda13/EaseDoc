<?php 
include "components/header.php";
date_default_timezone_set('Asia/Manila');
$dateToday = new DateTime(); 
$formattedDate = $dateToday->format('F j, Y'); 
$day = ltrim($dateToday->format('d'), '0'); // Remove the leading zero
$month = $dateToday->format('F');
$year = $dateToday->format('Y');

function getOrdinalSuffix($number) {
    if (!in_array(($number % 100), [11, 12, 13])) {
        switch ($number % 10) {
            case 1: return 'st';
            case 2: return 'nd';
            case 3: return 'rd';
        }
    }
    return 'th';
}

$ordinalDay = $day . getOrdinalSuffix((int)$day);

function getAge($birthday) {
    if (empty($birthday)) return 0;
    try {
        $birthDate = new DateTime($birthday);
        $today = new DateTime();
        return $today->diff($birthDate)->y;
    } catch (Exception $e) {
        return 0;
    }
}

$cr_id = isset($_GET['cr_id']) ? (int)$_GET['cr_id'] : 0;
$GetAllOrders = $db->viewOrderDetails($cr_id, $user_id);

if ($GetAllOrders && is_array($GetAllOrders)):
    foreach ($GetAllOrders as $order):
        $r_id = $order['r_id'] ?? '';
        $cr_code = $order['cr_code'] ?? '';
        $birthday = $order['r_bday'] ?? ''; 
        $cr_1X1_pic = $order['cr_1X1_pic'] ?? ''; 
        $cr_Signature = $order['cr_Signature'] ?? ''; 
        
        $address = trim(($order['r_region'] ?? '') . ' ' . ($order['r_province'] ?? '') . ' ' . ($order['r_municipality'] ?? '') . ' ' . ($order['r_barangay'] ?? '') . ' ' . ($order['r_street'] ?? ''));

        $formatted_birthday = '';
        if (!empty($birthday)) {
            try {
                $date = new DateTime($birthday);
                $formatted_birthday = $date->format('F j, Y');
            } catch (Exception $e) {
                $formatted_birthday = $birthday;
            }
        }

        $age = getAge($birthday);
        $fullName = ucfirst($order['r_fname'] ?? '') . ' ' . ($order['r_mname'] ?? '') . ' ' . ($order['r_lname'] ?? '');

        $formType = $order['cr_formtype'] ?? '';
        if ($formType === "Barangay Clearance") {
            include "templates/barangay_clearance.php";
        } elseif ($formType === "Barangay Indigency") {
            include "templates/barangay_indigency.php";
        } elseif ($formType === "Barangay ID") {
            include "templates/barangay_id.php";
        } elseif ($formType === "Barangay Residency") {
            include "templates/barangay_residency.php";
        }
    endforeach;
?>

<!-- Print Button -->
<div class="text-center mt-8 mb-8">
    <button id="printButton" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2.5 px-6 rounded-lg shadow transition">
      Print Document
    </button>
</div>

<script>
  $(document).ready(function () {
    $('#printButton').on('click', function () {
      const printableContent = $('#printableArea').html();
      const printWindow = window.open('', '_blank');
      printWindow.document.open();
      printWindow.document.write(`
        <html>
          <head>
            <title>Print Document</title>
            <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
            <style>
              @media print {
                body {
                  width: 8.5in;
                  height: 11in;
                  margin: 0.5in;
                  font-size: 12pt;
                }
                .max-w-4xl {
                  max-width: none;
                  width: 100%;
                }
                .p-8 {
                  padding: 1in;
                }
                .text-center {
                  text-align: center;
                }
                .underline {
                  text-decoration: underline;
                }
                .font-bold {
                  font-weight: bold;
                }
                .font-semibold {
                  font-weight: 600;
                }
                .font-extrabold {
                  font-weight: 800;
                }
                .mt-8 {
                  margin-top: 0.5in;
                }
                .mt-12 {
                  margin-top: 1in;
                }
                .mt-16 {
                  margin-top: 1.5in;
                }
                .border-t {
                  border-top: 1px solid black;
                }
              }
            </style>
          </head>
          <body>
            ${printableContent}
          </body>
        </html>
      `);
      printWindow.document.close();
      printWindow.print();
    });
  });
</script>

<?php else: ?>
<div class="max-w-md mx-auto mt-12 bg-white rounded-xl shadow p-6 text-center">
    <p class="text-gray-600">Order not found or access denied.</p>
    <a href="request.php" class="inline-block mt-4 text-primary-600 hover:underline">Back to Requests</a>
</div>
<?php endif; ?>

<?php
include "components/footer.php";
?>
