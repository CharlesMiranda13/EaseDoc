<?php 

include "components/header.php";

// Set the default step as "Pending" if not set
$defaultStep = 'Pending'; // Default value



if (isset($_GET['step'])) {
    $defaultStep = $_GET['step'];
}else{
    echo "<script>location.href='request.php?step=Pending'</script>";
}
?>

<div class="mb-6">
    <h1 class="text-xl font-bold text-gray-800">Document Requests</h1>
    <p class="text-sm text-gray-500 mt-0.5">Manage and track barangay document requests</p>
</div>

<!-- Tabs -->
<div class="flex space-x-2 border-b border-gray-200 mb-6 overflow-x-auto whitespace-nowrap scrollbar-none">
    <a href="?step=Pending" class="py-2.5 px-4 text-sm font-medium border-b-2 transition duration-150 <?= ($defaultStep == 'Pending' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300') ?>">Pending</a>
    <a href="?step=Approved" class="py-2.5 px-4 text-sm font-medium border-b-2 transition duration-150 <?= ($defaultStep == 'Approved' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300') ?>">Approved</a>
    <a href="?step=Shipped" class="py-2.5 px-4 text-sm font-medium border-b-2 transition duration-150 <?= ($defaultStep == 'Shipped' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300') ?>">Shipped</a>
    <a href="?step=Delivered" class="py-2.5 px-4 text-sm font-medium border-b-2 transition duration-150 <?= ($defaultStep == 'Delivered' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300') ?>">Delivered</a>
    <a href="?step=Rejected" class="py-2.5 px-4 text-sm font-medium border-b-2 transition duration-150 <?= ($defaultStep == 'Rejected' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300') ?>">Rejected</a>
    <a href="?step=Canceled" class="py-2.5 px-4 text-sm font-medium border-b-2 transition duration-150 <?= ($defaultStep == 'Canceled' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300') ?>">Canceled</a>
</div>

<div class="overflow-x-auto bg-white rounded-lg border border-gray-200 shadow-sm p-6" id="recordTable">
    <!-- Search Box -->
    <div class="mb-5 max-w-sm">
        <label for="searchInput" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Search Requests</label>
        <input type="text" id="searchInput" placeholder="Type to search..." class="pl-3 pr-3 py-2 border border-gray-300 rounded-lg text-sm w-full focus:outline-none focus:ring-2 focus:ring-primary-500">
    </div>

    <table class="min-w-full table-auto">
        <thead class="bg-gray-50 border-b-2 border-gray-200">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Request ID</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Form Type</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Resident</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Payment</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Order Date</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Price</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
        </tbody>
    </table>
</div>

<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<?php include "components/footer.php";?>

<script src="js/table_order.js"></script>