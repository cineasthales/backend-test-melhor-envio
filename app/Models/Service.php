<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Service extends Model
{
    public function requestsByService()
    {
        return DB::table('services')
            ->select('services.uuid as service_uuid', 'requests.method', 'requests.uri',
                'requests.url', 'requests.size', 'request_headers.accept',
                'request_headers.host', 'request_headers.user_agent')
            ->join('entries', 'entries.service_id', '=', 'services.id')
            ->join('requests', 'entries.request_id', '=', 'requests.id')
            ->join('request_headers', 'requests.request_header_id', '=', 'request_headers.id')
            ->orderBy('services.uuid')
            ->get();
    }

    public function averageLatenciesByService()
    {
        return DB::table('services')
            ->select('services.uuid as service_uuid', DB::raw('avg(proxy) as proxy_average,
                avg(kong) as kong_average, avg(request) as request_average'))
            ->join('entries', 'entries.service_id', '=', 'services.id')
            ->join('latencies', 'entries.latency_id', '=', 'latencies.id')
            ->groupBy('services.uuid')
            ->orderBy('services.uuid')
            ->get();
    }
}
