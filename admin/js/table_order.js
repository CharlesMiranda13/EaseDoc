/**
 * table_order.js — Admin Orders / Requests Table Script
 */
$(document).ready(function() {
    fetchOrders();
    AutoRefresh();
    bindTableFilter();
});

let autoRefreshInterval = null;

function AutoRefresh() {
    if (autoRefreshInterval) clearInterval(autoRefreshInterval);
    autoRefreshInterval = setInterval(function() {
        if ($('#searchInput').val().trim() === '') {
            fetchOrders();
        }
    }, 5000);
}

function bindTableFilter() {
    $('#searchInput').off('input').on('input', function() {
        const input = $(this).val().toLowerCase();
        const rows = $("#recordTable tbody tr");
        
        rows.each(function() {
            let rowText = $(this).text().toLowerCase();
            $(this).toggle(rowText.includes(input));
        });
    });
}

function fetchOrders() {
    $.ajax({
        type: "GET",
        url: 'backend/end-points/controller.php',
        data: { requestType: 'GetAllOrders' },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success' || response.status === true) {
                displayOrders(response.data || []);
            } else {
                alertify.error(response.message || 'Failed to fetch orders');
            }
        },
        error: function() {
            // Suppress error banner on background auto-refresh failure
        }
    });
}

function displayOrders(orders) {
    const urlParams = new URLSearchParams(window.location.search);
    const currentStep = urlParams.get('step') || 'Pending';

    let tableBody = $('#recordTable tbody');
    tableBody.empty();

    const filteredOrders = (orders || []).filter(function(orderItem) {
        return (orderItem.cr_status || 'Pending') === currentStep;
    });

    if (filteredOrders.length > 0) {
        filteredOrders.forEach(function(orderItem) { 
            var orderDate = new Date(orderItem.cr_request_date);
            var formattedDate = !isNaN(orderDate.getTime()) 
                ? orderDate.toLocaleString('en-US', { 
                    month: 'short', 
                    day: 'numeric', 
                    year: 'numeric', 
                    hour: 'numeric', 
                    minute: 'numeric', 
                    hour12: true 
                })
                : (orderItem.cr_request_date || '');

            let fullName = `${orderItem.r_fname || ''} ${orderItem.r_mname || ''} ${orderItem.r_lname || ''}`.trim();
            let price = parseFloat(orderItem.cr_total || orderItem.cr_price || 50).toFixed(2);

            let badgeClass = 'bg-gray-100 text-gray-800';
            switch (orderItem.cr_status) {
                case 'Pending': badgeClass = 'bg-amber-100 text-amber-800'; break;
                case 'Approved': badgeClass = 'bg-blue-100 text-blue-800'; break;
                case 'Shipped': badgeClass = 'bg-purple-100 text-purple-800'; break;
                case 'Delivered': badgeClass = 'bg-emerald-100 text-emerald-800'; break;
                case 'Rejected': badgeClass = 'bg-rose-100 text-rose-800'; break;
                case 'Canceled': badgeClass = 'bg-gray-100 text-gray-600'; break;
            }

            let orderRow = `
                <tr class="hover:bg-gray-50 transition border-b border-gray-100">
                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">${orderItem.cr_id}</td>
                    <td class="px-6 py-4 text-sm text-gray-800 font-medium">${orderItem.cr_formtype || ''}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">${fullName}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">${orderItem.cr_payment || 'COD'}</td>
                    <td class="px-6 py-4 text-sm text-gray-500 text-xs">${formattedDate}</td>
                    <td class="px-6 py-4 text-sm font-semibold text-gray-800">₱ ${price}</td>
                    <td class="px-6 py-4 text-sm">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold ${badgeClass}">
                            ${orderItem.cr_status}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <div class="flex items-center gap-2">
                            <select 
                                class="UpdateOrderStatus text-xs py-1.5 px-2.5 text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm cursor-pointer focus:ring-2 focus:ring-primary-500 focus:outline-none" 
                                data-orderid="${orderItem.cr_id}" 
                                data-initial-status="${orderItem.cr_status}">
                                ${generateStatusOptions(orderItem.cr_status)}
                            </select>

                            <a href="view_orders.php?cr_id=${orderItem.cr_id}" 
                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg shadow-sm transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268 2.943-9.542-7z"></path></svg>
                                View
                            </a>
                        </div>
                    </td>
                </tr>
            `;

            tableBody.append(orderRow);
        });
    } else {
        tableBody.append(`
            <tr>
                <td colspan="8" class="px-6 py-8 text-center text-sm text-gray-500">
                    No requests found in <span class="font-medium">${currentStep}</span> status.
                </td>
            </tr>
        `);
    }
}

function generateStatusOptions(currentStatus) {
    let options = '';
    const statusOptions = [
        { value: 'Pending', label: 'Pending' },
        { value: 'Approved', label: 'Approved' },
        { value: 'Shipped', label: 'Shipped' },
        { value: 'Delivered', label: 'Delivered' },
        { value: 'Rejected', label: 'Rejected' },
        { value: 'Canceled', label: 'Canceled' }
    ];

    statusOptions.forEach(option => {
        options += `<option value="${option.value}" ${option.value === currentStatus ? 'selected' : ''}>${option.label}</option>`;
    });

    return options;
}

$(document).on("change", ".UpdateOrderStatus", function () {
    const $select = $(this); 
    const orderId = $select.data("orderid");
    const initialStatus = $select.data("initial-status"); 
    const newStatus = $select.val(); 
  
    if (newStatus === "" || newStatus === initialStatus) {
        return;
    }
  
    $select.prop("disabled", true);
  
    $.ajax({
        url: "backend/end-points/controller.php",
        method: "POST",
        data: {
            orderId: orderId,
            orderStatus: newStatus,
            requestType: 'UpdateOrderStatus',
            csrf_token: $('meta[name="csrf-token"]').attr('content') || ''
        },
        dataType: "json",
        success: function (response) {
            if (response.status === "success" || response.status === true) {
                alertify.success(response.message || "Order status updated!");
                setTimeout(function() {
                    fetchOrders();
                }, 500);
            } else {
                alertify.error(response.message || 'Failed to update status');
                $select.val(initialStatus);
            }
        },
        error: function(xhr) {
            var msg = "An error occurred while updating status.";
            try {
                var res = JSON.parse(xhr.responseText);
                if (res && res.message) msg = res.message;
            } catch(e) {}
            alertify.error(msg);
            $select.val(initialStatus);
        },
        complete: function () {
            $select.prop("disabled", false);
        }
    });
});