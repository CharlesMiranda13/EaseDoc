<div id="printableArea" class="max-w-4xl mx-auto bg-white p-8 mt-12 shadow-lg rounded-lg">
    <div class="max-w-4xl mx-auto bg-white shadow-lg rounded-lg overflow-hidden mt-10">
        <!-- Card Header -->
        <div class="bg-blue-600 text-white p-4 flex justify-between items-center">
            <div class="flex items-center">
                <span class="ml-4 text-lg font-bold">REPUBLIC OF THE PHILIPPINES</span>
            </div>
            <span class="text-lg">Barangay Resident Identification Card</span>
        </div>

        <!-- Card Content -->
        <div class="p-6 grid grid-cols-3 gap-4">
            <!-- Image Section -->
            <div class="col-span-1 flex justify-center items-center -mt-6">
                <div class="w-32 h-32 border-2 border-gray-300 rounded-md overflow-hidden bg-gray-100">
                    <?php if (!empty($cr_1X1_pic)): ?>
                        <img src="../uploads/id/<?= htmlspecialchars($cr_1X1_pic) ?>" alt="Resident Photo" class="w-full h-full object-cover">
                    <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center text-gray-400 text-xs">No Photo</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Personal Information -->
            <div class="col-span-2">
                <div class="grid grid-cols-2 gap-x-4">
                    <div>
                        <p><strong>LAST NAME, FIRST NAME, M.I.</strong></p>
                        <p class="text-xl"><?= htmlspecialchars($order['r_lname'] ?? '') ?>, <?= htmlspecialchars($order['r_fname'] ?? '') ?> <?= !empty($order['r_mname']) ? htmlspecialchars($order['r_mname'][0]) . '.' : '' ?></p>
                    </div>
                    <div>
                        <p><strong>DATE OF BIRTH</strong></p>
                        <p class="text-xl"><?= htmlspecialchars($formatted_birthday ?? '') ?></p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-x-4 mt-4">
                    <div>
                        <p><strong>CIVIL STATUS</strong></p>
                        <p class="text-xl"><?= htmlspecialchars($order['r_civil_status'] ?? '') ?></p>
                    </div>
                    <div>
                        <p><strong>SEX</strong></p>
                        <p class="text-xl"><?= htmlspecialchars($order['r_gender'] ?? '') ?></p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-x-4 mt-4">
                    <div>
                        <p><strong>ADDRESS</strong></p>
                        <p class="text-xl"><?= htmlspecialchars($address ?? '') ?></p>
                    </div>
                    <div>
                        <p><strong>RESIDENT ID NUMBER</strong></p>
                        <p class="text-xl">XXX-0<?= htmlspecialchars((string)($r_id ?? '')) ?>-0000<?= htmlspecialchars((string)($r_id ?? '')) ?></p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-x-4 mt-4">
                    <div>
                        <p><strong>DATE ISSUED</strong></p>
                        <p class="text-xl"><?= htmlspecialchars($formattedDate ?? '') ?></p>
                    </div>
                    <div>
                        <p><strong>VALID UNTIL</strong></p>
                        <p class="text-xl">_______________</p>
                    </div>
                </div>

               <!-- Signature Section -->
                <div class="mt-6 border-t pt-4 text-center">
                    <div class="w-48 h-16 mx-auto border-2 border-dashed overflow-hidden flex items-center justify-center">
                        <?php if (!empty($cr_Signature)): ?>
                            <img src="../uploads/id/<?= htmlspecialchars($cr_Signature) ?>" alt="Resident Signature" class="w-full h-full object-contain">
                        <?php else: ?>
                            <span class="text-xs text-gray-400">No Signature</span>
                        <?php endif; ?>
                    </div>
                    <p class="mt-2 text-sm font-semibold">SIGNATURE</p>
                </div>
            </div>
        </div>
    </div>
</div>
