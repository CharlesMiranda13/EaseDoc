$(document).ready(function() {

    function getCsrfToken() {
        return $('meta[name="csrf-token"]').attr('content') || $('input[name="csrf_token"]').val() || '';
    }

    function setupImagePreview(inputSelector, previewContainerSelector, removeBtnSelector) {
        $(inputSelector).on('change', function() {
            var file = this.files[0];
            var $container = $(previewContainerSelector);
            var $img = $container.find('img');

            if (!file) {
                $container.addClass('hidden');
                $img.attr('src', '');
                return;
            }

            if (file.size > 10 * 1024 * 1024) {
                alertify.error('Selected image exceeds the 10MB limit.');
                $(this).val('');
                $container.addClass('hidden');
                return;
            }

            var validTypes = ['image/jpeg', 'image/png', 'image/webp'];
            if (!validTypes.includes(file.type)) {
                alertify.error('Only JPG, PNG, and WEBP image files are allowed.');
                $(this).val('');
                $container.addClass('hidden');
                return;
            }

            var reader = new FileReader();
            reader.onload = function(e) {
                $img.attr('src', e.target.result);
                $container.removeClass('hidden');
            };
            reader.readAsDataURL(file);
        });

        if (removeBtnSelector) {
            $(removeBtnSelector).on('click', function(e) {
                e.preventDefault();
                $(inputSelector).val('');
                $(previewContainerSelector).addClass('hidden').find('img').attr('src', '');
            });
        }
    }

    setupImagePreview('#create_image', '#createImagePreview', '#btnRemoveCreateImage');
    setupImagePreview('#edit_image', '#editImagePreview', '#btnRemoveEditNewImage');

    function filterAnnouncements() {
        var query = ($('#announcementSearch').val() || '').toLowerCase().trim();
        var cat = $('#categoryFilter').val();

        $('.announcement-item').each(function() {
            var $item = $(this);
            var title = ($item.data('title') || '').toString().toLowerCase();
            var content = $item.find('p').text().toLowerCase();
            var itemCat = ($item.data('category') || '').toString();

            var matchesQuery = !query || title.indexOf(query) !== -1 || content.indexOf(query) !== -1;
            var matchesCat = !cat || itemCat === cat;

            $item.toggleClass('hidden', !(matchesQuery && matchesCat));
        });
    }

    $('#announcementSearch').on('input', filterAnnouncements);
    $('#categoryFilter').on('change', filterAnnouncements);

    $('#btnCreateAnnouncement, .btnCreateAnnouncementTrigger').on('click', function() {
        $('#frmCreateAnnouncement')[0].reset();
        $('#createImagePreview').addClass('hidden').find('img').attr('src', '');
        $('#createAnnouncementModal').fadeIn(150);
    });

    $('.btnCloseCreateModal').on('click', function() {
        $('#createAnnouncementModal').fadeOut(120);
    });

    $('#frmCreateAnnouncement').on('submit', function(e) {
        e.preventDefault();

        var title = $('#create_title').val().trim();
        var content = $('#create_content').val().trim();

        if (!title) {
            alertify.error('Please enter an announcement title.');
            $('#create_title').focus();
            return;
        }

        if (!content) {
            alertify.error('Please enter announcement content.');
            $('#create_content').focus();
            return;
        }

        var formData = new FormData(this);
        formData.append('requestType', 'CreateAnnouncement');

        var $btn = $('#btnSubmitCreate');
        $btn.prop('disabled', true).text('Saving...');

        $.ajax({
            url: 'backend/end-points/controller.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    alertify.success(response.message || 'Announcement created successfully.');
                    setTimeout(function() {
                        location.reload();
                    }, 800);
                } else {
                    alertify.error(response.message || 'Failed to create announcement.');
                    $btn.prop('disabled', false).text('Save Announcement');
                }
            },
            error: function(xhr) {
                var msg = 'An error occurred while saving.';
                try {
                    var res = JSON.parse(xhr.responseText);
                    if (res && res.message) msg = res.message;
                } catch(err) {}
                alertify.error(msg);
                $btn.prop('disabled', false).text('Save Announcement');
            }
        });
    });

    $(document).on('click', '.btnEditAnnouncement', function() {
        var id = $(this).data('id');
        if (!id) return;

        $('#frmEditAnnouncement')[0].reset();
        $('#edit_remove_image').val('0');
        $('#editImagePreview').addClass('hidden').find('img').attr('src', '');
        $('#editCurrentImageContainer').addClass('hidden');

        $.ajax({
            url: 'backend/end-points/controller.php',
            method: 'GET',
            data: {
                requestType: 'GetAnnouncementDetails',
                announcement_id: id
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success' && response.data) {
                    var data = response.data;
                    $('#edit_announcement_id').val(data.announcement_id);
                    $('#edit_title').val(data.title);
                    $('#edit_category').val(data.category);
                    $('#edit_status').val(data.status);
                    $('#edit_content').val(data.content);
                    $('#edit_is_pinned').prop('checked', parseInt(data.is_pinned) === 1);

                    if (data.image) {
                        $('#editCurrentImage').attr('src', '../uploads/announcements/' + data.image);
                        $('#editCurrentImageContainer').removeClass('hidden');
                    }

                    $('#editAnnouncementModal').fadeIn(150);
                } else {
                    alertify.error(response.message || 'Failed to load announcement details.');
                }
            },
            error: function() {
                alertify.error('Failed to load announcement.');
            }
        });
    });

    $('#btnDeleteCurrentImage').on('click', function(e) {
        e.preventDefault();
        $('#editCurrentImageContainer').addClass('hidden');
        $('#edit_remove_image').val('1');
    });

    $('.btnCloseEditModal').on('click', function() {
        $('#editAnnouncementModal').fadeOut(120);
    });

    $('#frmEditAnnouncement').on('submit', function(e) {
        e.preventDefault();

        var title = $('#edit_title').val().trim();
        var content = $('#edit_content').val().trim();

        if (!title) {
            alertify.error('Please enter an announcement title.');
            $('#edit_title').focus();
            return;
        }

        if (!content) {
            alertify.error('Please enter announcement content.');
            $('#edit_content').focus();
            return;
        }

        var formData = new FormData(this);
        formData.append('requestType', 'UpdateAnnouncement');

        var $btn = $('#btnSubmitEdit');
        $btn.prop('disabled', true).text('Saving...');

        $.ajax({
            url: 'backend/end-points/controller.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    alertify.success(response.message || 'Announcement updated successfully.');
                    setTimeout(function() {
                        location.reload();
                    }, 800);
                } else {
                    alertify.error(response.message || 'Failed to update announcement.');
                    $btn.prop('disabled', false).text('Save Changes');
                }
            },
            error: function(xhr) {
                var msg = 'An error occurred while updating.';
                try {
                    var res = JSON.parse(xhr.responseText);
                    if (res && res.message) msg = res.message;
                } catch(err) {}
                alertify.error(msg);
                $btn.prop('disabled', false).text('Save Changes');
            }
        });
    });

    $(document).on('click', '.btnChangeStatus', function() {
        var id = $(this).data('id');
        var newStatus = $(this).data('status');
        var csrf = getCsrfToken();

        if (!id || !newStatus) return;

        $.ajax({
            url: 'backend/end-points/controller.php',
            method: 'POST',
            data: {
                requestType: 'UpdateAnnouncementStatus',
                announcement_id: id,
                status: newStatus,
                csrf_token: csrf
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    alertify.success(response.message || 'Status updated.');
                    setTimeout(function() {
                        location.reload();
                    }, 600);
                } else {
                    alertify.error(response.message || 'Failed to update status.');
                }
            },
            error: function() {
                alertify.error('An error occurred while updating status.');
            }
        });
    });

    $(document).on('click', '.btnTogglePin', function() {
        var id = $(this).data('id');
        var currentPin = parseInt($(this).data('pinned')) || 0;
        var newPin = currentPin === 1 ? 0 : 1;
        var csrf = getCsrfToken();

        if (!id) return;

        $.ajax({
            url: 'backend/end-points/controller.php',
            method: 'POST',
            data: {
                requestType: 'ToggleAnnouncementPin',
                announcement_id: id,
                is_pinned: newPin,
                csrf_token: csrf
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    alertify.success(response.message || 'Pin status updated.');
                    setTimeout(function() {
                        location.reload();
                    }, 600);
                } else {
                    alertify.error(response.message || 'Failed to update pin.');
                }
            },
            error: function() {
                alertify.error('An error occurred while updating pin status.');
            }
        });
    });

    $(document).on('click', '.btnDeleteAnnouncement', function() {
        var id = $(this).data('id');
        var title = $(this).data('title') || 'this announcement';
        var csrf = getCsrfToken();

        if (!id) return;

        Swal.fire({
            title: 'Delete Announcement?',
            text: 'Are you sure you want to delete "' + title + '"? This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'Cancel'
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'backend/end-points/controller.php',
                    method: 'POST',
                    data: {
                        requestType: 'DeleteAnnouncement',
                        announcement_id: id,
                        csrf_token: csrf
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            alertify.success(response.message || 'Announcement deleted.');
                            setTimeout(function() {
                                location.reload();
                            }, 700);
                        } else {
                            alertify.error(response.message || 'Failed to delete announcement.');
                        }
                    },
                    error: function() {
                        alertify.error('An error occurred while deleting the announcement.');
                    }
                });
            }
        });
    });

    $(document).on('click', '.viewAnnouncementBtn', function() {
        var id = $(this).data('id');
        if (!id) return;

        $.ajax({
            url: 'backend/end-points/controller.php',
            method: 'GET',
            data: {
                requestType: 'GetAnnouncementDetails',
                announcement_id: id
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success' && response.data) {
                    var a = response.data;
                    $('#viewTitle').text(a.title);
                    $('#viewContent').text(a.content);

                    var author = (a.user_fname ? a.user_fname + ' ' + (a.user_lname || '') : 'Barangay Admin').trim();
                    var dateStr = new Date(a.created_at).toLocaleDateString('en-US', {
                        year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit'
                    });
                    $('#viewMeta').text('Posted by ' + author + ' • ' + dateStr);

                    var badgesHtml = '';
                    if (parseInt(a.is_pinned) === 1) {
                        badgesHtml += '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-primary-100 text-primary-800 border border-primary-300"><svg class="w-3 h-3 text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg><span>Pinned</span></span>';
                    }
                    badgesHtml += '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">' + (a.category || 'General') + '</span>';
                    badgesHtml += '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">' + a.status + '</span>';
                    $('#viewBadges').html(badgesHtml);

                    if (a.image) {
                        $('#viewImage').attr('src', '../uploads/announcements/' + a.image);
                        $('#viewImageContainer').removeClass('hidden');
                    } else {
                        $('#viewImageContainer').addClass('hidden');
                    }

                    $('#viewAnnouncementModal').fadeIn(150);
                }
            }
        });
    });

    $('.btnCloseViewModal').on('click', function() {
        $('#viewAnnouncementModal').fadeOut(120);
    });

    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') {
            $('#createAnnouncementModal, #editAnnouncementModal, #viewAnnouncementModal').fadeOut(120);
        }
    });

});
