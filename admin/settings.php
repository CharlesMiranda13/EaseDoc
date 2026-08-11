<?php include "components/header.php"; ?>

<div class="mb-6">
    <h1 class="text-xl font-bold text-gray-800">Account Settings</h1>
    <p class="text-sm text-gray-500 mt-0.5">Manage your admin profile and account credentials</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <!-- Update Info Card -->
    <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
        <h3 class="text-2xl font-semibold text-gray-800 mb-6">Update Information</h3>
        <form id="frmUpdateInfo">
                <?= csrf_field(); ?>
            <input hidden type="text" value="<?=$user_id?>" name='user_id'>
            <div class="mb-6 flex items-center">
                <span class="material-icons text-gray-600 mr-3">person</span>
                <label for="user_fname" class="block text-sm font-medium text-gray-700">First Name</label>
                <input type="text" value="<?=$result[0]['user_fname']; ?>" id="user_fname" name="user_fname" class="mt-1 p-3 w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter your First name">
            </div>
            <div class="mb-6 flex items-center">
                <span class="material-icons text-gray-600 mr-3">person_add</span>
                <label for="user_mname" class="block text-sm font-medium text-gray-700">Middle Name</label>
                <input type="text" value="<?=$result[0]['user_mname']; ?>" id="user_mname" name="user_mname" class="mt-1 p-3 w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter your Middle name">
            </div>
            <div class="mb-6 flex items-center">
                <span class="material-icons text-gray-600 mr-3">person_outline</span>
                <label for="user_lname" class="block text-sm font-medium text-gray-700">Last Name</label>
                <input type="text" value="<?=$result[0]['user_lname']; ?>"  id="user_lname" name="user_lname" class="mt-1 p-3 w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter your Last name">
            </div>
            <div class="mb-6 flex items-center">
                <span class="material-icons text-gray-600 mr-3">email</span>
                <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                <input type="email" value="<?=$result[0]['user_email']; ?>" id="email" name="email" class="mt-1 p-3 w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter your email">
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500">Update Info</button>
        </form>
    </div>

    <!-- Update Password Card -->
    <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
        <h3 class="text-2xl font-semibold text-gray-800 mb-6">Update Password</h3>
        <form id="frmUpdatePassword">
                <?= csrf_field(); ?>
            <input hidden type="text" value="<?=$user_id?>" name='user_id'>
            <div class="mb-6 flex items-center">
                <span class="material-icons text-gray-600 mr-3">lock</span>
                <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                <input type="password" id="current_password" name="current_password" class="mt-1 p-3 w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter your current password">
            </div>
            <div class="mb-6 flex items-center">
                <span class="material-icons text-gray-600 mr-3">lock_reset</span>
                <label for="new_password" class="block text-sm font-medium text-gray-700">New Password</label>
                <input type="password" id="new_password" name="new_password" class="mt-1 p-3 w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter a new password">
            </div>
            <div class="mb-6 flex items-center">
                <span class="material-icons text-gray-600 mr-3">lock_clock</span>
                <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="mt-1 p-3 w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Confirm your new password">
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500">Update Password</button>
        </form>
    </div>

</div>

<?php include "components/footer.php"; ?>
<script src="js/app.js"></script>
