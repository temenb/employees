<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Hash;
use App\User;

class TelegramController extends Controller
{
    public function webhook(Request $request)
    {
        $data = addslashes(var_export($request->all(), true));
        
        Log::debug($data);

        `echo $data > /storage/now.txt`;
    }
    
}
