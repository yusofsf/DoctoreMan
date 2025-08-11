<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>وقت دکتر لغو شد</title>
    <style>
        body {
            font-family: 'Tahoma', 'Arial', sans-serif;
            direction: rtl;
            text-align: right;
            margin: 0;
            padding: 0;
            background-color: #f3f4f6;
            color: #374151;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .email-card {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            padding: 30px 20px;
            text-align: center;
        }
        .header-icon {
            width: 48px;
            height: 48px;
            background-color: #ffffff;
            border-radius: 50%;
            margin: 0 auto 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .header-title {
            color: #ffffff;
            font-size: 24px;
            font-weight: bold;
            margin: 0 0 8px 0;
        }
        .header-subtitle {
            color: #fecaca;
            font-size: 16px;
            margin: 0;
        }
        .content {
            padding: 30px 20px;
        }
        .section {
            margin-bottom: 24px;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #111827;
            margin-bottom: 12px;
        }
        .info-box {
            background-color: #f9fafb;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 16px;
        }
        .cancellation-box {
            background-color: #fef2f2;
            border-radius: 8px;
            padding: 16px;
            border-left: 4px solid #dc2626;
        }
        .doctor-box {
            background-color: #eff6ff;
            border-radius: 8px;
            padding: 16px;
            border-left: 4px solid #3b82f6;
        }
        .next-steps-box {
            background-color: #f0fdf4;
            border-radius: 8px;
            padding: 16px;
            border-left: 4px solid #22c55e;
        }
        .info-row {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: bold;
            color: #374151;
            margin-left: 8px;
        }
        .info-value {
            color: #111827;
        }
        .doctor-info {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
        }
        .doctor-avatar {
            width: 48px;
            height: 48px;
            background-color: #dbeafe;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: 16px;
        }
        .steps-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .step-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 8px;
        }
        .step-icon {
            width: 16px;
            height: 16px;
            margin-left: 8px;
            margin-top: 2px;
            flex-shrink: 0;
        }
        .footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
        }
        .footer-text {
            font-size: 14px;
            color: #6b7280;
            margin: 0;
        }
        .footer-link {
            color: #3b82f6;
            text-decoration: none;
        }
        @media only screen and (max-width: 600px) {
            .container {
                padding: 10px;
            }
            .content {
                padding: 20px 15px;
            }
            .header {
                padding: 20px 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="email-card">
            <!-- Header -->
            <div class="header">
                <div class="header-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <h1 class="header-title">وقت دکتر لغو شد!</h1>
                <p class="header-subtitle">وقت دکتر شما لغو شده است</p>
            </div>

            <!-- Content -->
            <div class="content">
                <!-- Cancellation Notice -->
                <div class="section">
                    <h2 class="section-title">اطلاعیه لغو</h2>
                    <div class="cancellation-box">
                        <div class="info-row">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" style="margin-left: 12px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            <span style="color: #dc2626; font-weight: bold;">اطلاعیه لغو</span>
                        </div>
                        <p style="color: #dc2626; font-size: 14px; margin-top: 8px;">
                            وقت دکتر شما در تاریخ مشخص شده لغو شده است. در صورت نیاز به وقت جدید، لطفا دوباره رزرو کنید.
                        </p>
                    </div>
                </div>

                <!-- Patient Info -->
                <div class="section">
                    <h2 class="section-title">اطلاعات بیمار</h2>
                    <div class="info-box">
                        <div class="info-row">
                            <span class="info-label">نام:</span>
                            <span class="info-value">{{ $patientUser->first_name ?? 'نام بیمار' }} {{ $patientUser->last_name ?? '' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">شماره تماس:</span>
                            <span class="info-value">{{ $patientUser->phone_number ?? '+98 912 345 6789' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Cancelled Appointment Details -->
                <div class="section">
                    <h2 class="section-title">جزئیات وقت لغو شده</h2>
                    <div class="cancellation-box">
                        <div class="info-row">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" style="margin-left: 12px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="info-label">تاریخ:</span>
                            <span class="info-value">{{ $appointment->date ?? 'تاریخ وقت' }}</span>
                        </div>
                        <div class="info-row">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" style="margin-left: 12px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="info-label">زمان:</span>
                            <span class="info-value">{{ $schedule->start_time ?? 'زمان وقت' }}</span>
                        </div>
                        <div class="info-row">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" style="margin-left: 12px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="info-label">مدت:</span>
                            <span class="info-value">{{ $user->session_duration ?? '30 دقیقه' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Doctor Information -->
                <div class="section">
                    <h2 class="section-title">اطلاعات دکتر</h2>
                    <div class="doctor-box">
                        <div class="doctor-info">
                            <div class="doctor-avatar">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 style="color: #111827; margin: 0 0 4px 0; font-size: 18px; font-weight: bold;">{{ $user->first_name ?? 'نام دکتر' }} {{ $user->last_name ?? '' }}</h3>
                                <p style="color: #374151; margin: 0;">{{ $user->specialization ?? 'تخصص' }}</p>
                            </div>
                        </div>
                        <div class="info-row">
                            <span class="info-label">شهر:</span>
                            <span class="info-value">{{ $user->city ?? 'شهر' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">آدرس:</span>
                            <span class="info-value">{{ $user->address ?? 'آدرس مطب' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">شماره تماس:</span>
                            <span class="info-value">{{ $user->phone_number ?? 'شماره تماس' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Next Steps -->
                <div class="section">
                    <h2 class="section-title">مراحل بعدی</h2>
                    <div class="next-steps-box">
                        <ul class="steps-list">
                            <li class="step-item">
                                <svg class="step-icon" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                </svg>
                                برای وقت جدید، لطفا دوباره رزرو کنید
                            </li>
                            <li class="step-item">
                                <svg class="step-icon" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                </svg>
                                در صورت سوال، با ما تماس بگیرید
                            </li>
                            <li class="step-item">
                                <svg class="step-icon" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                </svg>
                                امکان رزرو آنلاین در هر زمان
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p class="footer-text">در صورت سوال، با ما تماس بگیرید:</p>
                <p class="footer-text">
                    <a href="tel:+989123456789" class="footer-link">+98 912 345 6789</a> | 
                    <a href="mailto:support@doctoreman.com" class="footer-link">support@doctoreman.com</a>
                </p>
                <p class="footer-text" style="font-size: 12px; margin-top: 8px;">
                    این یک ایمیل خودکار است. لطفا به این ایمیل پاسخ ندهید.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
