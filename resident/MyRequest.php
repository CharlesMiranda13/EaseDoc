<?php 
include "components/header.php";
?>

<!-- Header & Quick Actions -->
<div class="mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">My Document Requests</h1>
            <p class="text-sm text-slate-500 mt-0.5">Submit new applications and track real-time processing status</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-primary-600 to-indigo-600 flex items-center justify-center text-white font-bold text-sm shadow-sm flex-shrink-0">
                <?= strtoupper(substr($resident['r_fname'] ?? 'R', 0, 1)); ?>
            </div>
            <div>
                <p class="text-sm font-bold text-slate-800 leading-tight"><?= $residentName; ?></p>
                <p class="text-xs text-emerald-600 font-medium">● Verified Account</p>
            </div>
        </div>
    </div>

    <!-- Request Buttons Grid -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-sm">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Request Official Document</p>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <button id="OpenBrgyIdModal" type="button" class="flex flex-col sm:flex-row items-center justify-center sm:justify-start gap-2.5 p-3 rounded-xl bg-primary-50 hover:bg-primary-100 text-primary-700 font-bold text-xs sm:text-sm border border-primary-200/60 transition group shadow-xs">
                <svg class="w-5 h-5 text-primary-600 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 012-2h2a2 2 0 012 2v2m-6 0h6"></path></svg>
                <span>Barangay ID</span>
            </button>

            <button id="OpenClearanceModal" type="button" class="flex flex-col sm:flex-row items-center justify-center sm:justify-start gap-2.5 p-3 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold text-xs sm:text-sm border border-emerald-200/60 transition group shadow-xs">
                <svg class="w-5 h-5 text-emerald-600 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span>Clearance</span>
            </button>

            <button id="OpenResidencyModal" type="button" class="flex flex-col sm:flex-row items-center justify-center sm:justify-start gap-2.5 p-3 rounded-xl bg-violet-50 hover:bg-violet-100 text-violet-700 font-bold text-xs sm:text-sm border border-violet-200/60 transition group shadow-xs">
                <svg class="w-5 h-5 text-violet-600 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span>Residency</span>
            </button>

            <button id="OpenIndigencyModal" type="button" class="flex flex-col sm:flex-row items-center justify-center sm:justify-start gap-2.5 p-3 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 font-bold text-xs sm:text-sm border border-amber-200/60 transition group shadow-xs">
                <svg class="w-5 h-5 text-amber-600 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                <span>Indigency</span>
            </button>
        </div>
    </div>
</div>

<!-- Requests Table Card -->
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 sm:p-6 mb-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-100">
        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <div class="relative w-full sm:w-72">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" id="searchInput" class="pl-9 pr-3 py-2 border border-slate-300 rounded-xl text-sm w-full focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition" placeholder="Search my requests...">
            </div>

            <!-- Status Filter -->
            <div class="w-full sm:w-auto">
                <select id="statusFilter" class="w-full sm:w-auto px-3 py-2 border border-slate-300 rounded-xl text-sm font-medium text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                    <option value="">All Statuses</option>
                    <option value="Pending">Pending</option>
                    <option value="Approved">Approved</option>
                    <option value="Shipped">Shipped</option>
                    <option value="Delivered">Delivered</option>
                    <option value="Rejected">Rejected</option>
                    <option value="Canceled">Canceled</option>
                </select>
            </div>
        </div>

        <span class="text-xs text-slate-400">Showing all records for your account</span>
    </div>

    <div class="overflow-x-auto -mx-2">
        <table id="userTable" class="w-full text-sm text-left text-slate-600">
            <thead class="bg-slate-50 text-slate-700 font-bold text-xs uppercase tracking-wider border-b border-slate-200">
                <tr>
                    <th class="p-3">Tracking No.</th>
                    <th class="p-3">Document Type</th>
                    <th class="p-3">Purpose</th>
                    <th class="p-3">Address</th>
                    <th class="p-3">Price & Payment</th>
                    <th class="p-3">Date Filed</th>
                    <th class="p-3">Status</th>
                    <th class="p-3">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php include "backend/end-points/request_list.php"; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ====================== MODAL: BARANGAY ID ====================== -->
