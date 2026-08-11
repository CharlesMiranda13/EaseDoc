<?php 
include "components/header.php";

$categoryFilter = isset($_GET['category']) ? trim($_GET['category']) : '';
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';

$allAnnouncements = $db->getPublishedAnnouncements(null, $categoryFilter ?: null, $searchQuery ?: null);
$categories = $db->getAnnouncementCategories();

$pinnedAnnouncements = [];
$regularAnnouncements = [];

foreach ($allAnnouncements as $ann) {
    if ((int)$ann['is_pinned'] === 1) {
        $pinnedAnnouncements[] = $ann;
    } else {
        $regularAnnouncements[] = $ann;
    }
}
?>

<div class="mb-6 bg-gradient-to-r from-slate-900 via-primary-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-lg relative overflow-hidden">
    <div class="relative z-10 max-w-2xl">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-primary-200 text-xs font-semibold backdrop-blur-xs mb-3 border border-white/10">
            <svg class="w-3.5 h-3.5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
            <span>Official Barangay Bulletin</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Barangay Announcements</h1>
        <p class="text-slate-300 text-sm mt-2 leading-relaxed">
            Stay informed with verified news, public advisories, schedule reminders, and emergency updates directly from the barangay administration.
        </p>
    </div>
    <div class="absolute -right-8 -bottom-8 w-60 h-60 bg-primary-600/20 rounded-full blur-3xl pointer-events-none"></div>
</div>

<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-4 sm:p-5 mb-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 md:pb-0 scrollbar-none">
            <a href="announcements.php" class="px-3.5 py-2 rounded-xl text-xs font-semibold transition flex-shrink-0 <?= empty($categoryFilter) ? 'bg-primary-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
                All Categories
            </a>
            <?php 
            $defaultCats = ['Advisory', 'Event', 'Emergency', 'Health', 'Public Notice', 'General'];
            $activeCats = !empty($categories) ? array_unique(array_merge($categories, $defaultCats)) : $defaultCats;
            foreach ($activeCats as $cat): 
                $isSelected = ($categoryFilter === $cat);
            ?>
                <a href="announcements.php?category=<?= urlencode($cat) ?>" class="px-3.5 py-2 rounded-xl text-xs font-semibold transition flex-shrink-0 <?= $isSelected ? 'bg-primary-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
                    <?= htmlspecialchars($cat) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="relative w-full md:w-64 flex-shrink-0">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" id="residentSearchInput" class="pl-9 pr-3 py-2 border border-slate-300 rounded-xl text-xs sm:text-sm w-full focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition" placeholder="Search notices..." value="<?= htmlspecialchars($searchQuery) ?>">
        </div>
    </div>
</div>

