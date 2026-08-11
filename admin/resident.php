<?php 
include "components/header.php";

$stats = $db->getResidentStats();
?>

<!-- Page Header & Statistics Cards -->
<div class="mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Resident Management</h1>
            <p class="text-sm text-slate-500 mt-0.5">Manage verified barangay residents, credentials, and verification records</p>
        </div>
        <div>
            <button id="addResidentButton" class="inline-flex items-center justify-center gap-2 bg-primary-600 hover:bg-primary-700 active:bg-primary-800 text-white px-4 py-2.5 text-sm font-semibold rounded-xl shadow-sm hover:shadow transition duration-150">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                <span>Add New Resident</span>
            </button>
        </div>
    </div>

    <!-- Quick Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-slate-200/80 p-5 shadow-sm hover:shadow-md transition duration-200 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Residents</p>
                <h3 class="text-2xl font-extrabold text-slate-800 mt-1"><?= number_format($stats['total'] ?? 0); ?></h3>
                <p class="text-xs text-emerald-600 font-medium mt-1">● Active Accounts</p>
            </div>
            <div class="w-12 h-12 bg-primary-50 text-primary-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200/80 p-5 shadow-sm hover:shadow-md transition duration-200 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Male Residents</p>
                <h3 class="text-2xl font-extrabold text-blue-600 mt-1"><?= number_format($stats['male'] ?? 0); ?></h3>
                <p class="text-xs text-slate-500 font-medium mt-1">Registered Males</p>
            </div>
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200/80 p-5 shadow-sm hover:shadow-md transition duration-200 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Female Residents</p>
                <h3 class="text-2xl font-extrabold text-pink-600 mt-1"><?= number_format($stats['female'] ?? 0); ?></h3>
                <p class="text-xs text-slate-500 font-medium mt-1">Registered Females</p>
            </div>
            <div class="w-12 h-12 bg-pink-50 text-pink-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            </div>
        </div>
    </div>
</div>

<!-- Main Table Card -->
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 sm:p-6 mb-8">

    <!-- Filters & Search Toolbar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-100">
        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <!-- Search -->
            <div class="relative w-full sm:w-72">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" id="searchInput" class="pl-9 pr-3 py-2 border border-slate-300 rounded-xl text-sm w-full focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition" placeholder="Search by name, email, ID...">
            </div>

            <!-- Gender Filter -->
            <div class="w-full sm:w-auto">
                <select id="genderFilter" class="w-full sm:w-auto px-3 py-2 border border-slate-300 rounded-xl text-sm font-medium text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                    <option value="">All Genders</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
        </div>

        <div class="flex items-center gap-2 text-xs text-slate-500">
            <span>Live records updated</span>
            <button id="refreshTableBtn" type="button" class="p-1.5 hover:bg-slate-100 rounded-lg text-slate-600 transition" title="Refresh list">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            </button>
        </div>
    </div>

    <!-- Responsive Table -->
    <div class="overflow-x-auto -mx-2">
        <table id="userTable" class="w-full text-sm text-left text-slate-600">
            <thead class="bg-slate-50 text-slate-700 font-bold text-xs uppercase tracking-wider border-b border-slate-200">
                <tr>
                    <th class="p-3">ID</th>
                    <th class="p-3">Resident</th>
                    <th class="p-3">Demographics</th>
                    <th class="p-3">Contact</th>
                    <th class="p-3">Address</th>
                    <th class="p-3 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php include "backend/end-points/resident_list.php"; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ====================== ADD RESIDENT MODAL ====================== -->
