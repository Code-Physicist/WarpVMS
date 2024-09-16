<html>
    <body>
        <div>Dear visitors,</div>
        <br>
        <div>You have been invited to {!! $dept_name !!}</div>
        <br>
        <div><strong>Title:</strong></div>
        <div>{!! $title !!}</div>
        <br>
        <div><strong>Agenda:</strong></div>
        <div>{!! $agenda !!}</div>
        <br>
        <div><strong>Dates:</strong> {!! $date_inv !!}</div>
        <div><strong>Meeting Time:</strong> {!! $time_inv !!}</div>
        <br>
        <div>Please use this QR code to access the building</div>
        <div style="text-align:center;"><img src="{!!$message->embedData($qr_image, 'QrCode.png', 'image/png')!!}"></div>
        <p>Best Regards,<br>Building Admin</p>
    </body>
</html>