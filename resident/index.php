<?php 
include "components/header.php";

$reqStats = $db->getResidentStats($r_id);
$recentRequests = $db->getRecentRequests($r_id, 5);
$latestAnnouncements = $db->getPublishedAnnouncements(2);
?>

<!-- Welcome Banner -->
<div class="mb-6 bg-gradient-to-r from-primary-700 via-primary-600 to-indigo-700 rounded-3xl p-6 sm:p-8 text-white shadow-lg relative overflow-hidden">
    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="max-w-xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 text-white text-xs font-semibold backdrop-blur-xs mb-3">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Barangay e-Services Portal</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Welcome back, <?= $residentFirst ?>!</h1>
            <p class="text-primary-100 text-sm mt-2 leading-relaxed">
                Easily request official barangay documents, track real-time status updates, and view important community announcements.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="MyRequest.php" class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-white text-primary-700 font-bold text-sm hover:bg-primary-50 active:bg-white transition duration-150 shadow-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>New Request</span>
            </a>
            <a href="announcements.php" class="inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-white/15 hover:bg-white/25 text-white font-bold text-sm transition duration-150 border border-white/20" title="View Announcements">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                <span>Announcements</span>
            </a>
        </div>
    </div>
    
    <!-- Decorative background glow -->
    <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
</div>

<!-- Latest Announcements Quick Banner (if any exist) -->
<?php if (!empty($latestAnnouncements)): ?>
<div class="mb-8">
    <div class="flex items-center justify-between mb-3.5">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-primary-600"></span>
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Latest Barangay Announcements</h2>
        </div>
        <a href="announcements.php" class="text-xs font-bold text-primary-600 hover:text-primary-700 flex items-center gap-1">
            <span>View All Announcements</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <?php foreach ($latestAnnouncements as $ann): 
            $annId = (int)$ann['announcement_id'];
            $annTitle = htmlspecialchars($ann['title']);
            $annContent = htmlspecialchars($ann['content']);
            $annCategory = htmlspecialchars($ann['category'] ?? 'General');
            $annDate = date('M d, Y', strtotime($ann['created_at']));
            $isPinned = (int)$ann['is_pinned'] === 1;

            $catClasses = match($ann['category']) {
                'Emergency' => 'bg-rose-50 text-rose-700 border-rose-200',
                'Advisory' => 'bg-amber-50 text-amber-700 border-amber-200',
                'Event' => 'bg-purple-50 text-purple-700 border-purple-200',
                'Health' => 'bg-teal-50 text-teal-700 border-teal-200',
                default => 'bg-primary-50 text-primary-700 border-primary-200'
            };
        ?>
        <div class="bg-white rounded-2xl border <?= $isPinned ? 'border-primary-300 bg-primary-50/20' : 'border-slate-200/80' ?> p-4 sm:p-5 shadow-sm hover:shadow-md transition duration-200 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between gap-2 mb-2">
                    <div class="flex items-center gap-1.5">
                        <?php if ($isPinned): ?>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-2xs font-semibold bg-primary-600 text-white">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>
                                <span>Pinned</span>
                            </span>
                        <?php endif; ?>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-2xs font-bold border <?= $catClasses ?>">
                            <?= $annCategory ?>
                        </span>
                    </div>
                    <span class="text-2xs text-slate-400 font-medium"><?= $annDate ?></span>
                </div>
                <h3 class="text-sm sm:text-base font-bold text-slate-900 line-clamp-1 hover:text-primary-600 transition cursor-pointer btnReadAnnouncement" data-id="<?= $annId ?>">
                    <?= $annTitle ?>
                </h3>
                <p class="text-xs text-slate-500 mt-1 line-clamp-2 leading-relaxed">
                    <?= nl2br($annContent) ?>
                </p>
            </div>
            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between">
                <button type="button" class="btnReadAnnouncement text-xs font-bold text-primary-600 hover:text-primary-700 inline-flex items-center gap-1" data-id="<?= $annId ?>">
                    <span>Read Notice</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm hover:shadow-md transition duration-200 flex items-center justify-between">
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Requests</p>
            <h3 class="text-2xl font-extrabold text-slate-800 mt-1"><?= number_format($reqStats['total'] ?? 0); ?></h3>
            <p class="text-xs text-slate-500 mt-1">Submitted documents</p>
        </div>
        <div class="w-12 h-12 bg-primary-50 text-primary-600 rounded-2xl flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm hover:shadow-md transition duration-200 flex items-center justify-between">
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pending Processing</p>
            <h3 class="text-2xl font-extrabold text-amber-600 mt-1"><?= number_format($reqStats['pending'] ?? 0); ?></h3>
            <p class="text-xs text-amber-600/80 mt-1">Awaiting admin review</p>
        </div>
        <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm hover:shadow-md transition duration-200 flex items-center justify-between">
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Completed / Ready</p>
            <h3 class="text-2xl font-extrabold text-emerald-600 mt-1"><?= number_format($reqStats['completed'] ?? 0); ?></h3>
            <p class="text-xs text-emerald-600/80 mt-1">Approved or Delivered</p>
        </div>
        <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
    </div>
