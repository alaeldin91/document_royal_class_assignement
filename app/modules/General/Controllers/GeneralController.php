<?php

namespace App\Modules\General\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\General\Services\EncryptionServiceImpl;

class GeneralController extends Controller
{
    private EncryptionServiceImpl $encryptService;

    public function __construct(EncryptionServiceImpl $encryptService)
    {
        $this->middleware('auth:general');
        $this->encryptService = $encryptService;
    }

    public function index()
    {
        $encrypted = $this->encryptService->encrypt('Hello General Module', 'general');
        $decrypted =  $this->encryptService->decrypt($encrypted, 'general');
    
        return response()->json([
            'encrypted' => $encrypted,
            'decrypted' => $decrypted,
        ]);
    }
}
