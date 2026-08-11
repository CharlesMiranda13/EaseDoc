<!-- Loading Screen -->
<div id="loadingScreen" class="fixed inset-0 bg-white z-[100] flex flex-col items-center justify-center transition-opacity duration-300 pointer-events-none" style="opacity: 1;">
  <div class="flex items-center gap-3">
    <div class="w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
    <span class="text-lg font-medium text-gray-700">Loading...</span>
  </div>
</div>

<script>
(function() {
    function hideLoader() {
        var loader = document.getElementById('loadingScreen');
        if (loader) {
            loader.style.opacity = '0';
            setTimeout(function() {
                loader.style.display = 'none';
            }, 250);
        }
    }
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        setTimeout(hideLoader, 50);
    } else {
        document.addEventListener('DOMContentLoaded', hideLoader);
        window.addEventListener('load', hideLoader);
        setTimeout(hideLoader, 800); // Safety fallback
    }
})();
</script>