<div id="addResidentModal" class="fixed inset-0 z-[60] bg-slate-900/60 backdrop-blur-sm overflow-y-auto px-4 py-6" style="display:none;">
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl mx-auto overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-primary-600 to-indigo-600 px-6 py-5 flex items-center justify-between text-white">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold">Register New Resident</h3>
                    <p class="text-xs text-primary-100">Enter personal details, credentials, and verification attachments</p>
                </div>
            </div>
            <button type="button" class="addResidentCloseModal text-white/80 hover:text-white p-1 rounded-lg hover:bg-white/10 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Spinner -->
        <div id="loadingSpinner" style="display:none;">
            <div class="absolute inset-0 bg-white/80 backdrop-blur-xs flex items-center justify-center z-20">
                <div class="flex flex-col items-center gap-3">
                    <div class="w-10 h-10 border-4 border-primary-600 border-t-transparent rounded-full animate-spin"></div>
                    <p class="text-xs font-semibold text-slate-600">Registering resident...</p>
                </div>
            </div>
        </div>

        <form id="frmAddResident" class="p-6 sm:p-8 space-y-6 max-h-[80vh] overflow-y-auto">
            <?= csrf_field(); ?>

            <!-- Section 1: Personal Details -->
            <div class="space-y-4">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-primary-600"></span> Personal Information
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label for="fname" class="block text-xs font-semibold text-slate-700 mb-1">First Name <span class="text-rose-500">*</span></label>
                        <input type="text" id="fname" name="fname" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:outline-none" placeholder="e.g. Juan" required>
                    </div>

                    <div>
                        <label for="mname" class="block text-xs font-semibold text-slate-700 mb-1">Middle Name</label>
                        <input type="text" id="mname" name="mname" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:outline-none" placeholder="e.g. Santos">
                    </div>

                    <div>
                        <label for="lname" class="block text-xs font-semibold text-slate-700 mb-1">Last Name <span class="text-rose-500">*</span></label>
                        <input type="text" id="lname" name="lname" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:outline-none" placeholder="e.g. Dela Cruz" required>
                    </div>

                    <div>
                        <label for="suffix" class="block text-xs font-semibold text-slate-700 mb-1">Suffix</label>
                        <input type="text" id="suffix" name="r_suffix" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:outline-none" placeholder="Jr., III (Optional)">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label for="Gender" class="block text-xs font-semibold text-slate-700 mb-1">Gender <span class="text-rose-500">*</span></label>
                        <select id="Gender" name="Gender" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:outline-none bg-white" required>
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>

                    <div>
                        <label for="civil_status" class="block text-xs font-semibold text-slate-700 mb-1">Civil Status <span class="text-rose-500">*</span></label>
                        <select id="civil_status" name="r_civil_status" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:outline-none bg-white" required>
                            <option value="">Select Civil Status</option>
                            <option value="Single">Single</option>
                            <option value="Married">Married</option>
                            <option value="Widowed">Widowed</option>
                            <option value="Divorced">Divorced</option>
                        </select>
                    </div>

                    <div>
                        <label for="citizenship" class="block text-xs font-semibold text-slate-700 mb-1">Citizenship <span class="text-rose-500">*</span></label>
                        <input type="text" id="citizenship" name="r_citizenship" value="Filipino" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:outline-none" required>
                    </div>

                    <div>
                        <label for="bday" class="block text-xs font-semibold text-slate-700 mb-1">Birthday <span class="text-rose-500">*</span></label>
                        <input type="date" id="bday" name="r_bday" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:outline-none" required>
                    </div>
                </div>
            </div>

            <!-- Section 2: Address (Philippine Cascading) -->
            <div class="space-y-4 pt-4 border-t border-slate-100">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-indigo-600"></span> Residential Address
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label for="region" class="block text-xs font-semibold text-slate-700 mb-1">Region <span class="text-rose-500">*</span></label>
                        <select id="region" name="r_region" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:outline-none bg-white" required>
                            <option value="">Select Region</option>
                        </select>
                    </div>

                    <div>
                        <label for="province" class="block text-xs font-semibold text-slate-700 mb-1">Province <span class="text-rose-500">*</span></label>
                        <select id="province" name="r_province" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:outline-none bg-white" required>
                            <option value="">Select Province</option>
                        </select>
                    </div>

                    <div>
                        <label for="city" class="block text-xs font-semibold text-slate-700 mb-1">City / Municipality <span class="text-rose-500">*</span></label>
                        <select id="city" name="r_city" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:outline-none bg-white" required>
                            <option value="">Select City</option>
                        </select>
                    </div>

                    <div>
                        <label for="barangay" class="block text-xs font-semibold text-slate-700 mb-1">Barangay <span class="text-rose-500">*</span></label>
                        <select id="barangay" name="r_barangay" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:outline-none bg-white" required>
                            <option value="">Select Barangay</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="street" class="block text-xs font-semibold text-slate-700 mb-1">Street / House No. / Building <span class="text-rose-500">*</span></label>
                    <input type="text" id="street" name="r_street" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:outline-none" placeholder="e.g. Blk 12 Lot 34 Sunflower St." required>
                </div>
            </div>

            <!-- Section 3: Contact & Account Credentials -->
            <div class="space-y-4 pt-4 border-t border-slate-100">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-600"></span> Account & Credentials
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label for="contact_number" class="block text-xs font-semibold text-slate-700 mb-1">Contact Number <span class="text-rose-500">*</span></label>
                        <input type="tel" id="contact_number" name="r_contact_number" pattern="(09|\+639)\d{9}" placeholder="09123456789" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:outline-none" required>
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Email Address <span class="text-rose-500">*</span></label>
                        <input type="email" id="email" name="r_email" placeholder="resident@email.com" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:outline-none" required>
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">Password <span class="text-rose-500">*</span></label>
                        <input type="password" id="password" name="r_password" minlength="6" placeholder="Min. 6 characters" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:outline-none" required>
                    </div>

                    <div>
                        <label for="confirm_Password" class="block text-xs font-semibold text-slate-700 mb-1">Confirm Password <span class="text-rose-500">*</span></label>
                        <input type="password" id="confirm_Password" name="c_Password" placeholder="Repeat password" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:outline-none" required>
                    </div>
                </div>
            </div>

            <!-- Section 4: Attachments / Uploads -->
            <div class="space-y-4 pt-4 border-t border-slate-100">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-600"></span> Verification Documents
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Profile Photo -->
                    <div class="p-4 border-2 border-dashed border-slate-200 rounded-2xl text-center bg-slate-50/50 hover:bg-slate-50 transition">
                        <label for="profileImg" class="cursor-pointer block">
                            <svg class="w-8 h-8 mx-auto text-slate-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <p class="text-xs font-bold text-slate-700">Profile Photo <span class="text-rose-500">*</span></p>
                            <p class="text-[11px] text-slate-400 mt-0.5">JPG, PNG, WEBP up to 10MB</p>
                        </label>
                        <input type="file" id="profileImg" name="profileImg" accept="image/*" class="hidden" required>
                        <div id="profileImgPreview" class="mt-3 flex justify-center"></div>
                    </div>

                    <!-- Valid ID Document -->
                    <div class="p-4 border-2 border-dashed border-slate-200 rounded-2xl text-center bg-slate-50/50 hover:bg-slate-50 transition">
                        <label for="validId" class="cursor-pointer block">
                            <svg class="w-8 h-8 mx-auto text-slate-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <p class="text-xs font-bold text-slate-700">Valid Government / Student ID <span class="text-rose-500">*</span></p>
                            <p class="text-[11px] text-slate-400 mt-0.5">JPG, PNG, PDF up to 10MB</p>
                        </label>
                        <input type="file" id="validId" name="validId" accept="image/*,application/pdf" class="hidden" required>
                        <div id="validIdPreview" class="mt-3 flex justify-center"></div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" class="addResidentCloseModal px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-sm transition">Cancel</button>
                <button id="AddResident" type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl text-sm shadow-md hover:shadow-lg transition">Save Resident</button>
            </div>
        </form>
    </div>