</div>

<!-- Quick Document Request Hub -->
<div class="mb-8">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Available Barangay Documents</h2>
            <p class="text-xs text-slate-500">Select a document below to start your online application</p>
        </div>
        <a href="MyRequest.php" class="text-xs font-bold text-primary-600 hover:text-primary-700 flex items-center gap-1">
            <span>View All</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Barangay ID -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm hover:shadow-md hover:border-primary-300 transition duration-200 flex flex-col justify-between group">
            <div>
                <div class="w-11 h-11 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center mb-3 group-hover:scale-110 transition duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 012-2h2a2 2 0 012 2v2m-6 0h6"></path></svg>
                </div>
                <h3 class="font-bold text-slate-800 text-base">Barangay ID</h3>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">Official resident identification card with digital photo and signature.</p>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                <span class="text-xs font-extrabold text-slate-700">₱ 50.00</span>
                <a href="MyRequest.php?open=id" class="inline-flex items-center gap-1 text-xs font-bold text-primary-600 hover:text-primary-700">
                    <span>Apply Now</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            </div>
        </div>

        <!-- Barangay Clearance -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm hover:shadow-md hover:border-emerald-300 transition duration-200 flex flex-col justify-between group">
            <div>
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-3 group-hover:scale-110 transition duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <h3 class="font-bold text-slate-800 text-base">Barangay Clearance</h3>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">Required for employment, business permits, and identification requirements.</p>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                <span class="text-xs font-extrabold text-slate-700">₱ 50.00</span>
                <a href="MyRequest.php?open=clearance" class="inline-flex items-center gap-1 text-xs font-bold text-emerald-600 hover:text-emerald-700">
                    <span>Apply Now</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            </div>
        </div>

        <!-- Certificate of Residency -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm hover:shadow-md hover:border-violet-300 transition duration-200 flex flex-col justify-between group">
            <div>
                <div class="w-11 h-11 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center mb-3 group-hover:scale-110 transition duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                </div>
                <h3 class="font-bold text-slate-800 text-base">Certificate of Residency</h3>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">Proof of residence for bank accounts, utility connections, and school requirements.</p>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                <span class="text-xs font-extrabold text-slate-700">₱ 50.00</span>
                <a href="MyRequest.php?open=residency" class="inline-flex items-center gap-1 text-xs font-bold text-violet-600 hover:text-violet-700">
                    <span>Apply Now</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            </div>
        </div>

        <!-- Certificate of Indigency -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm hover:shadow-md hover:border-amber-300 transition duration-200 flex flex-col justify-between group">
            <div>
                <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center mb-3 group-hover:scale-110 transition duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                </div>
                <h3 class="font-bold text-slate-800 text-base">Certificate of Indigency</h3>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">For educational scholarships, medical assistance, and government welfare programs.</p>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                <span class="text-xs font-extrabold text-slate-700">₱ 50.00</span>
                <a href="MyRequest.php?open=indigency" class="inline-flex items-center gap-1 text-xs font-bold text-amber-600 hover:text-amber-700">
                    <span>Apply Now</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Recent Requests Feed -->
