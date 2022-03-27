<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuthenticatedEntity;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function generateRequestsByConsumer()
    {
        try {
            $contents = AuthenticatedEntity::requestsByConsumer();
            $this->generateReport($contents, 'requests_by_consumer');

        } catch (\Exception $e) {
            return response()->json(['500' => 'Internal Server Error']);
        }

        return response()->json(['200' => 'OK']);
    }

    public function generateRequestsByService()
    {
        try {
            $contents = Service::requestsByService();
            $this->generateReport($contents, 'requests_by_service');

        } catch (Expection $e) {
            return response()->json(['500' => 'Internal Server Error']);
        }

        return response()->json(['200' => 'OK']);
    }

    public function generateAverageLatenciesByService()
    {
        try {
            $contents = Service::averageLatenciesByService();
            $this->generateReport($contents, 'average_latencies_by_service');

        } catch (\Exception $e) {
            return response()->json(['500' => 'Internal Server Error']);
        }

        return response()->json(['200' => 'OK']);
    }

    private function generateReport($contents, $filename)
    {
        $report = [];
        $report[0] = implode(',', array_keys((array) $contents[0])) . PHP_EOL;

        foreach ($contents as $content) {
            $report[] = implode(',', (array) $content) . PHP_EOL;
        }

        Storage::disk('reports')->put($filename . '.csv', $report);
    }
}