</div>

<!-- ====================== EDIT RESIDENT MODAL ====================== -->
<div id="editResidentModal" class="fixed inset-0 z-[60] bg-slate-900/60 backdrop-blur-sm overflow-y-auto px-4 py-6" style="display:none;">
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl mx-auto overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-5 flex items-center justify-between text-white">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold">Update Resident Information</h3>
                    <p class="text-xs text-emerald-100">Modify personal data, address, contact, or credentials</p>
                </div>
            </div>
            <button type="button" id="EditcloseResidentModal" class="text-white/80 hover:text-white p-1 rounded-lg hover:bg-white/10 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Spinner -->
        <div id="editResidentloadingSpinner" style="display:none;">
            <div class="absolute inset-0 bg-white/80 backdrop-blur-xs flex items-center justify-center z-20">
                <div class="flex flex-col items-center gap-3">
                    <div class="w-10 h-10 border-4 border-emerald-600 border-t-transparent rounded-full animate-spin"></div>
                    <p class="text-xs font-semibold text-slate-600">Updating resident...</p>
                </div>
            </div>
        </div>

        <form id="frmEditResident" class="p-6 sm:p-8 space-y-6 max-h-[80vh] overflow-y-auto">
            <?= csrf_field(); ?>
            <input type="hidden" id="r_id" name="r_id" required>

            <!-- Personal Info -->
            <div class="space-y-4">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-600"></span> Personal Details
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label for="r_fname" class="block text-xs font-semibold text-slate-700 mb-1">First Name <span class="text-rose-500">*</span></label>
                        <input type="text" id="r_fname" name="fname" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none" required>
                    </div>

                    <div>
                        <label for="r_mname" class="block text-xs font-semibold text-slate-700 mb-1">Middle Name</label>
                        <input type="text" id="r_mname" name="mname" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="r_lname" class="block text-xs font-semibold text-slate-700 mb-1">Last Name <span class="text-rose-500">*</span></label>
                        <input type="text" id="r_lname" name="lname" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none" required>
                    </div>

                    <div>
                        <label for="r_suffix" class="block text-xs font-semibold text-slate-700 mb-1">Suffix</label>
                        <input type="text" id="r_suffix" name="r_suffix" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label for="r_gender" class="block text-xs font-semibold text-slate-700 mb-1">Gender</label>
                        <select id="r_gender" name="Gender" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none bg-white">
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>

                    <div>
                        <label for="r_civil_status" class="block text-xs font-semibold text-slate-700 mb-1">Civil Status</label>
                        <select id="r_civil_status" name="r_civil_status" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none bg-white">
                            <option value="Single">Single</option>
                            <option value="Married">Married</option>
                            <option value="Widowed">Widowed</option>
                            <option value="Divorced">Divorced</option>
                        </select>
                    </div>

                    <div>
                        <label for="r_citizenship" class="block text-xs font-semibold text-slate-700 mb-1">Citizenship</label>
                        <input type="text" id="r_citizenship" name="r_citizenship" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="r_bday" class="block text-xs font-semibold text-slate-700 mb-1">Birthday</label>
                        <input type="date" id="r_bday" name="r_bday" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Address (Cascading Dropdowns) -->
            <div class="space-y-4 pt-4 border-t border-slate-100">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-teal-600"></span> Address Details
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label for="r_edit_region" class="block text-xs font-semibold text-slate-700 mb-1">Region</label>
                        <select id="r_edit_region" name="r_region" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none bg-white"></select>
                    </div>

                    <div>
                        <label for="r_edit_province" class="block text-xs font-semibold text-slate-700 mb-1">Province</label>
                        <select id="r_edit_province" name="r_province" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none bg-white"></select>
                    </div>

                    <div>
                        <label for="r_edit_city" class="block text-xs font-semibold text-slate-700 mb-1">City / Municipality</label>
                        <select id="r_edit_city" name="r_city" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none bg-white"></select>
                    </div>

                    <div>
                        <label for="r_edit_barangay" class="block text-xs font-semibold text-slate-700 mb-1">Barangay</label>
                        <select id="r_edit_barangay" name="r_barangay" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none bg-white"></select>
                    </div>
                </div>

                <div>
                    <label for="r_street" class="block text-xs font-semibold text-slate-700 mb-1">Street / House No.</label>
                    <input type="text" id="r_street" name="r_street" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
            </div>

            <!-- Contact & Password Update -->
            <div class="space-y-4 pt-4 border-t border-slate-100">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-slate-600"></span> Contact & Security
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label for="r_contact_number" class="block text-xs font-semibold text-slate-700 mb-1">Contact Number</label>
                        <input type="tel" id="r_contact_number" name="r_contact_number" pattern="(09|\+639)\d{9}" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="r_email" class="block text-xs font-semibold text-slate-700 mb-1">Email Address <span class="text-rose-500">*</span></label>
                        <input type="email" id="r_email" name="r_email" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none" required>
                    </div>

                    <div>
                        <label for="r_password" class="block text-xs font-semibold text-slate-700 mb-1">New Password (Optional)</label>
                        <input type="password" id="r_password" name="r_password" minlength="6" placeholder="Leave blank to keep" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="c_Password" class="block text-xs font-semibold text-slate-700 mb-1">Confirm New Password</label>
                        <input type="password" id="c_Password" name="c_Password" placeholder="Confirm password" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Documents Optional Update -->
            <div class="space-y-4 pt-4 border-t border-slate-100">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-600"></span> Documents (Optional Update)
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="p-4 border border-slate-200 rounded-2xl bg-slate-50/50">
                        <label for="r_profile" class="block text-xs font-bold text-slate-700 mb-1">Change Profile Image</label>
                        <input type="file" id="r_profile" name="profileImg" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                        <div id="r_profilePreview" class="mt-2"></div>
                    </div>

                    <div class="p-4 border border-slate-200 rounded-2xl bg-slate-50/50">
                        <label for="r_valid_ids" class="block text-xs font-bold text-slate-700 mb-1">Change Valid ID</label>
                        <input type="file" id="r_valid_ids" name="validId" accept="image/*,application/pdf" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                        <div id="r_valid_idsPreview" class="mt-2"></div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" id="btnCancelEdit" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-sm transition">Cancel</button>
                <button id="EditResident" type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl text-sm shadow-md hover:shadow-lg transition">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- ====================== VIEW RESIDENT DETAILS MODAL ====================== -->
