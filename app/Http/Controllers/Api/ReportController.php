<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\BadRequest;
use App\Exceptions\InternalServerError;
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

            if (!$contents) {
                throw new BadRequest;
            }

            $this->generateReport($contents, 'requests_by_consumer');

        } catch (\Exception $e) {
            throw new InternalServerError;
        }

        return response('OK', 200);
    }

    public function generateRequestsBySpecificConsumer(Request $request)
    {
        try {
            $contents = AuthenticatedEntity::requestsBySpecificConsumer($request->get('uuid'));

            if (!$contents) {
                throw new BadRequest;
            }

            $this->generateReport($contents, 'requests_by_specific_consumer');

        } catch (\Exception $e) {
            throw new InternalServerError;
        }

        return response('OK', 200);
    }

    public function generateRequestsByService()
    {
        try {
            $contents = Service::requestsByService();

            if (!$contents) {
                throw new BadRequest;
            }

            $this->generateReport($contents, 'requests_by_service');

        } catch (\Exception $e) {
            throw new InternalServerError;
        }

        return response('OK', 200);
    }

    public function generateRequestsBySpecificService(Request $request)
    {
        try {
            $contents = Service::requestsBySpecificService($request->get('uuid'));

            if (!$contents) {
                throw new BadRequest;
            }

            $this->generateReport($contents, 'requests_by_specific_service');

        } catch (\Exception $e) {
            throw new InternalServerError;
        }

        return response('OK', 200);
    }

    public function generateAverageLatenciesByService()
    {
        try {
            $contents = Service::averageLatenciesByService();

            if (!$contents) {
                throw new BadRequest;
            }

            $this->generateReport($contents, 'average_latencies_by_service');

        } catch (\Exception $e) {
            throw new InternalServerError;
        }

        return response('OK', 200);
    }

    public function generateAverageLatenciesBySpecificService(Request $request)
    {
        try {
            $contents = Service::averageLatenciesBySpecificService($request->get('uuid'));

            if (!$contents) {
                throw new BadRequest;
            }

            $this->generateReport($contents, 'average_latencies_by_specific_service');

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
