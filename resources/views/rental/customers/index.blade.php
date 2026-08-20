@extends('rental.layout')

@section('title', 'Category 2 - Data Pelanggan & Photo Capture')

@section('styles')
<style>
    .customer-page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.75rem;
    }

    .search-filter-box {
        display: flex;
        gap: 0.75rem;
        flex: 1;
        max-width: 500px;
    }

    .customer-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 1.5rem;
    }

    .customer-card {
        background-color: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        transition: var(--transition);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .customer-card:hover {
        transform: translateY(-4px);
        border-color: var(--border-highlight);
        box-shadow: var(--shadow-lg);
    }

    .customer-top-info {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .customer-avatar-lg {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid var(--primary);
        box-shadow: 0 0 12px rgba(99, 102, 241, 0.4);
        flex-shrink: 0;
    }

    .customer-meta h4 {
        font-family: var(--font-heading);
        font-size: 1.15rem;
        color: var(--text-main);
        margin-bottom: 0.2rem;
    }

    .customer-meta p {
        font-size: 0.825rem;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 0.4rem;
        margin-bottom: 0.15rem;
    }

    .customer-stats-bar {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
        background-color: var(--bg-input);
        padding: 0.85rem;
        border-radius: var(--radius-md);
        margin-bottom: 1.25rem;
        border: 1px solid var(--border);
    }

    .stat-pill {
        text-align: center;
    }

    .stat-pill .num {
        font-family: var(--font-heading);
        font-weight: 800;
        font-size: 1.1rem;
        color: var(--accent-cyan);
    }

    .stat-pill .lbl {
        font-size: 0.725rem;
        color: var(--text-dim);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .camera-container-modal {
        position: relative;
        width: 100%;
        height: 240px;
        background-color: #000;
        border-radius: var(--radius-md);
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px dashed var(--primary);
    }

    #webcamVideoCust, #webcamCanvasCust {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
</style>
@endsection

@section('content')
<div class="customer-page-header">
    <div>
        <h2 style="font-family: var(--font-heading); font-size: 1.6rem;"><i class="fa-solid fa-users" style="color: var(--primary);"></i> Data Pelanggan</h2>
        <p style="color: var(--text-muted); font-size: 0.9rem;">Kelola direktori pelanggan rental, histori sewa, dan upload foto via kamera/file.</p>
    </div>

    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
        <form action="{{ route('customers.index') }}" method="GET" class="search-filter-box">
            <input type="text" name="q" value="{{ $search ?? '' }}" class="form-control" placeholder="Cari nama, no. HP, atau NIK KTP...">
            <button type="submit" class="btn-custom btn-secondary-custom"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
            @if($search)
                <a href="{{ route('customers.index') }}" class="btn-custom btn-secondary-custom" title="Reset Search"><i class="fa-solid fa-rotate"></i></a>
            @endif
        </form>

        <button class="btn-custom btn-primary-custom" onclick="openAddCustomerModal()">
            <i class="fa-solid fa-user-plus"></i> Tambah Pelanggan Baru
        </button>
    </div>
</div>

<!-- CUSTOMER CARDS GRID -->
<div class="customer-grid">
    @forelse($customers as $c)
        <div class="customer-card">
            <div>
                <div class="customer-top-info">
                    <img src="{{ $c->photo_url }}" alt="{{ $c->name }}" class="customer-avatar-lg">
                    <div class="customer-meta">
                        <h4>{{ $c->name }}</h4>
                        <p><i class="fa-solid fa-phone" style="color: var(--primary);"></i> {{ $c->phone ?? 'Tidak Ada No. HP' }}</p>
                        @if($c->nik_ktp)
                            <p><i class="fa-solid fa-id-card" style="color: var(--accent-cyan);"></i> NIK: {{ $c->nik_ktp }}</p>
                        @endif
                        @if($c->address)
                            <p><i class="fa-solid fa-location-dot" style="color: var(--accent-amber);"></i> {{ Str::limit($c->address, 30) }}</p>
                        @endif
                    </div>
                </div>

                <div class="customer-stats-bar">
                    <div class="stat-pill">
                        <div class="num">{{ $c->rentals_count }} Kali</div>
                        <div class="lbl">Total Rent</div>
                    </div>
                    <div class="stat-pill">
                        <div class="num">Rp {{ number_format($c->rentals_sum_total_price ?? 0, 0, ',', '.') }}</div>
                        <div class="lbl">Total Belanja</div>
                    </div>
                </div>

                @if($c->notes)
                    <p style="font-size: 0.8rem; color: var(--text-dim); background: rgba(255,255,255,0.03); padding: 0.5rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                        <i class="fa-solid fa-note-sticky"></i> {{ $c->notes }}
                    </p>
                @endif
            </div>

            <div style="display: flex; gap: 0.5rem; border-top: 1px solid var(--border); padding-top: 1rem;">
                <button class="btn-custom btn-secondary-custom btn-sm" style="flex: 1;" onclick="viewCustomerDetail({{ $c->id }})">
                    <i class="fa-solid fa-eye"></i> Detail Histori
                </button>
                <button class="btn-custom btn-secondary-custom btn-sm" onclick="editCustomer({{ json_encode($c) }})">
                    <i class="fa-solid fa-pen-to-square"></i> Edit
                </button>
                <form action="{{ route('customers.destroy', $c->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pelanggan {{ $c->name }}?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-custom btn-danger-custom btn-sm" title="Hapus Pelanggan">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>
    @empty
        <div style="grid-column: 1 / -1; text-align: center; padding: 4rem; background-color: var(--bg-card); border-radius: var(--radius-lg);">
            <i class="fa-solid fa-users-slash fa-3x" style="color: var(--text-dim); margin-bottom: 1rem;"></i>
            <h3>Data Pelanggan Tidak Ditemukan</h3>
            <p style="color: var(--text-muted);">Belum ada pelanggan yang sesuai dengan pencarian Anda.</p>
        </div>
    @endforelse
</div>

<div style="margin-top: 1.5rem;">
    {{ $customers->links() }}
</div>

<!-- MODAL TAMBAH / EDIT PELANGGAN WITH WEBCAM INTEGRATION -->
<div class="modal-overlay" id="customerModal">
    <div class="modal-card">
        <div class="modal-header">
            <h3 id="customerModalTitle"><i class="fa-solid fa-user-plus" style="color: var(--primary);"></i> Tambah Pelanggan Baru</h3>
            <button class="modal-close" onclick="closeCustomerModal()">&times;</button>
        </div>
        <form id="customerForm" action="{{ route('customers.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" id="customerFormMethod" value="POST">

            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap Pelanggan *</label>
                    <input type="text" name="name" id="cust_name" class="form-control" placeholder="Contoh: Budi Santoso" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">No. Telepon / WhatsApp</label>
                        <input type="text" name="phone" id="cust_phone" class="form-control" placeholder="081234567890">
                    </div>
                    <div class="form-group">
                        <label class="form-label">NIK KTP (Opsional)</label>
                        <input type="text" name="nik_ktp" id="cust_nik_ktp" class="form-control" placeholder="317101...">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Alamat Pelanggan</label>
                    <textarea name="address" id="cust_address" class="form-control" rows="2" placeholder="Alamat rumah / domisili"></textarea>
                </div>

                <!-- DUAL PHOTO UPLOAD MODULE: FILE UPLOAD OR LIVE WEBCAM CAMERA -->
                <div class="form-group">
                    <label class="form-label"><i class="fa-solid fa-camera"></i> Foto Pelanggan (Upload Berkas ATAU Kamera Langsung)</label>
                    <div style="display: flex; gap: 1rem; margin-bottom: 0.5rem; font-size: 0.85rem;">
                        <label style="cursor: pointer;"><input type="radio" name="cust_photo_mode" value="file" onclick="toggleCustPhotoMode('file')" checked> Upload Manual File</label>
                        <label style="cursor: pointer;"><input type="radio" name="cust_photo_mode" value="camera" onclick="toggleCustPhotoMode('camera')"> Ambil Foto via Kamera (Webcam)</label>
                    </div>

                    <!-- File upload input -->
                    <div id="cust_photo_file_box">
                        <input type="file" name="photo_file" accept="image/*" class="form-control">
                    </div>

                    <!-- Live Camera Webcam Box -->
                    <div id="cust_photo_camera_box" style="display: none;">
                        <div class="camera-container-modal">
                            <video id="webcamVideoCust" autoplay playsinline></video>
                            <canvas id="webcamCanvasCust" style="display:none;"></canvas>
                            <div style="position: absolute; bottom: 10px; display: flex; gap: 0.5rem;">
                                <button type="button" class="btn-custom btn-primary-custom btn-sm" id="btnSnapCust" onclick="takeWebcamSnapshotCust()">
                                    <i class="fa-solid fa-camera"></i> Jepret Foto
                                </button>
                                <button type="button" class="btn-custom btn-secondary-custom btn-sm" id="btnRetakeCust" onclick="retakeWebcamPhotoCust()" style="display: none;">
                                    <i class="fa-solid fa-rotate-left"></i> Foto Ulang
                                </button>
                            </div>
                        </div>
                        <input type="hidden" name="photo_camera" id="cust_photo_camera_input">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Catatan Tambahan / Status Member</label>
                    <input type="text" name="notes" id="cust_notes" class="form-control" placeholder="Contoh: Member Gold, Langganan Malam">
                </div>
            </div>

            <div class="modal-header" style="border-top: 1px solid var(--border); border-bottom: none; justify-content: flex-end; gap: 0.75rem;">
                <button type="button" class="btn-custom btn-secondary-custom" onclick="closeCustomerModal()">Batal</button>
                <button type="submit" class="btn-custom btn-primary-custom"><i class="fa-solid fa-floppy-disk"></i> Simpan Data Pelanggan</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL DETAIL PELANGGAN & HISTORI SEWA -->
<div class="modal-overlay" id="detailCustomerModal">
    <div class="modal-card" style="max-width: 720px;">
        <div class="modal-header">
            <h3><i class="fa-solid fa-id-badge" style="color: var(--accent-cyan);"></i> Profil & Histori Pelanggan</h3>
            <button class="modal-close" onclick="closeDetailCustomerModal()">&times;</button>
        </div>
        <div class="modal-body" id="detailCustomerBody">
            <div style="text-align: center; padding: 2rem;">
                <i class="fa-solid fa-spinner fa-spin fa-2x" style="color: var(--primary);"></i>
                <p style="margin-top: 0.5rem; color: var(--text-muted);">Memuat detail data pelanggan...</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let custStream = null;

    function openAddCustomerModal() {
        document.getElementById('customerModalTitle').innerHTML = '<i class="fa-solid fa-user-plus" style="color: var(--primary);"></i> Tambah Pelanggan Baru';
        document.getElementById('customerForm').action = "{{ route('customers.store') }}";
        document.getElementById('customerFormMethod').value = "POST";
        document.getElementById('cust_name').value = "";
        document.getElementById('cust_phone').value = "";
        document.getElementById('cust_nik_ktp').value = "";
        document.getElementById('cust_address').value = "";
        document.getElementById('cust_notes').value = "";
        document.getElementById('customerModal').classList.add('active');
    }

    function editCustomer(c) {
        document.getElementById('customerModalTitle').innerHTML = '<i class="fa-solid fa-pen-to-square" style="color: var(--accent-cyan);"></i> Edit Pelanggan: ' + c.name;
        document.getElementById('customerForm').action = "/customers/" + c.id;
        document.getElementById('customerFormMethod').value = "PUT";
        document.getElementById('cust_name').value = c.name || "";
        document.getElementById('cust_phone').value = c.phone || "";
        document.getElementById('cust_nik_ktp').value = c.nik_ktp || "";
        document.getElementById('cust_address').value = c.address || "";
        document.getElementById('cust_notes').value = c.notes || "";
        document.getElementById('customerModal').classList.add('active');
    }

    function closeCustomerModal() {
        document.getElementById('customerModal').classList.remove('active');
        stopCustWebcamStream();
    }

    function toggleCustPhotoMode(mode) {
        const fileBox = document.getElementById('cust_photo_file_box');
        const cameraBox = document.getElementById('cust_photo_camera_box');
        if (mode === 'file') {
            fileBox.style.display = 'block';
            cameraBox.style.display = 'none';
            stopCustWebcamStream();
        } else {
            fileBox.style.display = 'none';
            cameraBox.style.display = 'block';
            startCustWebcamStream();
        }
    }

    function startCustWebcamStream() {
        const video = document.getElementById('webcamVideoCust');
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(stream => {
                    custStream = stream;
                    video.srcObject = stream;
                    video.style.display = 'block';
                    document.getElementById('webcamCanvasCust').style.display = 'none';
                    document.getElementById('btnSnapCust').style.display = 'inline-flex';
                    document.getElementById('btnRetakeCust').style.display = 'none';
                })
                .catch(err => {
                    alert('Gagal membuka kamera: ' + err.message);
                });
        }
    }

    function stopCustWebcamStream() {
        if (custStream) {
            custStream.getTracks().forEach(track => track.stop());
            custStream = null;
        }
    }

    function takeWebcamSnapshotCust() {
        const video = document.getElementById('webcamVideoCust');
        const canvas = document.getElementById('webcamCanvasCust');
        const input = document.getElementById('cust_photo_camera_input');

        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 480;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        input.value = canvas.toDataURL('image/jpeg');

        video.style.display = 'none';
        canvas.style.display = 'block';
        document.getElementById('btnSnapCust').style.display = 'none';
        document.getElementById('btnRetakeCust').style.display = 'inline-flex';
    }

    function retakeWebcamPhotoCust() {
        document.getElementById('cust_photo_camera_input').value = '';
        document.getElementById('webcamVideoCust').style.display = 'block';
        document.getElementById('webcamCanvasCust').style.display = 'none';
        document.getElementById('btnSnapCust').style.display = 'inline-flex';
        document.getElementById('btnRetakeCust').style.display = 'none';
    }

    function viewCustomerDetail(id) {
        document.getElementById('detailCustomerModal').classList.add('active');
        fetch('/customers/' + id, { headers: { 'Accept': 'application/json' } })
            .then(res => res.json())
            .then(data => {
                let html = `
                    <div style="display: flex; gap: 1.25rem; align-items: center; background: var(--bg-input); padding: 1.25rem; border-radius: var(--radius-md); margin-bottom: 1.25rem;">
                        <img src="${data.photo_url || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(data.name)}" style="width: 72px; height: 72px; border-radius: 50%; object-fit: cover; border: 3px solid var(--primary);">
                        <div>
                            <h3 style="font-family: var(--font-heading); font-size: 1.3rem;">${data.name}</h3>
                            <p style="color: var(--text-muted); font-size: 0.9rem;"><i class="fa-solid fa-phone"></i> ${data.phone || '-'}</p>
                            <p style="color: var(--text-dim); font-size: 0.85rem;"><i class="fa-solid fa-id-card"></i> KTP: ${data.nik_ktp || '-'} &bull; <i class="fa-solid fa-location-dot"></i> ${data.address || '-'}</p>
                        </div>
                    </div>

                    <h4 style="font-family: var(--font-heading); font-size: 1.05rem; margin-bottom: 0.85rem;"><i class="fa-solid fa-history"></i> Timeline Histori Rental</h4>
                `;

                if (data.rentals && data.rentals.length > 0) {
                    html += `<div style="max-height: 300px; overflow-y: auto;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; text-align: left;">
                            <thead>
                                <tr style="border-bottom: 1px solid var(--border); color: var(--text-muted);">
                                    <th style="padding: 0.5rem;">Unit</th>
                                    <th style="padding: 0.5rem;">Waktu</th>
                                    <th style="padding: 0.5rem;">Durasi</th>
                                    <th style="padding: 0.5rem;">Total</th>
                                    <th style="padding: 0.5rem;">Status</th>
                                </tr>
                            </thead>
                            <tbody>`;
                    data.rentals.forEach(r => {
                        html += `
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <td style="padding: 0.65rem; font-weight: bold; color: var(--accent-cyan);">${r.unit ? r.unit.code : 'Unit'}</td>
                                <td style="padding: 0.65rem;">${new Date(r.start_time).toLocaleString('id-ID')}</td>
                                <td style="padding: 0.65rem;">${r.duration_hours} Jam</td>
                                <td style="padding: 0.65rem; font-weight: bold;">Rp ${parseFloat(r.total_price).toLocaleString('id-ID')}</td>
                                <td style="padding: 0.65rem;"><span style="padding: 0.2rem 0.5rem; border-radius: 12px; font-size: 0.75rem; background: rgba(99,102,241,0.2); color: #a78bfa;">${r.status}</span></td>
                            </tr>
                        `;
                    });
                    html += `</tbody></table></div>`;
                } else {
                    html += `<p style="color: var(--text-dim); text-align: center; padding: 1.5rem;">Belum ada histori rental untuk pelanggan ini.</p>`;
                }

                document.getElementById('detailCustomerBody').innerHTML = html;
            });
    }

    function closeDetailCustomerModal() {
        document.getElementById('detailCustomerModal').classList.remove('active');
    }
</script>
@endsection
