<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pengiriman Driver</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #1f2937;
            margin: 30px;
        }

        h1 {
            text-align: center;
            font-size: 22px;
            color: #0f766e;
            margin: 0 0 10px 0;
        }

        .sub-title {
            text-align: center;
            font-size: 12px;
            color: #475569;
            margin-bottom: 25px;
        }

        .summary-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .summary-grid td {
            width: 25%;
            padding: 12px;
            border: 1px solid #e2e8f0;
            text-align: center;
        }

        .summary-value {
            display: block;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        th, td {
            border: 1px solid #e2e8f0;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: 600;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 10px;
            font-weight: 600;
            text-transform: capitalize;
        }
        .badge-delivery { background-color: #dcfce7; color: #166534; }
        .badge-return { background-color: #fee2e2; color: #991b1b; }
        .badge-progress { background-color: #fef3c7; color: #b45309; }

        .footer {
            margin-top: 40px;
            font-size: 10px;
            color: #64748b;
            text-align: center;
        }
    </style>
</head>
<body>
    @include('exports.partials.header')

    <h1>Laporan Aktivitas Driver</h1>
    <div class="sub-title">
        Periode: {{ $periodeLabel ?? '-' }}<br>
        Rentang tanggal:
        {{ isset($periodeStart) ? \Carbon\Carbon::parse($periodeStart)->translatedFormat('d F Y') : '-' }}
        &mdash;
        {{ isset($periodeEnd) ? \Carbon\Carbon::parse($periodeEnd)->translatedFormat('d F Y') : '-' }}
    </div>

    <table class="summary-grid">
        <tr>
            <td>
                <span class="summary-value">{{ $totalTasks }}</span>
                Total Penugasan
            </td>
            <td>
                <span class="summary-value">{{ $totalDeliveryTasks }}</span>
                Pengiriman Barang
            </td>
            <td>
                <span class="summary-value">{{ $totalReturnTasks }}</span>
                Penanganan Retur
            </td>
            <td>
                <span class="summary-value">
                    {{ $totalTasks > 0 ? round(($totalCompletedTasks / max($totalTasks, 1)) * 100, 1) : 0 }}%
                </span>
                Tingkat Penyelesaian
            </td>
        </tr>
    </table>

    <h3>Ringkasan per Driver</h3>
    <table>
        <thead>
            <tr>
                <th>Driver</th>
                <th class="text-center">Total Tugas</th>
                <th class="text-center">Pengiriman (Selesai)</th>
                <th class="text-center">Retur (Selesai)</th>
                <th class="text-center">Tingkat Selesai</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($driverSummaries as $summary)
                <tr>
                    <td>{{ $summary['driver']->name ?? 'Driver Tidak Diketahui' }}</td>
                    <td class="text-center">{{ $summary['total_tasks'] }}</td>
                    <td class="text-center">{{ $summary['delivery_total'] }} ({{ $summary['delivery_completed'] }})</td>
                    <td class="text-center">{{ $summary['return_total'] }} ({{ $summary['return_completed'] }})</td>
                    <td class="text-center">
                        {{ $summary['total_tasks'] > 0
                            ? round((($summary['delivery_completed'] + $summary['return_completed']) / $summary['total_tasks']) * 100, 1)
                            : 0 }}%
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada aktivitas driver pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h3>Detail Penugasan Driver</h3>
    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Driver</th>
                <th>Customer</th>
                <th>Tanggal Penugasan</th>
                <th>Status</th>
                <th>Jenis</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($tasks as $task)
                <tr>
                    <td>{{ $task->code }}</td>
                    <td>{{ $task->driver->name ?? '-' }}</td>
                    <td>{{ $task->customer->name ?? '-' }}</td>
                    @php
                        $taskDate = $task->receive_date ?? $task->created_at;
                    @endphp
                    <td>{{ $taskDate ? \Carbon\Carbon::parse($taskDate)->translatedFormat('d F Y H:i') : '-' }}</td>
                    @php
                        $statusLower = strtolower($task->status);
                        $statusBadge = 'badge-progress';
                        if (in_array($statusLower, ['sampai', 'completed'])) {
                            $statusBadge = 'badge-delivery';
                        } elseif (in_array($statusLower, ['retur_diajukan', 'retur_diambil', 'retur_selesai'])) {
                            $statusBadge = 'badge-return';
                        }
                    @endphp
                    <td>
                        <span class="badge {{ $statusBadge }}">
                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ in_array($statusLower, ['retur_diajukan','retur_diambil','retur_selesai']) ? 'badge-return' : 'badge-delivery' }}">
                            {{ in_array($statusLower, ['retur_diajukan','retur_diambil','retur_selesai']) ? 'Retur' : 'Pengiriman' }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Belum ada penugasan driver pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Laporan dibuat secara otomatis pada {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}
    </div>
</body>
</html>

