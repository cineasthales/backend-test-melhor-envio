<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generateRequestsReport(Request $request)
    {
        $contents = DB::table('requests')
            ->select('services.uuid', 'requests.method', 'requests.uri',
                'requests.url', 'requests.size', 'request_headers.accept',
                'request_headers.host', 'request_headers.user_agent')
            ->join('request_headers', 'requests.request_header_id', '=', 'request_headers.id')
            ->join('entries', 'entries.request_id', '=', 'requests.id')
            ->join('services', 'entries.service_id', '=', 'services.id')
            ->orderBy('services.uuid')
            ->get();

        $report = [];
        $report[0] = 'service_uuid,method,uri,url,size,accept,host,user_agent' . PHP_EOL;

        foreach ($contents as $content) {
            $report[] = implode(',', (array) $content) . PHP_EOL;
        }

        Storage::disk('reports')->put('report_requests_by_service.csv', $report);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generateLatenciesReport(Request $request)
    {
        $contents = DB::table('latencies')
            ->select('services.uuid', DB::raw('avg(proxy) as proxy_average,
                avg(kong) as kong_average, avg(request) as request_average'))
            ->join('entries', 'entries.latency_id', '=', 'latencies.id')
            ->join('services', 'entries.service_id', '=', 'services.id')
            ->groupBy('services.uuid')
            ->orderBy('services.uuid')
            ->get();

        $report = [];
        $report[0] = 'service_uuid,proxy_average,kong_average,request_average' . PHP_EOL;

        foreach ($contents as $content) {
            $report[] = implode(',', (array) $content) . PHP_EOL;
        }

        Storage::disk('reports')->put('report_average_latencies_by_service.csv', $report);
    }
}
