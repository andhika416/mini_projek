

import Alpine from 'alpinejs';
import DataTable from 'datatables.net-dt';
import L from 'leaflet';
import 'datatables.net-dt/css/dataTables.dataTables.css';
import 'leaflet/dist/leaflet.css';

window.Alpine = Alpine;

Alpine.data('counter', (target, duration = 1000) => ({
    current: 0,
    target: Number(target) || 0,
    formatter: new Intl.NumberFormat('id-ID'),

    init() {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            this.current = this.target;
            return;
        }

        const observer = new IntersectionObserver((entries) => {
            if (!entries[0].isIntersecting) return;

            observer.disconnect();
            this.animate();
        }, { threshold: 0.4 });

        observer.observe(this.$el);
    },

    animate() {
        const startedAt = performance.now();

        const update = (timestamp) => {
            const progress = Math.min((timestamp - startedAt) / duration, 1);
            const easedProgress = 1 - Math.pow(1 - progress, 3);
            this.current = Math.round(this.target * easedProgress);

            if (progress < 1) {
                requestAnimationFrame(update);
            } else {
                this.current = this.target;
            }
        };

        requestAnimationFrame(update);
    },
}));

Alpine.data('liveClock', (timeZone = 'Asia/Jakarta') => ({
    time: '',
    timer: null,

    init() {
        this.update();
        this.timer = window.setInterval(() => this.update(), 1000);
    },

    update() {
        this.time = new Intl.DateTimeFormat('id-ID', {
            timeZone,
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false,
        }).format(new Date());
    },

    destroy() {
        window.clearInterval(this.timer);
    },
}));

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    initializeLocationMap();
    initializeReportChart();

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

async function initializeReportChart() {
    const canvas = document.querySelector('#report-chart');
    if (!canvas) return;

    const { default: Chart } = await import('chart.js/auto');
    const labels = JSON.parse(canvas.dataset.labels || '[]');
    const values = JSON.parse(canvas.dataset.values || '[]');
    const context = canvas.getContext('2d');
    const gradient = context.createLinearGradient(0, 0, 0, 320);
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const lineRevealPlugin = {
        id: 'lineReveal',
        beforeInit(chart) {
            chart.$lineRevealProgress = reducedMotion ? 1 : 0;
        },
        beforeDatasetsDraw(chart) {
            if (chart.$lineRevealProgress >= 1) return;

            const { left, right, top, bottom } = chart.chartArea;
            chart.ctx.save();
            chart.ctx.beginPath();
            chart.ctx.rect(
                left - 10,
                top - 10,
                (right - left + 20) * chart.$lineRevealProgress,
                bottom - top + 20,
            );
            chart.ctx.clip();
        },
        afterDatasetsDraw(chart) {
            if (chart.$lineRevealProgress < 1) {
                chart.ctx.restore();
            }
        },
        afterRender(chart) {
            if (reducedMotion || chart.$lineRevealStarted) return;

            chart.$lineRevealStarted = true;
            const startedAt = performance.now();
            const duration = 1250;

            const reveal = (timestamp) => {
                const progress = Math.min((timestamp - startedAt) / duration, 1);
                chart.$lineRevealProgress = 1 - Math.pow(1 - progress, 4);
                chart.draw();

                if (progress < 1) requestAnimationFrame(reveal);
            };

            requestAnimationFrame(reveal);
        },
    };
    gradient.addColorStop(0, 'rgba(20, 184, 166, 0.28)');
    gradient.addColorStop(1, 'rgba(20, 184, 166, 0.02)');

    new Chart(context, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Laporan dibuat',
                data: values,
                backgroundColor: gradient,
                borderColor: '#0f9488',
                borderWidth: 3,
                fill: true,
                tension: 0.38,
                pointRadius: 5,
                pointHoverRadius: 7,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#0f9488',
                pointBorderWidth: 3,
                pointHoverBackgroundColor: '#0f9488',
                pointHoverBorderColor: '#ffffff',
            }],
        },
        plugins: [lineRevealPlugin],
        options: {
            animation: false,
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index',
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    displayColors: false,
                    padding: 12,
                    callbacks: {
                        label: (item) => `${item.raw} laporan`,
                    },
                },
            },
            scales: {
                x: {
                    border: { display: false },
                    grid: { display: false },
                    ticks: {
                        color: '#64748b',
                        font: { family: 'Poppins', size: 11, weight: 500 },
                    },
                },
                y: {
                    beginAtZero: true,
                    suggestedMax: Math.max(...values, 1) + 1,
                    border: { display: false },
                    grid: { color: 'rgba(148, 163, 184, 0.16)' },
                    ticks: {
                        color: '#94a3b8',
                        precision: 0,
                        stepSize: 1,
                        font: { family: 'Poppins', size: 11 },
                    },
                },
            },
        },
    });
}

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