<div id="BrgyIdModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm overflow-y-auto px-4 py-6" style="display:none;">
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-auto overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        
        <div class="bg-gradient-to-r from-primary-600 to-indigo-600 px-6 py-5 flex items-center justify-between text-white">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 012-2h2a2 2 0 012 2v2m-6 0h6"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold">Request Barangay ID</h3>
                    <p class="text-xs text-primary-100">Upload 1x1 photo, digital signature, and proof documents</p>
                </div>
            </div>
            <button type="button" class="closeModal text-white/80 hover:text-white p-1 rounded-lg hover:bg-white/10 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div id="loadingSpinner_BrgyId" style="display:none;">
            <div class="absolute inset-0 bg-white/80 backdrop-blur-xs flex items-center justify-center z-20">
                <div class="w-10 h-10 border-4 border-primary-600 border-t-transparent rounded-full animate-spin"></div>
            </div>
        </div>

        <form id="frmRequestBrgyId" class="p-6 sm:p-8 space-y-5 max-h-[80vh] overflow-y-auto">
            <?= csrf_field(); ?>
            <input type="hidden" value="<?= htmlspecialchars((string)($r_id ?? '')) ?>" name="r_id">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-3 border border-slate-200 rounded-xl bg-slate-50/50">
                    <label for="1x1pic_BrgyId" class="block text-xs font-bold text-slate-700 mb-1">1x1 Photo (White Background) <span class="text-rose-500">*</span></label>
                    <input type="file" id="1x1pic_BrgyId" name="1x1pic_BrgyId" accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700" required>
                    <div id="1x1picPreview_BrgyId" class="mt-2 flex justify-center"></div>
                </div>

                <div class="p-3 border border-slate-200 rounded-xl bg-slate-50/50">
                    <label for="signature_BrgyId" class="block text-xs font-bold text-slate-700 mb-1">Digital Signature <span class="text-rose-500">*</span></label>
                    <input type="file" id="signature_BrgyId" name="signature_BrgyId" accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700" required>
                    <div id="signaturePreview_BrgyId" class="mt-2 flex justify-center"></div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-3 border border-slate-200 rounded-xl bg-slate-50/50">
                    <label for="validId_BrgyId" class="block text-xs font-bold text-slate-700 mb-1">Valid ID Document <span class="text-rose-500">*</span></label>
                    <input type="file" id="validId_BrgyId" name="validId_BrgyId" accept="image/*,application/pdf" class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700" required>
                    <div id="validIdPreview_BrgyId" class="mt-2 flex justify-center"></div>
                </div>

                <div class="p-3 border border-slate-200 rounded-xl bg-slate-50/50">
                    <label for="proofResidency_BrgyId" class="block text-xs font-bold text-slate-700 mb-1">Proof of Residency (Billing/Cert) <span class="text-rose-500">*</span></label>
                    <input type="file" id="proofResidency_BrgyId" name="proofResidency_BrgyId" accept="image/*,application/pdf" class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700" required>
                    <div id="proofResidencyPreview_BrgyId" class="mt-2 flex justify-center"></div>
                </div>
            </div>

            <div>
                <label for="purpose_BrgyId" class="block text-xs font-bold text-slate-700 mb-1">Purpose of ID Request <span class="text-rose-500">*</span></label>
                <textarea id="purpose_BrgyId" name="purpose_BrgyId" rows="2" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:outline-none" placeholder="e.g. Resident identification, verification, local government requirements" required></textarea>
            </div>

            <div>
                <label for="addressForm_BrgyId" class="block text-xs font-bold text-slate-700 mb-1">Delivery / Residence Address <span class="text-rose-500">*</span></label>
                <textarea id="addressForm_BrgyId" name="addressForm_BrgyId" rows="2" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:outline-none" required><?= htmlspecialchars($address ?? '') ?></textarea>
            </div>

            <div>
                <label for="payment_BrgyId" class="block text-xs font-bold text-slate-700 mb-1">Payment Method</label>
                <select name="payment_BrgyId" id="payment_BrgyId" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:outline-none bg-white">
                    <option value="Cash on Delivery">Cash on Delivery (COD upon release)</option>
                </select>
            </div>

            <!-- Price Breakdown -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-1.5 text-xs">
                <div class="flex justify-between text-slate-600">
                    <span>Document Fee:</span>
                    <span id="documentPrice_BrgyId" data-documentPrice="50.00" class="font-bold text-slate-800">₱ 50.00</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Processing & Delivery:</span>
                    <span id="shippingFee_BrgyId" data-shippingFee="0.00" class="font-bold text-slate-800">₱ 0.00</span>
                </div>
                <div class="flex justify-between text-sm font-extrabold text-slate-900 border-t border-slate-200 pt-2">
                    <span>Total Amount:</span>
                    <span id="totalPrice_BrgyId" data-totalPrice="50.00" class="text-primary-600">₱ 50.00</span>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" class="closeModal px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-semibold transition">Cancel</button>
                <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-sm font-semibold shadow-md transition">Submit Request</button>
            </div>
        </form>
    </div>
