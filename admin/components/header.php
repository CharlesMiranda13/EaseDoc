<?php
require_once '../core/Auth.php';
require_once '../core/Csrf.php';
require_admin();

require_once __DIR__ . '/../backend/class.php';

$db = new global_class();
$user_id = get_admin_id();

$result = $db->check_account($user_id);

if (empty($result)) {
    header('Location: ../admin.html');
    exit();
}

$current_page = basename($_SERVER['PHP_SELF']);
$admin_name   = htmlspecialchars($result[0]['user_fname'] ?? 'Admin');
$admin_role   = htmlspecialchars($_SESSION['user_type'] ?? 'admin');
$admin_initial = strtoupper(substr($admin_name, 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $current_page === 'index.php' ? 'Dashboard' : ucfirst(basename($current_page, '.php')) ?> — EaseDocument Admin</title>
  <link rel="icon" type="image/png" href="../assets/logo.jpeg">

  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: {
              50:  '#eff6ff',
              100: '#dbeafe',
              200: '#bfdbfe',
              300: '#93c5fd',
              400: '#60a5fa',
              500: '#3b82f6',
              600: '#2563eb',
              700: '#1d4ed8',
              800: '#1e40af',
              900: '#1e3a8a',
            }
          }
        }
      }
    }
  </script>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/AlertifyJS/1.13.1/css/alertify.css" integrity="sha512-MpdEaY2YQ3EokN6lCD6bnWMl5Gwk7RjBbpKLovlrH6X+DRokrPRAF3zQJl1hZUiLXfo2e9MrOt+udOnHCAmi5w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/AlertifyJS/1.13.1/alertify.min.js" integrity="sha512-JnjG+Wt53GspUQXQhc+c4j8SBERsgJAoHeehagKHlxQN+MtCCmFDghX9/AcbkkNRZptyZU4zC8utK59M5L45Iw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <meta name="csrf-token" content="<?= csrf_token(); ?>">
</head>
<body class="bg-gray-100 font-sans antialiased">

<?php include "../function/pageLoader.php"; ?>

<div class="min-h-screen flex">

  <!-- Sidebar -->
  <aside id="sidebar" class="bg-slate-900 text-gray-300 w-64 flex-shrink-0 flex flex-col fixed inset-y-0 left-0 z-50 -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out shadow-2xl">

    <!-- Logo -->
    <div class="p-5 border-b border-slate-700/60">
      <div class="flex items-center gap-3">
        <img src="../assets/logo.jpeg" alt="EaseDocument Logo" class="w-9 h-9 rounded-lg border border-slate-600 object-cover">
        <div>
          <h1 class="text-sm font-bold text-white tracking-wide leading-tight">EASE DOCUMENT</h1>
          <p class="text-xs text-slate-400 mt-0.5">Barangay Management</p>
        </div>
      </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">

      <a href="index.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150 <?= $current_page === 'index.php' ? 'bg-primary-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' ?>">
        <svg class="w-4.5 h-4.5 flex-shrink-0" style="width:1.125rem;height:1.125rem" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
        <span>Dashboard</span>
      </a>

      <a href="resident.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150 <?= $current_page === 'resident.php' ? 'bg-primary-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' ?>">
        <svg class="flex-shrink-0" style="width:1.125rem;height:1.125rem" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
        <span>Residents</span>
      </a>

      <a href="announcements.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150 <?= $current_page === 'announcements.php' ? 'bg-primary-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' ?>">
        <svg class="flex-shrink-0" style="width:1.125rem;height:1.125rem" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
        <span>Announcements</span>
      </a>

      <a href="request.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150 <?= $current_page === 'request.php' ? 'bg-primary-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' ?>">
        <svg class="flex-shrink-0" style="width:1.125rem;height:1.125rem" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        <span>Requests</span>
      </a>

      <a href="settings.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150 <?= $current_page === 'settings.php' ? 'bg-primary-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' ?>">
        <svg class="flex-shrink-0" style="width:1.125rem;height:1.125rem" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
        <span>Settings</span>
      </a>

    </nav>

    <!-- User & Logout -->
    <div class="p-3 border-t border-slate-700/60">
      <div class="flex items-center gap-3 px-2 py-2 mb-1">
        <div class="w-8 h-8 rounded-full bg-primary-600 flex items-center justify-center text-white text-sm font-semibold flex-shrink-0">
          <?= $admin_initial ?>
        </div>
        <div class="min-w-0">
          <p class="text-sm font-medium text-white truncate"><?= $admin_name ?></p>
          <p class="text-xs text-slate-400 capitalize truncate"><?= $admin_role ?></p>
        </div>
      </div>
      <a href="logout.php" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-red-400 hover:bg-red-950/50 hover:text-red-300 transition-colors duration-150">
        <svg class="flex-shrink-0" style="width:1.125rem;height:1.125rem" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
        <span>Logout</span>
      </a>
    </div>

  </aside>

  <!-- Mobile overlay -->
  <div id="overlay" class="fixed inset-0 bg-black/50 hidden lg:hidden z-40"></div>

  <!-- Main content — offset by sidebar width on desktop -->
  <main class="flex-1 min-w-0 lg:ml-64 bg-gray-50 p-6">

    <!-- Mobile menu button -->
    <button id="menuButton" class="lg:hidden flex items-center gap-2 text-gray-600 mb-5 text-sm font-medium" aria-label="Open menu">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
      Menu
    </button>
