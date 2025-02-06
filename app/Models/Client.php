<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Redis;

class Client extends Model {
    use HasFactory, SoftDeletes;

    protected $table = 'my_client';

    protected $fillable = [
        'name', 'slug', 'is_project', 'self_capture',
        'client_prefix', 'client_logo', 'address',
        'phone_number', 'city'
    ];

    protected static function boot() {
        parent::boot();

        // static::created(function ($client) {
        //     Redis::set($client->slug, json_encode($client));
        // });

        static::updated(function ($client) {
            Redis::del($client->slug);
            Redis::set($client->slug, json_encode($client));
        });

        static::deleted(function ($client) {
            Redis::del($client->slug);
        });
    }
}