</div>

<!-- ====================== MODAL: CLEARANCE ====================== -->
<div id="clearanceModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm overflow-y-auto px-4 py-6" style="display:none;">
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-auto overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        
        <div class="bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-5 flex items-center justify-between text-white">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold">Request Barangay Clearance</h3>
                    <p class="text-xs text-emerald-100">Submit required valid ID and proof of residence</p>
                </div>
            </div>
            <button type="button" class="closeModal text-white/80 hover:text-white p-1 rounded-lg hover:bg-white/10 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div id="loadingSpinner_Clearance" style="display:none;">
            <div class="absolute inset-0 bg-white/80 backdrop-blur-xs flex items-center justify-center z-20">
                <div class="w-10 h-10 border-4 border-emerald-600 border-t-transparent rounded-full animate-spin"></div>
            </div>
        </div>

        <form id="frmRequestClearance" class="p-6 sm:p-8 space-y-5 max-h-[80vh] overflow-y-auto">
            <?= csrf_field(); ?>
            <input type="hidden" value="<?= htmlspecialchars((string)($r_id ?? '')) ?>" name="r_id">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-3 border border-slate-200 rounded-xl bg-slate-50/50">
                    <label for="validId_Clearance" class="block text-xs font-bold text-slate-700 mb-1">Valid ID Document <span class="text-rose-500">*</span></label>
                    <input type="file" id="validId_Clearance" name="validId" accept="image/*,application/pdf" class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700" required>
                    <div id="validIdPreview_Clearance" class="mt-2 flex justify-center"></div>
                </div>

                <div class="p-3 border border-slate-200 rounded-xl bg-slate-50/50">
                    <label for="proofResidency_Clearance" class="block text-xs font-bold text-slate-700 mb-1">Proof of Residency <span class="text-rose-500">*</span></label>
                    <input type="file" id="proofResidency_Clearance" name="proofResidency" accept="image/*,application/pdf" class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700" required>
                    <div id="proofResidencyPreview_Clearance" class="mt-2 flex justify-center"></div>
                </div>
            </div>

            <div>
                <label for="purpose_clearance" class="block text-xs font-bold text-slate-700 mb-1">Purpose of Clearance <span class="text-rose-500">*</span></label>
                <textarea id="purpose_clearance" name="purpose" rows="2" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none" placeholder="e.g. Job application, Business permit, Postal ID requirement" required></textarea>
            </div>

            <div>
                <label for="addressForm_clearance" class="block text-xs font-bold text-slate-700 mb-1">Address <span class="text-rose-500">*</span></label>
                <textarea id="addressForm_clearance" name="addressForm" rows="2" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none" required><?= htmlspecialchars($address ?? '') ?></textarea>
            </div>

            <div>
                <label for="payment_clearance" class="block text-xs font-bold text-slate-700 mb-1">Payment Method</label>
                <select name="payment" id="payment_clearance" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none bg-white">
                    <option value="Cash on Delivery">Cash on Delivery (COD)</option>
                </select>
            </div>

            <!-- Price Breakdown -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-1.5 text-xs">
                <div class="flex justify-between text-slate-600">
                    <span>Document Fee:</span>
                    <span id="documentPrice_Clearance" data-documentPrice="50.00" class="font-bold text-slate-800">₱ 50.00</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Processing & Delivery:</span>
                    <span id="shippingFee_Clearance" data-shippingFee="0.00" class="font-bold text-slate-800">₱ 0.00</span>
                </div>
                <div class="flex justify-between text-sm font-extrabold text-slate-900 border-t border-slate-200 pt-2">
                    <span>Total Amount:</span>
                    <span id="totalPrice_Clearance" data-totalPrice="50.00" class="text-emerald-600">₱ 50.00</span>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" class="closeModal px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-semibold transition">Cancel</button>
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-semibold shadow-md transition">Submit Request</button>
            </div>
        </form>
    </div>
