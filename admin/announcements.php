<?php 
include "components/header.php";

$statusFilter = isset($_GET['status']) ? trim($_GET['status']) : '';
$categoryFilter = isset($_GET['category']) ? trim($_GET['category']) : '';
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';

$stats = $db->getAnnouncementStats();
$announcements = $db->fetch_all_announcements($statusFilter ?: null, $categoryFilter ?: null, $searchQuery ?: null);
?>

<div class="mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Barangay Announcements</h1>
            <p class="text-sm text-slate-500 mt-0.5">Broadcast news, official notices, advisories, and events to residents</p>
        </div>
        <div>
            <button id="btnCreateAnnouncement" class="inline-flex items-center justify-center gap-2 bg-primary-600 hover:bg-primary-700 active:bg-primary-800 text-white px-4 py-2.5 text-sm font-semibold rounded-xl shadow-sm hover:shadow transition duration-150">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Create Announcement</span>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-slate-200/80 p-5 shadow-sm hover:shadow-md transition duration-200 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total</p>
                <h3 class="text-2xl font-extrabold text-slate-800 mt-1"><?= number_format($stats['total'] ?? 0); ?></h3>
                <p class="text-xs text-slate-500 font-medium mt-1">All records</p>
            </div>
            <div class="w-11 h-11 bg-slate-100 text-slate-600 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200/80 p-5 shadow-sm hover:shadow-md transition duration-200 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Published</p>
                <h3 class="text-2xl font-extrabold text-emerald-600 mt-1"><?= number_format($stats['published'] ?? 0); ?></h3>
                <p class="text-xs text-emerald-600 font-medium mt-1">Active for residents</p>
            </div>
            <div class="w-11 h-11 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200/80 p-5 shadow-sm hover:shadow-md transition duration-200 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Drafts</p>
                <h3 class="text-2xl font-extrabold text-amber-600 mt-1"><?= number_format($stats['draft'] ?? 0); ?></h3>
                <p class="text-xs text-amber-600 font-medium mt-1">Unpublished</p>
            </div>
            <div class="w-11 h-11 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200/80 p-5 shadow-sm hover:shadow-md transition duration-200 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pinned</p>
                <h3 class="text-2xl font-extrabold text-primary-600 mt-1"><?= number_format($stats['pinned'] ?? 0); ?></h3>
                <p class="text-xs text-primary-600 font-medium mt-1">Featured at top</p>
            </div>
            <div class="w-11 h-11 bg-primary-50 text-primary-600 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 sm:p-6 mb-8">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6 pb-5 border-b border-slate-100">
        <div class="flex items-center space-x-1 bg-slate-100/80 p-1 rounded-xl text-xs font-semibold overflow-x-auto">
            <a href="announcements.php" class="px-3.5 py-1.5 rounded-lg transition <?= empty($statusFilter) ? 'bg-white text-slate-800 shadow-xs' : 'text-slate-500 hover:text-slate-800' ?>">All</a>
            <a href="announcements.php?status=Published" class="px-3.5 py-1.5 rounded-lg transition <?= $statusFilter === 'Published' ? 'bg-white text-emerald-700 shadow-xs' : 'text-slate-500 hover:text-slate-800' ?>">Published</a>
            <a href="announcements.php?status=Draft" class="px-3.5 py-1.5 rounded-lg transition <?= $statusFilter === 'Draft' ? 'bg-white text-amber-700 shadow-xs' : 'text-slate-500 hover:text-slate-800' ?>">Drafts</a>
            <a href="announcements.php?status=Archived" class="px-3.5 py-1.5 rounded-lg transition <?= $statusFilter === 'Archived' ? 'bg-white text-slate-700 shadow-xs' : 'text-slate-500 hover:text-slate-800' ?>">Archived</a>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <div class="relative w-full sm:w-64">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" id="announcementSearch" class="pl-9 pr-3 py-2 border border-slate-300 rounded-xl text-sm w-full focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition" placeholder="Search announcements..." value="<?= htmlspecialchars($searchQuery) ?>">
            </div>

            <select id="categoryFilter" class="px-3 py-2 border border-slate-300 rounded-xl text-sm font-medium text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                <option value="">All Categories</option>
                <option value="General" <?= $categoryFilter === 'General' ? 'selected' : '' ?>>General</option>
                <option value="Advisory" <?= $categoryFilter === 'Advisory' ? 'selected' : '' ?>>Advisory</option>
                <option value="Event" <?= $categoryFilter === 'Event' ? 'selected' : '' ?>>Event</option>
                <option value="Emergency" <?= $categoryFilter === 'Emergency' ? 'selected' : '' ?>>Emergency</option>
                <option value="Health" <?= $categoryFilter === 'Health' ? 'selected' : '' ?>>Health</option>
                <option value="Public Notice" <?= $categoryFilter === 'Public Notice' ? 'selected' : '' ?>>Public Notice</option>
            </select>
        </div>
    </div>

    <?php if (empty($announcements)): ?>
        <div class="text-center py-12 px-4">
            <div class="w-16 h-16 bg-slate-100 text-slate-400 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
            </div>
            <h3 class="text-base font-bold text-slate-700">No announcements found</h3>
            <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">Get started by creating an official barangay announcement for residents.</p>
            <button class="btnCreateAnnouncementTrigger mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary-600 text-white text-xs font-semibold hover:bg-primary-700 transition shadow-xs">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Create First Announcement</span>
            </button>
        </div>
    <?php else: ?>
        <div class="space-y-4" id="announcementsContainer">
            <?php foreach ($announcements as $item): 
                $id = (int)$item['announcement_id'];
                $title = htmlspecialchars($item['title']);
                $content = htmlspecialchars($item['content']);
                $category = htmlspecialchars($item['category'] ?? 'General');
                $status = htmlspecialchars($item['status']);
                $isPinned = (int)$item['is_pinned'] === 1;
                $creator = htmlspecialchars(trim(($item['user_fname'] ?? '') . ' ' . ($item['user_lname'] ?? '')) ?: 'Barangay Admin');
                $dateFormatted = date('F j, Y • g:i A', strtotime($item['created_at']));
                $image = !empty($item['image']) ? htmlspecialchars($item['image']) : '';

                $catClasses = match($item['category']) {
                    'Emergency' => 'bg-rose-50 text-rose-700 border-rose-200',
                    'Advisory' => 'bg-amber-50 text-amber-700 border-amber-200',
                    'Event' => 'bg-purple-50 text-purple-700 border-purple-200',
                    'Health' => 'bg-teal-50 text-teal-700 border-teal-200',
                    'Public Notice' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                    default => 'bg-blue-50 text-blue-700 border-blue-200'
                };

                $statusClasses = match($status) {
                    'Published' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                    'Draft' => 'bg-amber-50 text-amber-700 border-amber-200',
                    default => 'bg-slate-100 text-slate-600 border-slate-200'
                };
            ?>
            <div class="announcement-item border rounded-2xl p-5 transition duration-200 <?= $isPinned ? 'border-primary-300 bg-primary-50/20 shadow-xs' : 'border-slate-200 bg-white hover:border-slate-300' ?>" data-id="<?= $id ?>" data-title="<?= strtolower($title) ?>" data-category="<?= $category ?>">
                <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-2">
                            <?php if ($isPinned): ?>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-primary-100 text-primary-800 border border-primary-300">
                                    <svg class="w-3 h-3 text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>
                                    <span>Pinned</span>
                                </span>
                            <?php endif; ?>

                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border <?= $catClasses ?>">
                                <?= $category ?>
                            </span>

                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border <?= $statusClasses ?>">
                                <?= $status ?>
                            </span>

                            <span class="text-xs text-slate-400 font-medium ml-1">
                                Posted by <?= $creator ?> on <?= $dateFormatted ?>
                            </span>
                        </div>

                        <h2 class="text-lg font-bold text-slate-800 hover:text-primary-600 transition cursor-pointer viewAnnouncementBtn" data-id="<?= $id ?>">
                            <?= $title ?>
                        </h2>

                        <p class="text-sm text-slate-600 mt-2 line-clamp-2 leading-relaxed">
                            <?= nl2br($content) ?>
                        </p>

                        <?php if (!empty($image)): ?>
                            <div class="mt-3 flex items-center gap-2">
                                <a href="../uploads/announcements/<?= $image ?>" target="_blank" class="inline-flex items-center gap-2 text-xs font-semibold text-primary-600 hover:text-primary-700 bg-primary-50 px-3 py-1.5 rounded-lg border border-primary-100 hover:bg-primary-100/60 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <span>Attached Image</span>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="flex flex-wrap items-center gap-1.5 sm:self-start flex-shrink-0 pt-1">
                        <button class="btnTogglePin inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-xs font-semibold border transition <?= $isPinned ? 'bg-primary-50 text-primary-700 border-primary-200 hover:bg-primary-100' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100' ?>" data-id="<?= $id ?>" data-pinned="<?= $isPinned ? '1' : '0' ?>" title="<?= $isPinned ? 'Unpin announcement' : 'Pin to top of resident feed' ?>">
                            <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>
                            <span><?= $isPinned ? 'Unpin' : 'Pin' ?></span>
                        </button>

                        <?php if ($status === 'Published'): ?>
                            <button class="btnChangeStatus px-2.5 py-1.5 rounded-xl text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 transition" data-id="<?= $id ?>" data-status="Draft" title="Set as Draft">
                                Unpublish
                            </button>
                        <?php else: ?>
                            <button class="btnChangeStatus px-2.5 py-1.5 rounded-xl text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition" data-id="<?= $id ?>" data-status="Published" title="Publish to residents">
                                Publish
                            </button>
                        <?php endif; ?>

                        <?php if ($status !== 'Archived'): ?>
                            <button class="btnChangeStatus px-2.5 py-1.5 rounded-xl text-xs font-semibold bg-slate-50 text-slate-600 border border-slate-200 hover:bg-slate-100 transition" data-id="<?= $id ?>" data-status="Archived" title="Archive announcement">
                                Archive
                            </button>
                        <?php endif; ?>

                        <button class="btnEditAnnouncement p-2 rounded-xl text-slate-600 bg-slate-50 border border-slate-200 hover:bg-slate-100 hover:text-primary-600 transition" data-id="<?= $id ?>" title="Edit details">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        </button>

                        <button class="btnDeleteAnnouncement p-2 rounded-xl text-rose-600 bg-rose-50 border border-rose-200 hover:bg-rose-100 transition" data-id="<?= $id ?>" data-title="<?= $title ?>" title="Delete permanently">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<div id="createAnnouncementModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden overflow-y-auto">
    <div class="min-h-screen px-4 py-8 flex items-center justify-center">
        <div class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border border-slate-100 relative">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Create Barangay Announcement</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Publish official information to verified residents</p>
                </div>
                <button type="button" class="btnCloseCreateModal text-slate-400 hover:text-slate-600 p-2 rounded-xl hover:bg-slate-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form id="frmCreateAnnouncement" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= csrf_token(); ?>">

                <div class="space-y-4">
                    <div>
                        <label for="create_title" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Announcement Title <span class="text-rose-500">*</span></label>
                        <input type="text" id="create_title" name="title" required maxlength="255" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition" placeholder="Enter announcement title">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="create_category" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Category <span class="text-rose-500">*</span></label>
                            <select id="create_category" name="category" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-sm font-medium bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                                <option value="General">General Notice</option>
                                <option value="Advisory">Public Advisory</option>
                                <option value="Event">Community Event</option>
                                <option value="Emergency">Emergency Alert</option>
                                <option value="Health">Health & Wellness</option>
                                <option value="Public Notice">Public Notice</option>
                            </select>
                        </div>

                        <div>
                            <label for="create_status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Publication Status</label>
                            <select id="create_status" name="status" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-sm font-medium bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                                <option value="Published">Published</option>
                                <option value="Draft">Draft</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="create_content" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Announcement Content <span class="text-rose-500">*</span></label>
                        <textarea id="create_content" name="content" required rows="6" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition leading-relaxed" placeholder="Enter announcement details"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Banner Image (Optional)</label>
                        <div class="border-2 border-dashed border-slate-200 hover:border-primary-400 rounded-2xl p-4 text-center transition">
                            <input type="file" id="create_image" name="image" accept="image/jpeg,image/png,image/webp" class="hidden">
                            <label for="create_image" class="cursor-pointer flex flex-col items-center justify-center">
                                <svg class="w-8 h-8 text-slate-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span class="text-xs font-semibold text-primary-600 hover:underline">Choose image file</span>
                                <span class="text-xs text-slate-400 mt-0.5">JPG, PNG, or WEBP (Max 10MB)</span>
                            </label>
                            <div id="createImagePreview" class="mt-2 hidden relative inline-block">
                                <img src="" alt="Preview" class="max-h-40 rounded-xl border border-slate-200 object-cover">
                                <button type="button" id="btnRemoveCreateImage" class="absolute -top-2 -right-2 bg-rose-600 text-white rounded-full p-1 shadow-md hover:bg-rose-700 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 p-3.5 bg-slate-50 rounded-2xl border border-slate-200/80">
                        <input type="checkbox" id="create_is_pinned" name="is_pinned" value="1" class="w-4 h-4 text-primary-600 rounded-md border-slate-300 focus:ring-primary-500">
                        <label for="create_is_pinned" class="text-xs font-medium text-slate-700 cursor-pointer">
                            <span class="font-semibold text-slate-800">Pin to Top</span> — Display at the top of the resident announcements page
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-slate-100">
                    <button type="button" class="btnCloseCreateModal px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">Cancel</button>
                    <button type="submit" id="btnSubmitCreate" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary-600 text-white text-xs font-bold hover:bg-primary-700 active:bg-primary-800 transition shadow-sm">
                        <span>Save Announcement</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="editAnnouncementModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden overflow-y-auto">
    <div class="min-h-screen px-4 py-8 flex items-center justify-center">
        <div class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border border-slate-100 relative">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Edit Announcement</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Modify announcement content, category, or status</p>
                </div>
                <button type="button" class="btnCloseEditModal text-slate-400 hover:text-slate-600 p-2 rounded-xl hover:bg-slate-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form id="frmEditAnnouncement" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= csrf_token(); ?>">
                <input type="hidden" id="edit_announcement_id" name="announcement_id" value="">
                <input type="hidden" id="edit_remove_image" name="remove_image" value="0">

                <div class="space-y-4">
                    <div>
                        <label for="edit_title" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Announcement Title <span class="text-rose-500">*</span></label>
                        <input type="text" id="edit_title" name="title" required maxlength="255" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="edit_category" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Category <span class="text-rose-500">*</span></label>
                            <select id="edit_category" name="category" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-sm font-medium bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                                <option value="General">General Notice</option>
                                <option value="Advisory">Public Advisory</option>
                                <option value="Event">Community Event</option>
                                <option value="Emergency">Emergency Alert</option>
                                <option value="Health">Health & Wellness</option>
                                <option value="Public Notice">Public Notice</option>
                            </select>
                        </div>

                        <div>
                            <label for="edit_status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Publication Status</label>
                            <select id="edit_status" name="status" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-sm font-medium bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                                <option value="Published">Published</option>
                                <option value="Draft">Draft</option>
                                <option value="Archived">Archived</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="edit_content" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Announcement Content <span class="text-rose-500">*</span></label>
                        <textarea id="edit_content" name="content" required rows="6" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition leading-relaxed"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Banner Image</label>
                        <div id="editCurrentImageContainer" class="mb-3 hidden">
                            <p class="text-xs font-medium text-slate-400 mb-1">Current Image</p>
                            <div class="relative inline-block">
                                <img id="editCurrentImage" src="" alt="Current Banner" class="max-h-36 rounded-xl border border-slate-200 object-cover">
                                <button type="button" id="btnDeleteCurrentImage" class="absolute -top-2 -right-2 bg-rose-600 text-white rounded-full p-1 shadow-md hover:bg-rose-700 transition" title="Remove current image">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                        </div>

                        <div class="border-2 border-dashed border-slate-200 hover:border-primary-400 rounded-2xl p-4 text-center transition">
                            <input type="file" id="edit_image" name="image" accept="image/jpeg,image/png,image/webp" class="hidden">
                            <label for="edit_image" class="cursor-pointer flex flex-col items-center justify-center">
                                <svg class="w-7 h-7 text-slate-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span class="text-xs font-semibold text-primary-600 hover:underline">Choose replacement image</span>
                                <span class="text-xs text-slate-400 mt-0.5">JPG, PNG, or WEBP (Max 10MB)</span>
                            </label>
                            <div id="editImagePreview" class="mt-2 hidden relative inline-block">
                                <img src="" alt="New Preview" class="max-h-36 rounded-xl border border-slate-200 object-cover">
                                <button type="button" id="btnRemoveEditNewImage" class="absolute -top-2 -right-2 bg-rose-600 text-white rounded-full p-1 shadow-md hover:bg-rose-700 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 p-3.5 bg-slate-50 rounded-2xl border border-slate-200/80">
                        <input type="checkbox" id="edit_is_pinned" name="is_pinned" value="1" class="w-4 h-4 text-primary-600 rounded-md border-slate-300 focus:ring-primary-500">
                        <label for="edit_is_pinned" class="text-xs font-medium text-slate-700 cursor-pointer">
                            <span class="font-semibold text-slate-800">Pin to Top</span> — Display at the top of the resident announcements page
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-slate-100">
                    <button type="button" class="btnCloseEditModal px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">Cancel</button>
                    <button type="submit" id="btnSubmitEdit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary-600 text-white text-xs font-bold hover:bg-primary-700 active:bg-primary-800 transition shadow-sm">
                        <span>Save Changes</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="viewAnnouncementModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden overflow-y-auto">
    <div class="min-h-screen px-4 py-8 flex items-center justify-center">
        <div class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border border-slate-100 relative">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                <div class="flex items-center gap-2" id="viewBadges"></div>
                <button type="button" class="btnCloseViewModal text-slate-400 hover:text-slate-600 p-2 rounded-xl hover:bg-slate-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div id="viewImageContainer" class="mb-5 hidden">
                <img id="viewImage" src="" alt="Announcement Banner" class="w-full max-h-72 object-cover rounded-2xl border border-slate-200">
            </div>

            <h1 id="viewTitle" class="text-2xl font-extrabold text-slate-900 tracking-tight leading-tight"></h1>
            <p id="viewMeta" class="text-xs text-slate-500 font-medium mt-2 pb-4 border-b border-slate-100"></p>
            <div id="viewContent" class="text-sm text-slate-700 leading-relaxed mt-4 whitespace-pre-line"></div>

            <div class="flex items-center justify-end mt-6 pt-4 border-t border-slate-100">
                <button type="button" class="btnCloseViewModal px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                    Close Preview
                </button>
            </div>
        </div>
    </div>
</div>

<script src="js/announcements.js"></script>

<?php include "components/footer.php"; ?>
