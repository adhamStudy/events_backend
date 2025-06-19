@php $mapId = 'leaflet-map-' . uniqid(); @endphp

<div wire:ignore>
    <div id="{{ $mapId }}" style="height: 400px; margin-bottom: 1rem;"></div>
</div>

<!-- Include Leaflet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const latInput = document.querySelector('input[name="data.latitude"]');
        const lngInput = document.querySelector('input[name="data.longitude"]');

        const lat = parseFloat(latInput?.value) || 21.4858;
        const lng = parseFloat(lngInput?.value) || 39.1925;

        const map = L.map('{{ $mapId }}').setView([lat, lng], 6);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const marker = L.marker([lat, lng], { draggable: true }).addTo(map);

        marker.on('dragend', function (e) {
            const position = marker.getLatLng();
            latInput.value = position.lat.toFixed(6);
            lngInput.value = position.lng.toFixed(6);

            // Trigger events for Livewire binding
            latInput.dispatchEvent(new Event('input'));
            lngInput.dispatchEvent(new Event('input'));
        });

        map.on('click', function (e) {
            marker.setLatLng(e.latlng);
            latInput.value = e.latlng.lat.toFixed(6);
            lngInput.value = e.latlng.lng.toFixed(6);

            latInput.dispatchEvent(new Event('input'));
            lngInput.dispatchEvent(new Event('input'));
        });
    });
</script>
