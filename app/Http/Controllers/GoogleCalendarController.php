<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PulkitJalan\Google\Facades\Google;
use Google\Client as GoogleClient;
use Google\Service\Calendar as GoogleCalendar;
use Illuminate\Support\Facades\Redirect;

class GoogleCalendarController extends Controller
{
    //

    public function redirectToGoogle(Request $request)
    {
        $client = Google::getClient();
        $client_id=env('GOOGLE_CLIENT_ID');
        $client->setClientId(env('GOOGLE_CLIENT_ID'));

        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
        $client->addScope(GoogleCalendar::CALENDAR_READONLY);

        $authUrl = $client->createAuthUrl();

        return Redirect::to($authUrl);

    }
    public function GoogleAllEvents(Request $request)
        {
            $client = new GoogleClient();
            $client->setClientId(env('GOOGLE_CLIENT_ID'));
            $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
            $client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
            $client->addScope(GoogleCalendar::CALENDAR_READONLY);

            $authCode = $request->get('code');

            try {
                $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
            } catch (\Exception $e) {
                return redirect()->route('google.redirect')->with('error', 'Error fetching access token: '.$e->getMessage());
            }

            if (empty($accessToken['access_token'])) {
                return redirect()->route('google.redirect')->with('error', 'Empty access token');
            }

            $client->setAccessToken($accessToken);

            $calendarService = new GoogleCalendar($client);

            $calendarList = $calendarService->calendarList->listCalendarList();

            $calendars = [];

            foreach ($calendarList as $calendar) {
                $events = $calendarService->events->listEvents($calendar->id);
                $calendars[] = [
                    'id' => $calendar->id,
                    'name' => $calendar->summary,
                    'events' => $events->getItems()
                ];
            }
            return view('google-calendar-events', compact('calendars'));

    }
}
