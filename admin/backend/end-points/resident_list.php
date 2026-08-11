<?php 
/**
 * resident_list.php — Resident Table Rows Partial
 */

$fetch_all_resident = $db->fetch_all_resident();

if ($fetch_all_resident && is_array($fetch_all_resident)): 
    foreach ($fetch_all_resident as $resident):
        $profileImg = !empty($resident['r_profile']) ? htmlspecialchars($resident['r_profile']) : '';
        $validIdFile = !empty($resident['r_valid_ids']) ? htmlspecialchars($resident['r_valid_ids']) : '';

        $profile = !empty($profileImg) 
            ? "<img src='../uploads/resident/{$profileImg}' alt='Profile' class='w-10 h-10 object-cover rounded-full border-2 border-primary-200 shadow-sm flex-shrink-0'>"
            : "<div class='w-10 h-10 rounded-full bg-gradient-to-tr from-primary-600 to-indigo-500 text-white flex items-center justify-center font-bold text-sm shadow-sm flex-shrink-0'>" . strtoupper(substr($resident['r_fname'] ?? 'R', 0, 1)) . "</div>";

        $addressParts = array_filter([
            $resident['r_street'] ?? '',
            $resident['r_barangay'] ?? '',
            $resident['r_municipality'] ?? '',
            $resident['r_province'] ?? '',
            $resident['r_region'] ?? ''
        ]);
        $Address = htmlspecialchars(implode(', ', $addressParts));
        $AddressDisplay = strlen($Address) > 45 ? substr($Address, 0, 42) . '...' : $Address;

        $fullname = htmlspecialchars(
            ($resident['r_lname'] ?? '') . 
            (!empty($resident['r_suffix']) ? ' ' . $resident['r_suffix'] : '') . 
            ', ' . ($resident['r_fname'] ?? '') . 
            (!empty($resident['r_mname']) ? ' ' . $resident['r_mname'] : '')
        );

        $residentId = (int)($resident['r_id'] ?? 0);
        $email = htmlspecialchars($resident['r_email'] ?? '');
        $contact = htmlspecialchars($resident['r_contact_number'] ?? '');
        $gender = htmlspecialchars($resident['r_gender'] ?? 'N/A');
        $civilStatus = htmlspecialchars($resident['r_civil_status'] ?? 'Single');
        $citizenship = htmlspecialchars($resident['r_citizenship'] ?? 'Filipino');
        $bday = htmlspecialchars($resident['r_bday'] ?? '');

        // Compute age if birthday exists
        $age = '—';
        if (!empty($bday) && $bday !== '0000-00-00') {
            try {
                $birthDate = new DateTime($bday);
                $today = new DateTime();
                $age = $today->diff($birthDate)->y . ' yrs';
            } catch (Exception $e) {}
        }

        $genderBadge = strtolower($gender) === 'male'
            ? '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">Male</span>'
            : (strtolower($gender) === 'female'
                ? '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-pink-100 text-pink-700">Female</span>'
                : '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">' . $gender . '</span>');