<div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-sm mb-8">
    <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
        <div>
            <h2 class="text-base font-bold text-slate-800">Recent Request Activity</h2>
            <p class="text-xs text-slate-500">Latest updates on your submitted document requests</p>
        </div>
        <a href="MyRequest.php" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition">
            View All Requests
        </a>
    </div>

    <?php if (!empty($recentRequests)): ?>
        <div class="overflow-x-auto -mx-2">
            <table class="w-full text-sm text-left text-slate-600">
                <thead class="bg-slate-50 text-slate-700 font-bold text-xs uppercase tracking-wider">
                    <tr>
                        <th class="p-3">Tracking Code</th>
                        <th class="p-3">Document Type</th>
                        <th class="p-3">Purpose</th>
                        <th class="p-3">Date</th>
                        <th class="p-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($recentRequests as $req): 
                        $st = $req['cr_status'] ?? 'Pending';
                        $badge = match($st) {
                            'Pending' => 'bg-amber-100 text-amber-800',
                            'Approved' => 'bg-blue-100 text-blue-800',
                            'Shipped' => 'bg-purple-100 text-purple-800',
                            'Delivered' => 'bg-emerald-100 text-emerald-800',
                            'Rejected' => 'bg-rose-100 text-rose-800',
                            'Canceled' => 'bg-slate-100 text-slate-800',
                            default => 'bg-slate-100 text-slate-700'
                        };
                        $reqDate = !empty($req['cr_request_date']) ? date('M d, Y', strtotime($req['cr_request_date'])) : '—';
                    ?>
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="p-3 font-semibold text-slate-800 text-xs"><?= htmlspecialchars($req['cr_code'] ?? ('#' . $req['cr_id'])); ?></td>
                            <td class="p-3 font-medium text-slate-800"><?= htmlspecialchars($req['cr_formtype'] ?? ''); ?></td>
                            <td class="p-3 text-slate-500 text-xs truncate max-w-xs"><?= htmlspecialchars($req['cr_purpose'] ?? '—'); ?></td>
                            <td class="p-3 text-slate-500 text-xs"><?= $reqDate; ?></td>
                            <td class="p-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold <?= $badge; ?>">
                                    <?= htmlspecialchars($st); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="text-center py-8">
            <svg class="w-12 h-12 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <p class="text-sm font-semibold text-slate-600">No recent document requests</p>
            <p class="text-xs text-slate-400 mt-0.5">Your submitted document requests will appear here.</p>
        </div>
    <?php endif; ?>
</div>

<div id="readModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden overflow-y-auto">
    <div class="min-h-screen px-4 py-8 flex items-center justify-center">
        <div class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border border-slate-100 relative">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <div class="flex items-center gap-2" id="modalBadges"></div>
                <button type="button" class="btnCloseReadModal text-slate-400 hover:text-slate-600 p-2 rounded-xl hover:bg-slate-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div id="modalImageContainer" class="mb-5 hidden">
                <img id="modalImage" src="" alt="Announcement Banner" class="w-full max-h-72 object-cover rounded-2xl border border-slate-200">
            </div>

            <h1 id="modalTitle" class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight leading-snug"></h1>

            <div class="flex items-center gap-2 text-xs text-slate-400 font-medium mt-2 pb-4 border-b border-slate-100">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <span id="modalMeta"></span>
            </div>

            <div id="modalContent" class="text-sm text-slate-700 leading-relaxed mt-4 whitespace-pre-line font-normal"></div>

            <div class="flex items-center justify-end mt-6 pt-4 border-t border-slate-100">
                <button type="button" class="btnCloseReadModal px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                    Close Notice
                </button>
            </div>
        </div>
    </div>
</div>

<script src="js/announcements.js"></script>

<?php include "components/footer.php"; ?>
