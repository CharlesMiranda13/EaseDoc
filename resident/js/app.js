/**
 * app.js — Resident Portal Frontend Logic
 */
$(document).ready(function() {

    // Helper for adding CSRF token to form data
    function appendCsrf(formData) {
        var csrfToken = $('meta[name="csrf-token"]').attr('content') || '';
        if (csrfToken && !formData.has('csrf_token')) {
            formData.append('csrf_token', csrfToken);
        }
    }

    // Generic file preview helper
    function renderPreview(input, containerSelector) {
        var file = input.files[0];
        var $container = $('#' + containerSelector);
        if (!file) {
            $container.empty();
            return;
        }

        if (file.type.startsWith('image/')) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $container.html('<img src="' + e.target.result + '" class="max-w-xs h-20 object-cover rounded-xl border border-slate-200 shadow-sm mt-1" alt="Preview">');
            };
            reader.readAsDataURL(file);
        } else if (file.type === 'application/pdf') {
            $container.html('<div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold rounded-xl mt-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>' + file.name + '</div>');
        } else {
            $container.html('<span class="text-xs text-slate-500 mt-1 block">' + file.name + '</span>');
        }
    }

    // Document file previews
    $('#validId_Clearance').on('change', function() { renderPreview(this, 'validIdPreview_Clearance'); });
    $('#proofResidency_Clearance').on('change', function() { renderPreview(this, 'proofResidencyPreview_Clearance'); });
    $('#1x1pic_BrgyId').on('change', function() { renderPreview(this, '1x1picPreview_BrgyId'); });
    $('#signature_BrgyId').on('change', function() { renderPreview(this, 'signaturePreview_BrgyId'); });
    $('#validId_BrgyId').on('change', function() { renderPreview(this, 'validIdPreview_BrgyId'); });
    $('#proofResidency_BrgyId').on('change', function() { renderPreview(this, 'proofResidencyPreview_BrgyId'); });
    $('#validId_Residency').on('change', function() { renderPreview(this, 'validIdPreview_Residency'); });
    $('#validId_Indigency').on('change', function() { renderPreview(this, 'validIdPreview_Indigency'); });

    // Modal Triggers
    $('#OpenClearanceModal').on('click', function() { $('#clearanceModal').fadeIn(200); });
    $('#OpenBrgyIdModal').on('click', function() { $('#BrgyIdModal').fadeIn(200); });
    $('#OpenResidencyModal').on('click', function() { $('#residencyModal').fadeIn(200); });
    $('#OpenIndigencyModal').on('click', function() { $('#indigencyModal').fadeIn(200); });

    $('.closeModal').on('click', function() {
        $('#clearanceModal, #BrgyIdModal, #residencyModal, #indigencyModal').fadeOut(150);
    });

    // 1. Request Barangay Clearance Submit
    $("#frmRequestClearance").on("submit", function(e) {
        e.preventDefault(); 
        $("#loadingSpinner_Clearance").show();

        var shippingFee = $("#shippingFee_Clearance").attr('data-shippingFee') || '0.00';
        var documentPrice = $("#documentPrice_Clearance").attr('data-documentPrice') || '50.00';
        var totalPrice = $("#totalPrice_Clearance").attr('data-totalPrice') || '50.00';
        var formData = new FormData(this);

        formData.append('shippingFee', shippingFee);
        formData.append('documentPrice', documentPrice);
        formData.append('totalPrice', totalPrice);
        formData.append("requestType", 'RequestClearance');
        appendCsrf(formData);

        $.ajax({
            url: "backend/end-points/controller.php",
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success' || response.status === true) {
                    alertify.success(response.message || "Barangay Clearance request submitted!");
                    setTimeout(function() {
                        location.href = "MyRequest.php";
                    }, 1200);
                } else {
                    alertify.error(response.message || "Failed to submit request.");
                }
            },
            error: function(xhr) {
                var msg = "An error occurred while submitting request.";
                try {
                    var res = JSON.parse(xhr.responseText);
                    if (res && res.message) msg = res.message;
                } catch(e) {}
                alertify.error(msg);
            },
            complete: function() {
                $("#loadingSpinner_Clearance").hide();
            }
        });
    });

    // 2. Request Barangay ID Submit
    $("#frmRequestBrgyId").on("submit", function(e) {
        e.preventDefault(); 
        $("#loadingSpinner_BrgyId").show();

        var shippingFee = $("#shippingFee_BrgyId").attr('data-shippingFee') || '0.00';
        var documentPrice = $("#documentPrice_BrgyId").attr('data-documentPrice') || '50.00';
        var totalPrice = $("#totalPrice_BrgyId").attr('data-totalPrice') || '50.00';
        var formData = new FormData(this);

        formData.append('shippingFee', shippingFee);
        formData.append('documentPrice', documentPrice);
        formData.append('totalPrice', totalPrice);
        formData.append("requestType", 'RequestBarangayID');
        appendCsrf(formData);

        $.ajax({
            url: "backend/end-points/controller.php",
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success' || response.status === true) {
                    alertify.success(response.message || "Barangay ID request submitted!");
                    setTimeout(function() {
                        location.href = "MyRequest.php";
                    }, 1200);
                } else {
                    alertify.error(response.message || "Failed to submit request.");
                }
            },
            error: function(xhr) {
                var msg = "An error occurred while submitting request.";
                try {
                    var res = JSON.parse(xhr.responseText);
                    if (res && res.message) msg = res.message;
                } catch(e) {}
                alertify.error(msg);
            },
            complete: function() {
                $("#loadingSpinner_BrgyId").hide();
            }
        });
    });

    // 3. Request Certificate of Residency Submit
    $("#frmRequest_Residency").on("submit", function(e) {
        e.preventDefault(); 
        $("#loadingSpinner_Residency").show();

        var shippingFee = $("#shippingFee_Residency").attr('data-shippingFee') || '0.00';
        var documentPrice = $("#documentPrice_Residency").attr('data-documentPrice') || '50.00';
        var totalPrice = $("#totalPrice_Residency").attr('data-totalPrice') || '50.00';
        var formData = new FormData(this);

        formData.append('shippingFee', shippingFee);
        formData.append('documentPrice', documentPrice);
        formData.append('totalPrice', totalPrice);
        formData.append("requestType", 'RequestResidency');
        appendCsrf(formData);

        $.ajax({
            url: "backend/end-points/controller.php",
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success' || response.status === true) {
                    alertify.success(response.message || "Certificate of Residency request submitted!");
                    setTimeout(function() {
                        location.href = "MyRequest.php";
                    }, 1200);
                } else {
                    alertify.error(response.message || "Failed to submit request.");
                }
            },
            error: function(xhr) {
                var msg = "An error occurred while submitting request.";
                try {
                    var res = JSON.parse(xhr.responseText);
                    if (res && res.message) msg = res.message;
                } catch(e) {}
                alertify.error(msg);
            },
            complete: function() {
                $("#loadingSpinner_Residency").hide();
            }
        });
    });

    // 4. Request Certificate of Indigency Submit
    $("#frmRequest_Indigency").on("submit", function(e) {
        e.preventDefault(); 
        $("#loadingSpinner_Indigency").show();

        var shippingFee = $("#shippingFee_Indigency").attr('data-shippingFee') || '0.00';
        var documentPrice = $("#documentPrice_Indigency").attr('data-documentPrice') || '50.00';
        var totalPrice = $("#totalPrice_Indigency").attr('data-totalPrice') || '50.00';
        var formData = new FormData(this);

        formData.append('shippingFee', shippingFee);
        formData.append('documentPrice', documentPrice);
        formData.append('totalPrice', totalPrice);
        formData.append("requestType", 'RequestIndigency');
        appendCsrf(formData);

        $.ajax({
            url: "backend/end-points/controller.php",
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success' || response.status === true) {
                    alertify.success(response.message || "Certificate of Indigency request submitted!");
                    setTimeout(function() {
                        location.href = "MyRequest.php";
                    }, 1200);
                } else {
                    alertify.error(response.message || "Failed to submit request.");
                }
            },
            error: function(xhr) {
                var msg = "An error occurred while submitting request.";
                try {
                    var res = JSON.parse(xhr.responseText);
                    if (res && res.message) msg = res.message;
                } catch(e) {}
                alertify.error(msg);
            },
            complete: function() {
                $("#loadingSpinner_Indigency").hide();
            }
        });
    });

    // 5. View Request Details Modal
    $(document).on('click', '.viewResidentRequestBtn', function() {
        var btn = $(this);
        $('#req_detail_code').text(btn.data('code'));
        $('#req_detail_type').text(btn.data('type'));
        $('#req_detail_purpose').text(btn.data('purpose') || '—');
        $('#req_detail_address').text(btn.data('address') || '—');
        $('#req_detail_price').text(btn.data('price') || '₱ 50.00');
        $('#req_detail_payment').text(btn.data('payment') || 'Cash on Delivery');
        $('#req_detail_date').text(btn.data('date') || '—');

        var st = btn.data('status') || 'Pending';
        var badgeClass = 'bg-slate-100 text-slate-700';
        if (st === 'Pending') badgeClass = 'bg-amber-100 text-amber-800';
        else if (st === 'Approved') badgeClass = 'bg-blue-100 text-blue-800';
        else if (st === 'Shipped') badgeClass = 'bg-purple-100 text-purple-800';
        else if (st === 'Delivered') badgeClass = 'bg-emerald-100 text-emerald-800';
        else if (st === 'Rejected') badgeClass = 'bg-rose-100 text-rose-800';
        else if (st === 'Canceled') badgeClass = 'bg-slate-100 text-slate-800';

        $('#req_detail_status').attr('class', 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold ' + badgeClass).text(st);

        $('#viewRequestModal').fadeIn(200);
    });

    $('.closeReqDetailModal').on('click', function() {
        $('#viewRequestModal').fadeOut(150);
    });

    // 6. Cancel Request Modal & Submit
    $(document).on('click', '.cancelRequest', function() {
        var reqId = $(this).data('requestid');
        var reqCode = $(this).data('code') || ('CR-' + reqId);

        $('#requestId').val(reqId);
        $('#cancelRequestCode').text(reqCode);
        $('#cancelOrderModal').fadeIn(200);
    });

    $('.closeCancelModal, #cancelOrderModal .closeModal').on('click', function() {
        $('#cancelOrderModal').fadeOut(150);
    });

    $("#frmCancelRequest").on("submit", function(e) {
        e.preventDefault(); 
        $("#loadingSpinner_cancelRequest").show();

        var formData = new FormData(this);
        formData.append("requestType", 'CancelRequest');
        appendCsrf(formData);

        $.ajax({
            url: "backend/end-points/controller.php",
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success' || response.status === true) {
                    alertify.success(response.message || "Request cancelled successfully.");
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    alertify.error(response.message || "Failed to cancel request.");
                }
            },
            error: function(xhr) {
                var msg = "An error occurred while cancelling request.";
                try {
                    var res = JSON.parse(xhr.responseText);
                    if (res && res.message) msg = res.message;
                } catch(e) {}
                alertify.error(msg);
            },
            complete: function() {
                $("#loadingSpinner_cancelRequest").hide();
                $('#cancelOrderModal').fadeOut(150);
            }
        });
    });

    // 7. Update Account Settings Submit
    $("#frmUpdateAccountSetting").on("submit", function(e) {
        e.preventDefault(); 
        $("#loadingSpinner_account").show();

        var newPassword = $('#newPassword').val();
        var confirmNewPassword = $('#confirmNewPassword').val();

        if (newPassword) {
            if (newPassword !== confirmNewPassword) {
                $("#loadingSpinner_account").hide();
                alertify.error('New passwords do not match. Please verify.');
                $('#confirmNewPassword').focus();
                return;
            }
            if (newPassword.length < 6) {
                $("#loadingSpinner_account").hide();
                alertify.error('New password must be at least 6 characters long.');
                $('#newPassword').focus();
                return;
            }
        }
      
        var formData = new FormData(this);
        formData.append("requestType", 'UpdateAccountSetting');
        appendCsrf(formData);

        $.ajax({
            url: "backend/end-points/controller.php",
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success' || response.status === true) {
                    alertify.success(response.message || "Profile updated successfully!");
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    alertify.error(response.message || "Failed to update profile.");
                }
            },
            error: function(xhr) {
                var msg = "An error occurred while updating profile.";
                try {
                    var res = JSON.parse(xhr.responseText);
                    if (res && res.message) msg = res.message;
                } catch(e) {}
                alertify.error(msg);
            },
            complete: function() {
                $("#loadingSpinner_account").hide();
            }
        });
    });

    // Global escape key to close modals
    $(document).on('keydown', function(e) {
        if (e.key === "Escape") {
            $('#clearanceModal, #BrgyIdModal, #residencyModal, #indigencyModal, #viewRequestModal, #cancelOrderModal').fadeOut(150);
        }
    });

});
