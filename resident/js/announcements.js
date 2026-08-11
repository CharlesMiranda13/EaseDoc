$(document).ready(function() {

    $('#residentSearchInput').on('input', function() {
        var query = $(this).val().toLowerCase().trim();

        $('.resident-ann-card').each(function() {
            var $card = $(this);
            var title = ($card.data('title') || '').toString().toLowerCase();
            var content = ($card.data('content') || '').toString().toLowerCase();

            $card.toggleClass('hidden', Boolean(query && title.indexOf(query) === -1 && content.indexOf(query) === -1));
        });
    });

    $(document).on('click', '.btnReadAnnouncement', function() {
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
                    $('#modalTitle').text(a.title);
                    $('#modalContent').text(a.content);

                    var dateObj = new Date(a.created_at);
                    var formattedDate = dateObj.toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                    $('#modalMeta').text('Released on ' + formattedDate + ' by Barangay Administration');

                    var category = a.category || 'General';
                    var isPinned = parseInt(a.is_pinned) === 1;

                    var catClasses = 'bg-blue-50 text-blue-700 border-blue-200';
                    if (category === 'Emergency') catClasses = 'bg-rose-50 text-rose-700 border-rose-200';
                    else if (category === 'Advisory') catClasses = 'bg-amber-50 text-amber-700 border-amber-200';
                    else if (category === 'Event') catClasses = 'bg-purple-50 text-purple-700 border-purple-200';
                    else if (category === 'Health') catClasses = 'bg-teal-50 text-teal-700 border-teal-200';
                    else if (category === 'Public Notice') catClasses = 'bg-indigo-50 text-indigo-700 border-indigo-200';

                    var badgesHtml = '';
                    if (isPinned) {
                        badgesHtml += '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-primary-600 text-white"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg><span>Pinned</span></span>';
                    }
                    badgesHtml += '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border ' + catClasses + '">' + category + '</span>';

                    $('#modalBadges').html(badgesHtml);

                    if (a.image) {
                        $('#modalImage').attr('src', '../uploads/announcements/' + a.image);
                        $('#modalImageContainer').removeClass('hidden');
                    } else {
                        $('#modalImageContainer').addClass('hidden');
                    }

                    $('#readModal').fadeIn(150);
                } else {
                    alertify.error(response.message || 'Announcement could not be loaded.');
                }
            },
            error: function() {
                alertify.error('Failed to load announcement details.');
            }
        });
    });

    $('.btnCloseReadModal').on('click', function() {
        $('#readModal').fadeOut(120);
    });

    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') {
            $('#readModal').fadeOut(120);
        }
    });

});
