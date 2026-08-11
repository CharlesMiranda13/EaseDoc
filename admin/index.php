<?php include "components/header.php"; ?>

<div class="mb-6">
  <h1 class="text-xl font-bold text-gray-800">Dashboard</h1>
  <p class="text-sm text-gray-500 mt-0.5">Barangay Document Request Overview</p>
</div>

<!-- Stat Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

  <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
    <div class="flex items-center justify-between mb-3">
      <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Barangay ID</p>
      <div class="w-8 h-8 rounded-md bg-blue-50 flex items-center justify-center flex-shrink-0">
        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 012-2h2a2 2 0 012 2v2m-6 0h6"></path></svg>
      </div>
    </div>
    <p id="barangayIDCount" class="text-3xl font-bold text-gray-900">—</p>
    <p class="text-xs text-gray-400 mt-1">Total requests</p>
  </div>

  <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
    <div class="flex items-center justify-between mb-3">
      <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Clearance</p>
      <div class="w-8 h-8 rounded-md bg-emerald-50 flex items-center justify-center flex-shrink-0">
        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
      </div>
    </div>
    <p id="barangayClearanceCount" class="text-3xl font-bold text-gray-900">—</p>
    <p class="text-xs text-gray-400 mt-1">Total requests</p>
  </div>

  <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
    <div class="flex items-center justify-between mb-3">
      <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Residency</p>
      <div class="w-8 h-8 rounded-md bg-violet-50 flex items-center justify-center flex-shrink-0">
        <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
      </div>
    </div>
    <p id="residencyCount" class="text-3xl font-bold text-gray-900">—</p>
    <p class="text-xs text-gray-400 mt-1">Total requests</p>
  </div>

  <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
    <div class="flex items-center justify-between mb-3">
      <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Indigency</p>
      <div class="w-8 h-8 rounded-md bg-amber-50 flex items-center justify-center flex-shrink-0">
        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
      </div>
    </div>
    <p id="indigencyCount" class="text-3xl font-bold text-gray-900">—</p>
    <p class="text-xs text-gray-400 mt-1">Total requests</p>
  </div>

</div>

<!-- Analytics -->
<div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5">
  <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Analytics</h2>
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div>
      <div id="barChart"></div>
    </div>
    <div>
      <div id="donutChart"></div>
    </div>
  </div>
</div>

<?php include "components/footer.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
$(function () {

  var barChart = new ApexCharts(document.getElementById('barChart'), {
    chart: {
      type: 'bar',
      height: 280,
      toolbar: { show: false },
      fontFamily: 'inherit'
    },
    series: [{ name: 'Requests', data: [0, 0, 0, 0] }],
    xaxis: {
      categories: ['Barangay ID', 'Clearance', 'Residency', 'Indigency'],
      labels: { style: { fontSize: '12px' } }
    },
    yaxis: {
      labels: { formatter: function (val) { return Math.floor(val); } }
    },
    colors: ['#2563eb'],
    plotOptions: {
      bar: { borderRadius: 4, columnWidth: '50%' }
    },
    dataLabels: { enabled: false },
    title: { text: 'Requests Breakdown', align: 'left', style: { fontSize: '13px', fontWeight: 600, color: '#374151' } },
    grid: { borderColor: '#f3f4f6' },
    tooltip: { y: { formatter: function (val) { return val + ' requests'; } } }
  });
  barChart.render();

  var donutChart = new ApexCharts(document.getElementById('donutChart'), {
    chart: {
      type: 'donut',
      height: 280,
      fontFamily: 'inherit'
    },
    series: [0, 0, 0, 0],
    labels: ['Barangay ID', 'Clearance', 'Residency', 'Indigency'],
    colors: ['#2563eb', '#10b981', '#7c3aed', '#d97706'],
    plotOptions: {
      pie: {
        donut: {
          size: '60%',
          labels: {
            show: true,
            total: {
              show: true,
              label: 'Total',
              fontSize: '13px',
              fontWeight: 600,
              color: '#374151'
            }
          }
        }
      }
    },
    dataLabels: { enabled: false },
    legend: { position: 'bottom', fontSize: '12px' },
    title: { text: 'Request Distribution', align: 'left', style: { fontSize: '13px', fontWeight: 600, color: '#374151' } }
  });
  donutChart.render();

  $.ajax({
    url: 'backend/end-points/barangay_Statistics.php',
    method: 'GET',
    dataType: 'json',
    success: function (data) {
      var counts = {
        '#barangayIDCount':       data['Barangay ID']        || 0,
        '#barangayClearanceCount': data['Barangay Clearance'] || 0,
        '#residencyCount':        data['Barangay Residency']  || 0,
        '#indigencyCount':        data['Barangay Indigency']  || 0
      };

      $.each(counts, function (id, target) {
        var $el = $(id);
        $({ n: 0 }).animate({ n: target }, {
          duration: 1200,
          easing: 'swing',
          step: function () { $el.text(Math.floor(this.n)); },
          complete: function () { $el.text(target); }
        });
      });

      var series = [
        data['Barangay ID']        || 0,
        data['Barangay Clearance'] || 0,
        data['Barangay Residency'] || 0,
        data['Barangay Indigency'] || 0
      ];
      barChart.updateSeries([{ name: 'Requests', data: series }]);
      donutChart.updateSeries(series);
    },
    error: function (xhr) {
      console.error('Failed to load statistics:', xhr.responseText);
    }
  });

});
</script>
