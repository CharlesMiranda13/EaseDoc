<?php 
/**
 * request_list.php — Resident Requests Table Rows Partial
 */

$fetch_all_clearance_request = $db->fetch_all_clearance_request($r_id);

if ($fetch_all_clearance_request && is_array($fetch_all_clearance_request)): 
    foreach ($fetch_all_clearance_request as $request):
        $isPending = ($request['cr_status'] ?? '') === 'Pending';
        $status = htmlspecialchars($request['cr_status'] ?? 'Pending');
        $requestId = (int)($request['cr_id'] ?? 0);
        $trackingCode = htmlspecialchars($request['cr_code'] ?? ('CR-' . $requestId));
        $formType = htmlspecialchars($request['cr_formtype'] ?? '');
        $purpose = htmlspecialchars($request['cr_purpose'] ?? '');
        $address = htmlspecialchars($request['cr_address'] ?? '');
        $payment = htmlspecialchars($request['cr_payment'] ?? 'Cash on Delivery');
        $price = number_format((float)($request['cr_total'] ?? $request['cr_price'] ?? 50), 2);
        
        $formattedDate = '';
        if (!empty($request['cr_request_date'])) {
            try {
                $formattedDate = (new DateTime($request['cr_request_date']))->format('M j, Y, g:i A');
            } catch (Exception $e) {
                $formattedDate = $request['cr_request_date'];
            }
        }

        $badgeClass = match($status) {
            'Pending' => 'bg-amber-100 text-amber-800 border border-amber-200',
            'Approved' => 'bg-blue-100 text-blue-800 border border-blue-200',
            'Shipped' => 'bg-purple-100 text-purple-800 border border-purple-200',
            'Delivered' => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
            'Rejected' => 'bg-rose-100 text-rose-800 border border-rose-200',
            'Canceled' => 'bg-slate-100 text-slate-800 border border-slate-200',
            default => 'bg-slate-100 text-slate-700 border border-slate-200'
        };

        $addressShort = strlen($address) > 40 ? substr($address, 0, 37) . '...' : $address;
?>
    <tr class="hover:bg-slate-50 transition-colors">
        <td class="p-3 font-semibold text-slate-800 text-xs">
            <span class="font-mono"><?= $trackingCode; ?></span>
        </td>
        <td class="p-3 font-bold text-slate-800 text-sm">
            <?= $formType; ?>
        </td>
        <td class="p-3 text-slate-600 text-xs max-w-xs truncate" title="<?= $purpose; ?>">
            <?= $purpose ?: '—'; ?>
        </td>
        <td class="p-3 text-slate-600 text-xs" title="<?= $address; ?>">
            <?= $addressShort ?: '—'; ?>
        </td>
        <td class="p-3 text-slate-600 text-xs">
            <span class="font-semibold text-slate-800">₱<?= $price; ?></span>
            <span class="text-[10px] text-slate-400 block"><?= $payment; ?></span>
        </td>
        <td class="p-3 text-slate-500 text-xs whitespace-nowrap">
            <?= $formattedDate; ?>
        </td>
        <td class="p-3">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold <?= $badgeClass; ?>">
                <?= $status; ?>
            </span>
        </td>
        <td class="p-3">
            <div class="flex items-center gap-2">
                <!-- View Details Button -->
                <button type="button" class="viewResidentRequestBtn inline-flex items-center justify-center p-1.5 bg-slate-100 hover:bg-primary-600 hover:text-white text-slate-600 rounded-lg text-xs font-medium transition"
                    title="View Request Details"
                    data-code="<?= $trackingCode; ?>"
                    data-type="<?= $formType; ?>"
                    data-purpose="<?= $purpose; ?>"
                    data-address="<?= $address; ?>"
                    data-payment="<?= $payment; ?>"
                    data-price="₱<?= $price; ?>"
                    data-date="<?= $formattedDate; ?>"
                    data-status="<?= $status; ?>">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                </button>

                <?php if ($isPending): ?>
                    <button type="button" class="cancelRequest inline-flex items-center gap-1 bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white py-1.5 px-2.5 text-xs font-semibold rounded-lg transition"
                        data-requestid="<?= $requestId; ?>"
                        data-code="<?= $trackingCode; ?>"
                        title="Cancel Request">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        <span>Cancel</span>
                    </button>
                <?php endif; ?>
            </div>
        </td>
    </tr>
<?php 
    endforeach; 
endif; 
?>