<div id="viewResidentModal" class="fixed inset-0 z-[60] bg-slate-900/60 backdrop-blur-sm overflow-y-auto px-4 py-6" style="display:none;">
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-auto overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-slate-900 to-indigo-900 p-6 text-white flex items-start justify-between">
            <div class="flex items-center gap-4">
                <div id="view_avatarContainer" class="w-16 h-16 rounded-2xl bg-white/10 border border-white/20 flex items-center justify-center text-2xl font-bold overflow-hidden shadow-inner flex-shrink-0">
                    <span id="view_avatarInitials">R</span>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 id="view_fullname" class="text-xl font-bold">Resident Name</h3>
                        <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">Verified</span>
                    </div>
                    <p id="view_email" class="text-xs text-slate-300 mt-0.5">email@example.com</p>
                    <p id="view_contact" class="text-xs text-slate-400 mt-0.5">09123456789</p>
                </div>
            </div>
            <button type="button" class="closeViewModal text-white/70 hover:text-white p-1 rounded-lg hover:bg-white/10 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Body Details -->
        <div class="p-6 space-y-6 max-h-[75vh] overflow-y-auto">
            <!-- Grid details -->
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 p-4 rounded-xl bg-slate-50 border border-slate-100">
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Gender</span>
                    <p id="view_gender" class="text-sm font-semibold text-slate-800 mt-0.5">—</p>
                </div>
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Civil Status</span>
                    <p id="view_civil_status" class="text-sm font-semibold text-slate-800 mt-0.5">—</p>
                </div>
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Citizenship</span>
                    <p id="view_citizenship" class="text-sm font-semibold text-slate-800 mt-0.5">Filipino</p>
                </div>
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Birthday</span>
                    <p id="view_bday" class="text-sm font-semibold text-slate-800 mt-0.5">—</p>
                </div>
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Age</span>
                    <p id="view_age" class="text-sm font-semibold text-slate-800 mt-0.5">—</p>
                </div>
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Resident ID</span>
                    <p id="view_id" class="text-sm font-bold text-primary-600 mt-0.5">#—</p>
                </div>
            </div>

            <!-- Complete Address -->
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Registered Address</span>
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-sm font-medium text-slate-700 flex items-start gap-2.5">
                    <svg class="w-4 h-4 text-primary-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span id="view_address">—</span>
                </div>
            </div>

            <!-- Valid ID Section -->
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-2">Submitted Valid ID Document</span>
                <div id="view_validIdContainer" class="p-4 rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-center min-h-[140px]">
                    <p class="text-xs text-slate-400">No document available</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
            <button type="button" class="closeViewModal px-5 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold rounded-xl text-sm transition">Close</button>
            <button type="button" id="view_editTrigger" class="px-5 py-2 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl text-sm transition">Edit Resident</button>
        </div>
    </div>
