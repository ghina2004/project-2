<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class NotificationController extends Controller
{
    public function storeFCMToken(Request $request)
    {
        $fcmToken = $request['fcm_token'];

        if ($fcmToken) {

            $user = User::find($request->user_id);
                $user['fcm_token'] = $fcmToken;
                $user->save();
                return response()->json(['message' => 'FCM token stored successfully'], 200);
            }


        return response()->json(['error' => 'Invalid FCM token'], 400);
    }

    public function sendNotification(){
$apiUrl = 'https://fcm.googleapis.com/v1/projects/laravel-777-f89a8/messages:send';

$access_token = Cache::remember('access_token', now()->addHour(), function () use ($apiUrl) {
    $credentialsFilePath = storage_path('app/fcm_file.json');
    $client = new \Google_Client();
    $client->setAuthConfig($credentialsFilePath);
    $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
    $client->fetchAccessTokenWithAssertion();
    $token = $client->getAccessToken();

    return $token['access_token'];
});


$headers = [
    "Authorization: Bearer $access_token",
    'Content-Type: application/json'
];

$test_data = [
    "title" => "TITLE_HERE",
    "description" => "DESCRIPTION_HERE",
];

    $user = User::find(auth()->id());
$data['data'] = $test_data;
$data['token'] = $user['fcm_token'];

$payload['message'] = $data;
$payload = json_encode($payload);


$response = Http::withHeaders($headers)->post($apiUrl, $payload);


if ($response->successful()) {
    return response()->json(['message' => 'Notification has been Sent'],200);
} else {
    return response()->json(['message' => 'Failed to send notification']);
}}

}
