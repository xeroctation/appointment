<!DOCTYPE html>
<html>
<head>
    <title>Appointment Notification</title>
</head>
<body>
<div style="font-family: Arial, sans-serif; font-size: 14px; line-height: 1.5;">
    <p>Dear {{ $appointment->user->name }},</p>
    <br>
    <p>Your appointment for <strong>{{ $appointment->name }}</strong> has been <strong>{{ $action }}</strong>.</p>
    <p>The type of the service is <strong>{{ $appointment->serviceProvider->name }}</strong>.</p>
    <p>The appointment start time is <strong>{{ \Carbon\Carbon::parse($appointment->start_time)->format('g:i A \o\n l, jS F Y') }}</strong>.</p>
    <p>The appointment end time is <strong>{{ \Carbon\Carbon::parse($appointment->end_time)->format('g:i A \o\n l, jS F Y') }}</strong>.</p>
    <p>Please contact us if you have any questions or concerns regarding your appointment.</p>
    <p>Thank you for choosing our service.</p>
    <br>
    <p>Best regards,</p>
    <p>The ScheduleMe Team</p>
</div>
</body>
</html>
