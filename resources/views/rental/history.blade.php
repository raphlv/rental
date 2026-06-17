@extends('rental.layout')

@section('title', 'Riwayat Rental')

@section('styles')
<style>
    .history-card {
        background-color: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: var(--shadow);
        overflow: hidden;
    }

    .table-container {
        overflow-x: auto;
        margin-top: 1rem;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    th {
        background-color: rgba(0,0,0,0.2);
        color: var(--text-muted);
        font-weight: 600;
        padding: 1rem;
        border-bottom: 2px solid var(--border);
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.05em;
    }

    td {
        padding: 1rem;
        border-bottom: 1px solid var(--border);
        font-size: 0.95rem;
        vertical-align: middle;
    }

    tr:hover td {
        background-color: rgba(255,255,255,0.02);
    }

    .history-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .history-title {
        font-size: 1.5rem;
        font-weight: 700;
        background: linear-gradient(135deg, #a78bfa, #8b5cf6);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .badge-status {
        padding: 0.25rem 0.5rem;
        border-radius: 0.5rem;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .badge-active {
        background-color: rgba(245, 158, 11, 0.15);
        color: var(--warning);
        border: 1px solid rgba(245, 158, 11, 0.3);
    }

    .badge-completed {
        background-color: rgba(16, 185, 129, 0.15);
        color: var(--accent);
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .proof-thumbnail {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 0.5rem;
        cursor: pointer;
        border: 1px solid var(--border);
        transition: var(--transition);
    }

    .proof-thumbnail:hover {
        transform: scale(1.1);
        border-color: var(--primary);
    }

    /* Modal for Image Preview */
    .image-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 2000;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
    }

    .image-modal.show {
        opacity: 1;
        pointer-events: auto;
    }

    .image-modal-content {
        max-width: 90%;
        max-height: 80%;
        border-radius: 0.75rem;
        border: 2px solid var(--border);
        box-shadow: 0 0 30px rgba(0,0,0,0.5);
    }

    .image-modal-close {
        position: absolute;
        top: 2rem;
        right: 2rem;
        color: white;
        font-size: 2.5rem;
        cursor: pointer;
        background: none;
        border: none;
    }

    /* Laravel Pagination Styling Override */
    .pagination-wrapper {
        margin-top: 1.5rem;
        display: flex;
        justify-content: center;
    }
    
    .pagination-wrapper .pagination {
        display: flex;
        list-style: none;
        gap: 0.25rem;
    }

    .pagination-wrapper .page-item .page-link {
        padding: 0.5rem 1rem;
        background-color: var(--bg-input);
        border: 1px solid var(--border);
        color: var(--text-muted);
        text-decoration: none;
        border-radius: 0.5rem;
        transition: var(--transition);
    }

    .pagination-wrapper .page-item.active .page-link {
        background-color: var(--primary);
        color: var(--text-main);
        border-color: var(--primary);
    }

    .pagination-wrapper .page-item .page-link:hover {
        background-color: var(--border);
        color: var(--text-main);
    }

    /* empty state */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
    }

    .empty-state svg {
        width: 64px;
        height: 64px;
        stroke: var(--text-muted);
        margin-bottom: 1rem;
    }

    .empty-state h3 {
        font-size: 1.25rem;
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        color: var(--text-muted);
    }
</style>
@endsection

@section('content')
<div class="history-card">
    <div class="history-header">
        <h2 class="history-title">Riwayat Transaksi Rental</h2>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm">Kembali ke Dashboard</a>
    </div>

    @if($rentals->isEmpty())
        <div class="empty-state">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="8" y1="12" x2="16" y2="12"></line>
            </svg>
            <h3>Belum Ada Riwayat</h3>
            <p>Transaksi persewaan yang berhasil akan tercatat secara otomatis di sini.</p>
        </div>
    @else
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Unit</th>
                        <th>Kategori</th>
                        <th>Nama Penyewa</th>
                        <th>Durasi</th>
                        <th>Waktu Mulai</th>
                        <th>Waktu Selesai</th>
                        <th>Metode Bayar</th>
                        <th>Total Harga</th>
                        <th>Bukti Foto</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rentals as $rental)
                        <tr>
                            <td>#{{ $rental->id }}</td>
                            <td style="font-weight: 600;">{{ $rental->unit ? $rental->unit->name : 'Unit Terhapus' }}</td>
                            <td><span class="unit-type" style="margin-top:0;">{{ $rental->unit ? $rental->unit->type : '-' }}</span></td>
                            <td>{{ $rental->customer_name }}</td>
                            <td>{{ $rental->duration }} Jam</td>
                            <td style="font-size: 0.85rem; color: var(--text-muted);">
                                {{ $rental->start_time->format('d M Y') }}<br>
                                <span style="font-weight: 600; color: var(--text-main);">{{ $rental->start_time->format('H:i') }} WIB</span>
                            </td>
                            <td style="font-size: 0.85rem; color: var(--text-muted);">
                                {{ $rental->end_time->format('d M Y') }}<br>
                                <span style="font-weight: 600; color: var(--text-main);">{{ $rental->end_time->format('H:i') }} WIB</span>
                            </td>
                            <td>{{ $rental->payment_method }}</td>
                            <td style="font-weight: 700; color: var(--primary);">
                                Rp {{ number_format($rental->total_price, 0, ',', '.') }}
                            </td>
                            <td>
                                @if($rental->photo_proof)
                                    <img src="{{ asset($rental->photo_proof) }}" 
                                         alt="Bukti Penerimaan" 
                                         class="proof-thumbnail" 
                                         onclick="previewImage('{{ asset($rental->photo_proof) }}')">
                                @else
                                    <span style="color: var(--text-muted); font-size: 0.85rem;">Tidak ada foto</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge-status {{ $rental->status === 'active' ? 'badge-active' : 'badge-completed' }}">
                                    {{ $rental->status === 'active' ? 'Aktif' : 'Selesai' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper">
            {{ $rentals->links() }}
        </div>
    @endif
</div>

<!-- Image Preview Modal -->
<div id="imagePreviewModal" class="image-modal">
    <button class="image-modal-close" onclick="closeImagePreview()">&times;</button>
    <img id="imagePreviewTarget" src="" class="image-modal-content" alt="Preview Bukti Foto">
</div>
@endsection

@section('scripts')
<script>
    function previewImage(src) {
        const modal = document.getElementById('imagePreviewModal');
        const img = document.getElementById('imagePreviewTarget');
        img.src = src;
        modal.classList.add('show');
    }

    function closeImagePreview() {
        const modal = document.getElementById('imagePreviewModal');
        modal.classList.remove('show');
    }

    // Close preview modal on ESC key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeImagePreview();
        }
    });
</script>
@endsection
