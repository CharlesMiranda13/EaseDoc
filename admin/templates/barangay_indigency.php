<div id="printableArea" class="max-w-4xl mx-auto bg-white p-8 mt-12 shadow-lg rounded-lg">
    <div class="text-center">
        <h1 class="text-xl font-bold uppercase">Republic of the Philippines</h1>
        <h2 class="text-lg font-semibold">City of Manila</h2>
        <h3 class="text-lg font-semibold">Fourth District</h3>
        <h4 class="text-2xl font-extrabold mt-4">Barangay 434, Zone 44</h4>
    </div>

    <div class="mt-8">
        <h1 class="text-xl font-bold text-center uppercase underline">Barangay Indigency Certificate</h1>
    </div>

    <div class="mt-12">
        <p class="text-justify">
            <span class="font-bold">TO WHOM IT MAY CONCERN:</span>
        </p>
        <p class="mt-4 text-justify">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            This is to certify that <span class="underline">______________<?= htmlspecialchars($fullName) ?>_________________</span>, 
            <span class="underline">____<?= htmlspecialchars((string)$age) ?>____</span> years old, and a resident of Barangay 434, Zone 44, 
            Fourth District, Manila, is a legitimate indigent citizen of this barangay and is in need of assistance.
        </p>
        <p class="mt-4 text-justify">
            To certify further, that he/she has no permanent source of income and is unable to support his/her own basic needs.
        </p>
    </div>

    <div class="mt-8">
        <p class="text-justify">
            <span class="font-bold">Issued</span> this 
            <span class="underline">____<?= htmlspecialchars($ordinalDay) ?>____</span> day of 
            <span class="underline">__________<?= htmlspecialchars($month) ?>______________</span>, 
            <?= htmlspecialchars((string)$year) ?> at Barangay 434, Zone 44, Fourth District, Manila, upon request of the interested party for whatever legal purposes it may serve.
        </p>
    </div>

    <div class="mt-12 text-right">
        <p class="font-bold">Mark "Bojet" Santos</p>
        <p class="text-sm">Barangay Captain</p>
    </div>

    <div class="mt-8 border-t pt-4">
        <p>O.R. Code: <span class="underline">____<?= htmlspecialchars($cr_code) ?></span></p>
        <p>Date Issued: <span class="underline">_________<?= htmlspecialchars($formattedDate) ?>____________</span></p>
        <p>Doc. Stamp: <span class="underline">Paid</span></p>
    </div>
</div>