</div>

<!-- ====================== MODAL: RESIDENCY ====================== -->
<div id="residencyModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm overflow-y-auto px-4 py-6" style="display:none;">
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-auto overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        
        <div class="bg-gradient-to-r from-violet-600 to-purple-600 px-6 py-5 flex items-center justify-between text-white">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold">Request Certificate of Residency</h3>
                    <p class="text-xs text-violet-100">Verify your current residential status in the barangay</p>
                </div>
            </div>
            <button type="button" class="closeModal text-white/80 hover:text-white p-1 rounded-lg hover:bg-white/10 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div id="loadingSpinner_Residency" style="display:none;">
            <div class="absolute inset-0 bg-white/80 backdrop-blur-xs flex items-center justify-center z-20">
                <div class="w-10 h-10 border-4 border-violet-600 border-t-transparent rounded-full animate-spin"></div>
            </div>
        </div>

        <form id="frmRequest_Residency" class="p-6 sm:p-8 space-y-5 max-h-[80vh] overflow-y-auto">
            <?= csrf_field(); ?>
            <input type="hidden" value="<?= htmlspecialchars((string)($r_id ?? '')) ?>" name="r_id">

            <div class="p-3 border border-slate-200 rounded-xl bg-slate-50/50">
                <label for="validId_Residency" class="block text-xs font-bold text-slate-700 mb-1">Valid ID Document <span class="text-rose-500">*</span></label>
                <input type="file" id="validId_Residency" name="validId" accept="image/*,application/pdf" class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-violet-50 file:text-violet-700" required>
                <div id="validIdPreview_Residency" class="mt-2 flex justify-center"></div>
            </div>

            <div>
                <label for="purpose_residency" class="block text-xs font-bold text-slate-700 mb-1">Purpose of Certificate <span class="text-rose-500">*</span></label>
                <textarea id="purpose_residency" name="purpose" rows="2" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-violet-500 focus:outline-none" placeholder="e.g. Bank account opening, Utility connection, School transfer" required></textarea>
            </div>

            <div>
                <label for="addressForm_residency" class="block text-xs font-bold text-slate-700 mb-1">Address <span class="text-rose-500">*</span></label>
                <textarea id="addressForm_residency" name="addressForm" rows="2" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-violet-500 focus:outline-none" required><?= htmlspecialchars($address ?? '') ?></textarea>
            </div>

            <div>
                <label for="payment_residency" class="block text-xs font-bold text-slate-700 mb-1">Payment Method</label>
                <select name="payment" id="payment_residency" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-violet-500 focus:outline-none bg-white">
                    <option value="Cash on Delivery">Cash on Delivery (COD)</option>
                </select>
            </div>

            <!-- Price Breakdown -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-1.5 text-xs">
                <div class="flex justify-between text-slate-600">
                    <span>Document Fee:</span>
                    <span id="documentPrice_Residency" data-documentPrice="50.00" class="font-bold text-slate-800">₱ 50.00</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Processing & Delivery:</span>
                    <span id="shippingFee_Residency" data-shippingFee="0.00" class="font-bold text-slate-800">₱ 0.00</span>
                </div>
                <div class="flex justify-between text-sm font-extrabold text-slate-900 border-t border-slate-200 pt-2">
                    <span>Total Amount:</span>
                    <span id="totalPrice_Residency" data-totalPrice="50.00" class="text-violet-600">₱ 50.00</span>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" class="closeModal px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-semibold transition">Cancel</button>
                <button type="submit" class="px-6 py-2.5 bg-violet-600 hover:bg-violet-700 text-white rounded-xl text-sm font-semibold shadow-md transition">Submit Request</button>
            </div>
        </form>
    </div>
</div>

