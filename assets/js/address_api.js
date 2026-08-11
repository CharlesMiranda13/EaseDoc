/**
 * address_api.js — Philippine Address Cascading Dropdown Helper
 * Supports multiple instances (Add Modal, Edit Modal, Account Settings) with pre-fill support.
 */
(function(window, $) {
    'use strict';

    var basePath = (window.location.pathname.indexOf('/admin') !== -1 || window.location.pathname.indexOf('/resident') !== -1)
        ? '../assets/data/'
        : 'assets/data/';

    var fallbackPath = (window.location.pathname.indexOf('/admin') !== -1 || window.location.pathname.indexOf('/resident') !== -1)
        ? '../ph-json/'
        : 'ph-json/';

    var cachedData = {};

    function fetchJson(filename, callback) {
        if (cachedData[filename]) {
            callback(cachedData[filename]);
            return;
        }
        $.getJSON(basePath + filename)
            .done(function(data) {
                cachedData[filename] = data;
                callback(data);
            })
            .fail(function() {
                $.getJSON(fallbackPath + filename)
                    .done(function(data) {
                        cachedData[filename] = data;
                        callback(data);
                    })
                    .fail(function() {
                        console.error('Failed to load address data: ' + filename);
                    });
            });
    }

    /**
     * Initializes a cascading address dropdown group.
     * @param {Object} options Configuration options:
     *   - region: selector for region dropdown (e.g. '#region')
     *   - province: selector for province dropdown (e.g. '#province')
     *   - city: selector for city/municipality dropdown (e.g. '#city')
     *   - barangay: selector for barangay dropdown (e.g. '#barangay')
     *   - defaultRegion: optional code or name to pre-select
     *   - defaultProvince: optional code or name to pre-select
     *   - defaultCity: optional code or name to pre-select
     *   - defaultBarangay: optional code or name to pre-select
     */
    function initAddressDropdowns(options) {
        var $region = $(options.region);
        var $province = $(options.province);
        var $city = $(options.city);
        var $barangay = $(options.barangay);

        if (!$region.length) return;

        // Reset lower dropdowns
        function resetProvince() {
            if ($province.length) $province.empty().append('<option value="">Select Province</option>');
        }
        function resetCity() {
            if ($city.length) $city.empty().append('<option value="">Select City / Municipality</option>');
        }
        function resetBarangay() {
            if ($barangay.length) $barangay.empty().append('<option value="">Select Barangay</option>');
        }

        // Load Regions
        fetchJson('region.json', function(data) {
            $region.empty().append('<option value="">Select Region</option>');
            if (data && data.data) {
                var selectedCode = '';
                $.each(data.data, function(i, item) {
                    var isSelected = (options.defaultRegion && (item.region_code === options.defaultRegion || item.region_name.toLowerCase() === String(options.defaultRegion).toLowerCase()));
                    $region.append($('<option>', {
                        value: item.region_code,
                        text: item.region_name,
                        selected: isSelected
                    }));
                    if (isSelected) selectedCode = item.region_code;
                });

                if (selectedCode) {
                    loadProvinces(selectedCode, options.defaultProvince);
                }
            }
        });

        // Load Provinces given a region code
        function loadProvinces(regionCode, defaultProv) {
            resetProvince();
            resetCity();
            resetBarangay();
            if (!regionCode || !$province.length) return;

            fetchJson('province.json', function(data) {
                if (data && data.data) {
                    var filtered = data.data.filter(function(p) {
                        return p.region_code === regionCode;
                    });
                    var selectedCode = '';
                    $.each(filtered, function(i, item) {
                        var isSelected = (defaultProv && (item.province_code === defaultProv || item.province_name.toLowerCase() === String(defaultProv).toLowerCase()));
                        $province.append($('<option>', {
                            value: item.province_code,
                            text: item.province_name,
                            selected: isSelected
                        }));
                        if (isSelected) selectedCode = item.province_code;
                    });

                    if (selectedCode) {
                        loadCities(selectedCode, options.defaultCity);
                    }
                }
            });
        }

        // Load Cities given a province code
        function loadCities(provinceCode, defaultCty) {
            resetCity();
            resetBarangay();
            if (!provinceCode || !$city.length) return;

            fetchJson('city.json', function(data) {
                if (data && data.data) {
                    var filtered = data.data.filter(function(c) {
                        return c.province_code === provinceCode;
                    });
                    var selectedCode = '';
                    $.each(filtered, function(i, item) {
                        var isSelected = (defaultCty && (item.city_code === defaultCty || item.city_name.toLowerCase() === String(defaultCty).toLowerCase()));
                        $city.append($('<option>', {
                            value: item.city_code,
                            text: item.city_name,
                            selected: isSelected
                        }));
                        if (isSelected) selectedCode = item.city_code;
                    });

                    if (selectedCode) {
                        loadBarangays(selectedCode, options.defaultBarangay);
                    }
                }
            });
        }

        // Load Barangays given a city code
        function loadBarangays(cityCode, defaultBrgy) {
            resetBarangay();
            if (!cityCode || !$barangay.length) return;

            fetchJson('barangay.json', function(data) {
                if (data && data.data) {
                    var filtered = data.data.filter(function(b) {
                        return b.city_code === cityCode;
                    });
                    $.each(filtered, function(i, item) {
                        var isSelected = (defaultBrgy && (item.brgy_code === defaultBrgy || item.brgy_name.toLowerCase() === String(defaultBrgy).toLowerCase()));
                        $barangay.append($('<option>', {
                            value: item.brgy_code,
                            text: item.brgy_name,
                            selected: isSelected
                        }));
                    });
                }
            });
        }

        // Event Handlers
        $region.off('change.addr').on('change.addr', function() {
            loadProvinces($(this).val());
        });

        $province.off('change.addr').on('change.addr', function() {
            loadCities($(this).val());
        });

        $city.off('change.addr').on('change.addr', function() {
            loadBarangays($(this).val());
        });
    }

    // Expose globally
    window.initAddressDropdowns = initAddressDropdowns;

    // Auto-init default selectors if present on page load
    $(document).ready(function() {
        if ($('#region').length > 0 && $('#province').length > 0) {
            initAddressDropdowns({
                region: '#region',
                province: '#province',
                city: '#city',
                barangay: '#barangay'
            });
        }
    });

})(window, jQuery);
