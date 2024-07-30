<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>One-Time Password (OTP)</title>
</head>
<body style="font-family: Arial, sans-serif; padding: 20px;">

  <table style="max-width: 600px; margin: 0 auto; background-color: #000; padding: 20px; border-radius: 10px;">
    <tr>
      <td style="padding: 20px 0;">
        <h2 style="margin: 0; color: #ffffff; ">Your One-Time Password (OTP)</h2>
      </td>
    </tr>
    <tr>
      <td>
        <p style="margin: 0; font-size: 16px; line-height: 1.6; color: #ffffff;">Dear {{$content['data']['name'] ?? ''}},</p>
        <p style="margin: 10px 0 20px 0; font-size: 16px; line-height: 1.6; color: #ffffff;">Thank you for using Truflix. To ensure the security of your account, we have generated a One-Time Password (OTP) for you.</p>
        <p style="margin: 0; font-size: 16px; line-height: 1.6; color: #ffffff;"><strong>Your One-Time Password (OTP):</strong> <span style="background-color: gray; padding: 10px 20px;letter-spacing: 5px;font-size: 18px;">{{$content['data']['otp'] ?? '----'}}</span></p>
        <p style="margin: 20px 0 0 0; font-size: 16px; line-height: 1.6; color: #ffffff;">This OTP is valid for a single use and should not be shared with anyone.</p>
      </td>
    </tr>
  </table>

</body>
</html>