<!-- ====================== MODAL: INDIGENCY ====================== -->
<div id="indigencyModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm overflow-y-auto px-4 py-6" style="display:none;">
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-auto overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        
        <div class="bg-gradient-to-r from-amber-600 to-orange-600 px-6 py-5 flex items-center justify-between text-white">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold">Request Certificate of Indigency</h3>
                    <p class="text-xs text-amber-100">For scholarship, medical assistance, or social welfare</p>
                </div>
            </div>
            <button type="button" class="closeModal text-white/80 hover:text-white p-1 rounded-lg hover:bg-white/10 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div id="loadingSpinner_Indigency" style="display:none;">
            <div class="absolute inset-0 bg-white/80 backdrop-blur-xs flex items-center justify-center z-20">
                <div class="w-10 h-10 border-4 border-amber-600 border-t-transparent rounded-full animate-spin"></div>
            </div>
        </div>

        <form id="frmRequest_Indigency" class="p-6 sm:p-8 space-y-5 max-h-[80vh] overflow-y-auto">
            <?= csrf_field(); ?>
            <input type="hidden" value="<?= htmlspecialchars((string)($r_id ?? '')) ?>" name="r_id">

            <div class="p-3 border border-slate-200 rounded-xl bg-slate-50/50">
                <label for="validId_Indigency" class="block text-xs font-bold text-slate-700 mb-1">Valid ID / Student ID <span class="text-rose-500">*</span></label>
                <input type="file" id="validId_Indigency" name="validId" accept="image/*,application/pdf" class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-50 file:text-amber-700" required>
                <div id="validIdPreview_Indigency" class="mt-2 flex justify-center"></div>
            </div>

            <div>
                <label for="purpose_indigency" class="block text-xs font-bold text-slate-700 mb-1">Purpose of Request <span class="text-rose-500">*</span></label>
                <textarea id="purpose_indigency" name="purpose" rows="2" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:outline-none" placeholder="e.g. Educational scholarship, Hospital medical assistance, Public attorney services" required></textarea>
            </div>

            <div>
                <label for="addressForm_indigency" class="block text-xs font-bold text-slate-700 mb-1">Address <span class="text-rose-500">*</span></label>
                <textarea id="addressForm_indigency" name="addressForm" rows="2" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:outline-none" required><?= htmlspecialchars($address ?? '') ?></textarea>
            </div>

            <div>
                <label for="payment_indigency" class="block text-xs font-bold text-slate-700 mb-1">Payment Method</label>
                <select name="payment" id="payment_indigency" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:outline-none bg-white">
                    <option value="Cash on Delivery">Cash on Delivery (COD)</option>
                </select>
            </div>

            <!-- Price Breakdown -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-1.5 text-xs">
                <div class="flex justify-between text-slate-600">
                    <span>Document Fee:</span>
                    <span id="documentPrice_Indigency" data-documentPrice="50.00" class="font-bold text-slate-800">₱ 50.00</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Processing & Delivery:</span>
                    <span id="shippingFee_Indigency" data-shippingFee="0.00" class="font-bold text-slate-800">₱ 0.00</span>
                </div>
                <div class="flex justify-between text-sm font-extrabold text-slate-900 border-t border-slate-200 pt-2">
                    <span>Total Amount:</span>
                    <span id="totalPrice_Indigency" data-totalPrice="50.00" class="text-amber-600">₱ 50.00</span>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" class="closeModal px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-semibold transition">Cancel</button>
                <button type="submit" class="px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-sm font-semibold shadow-md transition">Submit Request</button>
            </div>
        </form>
    </div>
</div>

<!-- ====================== MODAL: VIEW REQUEST DETAILS ====================== -->
<div id="viewRequestModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm overflow-y-auto px-4 py-6" style="display:none;">
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-auto overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        <div class="bg-gradient-to-r from-slate-900 to-indigo-900 p-6 text-white flex items-start justify-between">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Document Request</span>
                <h3 id="req_detail_type" class="text-xl font-bold">Barangay Clearance</h3>
                <p id="req_detail_code" class="text-xs text-primary-300 font-mono mt-0.5">CR-123456</p>
            </div>
            <button type="button" class="closeReqDetailModal text-white/70 hover:text-white p-1 rounded-lg hover:bg-white/10 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="p-6 space-y-4 text-sm">
            <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-xs font-semibold text-slate-500">Current Status:</span>
                <span id="req_detail_status" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">Pending</span>
            </div>

            <div class="space-y-3">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Purpose</span>
                    <p id="req_detail_purpose" class="text-slate-800 font-medium mt-0.5">—</p>
                </div>

                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Delivery Address</span>
                    <p id="req_detail_address" class="text-slate-800 font-medium mt-0.5">—</p>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Amount</span>
                        <p id="req_detail_price" class="text-base font-extrabold text-primary-600 mt-0.5">₱ 50.00</p>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Payment Method</span>
                        <p id="req_detail_payment" class="text-slate-800 font-medium mt-0.5">Cash on Delivery</p>
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-100">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Date Filed</span>
                    <p id="req_detail_date" class="text-xs text-slate-600 mt-0.5">—</p>
                </div>
            </div>
        </div>

        <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end">
            <button type="button" class="closeReqDetailModal px-5 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold rounded-xl text-sm transition">Close</button>
        </div>
    </div>
