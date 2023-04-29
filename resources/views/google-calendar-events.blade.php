<!doctype html>
<html lang="en">
  <head>
    <title>Google Calender Events</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.css" />
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.js" integrity="sha256-a9jBBRygX1Bh5lt8GZjXDzyOB+bWve9EiO7tROUtj/E=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
  <body>
    <div class="container-fluid m-25 " style="width:80%;margin-top:50px">
        <h4 style="text-align:center">Google upcoming events</h4>
        <button id="authorize_button" onclick="handleAuthClick()">Authorize</button>
        <button id="signout_button" onclick="handleSignoutClick()">Sign Out</button>
        <h4 style="text-align:center"><pre id="content" ></pre></h4>
        <h4 style="text-align:center">All Google events</h4>
        @foreach ($calendars as $calendar)
            <h3 style="text-align:center">{{ $calendar['name'] }}</h4>
            <table class="table table-sm" id="myTable">
                <thead>
                    <tr>
                        <th>Calendar</th>
                        <th>Event</th>
                        <th>Start Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($calendar['events'] as $event)
                        <tr>
                            <td>{{ $calendar['name'] }}</td>
                            <td>{{ $event->getSummary() }}</td>
                            @if ($event->getStart()->getDate())
                              <td>{{ (new DateTime($event->getStart()->getDate()))->format('j-F-Y') }}</td>
                            @else
                                <td>{{ (new DateTime($event->getStart()->getDateTime()))->format('j-F-Y h:i a') }}</td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach
    </div>
    <script type="text/javascript">
        const CLIENT_ID = '1040496875790-2gdrf3bnfqs6disoo23e4l1bg9300ejd.apps.googleusercontent.com';
        const API_KEY = 'AIzaSyDHERtWN3sy4wyELRCrgoNd4uAzo8MzZlY';
        const DISCOVERY_DOC = 'https://www.googleapis.com/discovery/v1/apis/calendar/v3/rest';
        const SCOPES = 'https://www.googleapis.com/auth/calendar.readonly';
        let tokenClient;
        let gapiInited = false;
        let gisInited = false;
        document.getElementById('authorize_button').style.visibility = 'hidden';
        document.getElementById('signout_button').style.visibility = 'hidden';
        function gapiLoaded() {
          gapi.load('client', initializeGapiClient);
        }

        async function initializeGapiClient() {
          await gapi.client.init({
            apiKey: API_KEY,
            discoveryDocs: [DISCOVERY_DOC],
          });
          gapiInited = true;
          maybeEnableButtons();
        }

        function gisLoaded() {
          tokenClient = google.accounts.oauth2.initTokenClient({
            client_id: CLIENT_ID,
            scope: SCOPES,
            callback: '', // defined later
          });
          gisInited = true;
          maybeEnableButtons();
        }

        function maybeEnableButtons() {
          if (gapiInited && gisInited) {
            document.getElementById('authorize_button').style.visibility = 'visible';
          }
        }
        function handleAuthClick() {
          tokenClient.callback = async (resp) => {
            if (resp.error !== undefined) {
              throw (resp);
            }
            document.getElementById('signout_button').style.visibility = 'visible';
            document.getElementById('authorize_button').innerText = 'Refresh';
            await listUpcomingEvents();
          };

          if (gapi.client.getToken() === null) {
            tokenClient.requestAccessToken({prompt: 'consent'});
          } else {
            tokenClient.requestAccessToken({prompt: ''});
          }
        }
        function handleSignoutClick() {
          const token = gapi.client.getToken();
          if (token !== null) {
            google.accounts.oauth2.revoke(token.access_token);
            gapi.client.setToken('');
            document.getElementById('content').innerText = '';
            document.getElementById('authorize_button').innerText = 'Authorize';
            document.getElementById('signout_button').style.visibility = 'hidden';
          }
        }
        async function listUpcomingEvents() {
          let response;
          try {
            const request = {
              'calendarId': 'primary',
              'timeMin': (new Date()).toISOString(),
              'showDeleted': false,
              'singleEvents': true,
              'maxResults': 10,
              'orderBy': 'startTime',
            };
            response = await gapi.client.calendar.events.list(request);
          } catch (err) {
            document.getElementById('content').innerText = err.message;
            return;
          }

          const events = response.result.items;
          if (!events || events.length == 0) {
            document.getElementById('content').innerText = 'No events found.';
            return;
          }
          const output = events.reduce(
              (str, event) => `${str}${event.summary} (${event.start.dateTime || event.start.date})\n`,
              'Events:\n');
          document.getElementById('content').innerText = output;
        }
      </script>
    <script >
        $(document).ready( function () {
            $('#myTable').DataTable();
        });
    </script>
    <script src="//cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"  ></script>
    <script async defer src="https://apis.google.com/js/api.js" onload="gapiLoaded()"></script>
    <script async defer src="https://accounts.google.com/gsi/client" onload="gisLoaded()"></script>
  </body>
</html>
