<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\MediaLibrary\Models\Media;

class MediaController extends Controller
{
    public function download(Request $request)
    {
        dd($request->all());
    }
}
