

import Alpine from 'alpinejs';
import DataTable from 'datatables.net-dt';
import L from 'leaflet';
import 'datatables.net-dt/css/dataTables.dataTables.css';
import 'leaflet/dist/leaflet.css';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    initializeLocationMap();

    const table = document.querySelector('#reports-table');
    if (!table) return;

    const dataTable = new DataTable(table, {
        pageLength: 10,
        order: [[0, 'desc']],
        language: {
            emptyTable: 'Belum ada laporan.',
            info: 'Menampilkan _START_–_END_ dari _TOTAL_ laporan',
            infoEmpty: 'Tidak ada laporan',
            zeroRecords: 'Laporan tidak ditemukan',
            paginate: { previous: 'Sebelumnya', next: 'Berikutnya' },
        },
        layout: {
            topStart: null,
            topEnd: null,
            bottomStart: 'info',
            bottomEnd: 'paging',
        },
        columnDefs: [{ orderable: false, targets: [-1] }],
    });

    document.querySelector('#report-search')?.addEventListener('input', (event) => {
        dataTable.search(event.target.value).draw();
    });

    document.querySelector('#page-length')?.addEventListener('change', (event) => {
        dataTable.page.len(Number(event.target.value)).draw();
    });
});

function initializeLocationMap() {
    const mapElement = document.querySelector('#location-map');
    const latitudeInput = document.querySelector('#latitude');
    const longitudeInput = document.querySelector('#longitude');
    const locationButton = document.querySelector('#get-location');
    const statusElement = document.querySelector('#gps-status');
    const addressElement = document.querySelector('#location-address');

    if (!mapElement || !latitudeInput || !longitudeInput) return;

    const defaultPosition = [-2.5489, 118.0149];
    const map = L.map(mapElement, { scrollWheelZoom: false }).setView(defaultPosition, 5);
    let marker = null;
    let reverseTimer = null;
    let reverseController = null;
    const addressCache = new Map();

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    }).addTo(map);

    const markerIcon = L.divIcon({
        className: 'location-map-marker',
        html: '<span></span>',
        iconSize: [24, 24],
        iconAnchor: [12, 12],
    });

    const validCoordinates = (latitude, longitude) => (
        Number.isFinite(latitude)
        && Number.isFinite(longitude)
        && latitude >= -90
        && latitude <= 90
        && longitude >= -180
        && longitude <= 180
    );

    const setAddress = (text, loading = false) => {
        if (!addressElement) return;
        addressElement.textContent = text;
        addressElement.classList.toggle('animate-pulse', loading);
    };

    const lookupAddress = (latitude, longitude) => {
        window.clearTimeout(reverseTimer);
        reverseTimer = window.setTimeout(async () => {
            const cacheKey = `${latitude.toFixed(5)},${longitude.toFixed(5)}`;
            if (addressCache.has(cacheKey)) {
                setAddress(addressCache.get(cacheKey));
                return;
            }

            reverseController?.abort();
            reverseController = new AbortController();
            setAddress('Mencari alamat titik lokasi...', true);

            try {
                const params = new URLSearchParams({
                    format: 'jsonv2',
                    lat: latitude,
                    lon: longitude,
                    addressdetails: 1,
                    zoom: 18,
                    'accept-language': 'id',
                });
                const response = await fetch(`https://nominatim.openstreetmap.org/reverse?${params}`, {
                    signal: reverseController.signal,
                    headers: { Accept: 'application/json' },
                });
                if (!response.ok) throw new Error('Address lookup failed');

                const result = await response.json();
                const address = result.display_name || 'Alamat tidak ditemukan untuk titik ini.';
                addressCache.set(cacheKey, address);
                setAddress(address);
            } catch (error) {
                if (error.name !== 'AbortError') {
                    setAddress('Alamat tidak dapat dimuat. Koordinat tetap dapat digunakan.');
                }
            }
        }, 1100);
    };

    const updatePosition = (latitude, longitude, options = {}) => {
        if (!validCoordinates(latitude, longitude)) return;

        latitudeInput.value = latitude.toFixed(7);
        longitudeInput.value = longitude.toFixed(7);

        if (!marker) {
            marker = L.marker([latitude, longitude], {
                draggable: true,
                icon: markerIcon,
                title: 'Lokasi laporan kerja',
            }).addTo(map);

            marker.on('dragend', () => {
                const position = marker.getLatLng();
                updatePosition(position.lat, position.lng);
                statusElement.textContent = 'Titik lokasi diperbarui dari marker.';
            });
        } else {
            marker.setLatLng([latitude, longitude]);
        }

        map.setView([latitude, longitude], options.zoom || Math.max(map.getZoom(), 16));
        lookupAddress(latitude, longitude);
    };

    map.on('click', (event) => {
        updatePosition(event.latlng.lat, event.latlng.lng);
        statusElement.textContent = 'Titik lokasi dipilih dari peta.';
    });

    const updateFromInputs = () => {
        const latitude = Number.parseFloat(latitudeInput.value);
        const longitude = Number.parseFloat(longitudeInput.value);
        if (validCoordinates(latitude, longitude)) {
            updatePosition(latitude, longitude);
            statusElement.textContent = 'Titik peta mengikuti koordinat yang dimasukkan.';
        }
    };

    latitudeInput.addEventListener('change', updateFromInputs);
    longitudeInput.addEventListener('change', updateFromInputs);

    locationButton?.addEventListener('click', () => {
        if (!navigator.geolocation) {
            statusElement.textContent = 'Browser tidak mendukung geolokasi.';
            return;
        }

        locationButton.disabled = true;
        statusElement.textContent = 'Mengambil lokasi perangkat...';
        navigator.geolocation.getCurrentPosition((position) => {
            updatePosition(position.coords.latitude, position.coords.longitude, { zoom: 17 });
            statusElement.textContent = `Lokasi berhasil diperbarui (akurasi ±${Math.round(position.coords.accuracy)} m).`;
            locationButton.disabled = false;
        }, () => {
            statusElement.textContent = 'Lokasi gagal diambil. Pastikan izin lokasi aktif dan situs memakai HTTPS.';
            locationButton.disabled = false;
        }, { enableHighAccuracy: true, timeout: 10000 });
    });

    const initialLatitude = Number.parseFloat(latitudeInput.value);
    const initialLongitude = Number.parseFloat(longitudeInput.value);
    if (validCoordinates(initialLatitude, initialLongitude)) {
        updatePosition(initialLatitude, initialLongitude, { zoom: 16 });
    } else {
        setAddress('Pilih titik pada peta atau ambil lokasi perangkat.');
    }

    window.setTimeout(() => map.invalidateSize(), 100);
}