<?php if (!empty($pinnedAnnouncements) && empty($categoryFilter) && empty($searchQuery)): ?>
<div class="mb-8">
    <div class="flex items-center gap-2 mb-3">
        <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>
        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Important Announcements</h2>
    </div>

    <div class="grid grid-cols-1 gap-4">
        <?php foreach ($pinnedAnnouncements as $pinned): 
            $pId = (int)$pinned['announcement_id'];
            $pTitle = htmlspecialchars($pinned['title']);
            $pContent = htmlspecialchars($pinned['content']);
            $pCategory = htmlspecialchars($pinned['category'] ?? 'General');
            $pDate = date('F j, Y', strtotime($pinned['created_at']));
            $pImage = !empty($pinned['image']) ? htmlspecialchars($pinned['image']) : '';

            $catClasses = match($pinned['category']) {
                'Emergency' => 'bg-rose-50 text-rose-700 border-rose-200',
                'Advisory' => 'bg-amber-50 text-amber-700 border-amber-200',
                'Event' => 'bg-purple-50 text-purple-700 border-purple-200',
                'Health' => 'bg-teal-50 text-teal-700 border-teal-200',
                default => 'bg-primary-50 text-primary-700 border-primary-200'
            };
        ?>
        <div class="bg-gradient-to-br from-white to-primary-50/40 rounded-2xl border-2 border-primary-200 p-5 sm:p-6 shadow-sm hover:shadow-md transition duration-200">
            <div class="flex flex-col md:flex-row gap-5 items-start">
                <?php if (!empty($pImage)): ?>
                    <img src="../uploads/announcements/<?= $pImage ?>" alt="<?= $pTitle ?>" class="w-full md:w-56 h-36 object-cover rounded-xl border border-primary-100 flex-shrink-0">
                <?php endif; ?>

                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-2">
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-primary-600 text-white">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>
                            <span>Pinned</span>
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border <?= $catClasses ?>">
                            <?= $pCategory ?>
                        </span>
                        <span class="text-xs text-slate-500 font-medium">
                            <?= $pDate ?>
                        </span>
                    </div>

                    <h3 class="text-lg sm:text-xl font-extrabold text-slate-900 hover:text-primary-600 transition cursor-pointer btnReadAnnouncement" data-id="<?= $pId ?>">
                        <?= $pTitle ?>
                    </h3>

                    <p class="text-sm text-slate-600 mt-2 line-clamp-3 leading-relaxed">
                        <?= nl2br($pContent) ?>
                    </p>

                    <div class="mt-4">
                        <button type="button" class="btnReadAnnouncement inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary-600 hover:bg-primary-700 active:bg-primary-800 text-white text-xs font-semibold transition shadow-xs" data-id="<?= $pId ?>">
                            <span>Read Full Announcement</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<div class="mb-8">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-base font-bold text-slate-800">
                <?= !empty($categoryFilter) ? htmlspecialchars($categoryFilter) . ' Announcements' : 'All Recent Announcements' ?>
            </h2>
            <p class="text-xs text-slate-500">Official notices released by the barangay</p>
        </div>
    </div>

    <?php if (empty($allAnnouncements)): ?>
        <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
            <div class="w-16 h-16 bg-slate-100 text-slate-400 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
            </div>
            <h3 class="text-base font-bold text-slate-700">No announcements available</h3>
            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">There are currently no announcements matching your selected category or filter.</p>
            <?php if (!empty($categoryFilter) || !empty($searchQuery)): ?>
                <a href="announcements.php" class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-semibold hover:bg-slate-200 transition">
                    <span>Clear Filters</span>
                </a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5" id="residentAnnouncementsGrid">
            <?php foreach ($allAnnouncements as $item): 
                $id = (int)$item['announcement_id'];
                $title = htmlspecialchars($item['title']);
                $content = htmlspecialchars($item['content']);
                $category = htmlspecialchars($item['category'] ?? 'General');
                $isPinned = (int)$item['is_pinned'] === 1;
                $dateFormatted = date('F j, Y', strtotime($item['created_at']));
                $image = !empty($item['image']) ? htmlspecialchars($item['image']) : '';

                $catClasses = match($item['category']) {
                    'Emergency' => 'bg-rose-50 text-rose-700 border-rose-200',
                    'Advisory' => 'bg-amber-50 text-amber-700 border-amber-200',
                    'Event' => 'bg-purple-50 text-purple-700 border-purple-200',
                    'Health' => 'bg-teal-50 text-teal-700 border-teal-200',
                    'Public Notice' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                    default => 'bg-blue-50 text-blue-700 border-blue-200'
                };
            ?>
            <div class="resident-ann-card bg-white rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition duration-200 flex flex-col overflow-hidden group" data-title="<?= strtolower($title) ?>" data-content="<?= strtolower($content) ?>" data-category="<?= $category ?>">
                <?php if (!empty($image)): ?>
                    <div class="h-44 w-full overflow-hidden bg-slate-100 relative">
                        <img src="../uploads/announcements/<?= $image ?>" alt="<?= $title ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        <?php if ($isPinned): ?>
                            <span class="absolute top-3 left-3 inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-primary-600 text-white shadow-md">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>
                                <span>Pinned</span>
                            </span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="p-5 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-2.5">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-2xs font-semibold border <?= $catClasses ?>">
                                <?= $category ?>
                            </span>
                            <span class="text-2xs text-slate-400 font-medium">
                                <?= $dateFormatted ?>
                            </span>
                        </div>

                        <h3 class="text-base font-bold text-slate-900 group-hover:text-primary-600 transition cursor-pointer line-clamp-2 btnReadAnnouncement" data-id="<?= $id ?>">
                            <?= $title ?>
                        </h3>

                        <p class="text-xs text-slate-500 mt-2 line-clamp-3 leading-relaxed">
                            <?= nl2br($content) ?>
                        </p>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                        <button type="button" class="btnReadAnnouncement inline-flex items-center gap-1.5 text-xs font-semibold text-primary-600 hover:text-primary-700 transition" data-id="<?= $id ?>">
                            <span>Read Announcement</span>
                            <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
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
