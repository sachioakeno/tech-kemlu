<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Analitik Pariwisata</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        :root {
            --bg-body: #f9fafb;
            --primary: #4f46e5;
            --text-main: #111827;
            --text-muted: #6b7280;
            --card-bg: #ffffff;
            --border-color: #f3f4f6;
            --success: #10b981;
            --danger: #ef4444;
            --neutral: #6b7280;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            letter-spacing: -0.01em;
        }

        .navbar-custom {
            background-color: rgba(255, 255, 255, 0.8) !important;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-color);
            z-index: 1050;
        }

        .navbar-brand {
            font-weight: 700;
            letter-spacing: -0.03em;
            font-size: 1.25rem;
        }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
        }

        #map {
            height: 500px;
            border-radius: 12px;
            z-index: 1;
        }

        .leaflet-tooltip {
            background: #ffffff !important;
            color: var(--text-main) !important;
            border-radius: 12px !important;
            border: none !important;
            padding: 12px !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
        }

        .table-responsive { border-radius: 12px; overflow: hidden; }
        #myTable thead th { background-color: var(--bg-body); font-size: 0.75rem; text-transform: uppercase; border: none; padding: 15px; }
        
        .flag-img {
            width: 24px;
            height: auto;
            border-radius: 2px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
            object-fit: cover;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-light navbar-custom sticky-top py-3 px-4">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <a class="navbar-brand d-flex align-items-center gap-2 m-0" href="/">
                <span class="text-dark">Analitik Pariwisata</span>
                <span class="text-primary">Global</span>
            </a>

            <div class="d-flex align-items-center gap-3">
                <span class="text-muted small fw-medium d-none d-sm-block">Periode Laporan:</span>
                <form action="{{ route('wisatawan.index') }}" method="GET" class="m-0">
                    <select name="tahun" class="form-select form-select-sm shadow-sm" style="width: 130px; border-radius: 8px; font-weight: 600; border-color: #d1d5db;" onchange="this.form.submit()">
                        @foreach($listTahun as $t)
                            <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>Tahun {{ $t }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4 pt-4">
        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card p-3 h-100 border-0 shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">Penyebaran Geografis</h5>
                        <span class="badge bg-light text-primary border px-3 py-2">Tahun {{ $tahun }}</span>
                    </div>
                    <div id="map"></div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card p-4 bg-dark text-white mb-4 border-0 shadow">
                    <small class="text-uppercase tracking-wider opacity-50 fw-bold">Total Kunjungan (Jan-Mei)</small>
                    <h2 class="display-6 fw-bold mt-2">
                        {{ number_format($data->sum(fn($item) => $item->januari + $item->februari + $item->maret + $item->april + $item->mei)) }}
                    </h2>
                </div>

                <div class="card p-4 shadow-sm mb-4 border-0">
                    <h6 class="fw-bold mb-3">5 Negara Terpopuler</h6>
                    <ul class="list-unstyled m-0">
                        @php
                            $isoMap = [
                                'IDN' => 'id', 'MYS' => 'my', 'SGP' => 'sg', 'THA' => 'th', 'PHL' => 'ph',
                                'VNM' => 'vn', 'BRN' => 'bn', 'KHM' => 'kh', 'LAO' => 'la', 'MMR' => 'mm',
                                'TLS' => 'tl', 'CHN' => 'cn', 'JPN' => 'jp', 'KOR' => 'kr', 'IND' => 'in',
                                'AUS' => 'au', 'NZL' => 'nz', 'USA' => 'us', 'CAN' => 'ca', 'GBR' => 'gb',
                                'FRA' => 'fr', 'DEU' => 'de', 'NLD' => 'nl', 'ITA' => 'it', 'ESP' => 'es',
                                'RUS' => 'ru', 'SAU' => 'sa', 'ARE' => 'ae', 'MEX' => 'mx', 'BRA' => 'br',
                                'ZAF' => 'za', 'PNG' => 'pg', 'HKG' => 'hk', 'MAC' => 'mo', 'TWN' => 'tw',
                                'BGD' => 'bd', 'PAK' => 'pk', 'LKA' => 'lk', 'UKR' => 'ua', 'SWE' => 'se',
                                'DNK' => 'dk', 'POL' => 'pl', 'CHE' => 'ch', 'INA' => 'id'
                            ];

                            $sortedData = $data->map(function ($item) {
                                $item->total_akumulasi = $item->januari + $item->februari + $item->maret + $item->april + $item->mei;
                                return $item;
                            })->sortByDesc('total_akumulasi')->take(5);
                        @endphp
                        @foreach($sortedData as $top)
                            <li class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom border-light">
                                <span class="text-muted fw-medium">{{ $top->nama_negara }}</span>
                                <span class="fw-bold text-primary">{{ number_format($top->total_akumulasi) }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="card p-4 shadow-sm mb-5 border-0 mt-4">
            <h5 class="fw-bold mb-4">Statistik Terperinci</h5>
            <div class="table-responsive">
                <table id="myTable" class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Negara</th>
                            <th>Januari</th><th>Februari</th><th>Maret</th><th>April</th><th>Mei</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $item)
                            @php
                                $flagCode = $isoMap[$item->kode_iso] ?? strtolower(substr($item->kode_iso, 0, 2));
                            @endphp
                            <tr>
                                <td>
                                    <img src="https://flagcdn.com/w40/{{ $flagCode }}.png" class="me-2 flag-img" onerror="this.src='https://flagcdn.com/w40/un.png'">
                                    <span class="fw-semibold text-dark">{{ $item->nama_negara }}</span>
                                </td>
                                <td>{{ number_format($item->januari) }}</td>
                                <td>{{ number_format($item->februari) }}</td>
                                <td>{{ number_format($item->maret) }}</td>
                                <td>{{ number_format($item->april) }}</td>
                                <td class="fw-bold text-primary">{{ number_format($item->mei) }}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-light btn-sm fw-bold px-3 border shadow-sm" data-bs-toggle="modal" data-bs-target="#editModal{{ $item->id }}">Ubah</button>
                                        
                                        <form id="delete-form-{{ $item->id }}" action="{{ route('wisatawan.destroy', $item->id) }}" method="POST" style="display:none;">
                                            @csrf @method('DELETE')
                                        </form>
                                        <button type="button" class="btn btn-outline-danger btn-sm fw-bold px-3" onclick="confirmDelete('{{ $item->id }}', '{{ $item->nama_negara }}')">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                                        <div class="modal-header border-0 pb-0 p-4">
                                            <h5 class="modal-title fw-bold">Ubah Statistik: {{ $item->nama_negara }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                        </div>
                                        <form action="{{ route('wisatawan.update', $item->id) }}" method="POST">
                                            @csrf @method('PUT')
                                            <div class="modal-body p-4">
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold text-muted">Nama Negara</label>
                                                    <input type="text" name="nama_negara" class="form-control" value="{{ $item->nama_negara }}" required>
                                                </div>
                                                <div class="row g-3">
                                                    @foreach(['januari', 'februari', 'maret', 'april', 'mei'] as $bulan)
                                                        <div class="col-6">
                                                            <label class="form-label small fw-bold text-muted text-capitalize">{{ $bulan }}</label>
                                                            <input type="number" name="{{ $bulan }}" class="form-control" value="{{ $item->$bulan }}">
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0 p-4 pt-0">
                                                <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm" style="border-radius: 8px;">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function () {
            $('#myTable').DataTable({ 
                pageLength: 10,
                language: { 
                    search: "", 
                    searchPlaceholder: "Cari Negara...",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    paginate: {
                        next: "Berikutnya",
                        previous: "Sebelumnya"
                    }
                }
            });

            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    timer: 3000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            @endif

            var map = L.map('map').setView([15, 110], 3);
            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png').addTo(map);

            @foreach($data as $item)
                @if($item->lat && $item->lng)
                    @php
                        $mei = (int)$item->mei;
                        $april = (int)$item->april;
                        $selisih = $mei - $april;

                        if ($selisih > 0) {
                            $trendColor = '#10b981'; $trendIcon = '↗️'; $trendStatus = 'Meningkat';
                        } elseif ($selisih < 0) {
                            $trendColor = '#ef4444'; $trendIcon = '↘️'; $trendStatus = 'Menurun';
                        } else {
                            $trendColor = '#6b7280'; $trendIcon = '➡️'; $trendStatus = 'Tetap';
                        }
                        
                        $flagCode = $isoMap[$item->kode_iso] ?? strtolower(substr($item->kode_iso, 0, 2));
                    @endphp

                    var marker = L.circleMarker([{{ $item->lat }}, {{ $item->lng }}], {
                        radius: {{ max(8, min(20, $item->mei / 15000)) }},
                        fillColor: '{{ $trendColor }}',
                        color: '#fff',
                        weight: 2,
                        opacity: 1,
                        fillOpacity: 0.8
                    }).addTo(map);

                    marker.bindTooltip(`
                        <div style="min-width: 160px;">
                            <div class="d-flex align-items-center mb-2">
                                <img src="https://flagcdn.com/w20/{{ $flagCode }}.png" class="me-2 shadow-sm" onerror="this.src='https://flagcdn.com/w20/un.png'">
                                <span class="fw-bold">{{ $item->nama_negara }}</span>
                            </div>
                            <div class="small text-muted">Bulan Mei: <strong>{{ number_format($mei) }}</strong></div>
                            <div class="small text-muted mb-1">Bulan April: <strong>{{ number_format($april) }}</strong></div>
                            <div class="pt-1 border-top mt-1 fw-bold" style="color: {{ $trendColor }}">
                                {{ $trendIcon }} {{ number_format(abs($selisih)) }} Wisatawan ({{ $trendStatus }})
                            </div>
                        </div>
                    `, { sticky: true, direction: 'top', offset: [0, -10] });
                @endif
            @endforeach
        });

        function confirmDelete(id, countryName) {
            Swal.fire({
                title: 'Hapus data?',
                text: "Anda akan menghapus data " + countryName + ". Tindakan ini permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            })
        }
    </script>
</body>
</html>