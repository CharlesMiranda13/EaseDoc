    <!-- page content ends here -->
  </main>
</div>

<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- DataTables core + Responsive extension -->
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.2/css/responsive.dataTables.min.css">
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/3.0.2/js/dataTables.responsive.min.js"></script>

<script>
  (function () {
    var sidebar    = document.getElementById('sidebar');
    var overlay    = document.getElementById('overlay');
    var menuButton = document.getElementById('menuButton');

    if (!menuButton || !sidebar || !overlay) return;

    menuButton.addEventListener('click', function () {
      sidebar.classList.toggle('-translate-x-full');
      overlay.classList.toggle('hidden');
    });

    overlay.addEventListener('click', function () {
      sidebar.classList.add('-translate-x-full');
      overlay.classList.add('hidden');
    });
  })();
</script>
</body>
</html>