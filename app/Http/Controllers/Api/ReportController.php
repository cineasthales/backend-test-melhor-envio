<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InternalServerError;
use App\Exceptions\NotFound;
use App\Http\Controllers\Controller;
use App\Models\AuthenticatedEntity;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function generateRequestsByConsumer()
    {
        $contents = AuthenticatedEntity::requestsByConsumer();
        if (!count($contents)) {
            throw new NotFound;
        }

        try {
            $this->generateReport($contents, 'requests_by_consumer');
        } catch (\Exception $e) {
            throw new InternalServerError;
        }

        return response('OK', 200);
    }

    public function generateRequestsBySpecificConsumer(Request $request)
    {
        $contents = AuthenticatedEntity::requestsBySpecificConsumer($request->uuid);
        if (!count($contents)) {
            throw new NotFound;
        }

        try {
            $this->generateReport($contents, 'requests_by_specific_consumer_' . $request->uuid);
        } catch (\Exception $e) {
            throw new InternalServerError;
        }

        return response('OK', 200);
    }

    public function generateRequestsByService()
    {
        $contents = Service::requestsByService();
        if (!count($contents)) {
            throw new NotFound;
        }

        try {
            $this->generateReport($contents, 'requests_by_service');
        } catch (\Exception $e) {
            throw new InternalServerError;
        }

        return response('OK', 200);
    }

    public function generateRequestsBySpecificService(Request $request)
    {
        $contents = Service::requestsBySpecificService($request->uuid);
        if (!count($contents)) {
            throw new NotFound;
        }

        try {
            $this->generateReport($contents, 'requests_by_specific_service_' . $request->uuid);
        } catch (\Exception $e) {
            throw new InternalServerError;
        }

        return response('OK', 200);
    }

    public function generateAverageLatenciesByService()
    {
        $contents = Service::averageLatenciesByService();
        if (!count($contents)) {
            throw new NotFound;
        }

        try {
            $this->generateReport($contents, 'average_latencies_by_service');
        } catch (\Exception $e) {
            throw new InternalServerError;
        }

        return response('OK', 200);
    }

    public function generateAverageLatenciesBySpecificService(Request $request)
    {
        $contents = Service::averageLatenciesBySpecificService($request->uuid);
        if (!count($contents)) {
            throw new NotFound;
        }

        try {
            $this->generateReport($contents, 'average_latencies_by_specific_service_' . $request->uuid);
        } catch (\Exception $e) {
            throw new InternalServerError;
        }

        return response('OK', 200);
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
