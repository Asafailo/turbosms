<?php

namespace App\Http\Controllers;

use App\Http\Requests\CodeRequest;
use App\Http\Requests\ValidateRegistrationInfo;
use App\Http\Services\TurboSmsService;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;


class RegistrationController extends Controller
{
    /**
     * @var int
     * Retry time in minutes
     */
    private int $retryTime = 5;

    public function registrationRequest(ValidateRegistrationInfo $request)
    {

        $request->validated();

        $phone = $request->input('phone');
        $name = $request->input('name');


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

        $user = User::getUserByPhone($phone);
        $this->saveSession($phone, $name);
        if ($user) {
            $notification = 'User with this phone already registered';
            return redirect()->route('auth.login')
                ->withErrors(['error' => $notification]);
        } else {
            $type='registration';
            return view('auth.confirmation')->with(compact('type'));
        }
    }

    private function saveSession(string $phone, string $name)
    {
        session()->put('name', $name);
        session()->put('phone', $phone);
    }

    public function registerUser(Request $request)
    {

        $code = $request->input('all-number');

        $phone = session()->get('phone');
        $savedCode = Redis::get('sms:' . $phone);
        $attempts = Redis::get('attempts:' . $phone);
        if ($savedCode && $savedCode == $code) {
            $user = User::getUserByPhone($phone);
            if ($user) {
                return redirect()->route('auth.login')->withErrors(['error' => 'User with this phone already registered']);
            } else {

                $formattedPhone = preg_replace('/[-()+]/', '', $phone);
                $user = User::create([
                        'phone' => trim($formattedPhone),
                        'name' => trim(session()->get('name'))]
                );

                Auth::login($user);
                return redirect()->route('dashboard.welcome');
            }
        } else {
            if ($attempts < 2) {


                Redis::incr("attempts:{$phone}");
//                Redis::set("attempts:{$phone}", $attempts + 1);
//                $timeSet = Redis::get('time:' . $phone);
//                $timeSet = Carbon::parse($timeSet);
//                $difference = Carbon::now()->diffInMinutes($timeSet);
//                Redis::expire("attempts:{$phone}", $difference * 60);
                $type='registration';
                $error='Wrong code. You have one more attempt';
                return view('auth.confirmation')->with(compact('type','error'));
            } else {
                $timeSet = Redis::get('time:' . $phone);
                $timeSet = Carbon::parse($timeSet);
                $difference = $this->retryTime - Carbon::now()->diffInMinutes($timeSet);

                return view('auth.register')->with(['error' => 'Max number of attempts reached. Please wait ' . $difference . ' minutes to repeat']);
            }

        }
    }


    private function saveCodeInRedis(string $phone, string $code)
    {

        Redis::set("sms:{$phone}", $code);
        Redis::expire("sms:{$phone}", $this->retryTime*60);
        Redis::set("attempt:{$phone}", 1);
        Redis::expire("attempt:{$phone}", $this->retryTime*60);
    }


}
