<html>
    <body>
        <p>Dear visitors,<br>Your QR Code:</p>
        <div style="text-align:center;"><img src="{!!$message->embedData($qr_image, 'QrCode.png', 'image/png')!!}"></div>
        <p>Best Regards,<br>Building Admin</p>
    </body>
</html>