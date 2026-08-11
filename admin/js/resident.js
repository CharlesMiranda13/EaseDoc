/**
 * resident.js — Admin Resident Management Scripts
 */
$(document).ready(function() {

    // Initialize Add Resident Address Dropdowns
    if (window.initAddressDropdowns) {
        window.initAddressDropdowns({
            region: '#region',
            province: '#province',
            city: '#city',
            barangay: '#barangay'
        });
    }

    // Live preview helpers
    function renderPreview(input, containerSelector, isPdfAllowed) {
        var file = input.files[0];
        var $container = $(containerSelector);
        if (!file) {
            $container.empty();
            return;
        }

        if (file.type.startsWith('image/')) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $container.html('<img src="' + e.target.result + '" class="max-w-xs h-24 object-cover rounded-xl border border-slate-200 shadow-sm mt-2" alt="Preview">');
            };
            reader.readAsDataURL(file);
        } else if (isPdfAllowed && file.type === 'application/pdf') {
            $container.html('<div class="inline-flex items-center gap-2 px-3 py-2 bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold rounded-xl mt-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>' + file.name + '</div>');
        } else {
            $container.html('<span class="text-xs text-slate-500 mt-2 block">' + file.name + '</span>');
        }
    }

    $('#profileImg').on('change', function() { renderPreview(this, '#profileImgPreview', false); });
    $('#validId').on('change', function() { renderPreview(this, '#validIdPreview', true); });
    $('#r_profile').on('change', function() { renderPreview(this, '#r_profilePreview', false); });
    $('#r_valid_ids').on('change', function() { renderPreview(this, '#r_valid_idsPreview', true); });

    // Open & Close Add Modal
    $('#addResidentButton').on('click', function() {
        $('#frmAddResident')[0].reset();
        $('#profileImgPreview, #validIdPreview').empty();
        $('#addResidentModal').fadeIn(200);
    });

    $('.addResidentCloseModal').on('click', function() {
        $('#addResidentModal').fadeOut(150);
    });

    // Add Resident Submit
    $("#frmAddResident").on("submit", function(e) {
        e.preventDefault();

        var pass = $('#password').val();
        var cPass = $('#confirm_Password').val();

        if (pass !== cPass) {
            alertify.error("Passwords do not match. Please verify.");
            $('#confirm_Password').focus();
            return;
        }

        $("#loadingSpinner").show();
        var formData = new FormData(this);
        formData.append("requestType", 'addResident');

        $.ajax({
            url: "backend/end-points/controller.php",
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success' || response.status === true) {
                    alertify.success(response.message || "Resident registered successfully!");
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    alertify.error(response.message || "Failed to register resident.");
                }
            },
            error: function(xhr) {
                var msg = "An error occurred. Please try again.";
                try {
                    var res = JSON.parse(xhr.responseText);
                    if (res && res.message) msg = res.message;
                } catch(e) {}
                alertify.error(msg);
            },
            complete: function() {
                $("#loadingSpinner").hide();
            }
        });
    });

    // View Resident Profile Modal
    $(document).on('click', '.viewResidentButton', function() {
        var btn = $(this);
        var rId = btn.data('r_id');
        var fullname = btn.data('r_fullname');
        var email = btn.data('r_email');
        var contact = btn.data('r_contact_number') || 'No contact number';
        var gender = btn.data('r_gender') || 'N/A';
        var civilStatus = btn.data('r_civil_status') || 'Single';
        var citizenship = btn.data('r_citizenship') || 'Filipino';
        var bday = btn.data('r_bday') || 'Not specified';
        var age = btn.data('r_age') || '—';
        var address = btn.data('r_address') || 'No address provided';
        var profileSrc = btn.data('r_profile');
        var validIdSrc = btn.data('r_valid_ids');

        $('#view_id').text('#' + rId);
        $('#view_fullname').text(fullname);
        $('#view_email').text(email);
        $('#view_contact').text(contact);
        $('#view_gender').text(gender);
        $('#view_civil_status').text(civilStatus);
        $('#view_citizenship').text(citizenship);
        $('#view_bday').text(bday);
        $('#view_age').text(age);
        $('#view_address').text(address);

        // Avatar
        if (profileSrc) {
            $('#view_avatarContainer').html('<img src="' + profileSrc + '" class="w-full h-full object-cover" alt="Profile">');
        } else {
            var initial = fullname.charAt(0).toUpperCase();
            $('#view_avatarContainer').html('<span class="text-2xl font-bold text-white">' + initial + '</span>');
        }

        // Valid ID Preview
        if (validIdSrc) {
            if (validIdSrc.toLowerCase().endsWith('.pdf')) {
                $('#view_validIdContainer').html(
                    '<a href="' + validIdSrc + '" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-50 hover:bg-primary-100 text-primary-700 font-semibold rounded-xl text-xs transition border border-primary-200">' +
                    '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>' +
                    'Open Attached PDF Document in New Tab</a>'
                );
            } else {
                $('#view_validIdContainer').html(
                    '<div class="relative group">' +
                    '<img src="' + validIdSrc + '" class="max-h-52 max-w-full rounded-xl border border-slate-200 object-contain shadow-sm" alt="Valid ID">' +
                    '<a href="' + validIdSrc + '" target="_blank" class="mt-2 inline-flex items-center gap-1 text-xs text-primary-600 hover:underline">' +
                    '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg> View Full Size</a>' +
                    '</div>'
                );
            }
        } else {
            $('#view_validIdContainer').html('<p class="text-xs text-slate-400">No identification document uploaded.</p>');
        }

        // Link Edit Trigger
        $('#view_editTrigger').off('click').on('click', function() {
            $('#viewResidentModal').fadeOut(150, function() {
                btn.closest('tr').find('.editResidentButton').trigger('click');
            });
        });

        $('#viewResidentModal').fadeIn(200);
    });

    $('.closeViewModal').on('click', function() {
        $('#viewResidentModal').fadeOut(150);
    });

    // Edit Resident Modal
    $(document).on('click', '.editResidentButton', function() {
        var btn = $(this);
        var rId = btn.data('r_id');

        $('#r_id').val(rId);
        $('#r_fname').val(btn.data('r_fname'));
        $('#r_mname').val(btn.data('r_mname'));
        $('#r_lname').val(btn.data('r_lname'));
        $('#r_suffix').val(btn.data('r_suffix'));
        $('#r_gender').val(btn.data('r_gender'));
        $('#r_civil_status').val(btn.data('r_civil_status'));
        $('#r_citizenship').val(btn.data('r_citizenship') || 'Filipino');
        $('#r_bday').val(btn.data('r_bday'));
        $('#r_contact_number').val(btn.data('r_contact_number'));
        $('#r_email').val(btn.data('r_email'));
        $('#r_street').val(btn.data('r_street'));
        $('#r_password').val('');
        $('#c_Password').val('');

        // Initialize Cascading address with current values
        if (window.initAddressDropdowns) {
            window.initAddressDropdowns({
                region: '#r_edit_region',
                province: '#r_edit_province',
                city: '#r_edit_city',
                barangay: '#r_edit_barangay',
                defaultRegion: btn.data('r_region'),
                defaultProvince: btn.data('r_province'),
                defaultCity: btn.data('r_municipality'),
                defaultBarangay: btn.data('r_barangay')
            });
        }

        // Previews
        var currentProfile = btn.data('r_profile');
        if (currentProfile) {
            $('#r_profilePreview').html('<img src="' + currentProfile + '" class="max-w-xs h-20 object-cover rounded-xl border border-slate-200 mt-2" alt="Current Photo">');
        } else {
            $('#r_profilePreview').html('<p class="text-[11px] text-slate-400 mt-1">No photo uploaded</p>');
        }

        var currentValidId = btn.data('r_valid_ids');
        if (currentValidId) {
            if (currentValidId.toLowerCase().endsWith('.pdf')) {
                $('#r_valid_idsPreview').html('<a href="' + currentValidId + '" target="_blank" class="text-xs text-primary-600 underline mt-1 block">View Current PDF ID</a>');
            } else {
                $('#r_valid_idsPreview').html('<img src="' + currentValidId + '" class="max-w-xs h-20 object-contain rounded-xl border border-slate-200 mt-2" alt="Current Valid ID">');
            }
        } else {
            $('#r_valid_idsPreview').html('<p class="text-[11px] text-slate-400 mt-1">No ID uploaded</p>');
        }

        $('#editResidentModal').fadeIn(200);
    });

    $('#EditcloseResidentModal, #btnCancelEdit').on('click', function() {
        $('#editResidentModal').fadeOut(150);
    });

    // Edit Resident Submit
    $("#frmEditResident").on("submit", function(e) {
        e.preventDefault();

        var newPass = $('#r_password').val();
        var confirmPass = $('#c_Password').val();

        if (newPass && newPass !== confirmPass) {
            alertify.error("New passwords do not match.");
            $('#c_Password').focus();
            return;
        }

        $("#editResidentloadingSpinner").show();
        var formData = new FormData(this);
        formData.append("requestType", 'EditResident');

        $.ajax({
            url: "backend/end-points/controller.php",
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success' || response.status === true) {
                    alertify.success(response.message || "Resident updated successfully!");
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    alertify.error(response.message || "Failed to update resident.");
                }
            },
            error: function(xhr) {
                var msg = "An error occurred. Please try again.";
                try {
                    var res = JSON.parse(xhr.responseText);
                    if (res && res.message) msg = res.message;
                } catch(e) {}
                alertify.error(msg);
            },
            complete: function() {
                $("#editResidentloadingSpinner").hide();
            }
        });
    });

    // Delete Resident Modal
    $(document).on('click', '.deleteResidentButton', function() {
        var residentId = $(this).data('r_id');
        var residentName = $(this).data('r_name') || 'this resident';

        $('#TargetdelResidentId').val(residentId);
        $('#delResidentName').text(residentName);

        $('#deleteConfirmationModal').removeClass('opacity-0 invisible').addClass('opacity-100 visible');
    });

    $('.cancelDeleteResident').on('click', function() {
        $('#deleteConfirmationModal').removeClass('opacity-100 visible').addClass('opacity-0 invisible');
    });

    $('#confirmDeleteResident').on('click', function() {
        $("#DeleteResidentloadingSpinner").show();
        $(this).prop('disabled', true);

        var residentId = $('#TargetdelResidentId').val();
        var csrfToken = $('meta[name="csrf-token"]').attr('content') || '';

        $.ajax({
            url: "backend/end-points/controller.php",
            method: 'POST',
            data: {
                requestType: 'DeleteResident',
                residentId: residentId,
                csrf_token: csrfToken
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success' || response.status === true) {
                    alertify.success("Resident archived successfully!");
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    alertify.error(response.message || "Failed to archive resident.");
                }
            },
            error: function() {
                alertify.error("An error occurred while archiving resident.");
            },
            complete: function() {
                $("#DeleteResidentloadingSpinner").hide();
                $('#confirmDeleteResident').prop('disabled', false);
                $('#deleteConfirmationModal').removeClass('opacity-100 visible').addClass('opacity-0 invisible');
            }
        });
    });

    // Global escape key to close open modals
    $(document).on('keydown', function(e) {
        if (e.key === "Escape") {
            $('#addResidentModal, #editResidentModal, #viewResidentModal').fadeOut(150);
            $('#deleteConfirmationModal').removeClass('opacity-100 visible').addClass('opacity-0 invisible');
        }
    });

});
