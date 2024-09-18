<?php

namespace App\Jobs;

use App\Models\const_trip_reservation;
use App\Models\optionaljourny;
use App\Models\trip_schadual;
use App\Models\User;
use Google\Service\AnalyticsReporting\Activity;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendActivityNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $tripSchadual;
    protected $activityDetails;
    protected $constReservation;
    protected $optionalReservation;
    /**
     * Create a new job instance.
     */

    public function __construct( trip_schadual $tripSchadual,const_trip_reservation $constReservation,optionaljourny $optionalReservation,array $activityDetails)
    {
        $this->tripSchadual = $tripSchadual;
        $this->activityDetails = $activityDetails;
        $this->constReservation= $constReservation;
        $this->optionalReservation= $optionalReservation;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $apiUrl = 'https://fcm.googleapis.com/v1/projects/laravel-final/messages:send';

        $access_token = Cache::remember('access_token', now()->addHour(), function () use ($apiUrl) {
            $credentialsFilePath = storage_path('app/laravel.json');
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

        $notificationData = [
            "title" => $this->activityDetails['title'],
            "description" => $this->activityDetails['description'],
        ];


        $userIds = [
            $this->constReservation->user_id,
            $this->optionalReservation->user_id
        ];

        $users = User::whereIn('id',[11])->get();


        foreach ($users as $user) {
            if ($user && $user->fcm_token) {
                $data['data'] = $notificationData;
                $data['token'] = $user->fcm_token;
                $payload['message'] = $data;
                $payload = json_encode($payload);

                $response = Http::withHeaders($headers)->post($apiUrl, $payload);

                if ($response->successful()) {
                    Log::info("Notification sent successfully for user: {$user->id}");
                } else {
                    Log::error("Failed to send notification for user: {$user->id}");
                }
            }
        }


    }}
