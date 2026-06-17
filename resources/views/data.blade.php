@extends('layouts.app')

@section('content')
<div class="fade-in">
    <div class="card">
        <h2 class="card-title">
            <i class="fa-solid fa-database" style="color: var(--color-accent);"></i> Kelola Database Iklim & Curah Hujan
        </h2>
        <p style="color: var(--text-secondary); line-height: 1.6; margin-bottom: 2rem;">
            Silakan unggah berkas CSV asli Anda dari portal BMKG atau gunakan generator simulasi 2 tahun ke belakang 
            untuk mengisi database dan menguji performa algoritma.
        </p>

        <div class="grid-2">
            <!-- Upload CSV Card -->
            <div style="background: rgba(255, 255, 255, 0.01); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.5rem;">
                <h3 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 1rem; color: var(--color-svm);">
                    <i class="fa-solid fa-file-csv"></i> Unggah Berkas CSV BMKG
                </h3>
                
                <form action="{{ route('data.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label class="form-label" for="csv_file">Berkas CSV</label>
                        <input type="file" id="csv_file" name="csv_file" class="form-control" accept=".csv,.txt" required>
                        <span style="font-size: 0.75rem; color: var(--text-muted); display: block; margin-top: 0.25rem;">
                            Harus mengandung kolom tanggal/date, tavg, rh_avg, dan rr. Pemisah kolom bisa koma (,) atau titik koma (;).
                        </span>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="threshold_upload">Ambang Batas Curah Hujan Ekstrem (mm/hari)</label>
                        <input type="number" id="threshold_upload" name="threshold" class="form-control" value="50" min="1" step="1" required>
                    </div>

                    <button type="submit" class="btn btn-svm" style="width: 100%; margin-top: 1rem;">
                        <i class="fa-solid fa-cloud-arrow-up"></i> Unggah & Proses
                    </button>
                </form>
            </div>

            <!-- Generate/Simulate Card -->
            <div style="background: rgba(255, 255, 255, 0.01); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.5rem; display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <h3 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 1rem; color: var(--color-rf);">
                        <i class="fa-solid fa-wand-magic-sparkles"></i> Generator Simulasi Data (2 Tahun)
                    </h3>
                    <p style="font-size: 0.85rem; color: var(--text-secondary); line-height: 1.5; margin-bottom: 1.25rem;">
                        Belum punya data BMKG? Tekan tombol di bawah untuk membuat data cuaca simulasi harian selama 2 tahun terakhir (~730 data) yang merepresentasikan pola musim hujan dan kemarau di Indonesia.
                    </p>
                </div>
                
                <form action="{{ route('data.generate') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-label" for="threshold_generate">Ambang Batas Curah Hujan Ekstrem (mm/hari)</label>
                        <input type="number" id="threshold_generate" name="threshold" class="form-control" value="50" min="1" step="1" required>
                    </div>

                    <button type="submit" class="btn btn-rf" style="width: 100%; margin-top: 1rem;">
                        <i class="fa-solid fa-gears"></i> Generate Data Simulasi
                    </button>
                </form>
            </div>
        </div>

        @if($totalRecords > 0)
            <div style="margin-top: 2rem; display: flex; justify-content: flex-end;">
                <form action="{{ route('data.reset') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengosongkan seluruh isi database? Data yang dihapus tidak bisa dikembalikan.');">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <i class="fa-solid fa-trash-can"></i> Kosongkan Database
                    </button>
                </form>
            </div>
        @endif
    </div>

    <!-- Data Table View -->
    <div class="card">
        <h3 class="card-title">
            <i class="fa-solid fa-table" style="color: var(--color-rf);"></i> Tabel Data Cuaca Historis
            <span style="font-size: 0.95rem; font-weight: 400; color: var(--text-muted); margin-left: auto;">
                Menampilkan {{ $dataList->count() }} dari {{ $totalRecords }} Data
            </span>
        </h3>

        @if($totalRecords > 0)
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Suhu Rata-rata (TAVG)</th>
                            <th>Kelembapan Rata-rata (RH_AVG)</th>
                            <th>Curah Hujan (RR)</th>
                            <th>Kategori Curah Hujan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dataList as $index => $row)
                            <tr>
                                <td>{{ $dataList->firstItem() + $index }}</td>
                                <td>{{ $row->tanggal->format('d M Y') }}</td>
                                <td>{{ $row->tavg !== null ? number_format($row->tavg, 1) . ' °C' : '-' }}</td>
                                <td>{{ $row->rh_avg !== null ? number_format($row->rh_avg, 1) . ' %' : '-' }}</td>
                                <td>{{ number_format($row->rr, 1) }} mm</td>
                                <td>
                                    @if($row->class_actual == 1)
                                        <span class="badge badge-extreme"><i class="fa-solid fa-triangle-exclamation"></i> Ekstrem</span>
                                    @else
                                        <span class="badge badge-normal"><i class="fa-solid fa-circle-check"></i> Normal</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Custom Pagination -->
            <div class="pagination-container">
                <div style="font-size: 0.85rem; color: var(--text-secondary);">
                    Menampilkan data {{ $dataList->firstItem() }} sampai {{ $dataList->lastItem() }} dari {{ $dataList->total() }} baris.
                </div>
                <div class="pagination-links">
                    {{-- Previous Page Link --}}
                    @if ($dataList->onFirstPage())
                        <span class="disabled">&laquo;</span>
                    @else
                        <a href="{{ $dataList->previousPageUrl() }}" rel="prev">&laquo;</a>
                    @endif

                    {{-- Simple pagination logic --}}
                    @php
                        $start = max(1, $dataList->currentPage() - 2);
                        $end = min($dataList->lastPage(), $dataList->currentPage() + 2);
                    @endphp

                    @for ($i = $start; $i <= $end; $i++)
                        @if ($i == $dataList->currentPage())
                            <span class="active">{{ $i }}</span>
                        @else
                            <a href="{{ $dataList->url($i) }}">{{ $i }}</a>
                        @endif
                    @endfor

                    {{-- Next Page Link --}}
                    @if ($dataList->hasMorePages())
                        <a href="{{ $dataList->nextPageUrl() }}" rel="next">&raquo;</a>
                    @else
                        <span class="disabled">&raquo;</span>
                    @endif
                </div>
            </div>
        @else
            <div style="text-align: center; padding: 4rem 2rem; color: var(--text-muted); display: flex; flex-direction: column; align-items: center; gap: 1rem;">
                <i class="fa-solid fa-cloud-arrow-down" style="font-size: 3.5rem; color: var(--border-color);"></i>
                <p>Belum ada data dalam database. Silakan unggah berkas CSV BMKG atau klik tombol <strong>Generate Data Simulasi</strong> di atas.</p>
            </div>
        @endif
    </div>
</div>
@endsection