</div>

<!-- ====================== MODAL: CANCEL ORDER ====================== -->
<div id="cancelOrderModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display:none;">
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 space-y-4 animate-in fade-in zoom-in-95 duration-200">
        
        <div id="loadingSpinner_cancelRequest" style="display:none;">
            <div class="absolute inset-0 bg-white/80 backdrop-blur-xs flex items-center justify-center rounded-2xl z-20">
                <div class="w-8 h-8 border-4 border-rose-600 border-t-transparent rounded-full animate-spin"></div>
            </div>
        </div>

        <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center mx-auto sm:mx-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        </div>
        
        <form id="frmCancelRequest" class="space-y-4">
            <?= csrf_field(); ?>
            <input type="hidden" id="requestId" name="requestId">

            <div>
                <h3 class="text-lg font-bold text-slate-900">Cancel Document Request</h3>
                <p class="text-sm text-slate-500 mt-1">
                    Are you sure you want to cancel request <span id="cancelRequestCode" class="font-bold text-slate-800 font-mono">CR-000</span>? This action cannot be reversed.
                </p>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" class="closeCancelModal px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-semibold transition">Back</button>
                <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-sm font-semibold shadow-md transition">Confirm Cancellation</button>
            </div>
        </form>
    </div>
</div>

<?php include "components/footer.php";?>
<script src="js/app.js"></script>

<script>
$(document).ready(function () {
    if ($.fn.DataTable) {
        $.fn.dataTable.ext.errMode = 'none';

        var myTable = $('#userTable').DataTable({
            responsive: true,
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50],
            language: {
                emptyTable: '<div class="py-6 text-center text-slate-500"><svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg><p class="font-semibold text-slate-700">No document requests found</p><p class="text-xs text-slate-400 mt-0.5">Click one of the buttons above to submit your first request.</p></div>',
                zeroRecords: 'No matching requests found.',
                info: 'Showing _START_ to _END_ of _TOTAL_ requests',
                infoEmpty: 'No requests filed yet',
                search: '',
                searchPlaceholder: 'Search requests...'
            },
            columnDefs: [
                { responsivePriority: 1, targets: 0 }, // Tracking
                { responsivePriority: 2, targets: 1 }, // Type
                { responsivePriority: 3, targets: 6 }, // Status
                { responsivePriority: 4, targets: 7 }, // Actions
                { responsivePriority: 5, targets: 4 }, // Price
                { responsivePriority: 6, targets: 5 }, // Date
                { responsivePriority: 7, targets: 2 }, // Purpose
                { responsivePriority: 8, targets: 3 }  // Address
            ],
            dom: '<"flex flex-wrap items-center justify-between gap-3 mb-4"l>rtip'
        });

        // Search Input
        $('#searchInput').off('input').on('input', function () {
            myTable.search($(this).val()).draw();
        });

        // Status Filter
        $('#statusFilter').on('change', function () {
            var selectedStatus = $(this).val();
            myTable.column(6).search(selectedStatus).draw();
        });
    }

    // Auto-open modal if URL has ?open=...
    var urlParams = new URLSearchParams(window.location.search);
    var openType = urlParams.get('open');
    if (openType === 'id') $('#OpenBrgyIdModal').trigger('click');
    else if (openType === 'clearance') $('#OpenClearanceModal').trigger('click');
    else if (openType === 'residency') $('#OpenResidencyModal').trigger('click');
    else if (openType === 'indigency') $('#OpenIndigencyModal').trigger('click');
});
</script>
