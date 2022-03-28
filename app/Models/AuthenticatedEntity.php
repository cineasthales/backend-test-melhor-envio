<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AuthenticatedEntity extends Model
{
    public function requestsByConsumer()
    {
        return DB::table('authenticated_entities')
            ->select('authenticated_entities.uuid as authenticated_entity_uuid', 'requests.method', 'requests.uri',
                'requests.url', 'requests.size', 'request_headers.accept',
                'request_headers.host', 'request_headers.user_agent')
            ->join('entries', 'entries.authenticated_entity_id', '=', 'authenticated_entities.id')
            ->join('requests', 'entries.request_id', '=', 'requests.id')
            ->join('request_headers', 'requests.request_header_id', '=', 'request_headers.id')
            ->orderBy('authenticated_entities.uuid')
            ->get();
    }

    public function requestsBySpecificConsumer($uuid)
    {
        return DB::table('authenticated_entities')
            ->select('authenticated_entities.uuid as authenticated_entity_uuid', 'requests.method', 'requests.uri',
                'requests.url', 'requests.size', 'request_headers.accept',
                'request_headers.host', 'request_headers.user_agent')
            ->join('entries', 'entries.authenticated_entity_id', '=', 'authenticated_entities.id')
            ->join('requests', 'entries.request_id', '=', 'requests.id')
            ->join('request_headers', 'requests.request_header_id', '=', 'request_headers.id')
            ->where('authenticated_entities.uuid', $uuid)
            ->get();
    }
}
