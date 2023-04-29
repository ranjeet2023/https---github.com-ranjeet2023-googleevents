<!doctype html>
<html lang="en">
  <head>
    <title>Title</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.4.js" integrity="sha256-a9jBBRygX1Bh5lt8GZjXDzyOB+bWve9EiO7tROUtj/E=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  </head>
  <body>
    @for($i = 10; $i >=0 ; $i--)
    @for($j = $i; $j >= 0; $j--)
      {{ '*' }}
    @endfor
    <br>
    @endfor
    <table class="table">
        <thead>
            <tr>
                <th>name</th>
                <th>email</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $data )
            <tr>
                <td scope="row">{{ $data->name }}</td>
                <td scope="row">{{ $data->email }}</td>
                <td scope="row" >
                <button class="btn btn-primary send-email-btn" data-user-id="{{ $data->id }}">Send Email</button>
             </td>
            </tr>
        @endforeach
        </tbody>
    </table>
           {{-- @foreach ($events as $event)
                <li>
                    {{ $event->getSummary() }} - {{ $event->getStart()->getDateTime() }}
                </li>
            @endforeach --}}


    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->

<script>
    $(document).ready(function() {
        $(document).on('click', '.send-email-btn', function() {
            var userId = $(this).data('user-id');
            Swal.fire({
                title: 'Send Email',
                html:
                    '<label for="email_subject">Subject</label>' +
                    '<input id="email_subject" type="text" class="swal2-input" required>' +
                    '<label for="email_body">Message</label>' +
                    '<textarea id="email_body" class="swal2-textarea" required></textarea>',
                focusConfirm: false,
                showCancelButton: true,
                showCancelButton: true,
                confirmButtonText: 'Send',
                preConfirm: () => {
                    var emailSubject = $('#email_subject').val();
                    var emailBody = $('#email_body').val();
                    return { emailSubject: emailSubject, emailBody: emailBody }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ url('/send') }}/'+ userId,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            email_subject: result.value.emailSubject,
                            email_body: result.value.emailBody
                        },
                        success: function(response) {
                            Swal.fire('Email Sent', '', 'success');
                        },
                        error: function(response) {
                            console.log(response);
                            Swal.fire('Error', 'Failed to send email', 'error');
                        }
                    });
                }else {
                    Swal.fire('Email Cancelled', '', 'info');
                }
            });
        });
});
</script>
  </body>
</html>
