<?php

declare(strict_types=1);

namespace App\Modules\Trade\Livewire;

use App\Modules\Trade\Domain\Trade;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Carbon\Carbon;

class Chart extends Component
{
    public string $interval = '1h'; 
    public string $selectedCurrency = 'BTC'; // Dynamic fallback property

    protected array $intervals = [
        '1m'  => ['format' => '%Y-%m-%d %H:%i:00', 'sub' => 'subHours', 'amount' => 2,   'step' => 'addMinute'],
        '5m'  => ['format' => '%Y-%m-%d %H:',       'sub' => 'subHours', 'amount' => 12,  'step' => 'addMinutes'],
        '1h'  => ['format' => '%Y-%m-%d %H:00:00', 'sub' => 'subDays',  'amount' => 7,   'step' => 'addHour'],
        '1d'  => ['format' => '%Y-%m-%d 00:00:00', 'sub' => 'subDays',  'amount' => 30,  'step' => 'addDay'],
    ];

    public function mount(?string $symbol = null): void
    {
        if ($symbol) {
            $this->selectedCurrency = $symbol;
        }
    }

    public function changeInterval(string $newInterval): void
    {
        if (array_key_exists($newInterval, $this->intervals)) {
            $this->interval = $newInterval;
            $this->dispatchChartUpdate();
        }
    }

    private function getChartData(): array
    {
        $config = $this->intervals[$this->interval] ?? $this->intervals['1h'];
        $subMethod = $config['sub'];
        $threshold = now()->$subMethod($config['amount']);
        $dateBucketExpr = "DATE_FORMAT(created_at, '{$config['format']}')";

        if ($this->interval === '5m') {
            $dateBucketExpr = "CONCAT(DATE_FORMAT(created_at, '%Y-%m-%d %H:'), LPAD(FLOOR(MINUTE(created_at) / 5) * 5, 2, '0'), ':00')";
        }

        $rawTrades = Trade::select([
                DB::raw("$dateBucketExpr as time_bucket"),
                DB::raw("CAST(MIN(price) AS DECIMAL(16,8)) as low"),
                DB::raw("CAST(MAX(price) AS DECIMAL(16,8)) as high"),
                DB::raw("CAST(SUBSTRING_INDEX(MIN(CONCAT(created_at, '_', price)), '_', -1) AS DECIMAL(16,8)) as open"),
                DB::raw("CAST(SUBSTRING_INDEX(MAX(CONCAT(created_at, '_', price)), '_', -1) AS DECIMAL(16,8)) as close"),
            ])
            ->where('currency', $this->selectedCurrency)
            ->where('created_at', '>=', $threshold)
            ->groupBy(DB::raw($dateBucketExpr))
            ->orderBy('time_bucket', 'asc')
            ->get();

        if ($rawTrades->isEmpty()) {
            return [];
        }

        $keyedData = [];
        foreach ($rawTrades as $trade) {
            $keyedData[trim($trade->time_bucket)] = [
                'o' => (float) $trade->open,
                'h' => (float) $trade->high,
                'l' => (float) $trade->low,
                'c' => (float) $trade->close,
            ];
        }

        $startTime = Carbon::parse($rawTrades->first()->time_bucket);
        $endTime = now();
        $filledChartData = [];
        $lastKnownPrice = null;

        while ($startTime->lessThanOrEqualTo($endTime)) {
            if ($this->interval === '5m') {
                $bucketStr = $startTime->format('Y-m-d H:') . str_pad((string)(floor($startTime->minute / 5) * 5), 2, '0', STR_PAD_LEFT) . ':00';
            } else {
                $formatMap = ['1m' => 'Y-m-d H:i:00', '1h' => 'Y-m-d H:00:00', '1d' => 'Y-m-d 00:00:00'];
                $bucketStr = $startTime->format($formatMap[$this->interval] ?? 'Y-m-d H:00:00');
            }

            $bucketStr = trim($bucketStr);

            if (isset($keyedData[$bucketStr])) {
                $candle = $keyedData[$bucketStr];
                $lastKnownPrice = $candle['c']; 
            } else {
                $candle = [
                    'o' => $lastKnownPrice ?? 0.0,
                    'h' => $lastKnownPrice ?? 0.0,
                    'l' => $lastKnownPrice ?? 0.0,
                    'c' => $lastKnownPrice ?? 0.0,
                ];
            }

            $filledChartData[] = [
                'x' => strtotime($bucketStr) * 1000,
                'o' => $candle['o'],
                'h' => $candle['h'],
                'l' => $candle['l'],
                'c' => $candle['c'],
            ];

            if ($this->interval === '5m') {
                $startTime->addMinutes(5);
            } else {
                $stepMethod = $config['step'];
                $startTime->$stepMethod();
            }
        }

        return $filledChartData;
    }

    private function dispatchChartUpdate(): void
    {
        $chartUnitMap = ['1m' => 'minute', '5m' => 'minute', '1h' => 'hour', '1d' => 'day'];

        $this->dispatch('chart-updated', [
            'chartData' => $this->getChartData(),
            'chartUnit' => $chartUnitMap[$this->interval] ?? 'hour'
        ]);
    }

    public function render(): View
    {
        $chartUnitMap = ['1m' => 'minute', '5m' => 'minute', '1h' => 'hour', '1d' => 'day'];

        return view('livewire.trading.crypto-chart', [
            'initialData' => $this->getChartData(),
            'initialUnit' => $chartUnitMap[$this->interval] ?? 'hour'
        ]);
    }
}