/**
 * EaseDocument — Unified Public & Authentication JavaScript Engine
 * Fullstack & Security hardened with Client Validation, Caps Lock Detection,
 * Role Switching, Brute Force Mitigation, Live Tracking & Dynamic Bulletin.
 */

$(document).ready(function () {
    // Initialize Alertify defaults
    if (typeof alertify !== 'undefined') {
        alertify.set('notifier', 'position', 'top-right');
        alertify.set('notifier', 'delay', 4);
    }

    // ==========================================
    // 1. Dedicated Portal Initialization
    // ==========================================
    if ($('#residentEmail').length && !$('#adminEmail').length) {
        $('#residentEmail').focus();
    } else if ($('#adminEmail').length && !$('#residentEmail').length) {
        $('#adminEmail').focus();
    }

    // ==========================================
    // 2. Remember Email Feature (Safe LocalStorage)
    // ==========================================
    const savedResidentEmail = localStorage.getItem('easedoc_resident_email');
    if (savedResidentEmail && $('#residentEmail').length) {
        $('#residentEmail').val(savedResidentEmail);
        $('#rememberResident').prop('checked', true);
    }

    const savedAdminEmail = localStorage.getItem('easedoc_admin_email');
    if (savedAdminEmail && $('#adminEmail').length) {
        $('#adminEmail').val(savedAdminEmail);
        $('#rememberAdmin').prop('checked', true);
    }

    // ==========================================
    // 3. Password Visibility Toggle
    // ==========================================
    $(document).on('click', '.togglePassword', function (e) {
        e.preventDefault();
        const inputGroup = $(this).closest('.relative');
        const input = inputGroup.find('input');
        const isPassword = input.attr('type') === 'password';
        
        input.attr('type', isPassword ? 'text' : 'password');
        
        // Update SVG icon
        if (isPassword) {
            $(this).html(`
                <svg class="w-5 h-5 text-primary-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path>
                </svg>
            `);
            $(this).attr('aria-label', 'Hide password');
        } else {
            $(this).html(`
                <svg class="w-5 h-5 text-slate-400 hover:text-slate-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
            `);
            $(this).attr('aria-label', 'Show password');
        }
    });

    // ==========================================
    // 4. Caps Lock Detection Indicator
    // ==========================================
    $('input[type="password"]').on('keyup keydown', function (e) {
        const capsNotice = $(this).closest('.space-y-4, .space-y-6, form').find('.caps-lock-warning');
        if (e.originalEvent && typeof e.originalEvent.getModifierState === 'function') {
            const isCaps = e.originalEvent.getModifierState('CapsLock');
            if (isCaps) {
                capsNotice.removeClass('hidden');
            } else {
                capsNotice.addClass('hidden');
            }
        }
    }).on('blur', function () {
        $(this).closest('.space-y-4, .space-y-6, form').find('.caps-lock-warning').addClass('hidden');
    });

    // ==========================================
    // 5. Cooldown Timer Handler
    // ==========================================
    function startAuthCooldown(btnSelector, countdownSeconds) {
        const btn = $(btnSelector);
        btn.prop('disabled', true).addClass('opacity-70 cursor-not-allowed');
        let remaining = countdownSeconds;

        const originalText = btn.find('.btn-label').text();
        const interval = setInterval(function () {
            btn.find('.btn-label').text(`Wait ${remaining}s...`);
            remaining--;
            if (remaining < 0) {
                clearInterval(interval);
                btn.prop('disabled', false).removeClass('opacity-70 cursor-not-allowed');
                btn.find('.btn-label').text(originalText);
            }
        }, 1000);
    }

    // ==========================================
    // 6. Resident Login AJAX Submission
    // ==========================================
    $('#frmLoginResident').on('submit', function (e) {
        e.preventDefault();
        const emailInput = $('#residentEmail');
        const passwordInput = $('#residentPassword');
        const submitBtn = $('#btnLoginResident');
        const email = $.trim(emailInput.val());
        const password = passwordInput.val();

        // Client-side Validation
        if (!email) {
            if (typeof alertify !== 'undefined') alertify.error('Please enter your email address.');
            emailInput.focus();
            return;
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            if (typeof alertify !== 'undefined') alertify.error('Please enter a valid email format.');
            emailInput.focus();
            return;
        }

        if (!password) {
            if (typeof alertify !== 'undefined') alertify.error('Please enter your password.');
            passwordInput.focus();
            return;
        }

        // Remember Email preference
        if ($('#rememberResident').is(':checked')) {
            localStorage.setItem('easedoc_resident_email', email);
        } else {
            localStorage.removeItem('easedoc_resident_email');
        }

        // Loading state
        submitBtn.prop('disabled', true);
        submitBtn.find('.btn-label').addClass('opacity-0');
        submitBtn.find('.btn-spinner').removeClass('hidden');

        $.ajax({
            type: 'POST',
            url: 'backend/end-points/controller.php',
            data: {
                requestType: 'LoginResident',
                email: email,
                password: password
            },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    if (typeof alertify !== 'undefined') alertify.success('Login Successful! Redirecting...');
                    submitBtn.removeClass('bg-primary-600 hover:bg-primary-700').addClass('bg-emerald-600');
                    submitBtn.find('.btn-label').removeClass('opacity-0').text('Authenticated ✓');
                    submitBtn.find('.btn-spinner').addClass('hidden');

                    setTimeout(function () {
                        window.location.href = 'resident/index.php';
                    }, 800);
                } else {
                    submitBtn.prop('disabled', false);
                    submitBtn.find('.btn-label').removeClass('opacity-0');
                    submitBtn.find('.btn-spinner').addClass('hidden');
                    
                    const msg = response.message || 'Invalid email or password.';
                    if (typeof alertify !== 'undefined') alertify.error(msg);

                    if (response.code === 429) {
                        startAuthCooldown('#btnLoginResident', 30);
                    }
                }
            },
            error: function (xhr) {
                submitBtn.prop('disabled', false);
                submitBtn.find('.btn-label').removeClass('opacity-0');
                submitBtn.find('.btn-spinner').addClass('hidden');

                let errorMsg = 'An unexpected connection error occurred. Please try again.';
                if (xhr.status === 429) {
                    errorMsg = 'Too many failed login attempts. Please wait 5 minutes.';
                    startAuthCooldown('#btnLoginResident', 60);
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                if (typeof alertify !== 'undefined') alertify.error(errorMsg);
            }
        });
    });

    // ==========================================
    // 7. Admin / Staff Login AJAX Submission
    // ==========================================
    $('#frmLoginAdmin').on('submit', function (e) {
        e.preventDefault();
        const emailInput = $('#adminEmail');
        const passwordInput = $('#adminPassword');
        const submitBtn = $('#btnLoginAdmin');
        const email = $.trim(emailInput.val());
        const password = passwordInput.val();

        // Client-side Validation
        if (!email) {
            if (typeof alertify !== 'undefined') alertify.error('Please enter your staff email address.');
            emailInput.focus();
            return;
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            if (typeof alertify !== 'undefined') alertify.error('Please enter a valid email format.');
            emailInput.focus();
            return;
        }

        if (!password) {
            if (typeof alertify !== 'undefined') alertify.error('Please enter your staff password.');
            passwordInput.focus();
            return;
        }

        // Remember Email preference
        if ($('#rememberAdmin').is(':checked')) {
            localStorage.setItem('easedoc_admin_email', email);
        } else {
            localStorage.removeItem('easedoc_admin_email');
        }

        // Loading state
        submitBtn.prop('disabled', true);
        submitBtn.find('.btn-label').addClass('opacity-0');
        submitBtn.find('.btn-spinner').removeClass('hidden');

        $.ajax({
            type: 'POST',
            url: 'backend/end-points/controller.php',
            data: {
                requestType: 'LoginAdmin',
                email: email,
                password: password
            },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    if (typeof alertify !== 'undefined') alertify.success('Staff Authorized! Entering Dashboard...');
                    submitBtn.removeClass('bg-primary-600 hover:bg-primary-700').addClass('bg-emerald-600');
                    submitBtn.find('.btn-label').removeClass('opacity-0').text('Authorized ✓');
                    submitBtn.find('.btn-spinner').addClass('hidden');

                    setTimeout(function () {
                        window.location.href = 'admin/index.php';
                    }, 800);
                } else {
                    submitBtn.prop('disabled', false);
                    submitBtn.find('.btn-label').removeClass('opacity-0');
                    submitBtn.find('.btn-spinner').addClass('hidden');

                    const msg = response.message || 'Invalid admin credentials.';
                    if (typeof alertify !== 'undefined') alertify.error(msg);

                    if (response.code === 429) {
                        startAuthCooldown('#btnLoginAdmin', 30);
                    }
                }
            },
            error: function (xhr) {
                submitBtn.prop('disabled', false);
                submitBtn.find('.btn-label').removeClass('opacity-0');
                submitBtn.find('.btn-spinner').addClass('hidden');

                let errorMsg = 'An unexpected connection error occurred. Please try again.';
                if (xhr.status === 429) {
                    errorMsg = 'Too many failed login attempts. Please wait 5 minutes.';
                    startAuthCooldown('#btnLoginAdmin', 60);
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                if (typeof alertify !== 'undefined') alertify.error(errorMsg);
            }
        });
    });

    // ==========================================
    // 8. Public Document Tracking on Landing Page
    // ==========================================
    $('#frmTrackDocument').on('submit', function (e) {
        e.preventDefault();
        const codeInput = $('#trackingCodeInput');
        const code = $.trim(codeInput.val());
        const trackBtn = $('#btnTrackSubmit');
        const resultCard = $('#trackingResultCard');
        const errorNotice = $('#trackingErrorNotice');

        if (!code) {
            if (typeof alertify !== 'undefined') alertify.error('Please enter a valid tracking code.');
            codeInput.focus();
            return;
        }

        // Loading state
        trackBtn.prop('disabled', true).addClass('opacity-75');
        trackBtn.find('.btn-track-text').addClass('opacity-0');
        trackBtn.find('.btn-track-spinner').removeClass('hidden');
        resultCard.addClass('hidden');
        errorNotice.addClass('hidden');

        $.ajax({
            type: 'POST',
            url: 'backend/end-points/controller.php',
            data: {
                requestType: 'TrackDocument',
                tracking_code: code
            },
            dataType: 'json',
            success: function (response) {
                trackBtn.prop('disabled', false).removeClass('opacity-75');
                trackBtn.find('.btn-track-text').removeClass('opacity-0');
                trackBtn.find('.btn-track-spinner').addClass('hidden');

                if (response.status === 'success' && response.data) {
                    renderTrackingResult(response.data);
                } else {
                    const msg = response.message || 'No record found with that tracking code.';
                    errorNotice.text(msg).removeClass('hidden');
                }
            },
            error: function (xhr) {
                trackBtn.prop('disabled', false).removeClass('opacity-75');
                trackBtn.find('.btn-track-text').removeClass('opacity-0');
                trackBtn.find('.btn-track-spinner').addClass('hidden');

                let msg = 'Failed to connect to tracking service. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                errorNotice.text(msg).removeClass('hidden');
            }
        });
    });

    function renderTrackingResult(d) {
        $('#trackResultCode').text(d.tracking_code);
        $('#trackResultDocType').text(d.form_type);
        $('#trackResultResident').text(d.masked_resident);
        $('#trackResultDate').text(d.request_date);
        $('#trackResultPurpose').text(d.purpose);
        $('#trackResultPayment').text(d.payment_method + ' (₱' + d.total_amount + ')');

        // Status Badge
        const statusBadge = $('#trackResultStatusBadge');
        statusBadge.text(d.current_status);
        statusBadge.removeClass('bg-amber-100 text-amber-800 bg-blue-100 text-blue-800 bg-indigo-100 text-indigo-800 bg-emerald-100 text-emerald-800 bg-rose-100 text-rose-800');

        if (d.badge_color === 'emerald') {
            statusBadge.addClass('bg-emerald-100 text-emerald-800 border border-emerald-200');
        } else if (d.badge_color === 'indigo') {
            statusBadge.addClass('bg-indigo-100 text-indigo-800 border border-indigo-200');
        } else if (d.badge_color === 'blue') {
            statusBadge.addClass('bg-blue-100 text-blue-800 border border-blue-200');
        } else if (d.badge_color === 'rose') {
            statusBadge.addClass('bg-rose-100 text-rose-800 border border-rose-200');
        } else {
            statusBadge.addClass('bg-amber-100 text-amber-800 border border-amber-200');
        }

        // Timeline Step indicators (Steps 1 to 4)
        const step = d.step;
        for (let i = 1; i <= 4; i++) {
            const stepEl = $(`#trackStep${i}`);
            const lineEl = $(`#trackLine${i}`);
            const dotEl = $(`#trackDot${i}`);

            if (step === -1) {
                // Canceled / Declined
                dotEl.removeClass('bg-indigo-600 bg-emerald-600 bg-slate-200 text-white').addClass('bg-rose-500 text-white');
                if (lineEl.length) lineEl.removeClass('bg-indigo-600 bg-emerald-600').addClass('bg-rose-300');
            } else if (i < step) {
                // Completed step
                dotEl.removeClass('bg-slate-200 text-slate-400 bg-indigo-600').addClass('bg-emerald-600 text-white');
                if (lineEl.length) lineEl.removeClass('bg-slate-200').addClass('bg-emerald-600');
            } else if (i === step) {
                // Current active step
                dotEl.removeClass('bg-slate-200 text-slate-400 bg-emerald-600').addClass('bg-indigo-600 text-white ring-4 ring-indigo-100');
                if (lineEl.length) lineEl.removeClass('bg-emerald-600').addClass('bg-slate-200');
            } else {
                // Future step
                dotEl.removeClass('bg-indigo-600 bg-emerald-600 ring-4 ring-indigo-100').addClass('bg-slate-200 text-slate-400');
                if (lineEl.length) lineEl.removeClass('bg-indigo-600 bg-emerald-600').addClass('bg-slate-200');
            }
        }

        $('#trackingResultCard').removeClass('hidden').hide().fadeIn(300);
        
        // Scroll smoothly to result card on mobile
        $('html, body').animate({
            scrollTop: $('#trackingResultCard').offset().top - 120
        }, 400);
    }

    // ==========================================
    // 9. Public Announcements Loader on Landing Page
    // ==========================================
    if ($('#announcementsContainer').length) {
        loadPublicAnnouncements();
    }

    function loadPublicAnnouncements() {
        $.ajax({
            type: 'GET',
            url: 'backend/end-points/controller.php?requestType=GetPublicAnnouncements&limit=6',
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success' && response.data && response.data.length > 0) {
                    renderAnnouncements(response.data);
                } else {
                    $('#announcementsContainer').html(`
                        <div class="col-span-full py-12 text-center bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                            <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15"></path>
                            </svg>
                            <p class="text-slate-600 font-semibold text-sm">No Public Announcements at this time</p>
                            <p class="text-slate-400 text-xs mt-1">Check back later for community notices and advisories.</p>
                        </div>
                    `);
                }
            },
            error: function () {
                $('#announcementsContainer').html(`
                    <div class="col-span-full py-8 text-center text-slate-400 text-xs">
                        Announcements bulletin is temporarily unavailable.
                    </div>
                `);
            }
        });
    }

    function renderAnnouncements(items) {
        let html = '';
        items.forEach(function (item) {
            const pinnedBadge = item.is_pinned ? `<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-2xs font-bold bg-amber-100 text-amber-800"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"></path></svg> Pinned</span>` : '';
            
            let catColor = 'bg-indigo-50 text-indigo-700 border-indigo-100';
            if (item.category === 'Emergency') catColor = 'bg-rose-50 text-rose-700 border-rose-100';
            if (item.category === 'Advisory') catColor = 'bg-amber-50 text-amber-700 border-amber-100';
            if (item.category === 'Health') catColor = 'bg-emerald-50 text-emerald-700 border-emerald-100';

            const imageThumb = item.image ? `<img src="uploads/announcements/${item.image}" alt="${item.title}" class="w-full h-44 object-cover rounded-xl mb-4 border border-slate-100">` : '';

            html += `
                <div class="group bg-white p-6 rounded-2xl border border-slate-200/80 hover:border-indigo-300 shadow-xs hover:shadow-lg transition-all duration-300 flex flex-col justify-between">
                    <div>
                        ${imageThumb}
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-2xs font-semibold uppercase tracking-wider border ${catColor}">
                                ${item.category}
                            </span>
                            <div class="flex items-center gap-2">
                                ${pinnedBadge}
                                <span class="text-xs text-slate-400 font-medium">${item.formatted_date}</span>
                            </div>
                        </div>
                        <h4 class="text-lg font-bold text-slate-900 group-hover:text-indigo-600 transition-colors line-clamp-2 mb-2">
                            ${item.title}
                        </h4>
                        <p class="text-xs text-slate-600 leading-relaxed line-clamp-3 mb-4">
                            ${item.snippet}
                        </p>
                    </div>
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-2xs font-medium text-slate-400">${item.time_ago}</span>
                        <button type="button" class="btn-read-announcement text-xs font-bold text-indigo-600 hover:text-indigo-800 inline-flex items-center gap-1 group-hover:translate-x-0.5 transition-transform" 
                                data-title="${encodeURIComponent(item.title)}" 
                                data-category="${encodeURIComponent(item.category)}" 
                                data-date="${encodeURIComponent(item.formatted_date)}" 
                                data-content="${encodeURIComponent(item.content)}"
                                data-image="${item.image ? encodeURIComponent(item.image) : ''}">
                            Read Full Notice &rarr;
                        </button>
                    </div>
                </div>
            `;
        });
        $('#announcementsContainer').html(html);
    }

    // Modal view for announcement
    $(document).on('click', '.btn-read-announcement', function () {
        const title = decodeURIComponent($(this).data('title'));
        const category = decodeURIComponent($(this).data('category'));
        const date = decodeURIComponent($(this).data('date'));
        const content = decodeURIComponent($(this).data('content'));
        const image = $(this).data('image') ? decodeURIComponent($(this).data('image')) : '';

        $('#modalNoticeTitle').text(title);
        $('#modalNoticeCategory').text(category);
        $('#modalNoticeDate').text(date);
        $('#modalNoticeContent').text(content);

        if (image) {
            $('#modalNoticeImage').attr('src', 'uploads/announcements/' + image).removeClass('hidden');
        } else {
            $('#modalNoticeImage').addClass('hidden');
        }

        $('#noticeModal').removeClass('hidden').addClass('flex');
    });

    $('#btnCloseNoticeModal, #noticeModalBackdrop').on('click', function () {
        $('#noticeModal').addClass('hidden').removeClass('flex');
    });

    // ==========================================
    // 10. Document Requirements & Fee Calculator Modal
    // ==========================================
    const documentCatalog = {
        clearance: {
            title: "Barangay Clearance",
            fee: "₱50.00",
            turnaround: "24-48 Hours",
            requirements: [
                "1 Valid Government-issued ID (e.g. PhilID, Passport, Driver's License)",
                "Proof of Residency (Utility Bill or Barangay Certificate)",
                "1x1 or 2x2 colored ID picture with white background",
                "Community Tax Certificate (Cedula) for the current year"
            ],
            purposes: ["Employment / Job Application", "Postal ID Application", "Bank Account Opening", "Police Clearance Requirement", "Business Requirement"]
        },
        residency: {
            title: "Certificate of Residency",
            fee: "₱50.00",
            turnaround: "24 Hours",
            requirements: [
                "1 Valid Government ID showing current address",
                "Proof of Billing or Lease Contract stating address",
                "Minimum of 6 months continuous residency in the Barangay"
            ],
            purposes: ["School / University Admission", "Bank Requirement", "Electric / Water Utility Application", "Court Proceeding"]
        },
        indigency: {
            title: "Certificate of Indigency",
            fee: "FREE (₱0.00)",
            turnaround: "Same-Day / 24 Hours",
            requirements: [
                "1 Valid ID or Barangay Verification Record",
                "Letter of Endorsement from Purok / Zone Leader",
                "Proof of qualification for social / medical assistance"
            ],
            purposes: ["DSWD / AICS Assistance", "Medical / Hospital Bill Waiver", "Free Legal Assistance / PAO", "Educational Scholarship Grant"]
        },
        barangay_id: {
            title: "Barangay Identification Card (Barangay ID)",
            fee: "₱100.00",
            turnaround: "2-3 Working Days",
            requirements: [
                "Birth Certificate (PSA / NSO) or Marriage Certificate",
                "Proof of Residency in Barangay",
                "1x1 ID Picture & Digital Signature"
            ],
            purposes: ["Primary Local Identification", "Barangay Health Center Records", "Local Resident Transaction Discount"]
        },
        business: {
            title: "Barangay Business Clearance",
            fee: "₱150.00 - ₱300.00",
            turnaround: "2-3 Working Days",
            requirements: [
                "DTI / SEC Registration Certificate",
                "Contract of Lease / Proof of Property Ownership",
                "Location Sketch / Photos of Business Establishment"
            ],
            purposes: ["Mayor's / Business Permit Renewal", "New Business Registration", "Commercial Operations Endorsement"]
        },
        jobseeker: {
            title: "First-Time Jobseeker Certificate (RA 11261)",
            fee: "FREE (₱0.00)",
            turnaround: "24 Hours",
            requirements: [
                "Oath of Undertaking (signed at Barangay Hall or online)",
                "Proof of Residency (min. 6 months)",
                "School Diploma / TOR or Certificate of Graduation"
            ],
            purposes: ["First-time Employment Document Fee Waivers (NBI, Police, Cedula, etc.)"]
        }
    };

    $(document).on('click', '.btn-view-doc-info', function () {
        const docKey = $(this).data('doc');
        const doc = documentCatalog[docKey];
        if (!doc) return;

        $('#modalDocTitle').text(doc.title);
        $('#modalDocFee').text(doc.fee);
        $('#modalDocTurnaround').text(doc.turnaround);

        let reqHtml = '';
        doc.requirements.forEach(function (r) {
            reqHtml += `
                <li class="flex items-start gap-2.5 text-xs text-slate-700">
                    <svg class="w-4 h-4 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>${r}</span>
                </li>
            `;
        });
        $('#modalDocReqList').html(reqHtml);

        let purposeHtml = '';
        doc.purposes.forEach(function (p) {
            purposeHtml += `<span class="px-2.5 py-1 bg-slate-100 rounded-lg text-2xs font-semibold text-slate-700">${p}</span>`;
        });
        $('#modalDocPurposes').html(purposeHtml);

        $('#docInfoModal').removeClass('hidden').addClass('flex');
    });

    $('#btnCloseDocModal, #docInfoModalBackdrop').on('click', function () {
        $('#docInfoModal').addClass('hidden').removeClass('flex');
    });

    // ==========================================
    // 11. FAQ Accordion Toggle
    // ==========================================
    $(document).on('click', '.faq-toggle-btn', function () {
        const item = $(this).closest('.faq-item');
        const answer = item.find('.faq-answer');
        const icon = $(this).find('.faq-icon');
        const isOpen = !answer.hasClass('hidden');

        // Close all other FAQs
        $('.faq-answer').addClass('hidden');
        $('.faq-icon').removeClass('rotate-180 text-indigo-600').addClass('text-slate-400');
        $('.faq-item').removeClass('border-indigo-200 bg-indigo-50/20');

        if (!isOpen) {
            answer.removeClass('hidden');
            icon.addClass('rotate-180 text-indigo-600').removeClass('text-slate-400');
            item.addClass('border-indigo-200 bg-indigo-50/20');
        }
    });

    // ==========================================
    // 12. Smooth Scrolling for Navigation
    // ==========================================
    $('a[href^="#"]').on('click', function (e) {
        const target = $(this.getAttribute('href'));
        if (target.length) {
            e.preventDefault();
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 80
            }, 500);

            // Close mobile menu if open
            if ($('#mobile-menu').length && !$('#mobile-menu').hasClass('hidden')) {
                $('#mobile-menu').addClass('hidden');
                $('#menu-icon').attr('d', 'M4 6h16M4 12h16M4 18h16');
            }
        }
    });
});