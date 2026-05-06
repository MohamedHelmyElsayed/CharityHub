<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function index()
    {
        $certificates = Certificate::orderByDesc('created_at')
            ->paginate(20);
            
        return view('admin.certificates', compact('certificates'));
    }
}