?>
    <tr class="hover:bg-slate-50 transition-colors">
        <td class="p-3 font-semibold text-slate-700">#<?= $residentId; ?></td>
        <td class="p-3">
            <div class="flex items-center gap-3">
                <?= $profile; ?>
                <div class="min-w-0">
                    <p class="font-bold text-slate-900 leading-snug"><?= $fullname; ?></p>
                    <p class="text-xs text-slate-500 truncate"><?= $email; ?></p>
                </div>
            </div>
        </td>
        <td class="p-3">
            <div class="flex flex-col gap-1">
                <div><?= $genderBadge; ?></div>
                <span class="text-xs text-slate-500 font-medium"><?= $civilStatus; ?> &bull; <?= $age; ?></span>
            </div>
        </td>
        <td class="p-3 text-slate-600 text-xs font-medium"><?= $contact ?: '—'; ?></td>
        <td class="p-3 text-slate-600 text-xs" title="<?= $Address; ?>">
            <div class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span class="truncate max-w-[200px]"><?= $AddressDisplay ?: 'No address recorded'; ?></span>
            </div>
        </td>
        <td class="p-3">
            <div class="flex items-center gap-1.5">
                <!-- View Profile Button -->
                <button type="button" class="viewResidentButton inline-flex items-center justify-center p-2 bg-indigo-50 hover:bg-indigo-600 text-indigo-600 hover:text-white rounded-lg transition duration-150 shadow-sm"
                    title="View Profile Details"
                    data-r_id="<?= $residentId; ?>"
                    data-r_fullname="<?= $fullname; ?>"
                    data-r_fname="<?= htmlspecialchars($resident['r_fname'] ?? '', ENT_QUOTES); ?>"
                    data-r_mname="<?= htmlspecialchars($resident['r_mname'] ?? '', ENT_QUOTES); ?>"
                    data-r_lname="<?= htmlspecialchars($resident['r_lname'] ?? '', ENT_QUOTES); ?>"
                    data-r_suffix="<?= htmlspecialchars($resident['r_suffix'] ?? '', ENT_QUOTES); ?>"
                    data-r_gender="<?= $gender; ?>"
                    data-r_civil_status="<?= $civilStatus; ?>"
                    data-r_citizenship="<?= $citizenship; ?>"
                    data-r_bday="<?= $bday; ?>"
                    data-r_age="<?= $age; ?>"
                    data-r_street="<?= htmlspecialchars($resident['r_street'] ?? '', ENT_QUOTES); ?>"
                    data-r_region="<?= htmlspecialchars($resident['r_region'] ?? '', ENT_QUOTES); ?>"
                    data-r_province="<?= htmlspecialchars($resident['r_province'] ?? '', ENT_QUOTES); ?>"
                    data-r_municipality="<?= htmlspecialchars($resident['r_municipality'] ?? '', ENT_QUOTES); ?>"
                    data-r_barangay="<?= htmlspecialchars($resident['r_barangay'] ?? '', ENT_QUOTES); ?>"
                    data-r_address="<?= $Address; ?>"
                    data-r_contact_number="<?= $contact; ?>"
                    data-r_email="<?= $email; ?>"
                    data-r_profile="<?= !empty($profileImg) ? '../uploads/resident/' . $profileImg : ''; ?>"
                    data-r_valid_ids="<?= !empty($validIdFile) ? '../uploads/resident_id/' . $validIdFile : ''; ?>">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                </button>

                <!-- Edit Button -->
                <button type="button" class="editResidentButton inline-flex items-center justify-center p-2 bg-emerald-50 hover:bg-emerald-600 text-emerald-600 hover:text-white rounded-lg transition duration-150 shadow-sm"
                    title="Edit Resident"
                    data-r_id="<?= $residentId; ?>"
                    data-r_fname="<?= htmlspecialchars($resident['r_fname'] ?? '', ENT_QUOTES); ?>"
                    data-r_mname="<?= htmlspecialchars($resident['r_mname'] ?? '', ENT_QUOTES); ?>"
                    data-r_lname="<?= htmlspecialchars($resident['r_lname'] ?? '', ENT_QUOTES); ?>"
                    data-r_suffix="<?= htmlspecialchars($resident['r_suffix'] ?? '', ENT_QUOTES); ?>"
                    data-r_gender="<?= $gender; ?>"
                    data-r_civil_status="<?= $civilStatus; ?>"
                    data-r_citizenship="<?= $citizenship; ?>"
                    data-r_bday="<?= $bday; ?>"
                    data-r_street="<?= htmlspecialchars($resident['r_street'] ?? '', ENT_QUOTES); ?>"
                    data-r_region="<?= htmlspecialchars($resident['r_region'] ?? '', ENT_QUOTES); ?>"
                    data-r_province="<?= htmlspecialchars($resident['r_province'] ?? '', ENT_QUOTES); ?>"
                    data-r_municipality="<?= htmlspecialchars($resident['r_municipality'] ?? '', ENT_QUOTES); ?>"
                    data-r_barangay="<?= htmlspecialchars($resident['r_barangay'] ?? '', ENT_QUOTES); ?>"
                    data-r_contact_number="<?= $contact; ?>"
                    data-r_email="<?= $email; ?>"
                    data-r_profile="<?= !empty($profileImg) ? '../uploads/resident/' . $profileImg : ''; ?>"
                    data-r_valid_ids="<?= !empty($validIdFile) ? '../uploads/resident_id/' . $validIdFile : ''; ?>">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </button>
              
                <!-- Delete Button -->
                <button type="button" class="deleteResidentButton inline-flex items-center justify-center p-2 bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white rounded-lg transition duration-150 shadow-sm" 
                    title="Delete Resident"
                    data-r_id="<?= $residentId; ?>"
                    data-r_name="<?= $fullname; ?>">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </div>
        </td>
    </tr>
<?php 
    endforeach; 
endif; 
?>