</div>

<!-- ====================== DELETE CONFIRMATION MODAL ====================== -->
<div id="deleteConfirmationModal" class="fixed inset-0 z-[70] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm opacity-0 invisible transition-all duration-200 p-4">
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 space-y-4 animate-in fade-in zoom-in-95 duration-200">
        
        <div id="DeleteResidentloadingSpinner" style="display:none;">
            <div class="absolute inset-0 bg-white/80 backdrop-blur-xs flex items-center justify-center rounded-2xl z-10">
                <div class="w-8 h-8 border-4 border-rose-600 border-t-transparent rounded-full animate-spin"></div>
            </div>
        </div>

        <input type="hidden" id="TargetdelResidentId" name="TargetdelResidentId">
        
        <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center mx-auto sm:mx-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        </div>

        <div class="text-center sm:text-left">
            <h3 class="text-lg font-bold text-slate-900">Archive Resident Record</h3>
            <p class="text-sm text-slate-500 mt-1.5">
                Are you sure you want to remove <span id="delResidentName" class="font-bold text-slate-800">this resident</span>? Their active account will be archived.
            </p>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <button type="button" class="cancelDeleteResident px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-semibold transition">Cancel</button>
            <button id="confirmDeleteResident" type="button" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-sm font-semibold shadow-md transition">Confirm Archive</button>
        </div>
    </div>
