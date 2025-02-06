<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redis;

class ClientController extends Controller {

    public function index() {
        return response()->json(Client::all());
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|max:250',
            'slug' => 'required|string|max:100|unique:my_client,slug',
            'client_prefix' => 'required|string|max:4',
            'address'   => 'required|string',
            'city'      => 'required|string',
            'phone_number'  => 'required',
            'client_logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $data = $request->except('client_logo');

        if ($request->hasFile('client_logo')) {
            

             //ini buat yang local
            // $logoPath = $request->file('client_logo')->store('public/client_logos');
            // $data['client_logo'] =  Storage::url($logoPath); // **Dapatkan URL gambar**

            //ini buat yang AWS S3
            $path = $request->file('client_logo')->store('clients', 's3');
            $data['client_logo'] = Storage::disk('s3')->url($path);
        }

        $client = Client::create($data);
        // Redis::set($client->slug, json_encode($client));

        return response()->json($client, 201);
    }

    public function show($slug) {
        $cachedClient = Redis::get($slug);
        if ($cachedClient) {
            return response()->json(json_decode($cachedClient));
        }

        $client = Client::where('slug', $slug)->firstOrFail();
        Redis::set($slug, json_encode($client));

        return response()->json($client);
    }

    public function update(Request $request, $id) {
        $client = Client::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|string|max:250',
            'slug' => 'sometimes|string|max:100|unique:my_client,slug,' . $id,
            'client_prefix' => 'sometimes|string|max:4',
            'address'   => 'sometimes|string',
            'city'      => 'sometimes|string',
            'phone_number'  => 'sometimes',
            'client_logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $data = $request->except('client_logo');

        if ($request->hasFile('client_logo')) {
            if ($client->client_logo !== 'no-image.jpg') {
                Storage::disk('s3')->delete($client->client_logo);
            }
            $path = $request->file('client_logo')->store('clients', 's3');
            $data['client_logo'] = Storage::disk('s3')->url($path);
        }

        Redis::del($client->slug);
        $client->update($data);
        Redis::set($client->slug, json_encode($client));

        return response()->json($client);
    }

    public function destroy($id) {
        $client = Client::findOrFail($id);
        Redis::del($client->slug);
        $client->delete();
        return response()->json(['message' => 'Client deleted'], 200);
    }
}
