<?php
require_once '../core/Auth.php';
require_once '../core/Csrf.php';
require_resident();

require_once __DIR__ . '/../backend/class.php';

$db = new global_class();
$r_id = get_resident_id();

// Get resident account info
$result = $db->check_account($r_id);

if (empty($result)) {
    header('location: ../login.html');
    exit();
}

$resident = $result[0];
$residentName = htmlspecialchars($resident['r_fname'] . ' ' . $resident['r_lname']);
$residentFirst = htmlspecialchars($resident['r_fname'] ?? 'Resident');
$profileImg = !empty($resident['r_profile']) ? htmlspecialchars($resident['r_profile']) : '';

// Build formatted address string
$addrParts = array_filter([
    $resident['r_street'] ?? '',
    $resident['r_barangay'] ?? '',
    $resident['r_municipality'] ?? '',
    $resident['r_province'] ?? '',
    $resident['r_region'] ?? ''
]);
$address = !empty($addrParts) ? implode(', ', $addrParts) : 'Enter Complete Address';

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $current_page === 'index.php' ? 'Dashboard' : ($current_page === 'MyRequest.php' ? 'My Requests' : ($current_page === 'account_setting.php' ? 'Account Settings' : ucfirst(basename($current_page, '.php')))) ?> — EaseDocument Resident</title>
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
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/AlertifyJS/1.13.1/alertify.min.js" integrity="sha512-JnjG+Wt53GspUQXQhc+c4j8SBERsgJAoHeehagKHlxQN+MtCCmFDghX9/AcbkkNRZptyZU4zC8utK59M5L45Iw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <meta name="csrf-token" content="<?= csrf_token(); ?>">
</head>
<body class="bg-slate-50 font-sans antialiased">

<?php include "../function/pageLoader.php"; ?>

<div class="min-h-screen flex">
    
  <!-- Sidebar -->
  <aside id="sidebar" class="bg-slate-900 text-slate-300 w-64 flex-shrink-0 flex flex-col fixed inset-y-0 left-0 z-50 -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out shadow-2xl">
    <!-- Logo -->
    <div class="p-5 border-b border-slate-700/60">
      <div class="flex items-center gap-3">
        <img src="../assets/logo.jpeg" alt="EaseDocument Logo" class="w-9 h-9 rounded-xl border border-slate-600 object-cover">
        <div>
          <h1 class="text-sm font-bold text-white tracking-wide leading-tight">EASE DOCUMENT</h1>
          <p class="text-xs text-slate-400 mt-0.5">Resident Portal</p>
        </div>
      </div>
    </div>
    
    <!-- Navigation Links -->
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
      <a href="index.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 <?= $current_page === 'index.php' ? 'bg-primary-600 text-white shadow-md shadow-primary-900/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' ?>">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
        <span>Dashboard</span>
      </a>
      
      <a href="MyRequest.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 <?= $current_page === 'MyRequest.php' ? 'bg-primary-600 text-white shadow-md shadow-primary-900/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' ?>">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        <span>My Requests</span>
      </a>
      
      <a href="announcements.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 <?= $current_page === 'announcements.php' ? 'bg-primary-600 text-white shadow-md shadow-primary-900/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' ?>">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
        <span>Announcements</span>
      </a>
      
      <a href="account_setting.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 <?= $current_page === 'account_setting.php' ? 'bg-primary-600 text-white shadow-md shadow-primary-900/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' ?>">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
        <span>Account Settings</span>
      </a>
    </nav>
    
    <!-- User Profile & Logout -->
    <div class="p-3 border-t border-slate-700/60">
      <div class="flex items-center gap-3 px-2 py-2 mb-1">
        <?php if (!empty($profileImg)): ?>
          <img src="../uploads/resident/<?= $profileImg ?>" alt="Profile" class="w-9 h-9 rounded-xl object-cover border border-slate-600 flex-shrink-0">
        <?php else: ?>
          <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-primary-600 to-indigo-500 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
            <?= strtoupper(substr($resident['r_fname'] ?? 'R', 0, 1)); ?>
          </div>
        <?php endif; ?>
        <div class="min-w-0">
          <p class="text-sm font-bold text-white truncate"><?= $residentFirst ?></p>
          <p class="text-xs text-emerald-400 font-medium truncate">● Verified Resident</p>
        </div>
      </div>
      <a href="logout.php" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium text-rose-400 hover:bg-rose-950/40 hover:text-rose-300 transition-colors duration-150">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
        <span>Logout</span>
      </a>
    </div>
  </aside>

  <!-- Overlay for Mobile Sidebar -->
  <div id="overlay" class="fixed inset-0 bg-slate-950/50 backdrop-blur-xs hidden lg:hidden z-40"></div>

  <!-- Main Content wrapper -->
  <main class="flex-1 min-w-0 lg:ml-64 bg-slate-50 p-4 sm:p-6 lg:p-8">
    <!-- Mobile top bar with menu button -->
    <div class="lg:hidden flex items-center justify-between bg-white p-3.5 mb-5 rounded-2xl border border-slate-200 shadow-sm">
      <button id="menuButton" class="flex items-center gap-2 text-slate-700 font-semibold text-sm" aria-label="Open menu">
        <svg class="w-6 h-6 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        <span>Menu</span>
      </button>
      <span class="text-xs font-bold text-primary-600 bg-primary-50 px-2.5 py-1 rounded-full border border-primary-200/60">Resident Portal</span>
    </div>