</div>

<?php include "components/footer.php";?>

<script src="js/address_api.js"></script>
<script src="js/resident.js"></script>

<script>
$(document).ready(function () {
    if ($.fn.DataTable) {
        $.fn.dataTable.ext.errMode = 'none';

        var residentTable = $('#userTable').DataTable({
            responsive: true,
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50],
            language: {
                emptyTable: '<div class="py-6 text-center text-slate-500"><svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg><p class="font-medium text-slate-600">No resident records found.</p><p class="text-xs text-slate-400 mt-0.5">Click "Add Resident" to register a new resident.</p></div>',
                zeroRecords: 'No matching residents found.',
                info: 'Showing _START_ to _END_ of _TOTAL_ residents',
                infoEmpty: 'No residents available',
                search: '',
                searchPlaceholder: 'Quick search...'
            },
            columnDefs: [
                { responsivePriority: 1, targets: 1 }, // Resident Details
                { responsivePriority: 2, targets: 5 }, // Actions
                { responsivePriority: 3, targets: 2 }, // Demographics
                { responsivePriority: 4, targets: 3 }, // Contact
                { responsivePriority: 5, targets: 4 }, // Address
                { responsivePriority: 6, targets: 0 }  // ID
            ],
            dom: '<"flex flex-wrap items-center justify-between gap-3 mb-4"l>rtip'
        });

        // Search Input Binding
        $('#searchInput').off('input').on('input', function () {
            residentTable.search($(this).val()).draw();
        });

        // Gender Filter Binding
        $('#genderFilter').on('change', function () {
            var selectedGender = $(this).val();
            residentTable.column(2).search(selectedGender).draw();
        });

        // Refresh table button
        $('#refreshTableBtn').on('click', function() {
            location.reload();
        });
    }
});
</script>
