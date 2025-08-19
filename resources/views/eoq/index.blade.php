@extends('layouts.master')

@section('title')
    Perhitungan EOQ
@endsection

@section('breadcrumb')
    @parent
    <li class="active">EOQ</li>
@endsection

@section('content')
    <div class="container">
        <h3 class="mb-4">Perhitungan EOQ</h3>
        <div class="mb-3">
            <a href="{{ route('eoq.generate.all') }}" class="btn btn-primary">Hitung Ulang Semua</a>

            <form action="{{ route('eoq.reset') }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger"
                        onclick="return confirm('Yakin ingin reset semua perhitungan EOQ?')">
                    Reset Perhitungan
                </button>
            </form>
        </div>


        {{-- Notifikasi --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Tabel bahan baku --}}
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Pilih Bahan Baku</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Bahan Baku</th>
                            <th>Harga</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bahanBaku as $bahan)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $bahan->nama }}</td>
                                <td>Rp {{ number_format($bahan->harga) }}</td>
                                <td>
                                    <a href="{{ route('eoq.generate', $bahan->id) }}" class="btn btn-sm btn-primary">
                                        Hitung EOQ
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tabel hasil EOQ --}}
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">Hasil Perhitungan EOQ</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Bahan Baku</th>
                            <th>Permintaan Tahunan (D)</th>
                            <th>Biaya Pemesanan (S)</th>
                            <th>Biaya Penyimpanan (H)</th>
                            <th>EOQ</th>
                            <th>ROP</th>
                            <th>Tanggal Hitung</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $row)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $row->bahanBaku->nama ?? '-' }}</td>
                                <td>{{ number_format($row->permintaan_tahunan) }}</td>
                                <td>Rp {{ number_format($row->biaya_pemesanan) }}</td>
                                <td>Rp {{ number_format($row->biaya_penyimpanan) }}</td>
                                <td><span class="badge bg-blue">{{ $row->eoq_result }}</span></td>
                                <td><span class="badge bg-yellow">{{ $row->rop }}</span></td>
                                <td>{{ $row->tanggal_hitung }}</td>
                                <td>
                                    <a href="{{ route('eoq.generate', $row->bahan_baku_id) }}"
                                        class="btn btn-sm btn-success">
                                        Hitung Ulang
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">Belum ada data perhitungan EOQ</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-3">
                    {{ $data->links() }}
                </div>
            </div>
        </div>

        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title">Grafik EOQ & ROP per Bahan Baku</h3>
            </div>
            <div class="box-body p-5">
                <h5 class="card-title">Grafik EOQ & ROP per Bahan Baku</h5>
                <canvas id="eoqChart" height="120"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('eoqChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($labels),
                datasets: [{
                        label: 'EOQ',
                        data: @json($eoqs),
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    },
                    {
                        label: 'ROP',
                        data: @json($rops),
                        backgroundColor: 'rgba(255, 99, 132, 0.7)',
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    title: {
                        display: true,
                        text: 'Perbandingan EOQ & ROP'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@endsection
