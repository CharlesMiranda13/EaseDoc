<?php 
include "components/header.php"; 
$profileImg = !empty($resident['r_profile']) ? htmlspecialchars($resident['r_profile']) : '';
$citizenship = htmlspecialchars($resident['r_citizenship'] ?? 'Filipino');
?>

<div class="max-w-4xl mx-auto py-4 sm:py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Account Settings</h1>
        <p class="text-sm text-slate-500 mt-0.5">Manage your personal profile, registered address, and login credentials</p>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8 relative">
        
        <!-- Loading Spinner -->
        <div id="loadingSpinner_account" style="display:none;">
            <div class="absolute inset-0 bg-white/80 backdrop-blur-xs flex items-center justify-center rounded-3xl z-20">
                <div class="flex flex-col items-center gap-3">
                    <div class="w-10 h-10 border-4 border-primary-600 border-t-transparent rounded-full animate-spin"></div>
                    <p class="text-xs font-semibold text-slate-600">Updating profile...</p>
                </div>
            </div>
        </div>

        <form id="frmUpdateAccountSetting" class="space-y-8">
            <?= csrf_field(); ?>
            <input type="hidden" id="r_id" name="r_id" value="<?= htmlspecialchars((string)($resident['r_id'] ?? '')) ?>" required>

            <!-- Profile Picture Header -->
            <div class="flex flex-col sm:flex-row items-center gap-6 pb-6 border-b border-slate-100">
                <div class="relative group">
                    <img id="image-preview" src="" alt="Profile Preview" class="w-28 h-28 object-cover rounded-3xl border-4 border-primary-500 shadow-md" style="display: none;">
                    
                    <?php if (!empty($profileImg)): ?>
                        <img src="../uploads/resident/<?= $profileImg ?>" alt="Profile" id="default-image" class="w-28 h-28 object-cover rounded-3xl border-4 border-slate-100 shadow-md">
                    <?php else: ?>
                        <div id="default-image" class="w-28 h-28 rounded-3xl border-4 border-slate-100 bg-gradient-to-tr from-primary-600 to-indigo-600 text-white flex items-center justify-center text-3xl font-extrabold shadow-md">
                            <?= strtoupper(substr($resident['r_fname'] ?? 'R', 0, 1)); ?>
                        </div>
                    <?php endif; ?>

                    <label for="r_profile" class="absolute -bottom-2 -right-2 w-9 h-9 bg-primary-600 hover:bg-primary-700 active:bg-primary-800 text-white rounded-xl border-2 border-white cursor-pointer flex items-center justify-center shadow-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </label>
                    <input type="file" id="r_profile" name="r_profile" class="hidden" accept="image/*" onchange="previewProfileImage(event)">
                </div>

                <div class="text-center sm:text-left">
                    <h2 class="text-lg font-bold text-slate-800"><?= $residentName ?></h2>
                    <p class="text-xs text-slate-500 mt-0.5">Resident ID: <span class="font-mono font-bold text-primary-600">#<?= (int)($resident['r_id'] ?? 0) ?></span></p>
                    <p class="text-xs text-slate-400 mt-1">Click the camera icon to upload a new profile photo (JPG, PNG, WEBP)</p>
                </div>
            </div>

            <!-- Section 1: Personal Information -->
            <div class="space-y-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-primary-600"></span> Personal Information
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label for="r_fname" class="block text-xs font-semibold text-slate-700 mb-1">First Name <span class="text-rose-500">*</span></label>
                        <input type="text" id="r_fname" name="r_fname" value="<?= htmlspecialchars($resident['r_fname'] ?? '') ?>" class="w-full px-3.5 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:outline-none" required>
                    </div>

                    <div>
                        <label for="r_mname" class="block text-xs font-semibold text-slate-700 mb-1">Middle Name</label>
                        <input type="text" id="r_mname" name="r_mname" value="<?= htmlspecialchars($resident['r_mname'] ?? '') ?>" class="w-full px-3.5 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="r_lname" class="block text-xs font-semibold text-slate-700 mb-1">Last Name <span class="text-rose-500">*</span></label>
                        <input type="text" id="r_lname" name="r_lname" value="<?= htmlspecialchars($resident['r_lname'] ?? '') ?>" class="w-full px-3.5 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:outline-none" required>
                    </div>

                    <div>
                        <label for="r_suffix" class="block text-xs font-semibold text-slate-700 mb-1">Suffix</label>
                        <input type="text" id="r_suffix" name="r_suffix" value="<?= htmlspecialchars($resident['r_suffix'] ?? '') ?>" placeholder="Jr., III (Optional)" class="w-full px-3.5 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label for="r_gender" class="block text-xs font-semibold text-slate-700 mb-1">Gender</label>
                        <select id="r_gender" name="r_gender" class="w-full px-3.5 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:outline-none bg-white">
                            <?php $gen = $resident['r_gender'] ?? ''; ?>
                            <option <?= $gen === "Male" ? 'selected' : '' ?> value="Male">Male</option>
                            <option <?= $gen === "Female" ? 'selected' : '' ?> value="Female">Female</option>
                        </select>
                    </div>

                    <div>
                        <label for="r_civil_status" class="block text-xs font-semibold text-slate-700 mb-1">Civil Status</label>
                        <select id="r_civil_status" name="r_civil_status" class="w-full px-3.5 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:outline-none bg-white">
                            <?php $cs = $resident['r_civil_status'] ?? ''; ?>
                            <option <?= $cs === "Single" ? 'selected' : '' ?> value="Single">Single</option>
                            <option <?= $cs === "Married" ? 'selected' : '' ?> value="Married">Married</option>
                            <option <?= $cs === "Widowed" ? 'selected' : '' ?> value="Widowed">Widowed</option>
                            <option <?= $cs === "Divorced" ? 'selected' : '' ?> value="Divorced">Divorced</option>
                        </select>
                    </div>

                    <div>
                        <label for="r_citizenship" class="block text-xs font-semibold text-slate-700 mb-1">Citizenship</label>
                        <input type="text" id="r_citizenship" name="r_citizenship" value="<?= $citizenship ?>" class="w-full px-3.5 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:outline-none" required>
                    </div>

                    <div>
                        <label for="r_bday" class="block text-xs font-semibold text-slate-700 mb-1">Date of Birth <span class="text-rose-500">*</span></label>
                        <input type="date" id="r_bday" name="r_bday" value="<?= htmlspecialchars($resident['r_bday'] ?? '') ?>" class="w-full px-3.5 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:outline-none" required>
                    </div>
                </div>
            </div>

            <!-- Section 2: Residential Address -->
            <div class="space-y-4 pt-6 border-t border-slate-100">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-indigo-600"></span> Registered Address
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label for="setting_region" class="block text-xs font-semibold text-slate-700 mb-1">Region</label>
                        <select id="setting_region" name="r_region" class="w-full px-3.5 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:outline-none bg-white"></select>
                    </div>

                    <div>
                        <label for="setting_province" class="block text-xs font-semibold text-slate-700 mb-1">Province</label>
                        <select id="setting_province" name="r_province" class="w-full px-3.5 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:outline-none bg-white"></select>
                    </div>

                    <div>
                        <label for="setting_city" class="block text-xs font-semibold text-slate-700 mb-1">City / Municipality</label>
                        <select id="setting_city" name="r_municipality" class="w-full px-3.5 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:outline-none bg-white"></select>
                    </div>

                    <div>
                        <label for="setting_barangay" class="block text-xs font-semibold text-slate-700 mb-1">Barangay</label>
                        <select id="setting_barangay" name="r_barangay" class="w-full px-3.5 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:outline-none bg-white"></select>
                    </div>
                </div>

                <div>
                    <label for="r_street" class="block text-xs font-semibold text-slate-700 mb-1">Street / House No. / Building <span class="text-rose-500">*</span></label>
                    <input type="text" id="r_street" name="r_street" value="<?= htmlspecialchars($resident['r_street'] ?? '') ?>" class="w-full px-3.5 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:outline-none" required>
                </div>
            </div>

            <!-- Section 3: Contact & Security -->
            <div class="space-y-4 pt-6 border-t border-slate-100">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-600"></span> Contact & Security Credentials
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="r_email" class="block text-xs font-semibold text-slate-700 mb-1">Email Address <span class="text-rose-500">*</span></label>
                        <input type="email" id="r_email" name="r_email" value="<?= htmlspecialchars($resident['r_email'] ?? '') ?>" class="w-full px-3.5 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:outline-none" required>
                    </div>

                    <div>
                        <label for="r_contact_number" class="block text-xs font-semibold text-slate-700 mb-1">Mobile Contact Number <span class="text-rose-500">*</span></label>
                        <input type="tel" id="r_contact_number" name="r_contact_number" value="<?= htmlspecialchars($resident['r_contact_number'] ?? '') ?>" pattern="(09|\+639)\d{9}" class="w-full px-3.5 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:outline-none" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div>
                        <label for="newPassword" class="block text-xs font-semibold text-slate-700 mb-1">New Password (Optional)</label>
                        <input type="password" id="newPassword" name="newPassword" minlength="6" class="w-full px-3.5 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:outline-none" placeholder="Leave blank to keep current password">
                    </div>

                    <div>
                        <label for="confirmNewPassword" class="block text-xs font-semibold text-slate-700 mb-1">Confirm New Password</label>
                        <input type="password" id="confirmNewPassword" name="confirmNewPassword" class="w-full px-3.5 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:outline-none" placeholder="Repeat new password">
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                <button type="submit" id="btnSaveResidentInfo" class="w-full sm:w-auto px-8 bg-primary-600 hover:bg-primary-700 text-white font-bold py-2.5 rounded-xl shadow-md hover:shadow-lg transition">
                    Save Profile Changes
                </button>
            </div>
        </form>
    </div>
</div>

<?php include "components/footer.php"; ?>

<script src="js/address_api.js"></script>
<script src="js/app.js"></script>

<script>
    function previewProfileImage(event) {
        var preview = document.getElementById('image-preview');
        var defaultImage = document.getElementById('default-image');
        if (event.target.files && event.target.files[0]) {
            var reader = new FileReader();
            reader.onload = function () {
                preview.src = reader.result;
                preview.style.display = 'block';
                if (defaultImage) defaultImage.style.display = 'none';
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    }

    $(document).ready(function() {
        if (window.initAddressDropdowns) {
            window.initAddressDropdowns({
                region: '#setting_region',
                province: '#setting_province',
                city: '#setting_city',
                barangay: '#setting_barangay',
                defaultRegion: <?= json_encode($resident['r_region'] ?? '') ?>,
                defaultProvince: <?= json_encode($resident['r_province'] ?? '') ?>,
                defaultCity: <?= json_encode($resident['r_municipality'] ?? '') ?>,
                defaultBarangay: <?= json_encode($resident['r_barangay'] ?? '') ?>
            });
        }
    });
</script>
