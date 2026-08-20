@extends('rental.layout')

@section('title', 'Category 3 - Riwayat Pelanggan & Laporan Periodik')

@section('styles')
<style>
    .history-header {
        margin-bottom: 1.75rem;
    }

    .period-tab-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        background-color: var(--bg-surface);
        padding: 0.85rem 1.25rem;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        margin-bottom: 1.5rem;
    }

    .period-tabs {
        display: flex;
        gap: 0.5rem;
    }

    .period-tab {
        padding: 0.5rem 1.1rem;
        border-radius: var(--radius-md);
        color: var(--text-muted);
        text-decoration: none;
        font-weight: 600;
        font-size: 0.85rem;
        transition: var(--transition);
        border: 1px solid transparent;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .period-tab:hover {
        color: var(--text-main);
        background-color: var(--bg-card);
    }

    .period-tab.active {
        background-color: var(--primary);
        color: #fff;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.35);
    }

    .period-filter-form {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    /* Periodic Analytics Cards */
    .history-analytics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.25rem;
        margin-bottom: 1.75rem;
    }

    .analytic-card {
        background-color: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 1.25rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .analytic-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        flex-shrink: 0;
    }

    .icon-revenue { background: rgba(16, 185, 129, 0.15); color: #34d399; }
    .icon-rentals { background: rgba(99, 102, 241, 0.15); color: #a78bfa; }
    .icon-hours { background: rgba(6, 182, 212, 0.15); color: #22d3ee; }
    .icon-fav { background: rgba(245, 158, 11, 0.15); color: #fbbf24; }

    .analytic-info p {
        font-size: 0.75rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.15rem;
    }

    .analytic-info h3 {
        font-family: var(--font-heading);
        font-size: 1.35rem;
        font-weight: 800;
        color: var(--text-main);
    }

    /* History Table Card */
    .table-card {
        background-color: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .custom-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 0.9rem;
    }

    .custom-table th {
        background-color: var(--bg-surface);
        color: var(--text-muted);
        font-weight: 700;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--border);
    }

    .custom-table td {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid rgba(255,255,255,0.05);
        color: var(--text-main);
    }

    .custom-table tr:hover {
        background-color: var(--bg-card-hover);
    }

    .customer-cell {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .customer-thumb {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--border-highlight);
    }

    /* Printable Receipt Modal */
    .receipt-box {
        background-color: #ffffff;
        color: #1e293b;
        padding: 1.5rem;
        border-radius: var(--radius-md);
        font-family: 'Courier New', Courier, monospace;
    }

    @media print {
        body * { visibility: hidden; }
        .receipt-box, .receipt-box * { visibility: visible; }
        .receipt-box { position: absolute; left: 0; top: 0; width: 100%; }
    }
</style>
@endsection

@section('content')
<div class="history-header">
    <h2 style="font-family: var(--font-heading); font-size: 1.6rem;"><i class="fa-solid fa-clock-rotate-left" style="color: var(--primary);"></i> Riwayat Pelanggan & Laporan Periodic</h2>
    <p style="color: var(--text-muted); font-size: 0.9rem;">Rekap data penyewa dan omzet rental berdasarkan periode: Per Hari, Per Minggu, Per Bulan, dan Per Tahun.</p>
</div>

<!-- PERIOD TABS & DATE PICKER CONTROLS -->
<div class="period-tab-bar">
    <!-- 4 REQUIRED PERIOD TABS -->
    <div class="period-tabs">
        <a href="{{ route('history', ['period' => 'daily', 'date' => $selectedDate]) }}" class="period-tab {{ $period === 'daily' ? 'active' : '' }}">
            <i class="fa-solid fa-calendar-day"></i> Per Hari
        </a>
        <a href="{{ route('history', ['period' => 'weekly', 'date' => $selectedDate]) }}" class="period-tab {{ $period === 'weekly' ? 'active' : '' }}">
            <i class="fa-solid fa-calendar-week"></i> Per Minggu
        </a>
        <a href="{{ route('history', ['period' => 'monthly', 'month' => $selectedMonth]) }}" class="period-tab {{ $period === 'monthly' ? 'active' : '' }}">
            <i class="fa-solid fa-calendar-days"></i> Per Bulan
        </a>
        <a href="{{ route('history', ['period' => 'yearly', 'year' => $selectedYear]) }}" class="period-tab {{ $period === 'yearly' ? 'active' : '' }}">
            <i class="fa-solid fa-chart-line"></i> Per Tahun
        </a>
    </div>

    <!-- PERIOD INPUT FORM -->
    <form action="{{ route('history') }}" method="GET" class="period-filter-form">
        <input type="hidden" name="period" value="{{ $period }}">

        @if($period === 'daily' || $period === 'weekly')
            <input type="date" name="date" value="{{ $selectedDate }}" class="form-control" style="width: auto;" onchange="this.form.submit()">
        @elseif($period === 'monthly')
            <input type="month" name="month" value="{{ $selectedMonth }}" class="form-control" style="width: auto;" onchange="this.form.submit()">
        @elseif($period === 'yearly')
            <select name="year" class="form-select" style="width: auto;" onchange="this.form.submit()">
                @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                    <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        @endif

        <span style="font-size: 0.85rem; font-weight: 700; color: var(--accent-cyan); margin-left: 0.5rem;">
            {{ $filterLabel }}
        </span>
    </form>
</div>

<!-- PERIODIC ANALYTICS METRICS CARDS -->
<div class="history-analytics-grid">
    <div class="analytic-card">
        <div class="analytic-icon icon-revenue">
            <i class="fa-solid fa-money-bill-wave"></i>
        </div>
        <div class="analytic-info">
            <p>Total Pendapatan</p>
            <h3>Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
        </div>
    </div>

    <div class="analytic-card">
        <div class="analytic-icon icon-rentals">
            <i class="fa-solid fa-receipt"></i>
        </div>
        <div class="analytic-info">
            <p>Total Transaksi / Penyewa</p>
            <h3>{{ $totalRentals }} Penyewa</h3>
        </div>
    </div>

    <div class="analytic-card">
        <div class="analytic-icon icon-hours">
            <i class="fa-solid fa-stopwatch"></i>
        </div>
        <div class="analytic-info">
            <p>Total Durasi Tersewa</p>
            <h3>{{ number_format($totalHours, 1) }} Jam</h3>
        </div>
    </div>

    <div class="analytic-card">
        <div class="analytic-icon icon-fav">
            <i class="fa-solid fa-trophy"></i>
        </div>
        <div class="analytic-info">
            <p>Console Terlaris</p>
            <h3>{{ $topConsoleName }}</h3>
        </div>
    </div>
</div>

<!-- DETAILED RENTAL HISTORY TABLE -->
<div class="table-card">
    <div style="padding: 1.25rem; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
        <h3 style="font-family: var(--font-heading); font-size: 1.15rem;">
            <i class="fa-solid fa-list-check" style="color: var(--primary);"></i> Tabel Riwayat Pelanggan ({{ $filterLabel }})
        </h3>
        <button class="btn-custom btn-secondary-custom btn-sm" onclick="window.print()">
            <i class="fa-solid fa-print"></i> Cetak Laporan
        </button>
    </div>

    <div style="overflow-x: auto;">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Pelanggan</th>
                    <th>Unit PS</th>
                    <th>Waktu Mulai - Selesai</th>
                    <th>Durasi</th>
                    <th>Pembayaran</th>
                    <th>Total Bayar</th>
                    <th>Status</th>
                    <th style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rentals as $rental)
                    <tr>
                        <td>
                            <div class="customer-cell">
                                <img src="{{ $rental->photo_url }}" alt="{{ $rental->customer_name }}" class="customer-thumb">
                                <div>
                                    <div style="font-weight: 700; color: var(--text-main);">{{ $rental->customer_name }}</div>
                                    <div style="font-size: 0.775rem; color: var(--text-muted);"><i class="fa-solid fa-phone"></i> {{ $rental->customer_phone ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span style="font-weight: 800; color: var(--accent-cyan);">{{ $rental->unit ? $rental->unit->code : 'Unit' }}</span>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $rental->unit ? $rental->unit->type : '' }}</div>
                        </td>
                        <td>
                            <div>{{ $rental->start_time->isoFormat('D MMM YYYY, HH:mm') }}</div>
                            <div style="font-size: 0.775rem; color: var(--text-dim);">s/d {{ $rental->end_time->isoFormat('HH:mm') }}</div>
                        </td>
                        <td>
                            <span style="font-weight: 700;">{{ $rental->duration_hours }} Jam</span>
                        </td>
                        <td>
                            <span style="background: rgba(255,255,255,0.06); padding: 0.2rem 0.5rem; border-radius: 6px; font-size: 0.8rem; font-weight: 600;">
                                {{ $rental->payment_method }}
                            </span>
                            <div style="font-size: 0.75rem; color: var(--accent-emerald); margin-top: 0.15rem;">
                                <i class="fa-solid fa-circle-check"></i> {{ $rental->payment_status }}
                            </div>
                        </td>
                        <td>
                            <span style="font-weight: 800; font-family: var(--font-heading); color: var(--accent-cyan); font-size: 1rem;">
                                Rp {{ number_format($rental->total_price, 0, ',', '.') }}
                            </span>
                        </td>
                        <td>
                            @if($rental->status === 'completed')
                                <span style="background: rgba(16, 185, 129, 0.15); color: #34d399; padding: 0.25rem 0.65rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700;">
                                    <i class="fa-solid fa-check"></i> Selesai
                                </span>
                            @elseif($rental->status === 'active')
                                <span style="background: rgba(245, 158, 11, 0.15); color: #fbbf24; padding: 0.25rem 0.65rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700;">
                                    <i class="fa-solid fa-spinner fa-spin"></i> Aktif
                                </span>
                            @else
                                <span style="background: rgba(244, 63, 94, 0.15); color: #fb7185; padding: 0.25rem 0.65rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700;">
                                    <i class="fa-solid fa-xmark"></i> Batal
                                </span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <button class="btn-custom btn-secondary-custom btn-sm" onclick="showReceiptModal({{ json_encode($rental) }})">
                                <i class="fa-solid fa-receipt"></i> Struk
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                            <i class="fa-solid fa-calendar-xmark fa-2x" style="margin-bottom: 0.5rem; color: var(--text-dim);"></i>
                            <p>Tidak ada riwayat penyewa pada periode ini.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="padding: 1rem 1.25rem;">
        {{ $rentals->links() }}
    </div>
</div>

<!-- STRUK / RECEIPT MODAL -->
<div class="modal-overlay" id="receiptModal">
    <div class="modal-card" style="max-width: 480px;">
        <div class="modal-header">
            <h3><i class="fa-solid fa-receipt" style="color: var(--primary);"></i> Struk Penyewaan PS</h3>
            <button class="modal-close" onclick="closeReceiptModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="receipt-box" id="receiptContent">
                <!-- Receipt details populated via JS -->
            </div>
        </div>
        <div class="modal-header" style="border-top: 1px solid var(--border); border-bottom: none; justify-content: flex-end; gap: 0.75rem;">
            <button type="button" class="btn-custom btn-secondary-custom" onclick="closeReceiptModal()">Tutup</button>
            <button type="button" class="btn-custom btn-primary-custom" onclick="window.print()"><i class="fa-solid fa-print"></i> Cetak Struk</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function showReceiptModal(r) {
        document.getElementById('receiptModal').classList.add('active');
        const unitCode = r.unit ? r.unit.code : 'Unit';
        const unitType = r.unit ? r.unit.type : '';
        const startTime = new Date(r.start_time).toLocaleString('id-ID');
        const endTime = new Date(r.end_time).toLocaleString('id-ID');
        const total = parseFloat(r.total_price).toLocaleString('id-ID');

        let html = `
            <div style="text-align: center; margin-bottom: 1rem; border-bottom: 2px dashed #000; padding-bottom: 0.85rem;">
                <h2 style="font-size: 1.2rem; font-weight: bold; text-transform: uppercase;">ABDILLANS GAMING</h2>
                <p style="font-size: 0.8rem;">Rental PlayStation & Gaming Zone</p>
                <p style="font-size: 0.75rem; color: #64748b;">No. Struk: #RENT-${r.id}</p>
            </div>
            <div style="font-size: 0.85rem; line-height: 1.6; margin-bottom: 1rem;">
                <div><strong>Pelanggan:</strong> ${r.customer_name}</div>
                <div><strong>No. HP:</strong> ${r.customer_phone || '-'}</div>
                <div><strong>Unit PS:</strong> ${unitCode} (${unitType})</div>
                <div><strong>Waktu Mulai:</strong> ${startTime}</div>
                <div><strong>Waktu Selesai:</strong> ${endTime}</div>
                <div><strong>Durasi:</strong> ${r.duration_hours} Jam</div>
                <div><strong>Metode Bayar:</strong> ${r.payment_method} (${r.payment_status})</div>
            </div>
            <div style="border-top: 2px dashed #000; padding-top: 0.85rem; text-align: right;">
                <span style="font-size: 0.9rem;">TOTAL BAYAR:</span>
                <div style="font-size: 1.4rem; font-weight: bold;">Rp ${total}</div>
            </div>
            <div style="text-align: center; margin-top: 1.25rem; font-size: 0.75rem; color: #64748b;">
                *** Terima Kasih Telah Bermain di Abdillans Gaming ***
            </div>
        `;
        document.getElementById('receiptContent').innerHTML = html;
    }

    function closeReceiptModal() {
        document.getElementById('receiptModal').classList.remove('active');
    }
</script>
@endsection
