<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Services\TurboSmsService;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class LoginController extends Controller
{
    /**
     * @var int
     * Retry time in minutes
     */
    private int $retryTime = 5;

    public function loginRequest(LoginRequest $request)
    {
        $request->validated();

        $phone = $request->input('phone');

        if (Redis::get('sms:' . $phone)) {
            $timeSet = Redis::get('time:' . $phone);
            $timeSet = Carbon::parse($timeSet);
            $difference = $this->retryTime - Carbon::now()->diffInMinutes($timeSet);

            Log::info('timeSet' . $timeSet);
            Log::info('$difference' . $difference);

            $notification = 'Its allowed to sent not more than one sms within 5 minutes.
            Remained ' . $difference . ' minuted. Please wait and repeat request';
            Log::info('$notification' . $notification);
            return redirect()->route('auth.login')->withErrors(['error' => $notification]);
        }
        $turboSmsService = new TurboSmsService([$phone]);
//      For using actual turbosms functionality

//        $result = $turboSmsService->sendCode();
//        $code = $result['code'];


        // For testing
        $code = $turboSmsService->generateCode();
        Redis::set("sms:{$phone}", $code);
        Redis::expire("sms:{$phone}", $this->retryTime * 5);
        Redis::set("attempts:{$phone}", 1);
        Redis::expire("attempts:{$phone}", $this->retryTime * 5);
        Redis::set("time:{$phone}", Carbon::now());
        Redis::expire("time:{$phone}", $this->retryTime * 5);
        $formattedPhone = preg_replace('/[-()+]/', '', $phone);
        $user = User::getUserByPhone($formattedPhone);
        if (!$user) {
            $notification = 'there is no user registered with such phone. Please register';
            return view('auth.register')->with(['error' => $notification]);
        } else {
            $type = 'login';
            return view('auth.confirmation')->with(compact('type'));
        }
    }

    public function loginUser(Request $request)
    {
        $code = $request->input('all-number');

        $phone = session()->get('phone');
        $savedCode = Redis::get('sms:' . $phone);
        $attempts = Redis::get('attempts:' . $phone);
        if ($savedCode && $savedCode == $code) {
            $formattedPhone = preg_replace('/[-()+]/', '', $phone);
            $user = User::getUserByPhone($formattedPhone);
            if (!$user) {
                return redirect()->route('auth.login')->withErrors(['error' => 'User with this phone is not registered']);
            } else {
                Auth::login($user);

                return view('dashboard.welcome');
            }
        } else {
            if ($attempts < 2) {
                Redis::incr("attempts:{$phone}");
                $type = 'login';
                $error = 'Wrong code. You have one more attempt';
                return view('auth.confirmation')->with(compact('type', 'error'));
            } else {
                $timeSet = Redis::get('time:' . $phone);
                $timeSet = Carbon::parse($timeSet);
                $difference = $this->retryTime - Carbon::now()->diffInMinutes($timeSet);

                return redirect()->route('auth.login')->withErrors(['error' => 'Max number of attempts reached. Please wait ' . $difference . ' minutes to repeat']);
            }
        }
    }
}
