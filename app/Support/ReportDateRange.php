<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportDateRange
{
    public static function fromRequest(Request $request, string $defaultRange = 'bulanan'): array
    {
        $requested = $request->input('range') ?? $request->input('filter') ?? $request->input('periode') ?? $defaultRange;
        $range = self::normalizeRange($requested, $defaultRange);
        $now = Carbon::now();
        $yearInput = $request->input('year');
        $dateInput = $request->input('date') ?? $request->input('tanggal');
        $monthInput = $request->input('month') ?? $request->input('bulan');

        switch ($range) {
            case 'harian':
                $targetDate = $dateInput ? Carbon::parse($dateInput) : $now;
                $start = $targetDate->copy()->startOfDay();
                $end = $targetDate->copy()->endOfDay();
                $label = 'Harian - ' . $start->translatedFormat('d F Y');
                break;
            case 'tahunan':
                $targetYear = is_numeric($yearInput) ? (int) $yearInput : $now->year;
                $start = Carbon::create($targetYear, 1, 1, 0, 0, 0)->startOfYear();
                $end = Carbon::create($targetYear, 12, 31, 23, 59, 59)->endOfYear();
                $label = 'Tahunan - ' . $targetYear;
                break;
            case 'bulanan':
            default:
                if ($monthInput) {
                    try {
                        $targetMonth = Carbon::createFromFormat('Y-m', $monthInput)->startOfMonth();
                    } catch (\Throwable $e) {
                        $targetMonth = $now->copy()->startOfMonth();
                    }
                } else {
                    $targetMonth = $now->copy()->startOfMonth();
                }

                $start = $targetMonth->copy()->startOfMonth();
                $end = $targetMonth->copy()->endOfMonth();
                $label = 'Bulanan - ' . $start->translatedFormat('F Y');
                break;
        }

        return [
            'range' => $range,
            'label' => $label,
            'start' => $start,
            'end' => $end,
            'year' => $range === 'tahunan' ? $start->year : null,
            'selected_date' => $range === 'harian' ? $start->toDateString() : null,
        ];
    }

    public static function options(): array
    {
        $now = Carbon::now();

        return [
            [
                'value' => 'bulanan',
                'label' => 'Bulanan',
                'description' => 'Pilih bulan tertentu atau gunakan bulan ini (' . $now->translatedFormat('F Y') . ')',
            ],
            [
                'value' => 'harian',
                'label' => 'Harian',
                'description' => 'Pilih tanggal tertentu untuk melihat data per hari',
            ],
            [
                'value' => 'tahunan',
                'label' => 'Tahunan',
                'description' => 'Pilih tahun tertentu untuk melihat rangkuman',
            ],
        ];
    }

    protected static function normalizeRange(string $value, string $default): string
    {
        $map = [
            'hari' => 'harian',
            'harian' => 'harian',
            'daily' => 'harian',
            'minggu' => 'mingguan',
            'mingguan' => 'mingguan',
            'week' => 'mingguan',
            'bulan' => 'bulanan',
            'bulanan' => 'bulanan',
            'month' => 'bulanan',
            'tahun' => 'tahunan',
            'tahunan' => 'tahunan',
            'year' => 'tahunan',
        ];

        return $map[strtolower($value)] ?? $default;
    }
}

