<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency Alert - CaragaDos Eagles Club</title>
    <style>
        :root {
            --primary: #ef4444;
            --secondary: #3b82f6;
            --bg: #f8fafc;
            --card: #ffffff;
            --text: #1e293b;
            --muted: #64748b;
            --border: #e2e8f0;
        }
        body { 
            font-family: 'Inter', system-ui, -apple-system, sans-serif; 
            line-height: 1.6; 
            color: #1e293b; 
            background-color: #f8fafc; 
            margin: 0; 
            padding: 0; 
        }
        .wrapper {
            padding: 40px 20px;
        }
        .container { 
            max-width: 600px; 
            margin: 0 auto; 
            background: #ffffff; 
            border-radius: 16px; 
            overflow: hidden; 
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); 
            border: 1px solid #e2e8f0; 
        }
        .header { 
            background-color: #ef4444; 
            padding: 40px 30px; 
            text-align: center; 
            color: #ffffff; 
        }
        .alert-icon {
            font-size: 48px;
            margin-bottom: 10px;
            display: block;
        }
        .alert-title { 
            font-size: 28px; 
            font-weight: 800; 
            margin: 0; 
            letter-spacing: -0.025em;
            text-transform: uppercase;
        }
        .alert-subtitle { 
            font-size: 16px; 
            opacity: 0.9; 
            margin: 10px 0 0; 
            font-weight: 500;
        }
        .content { 
            padding: 40px 30px; 
        }
        
        .member-card {
            display: table;
            width: 100%;
            background-color: #f1f5f9;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            border: 1px solid #e2e8f0;
        }
        .member-photo-cell {
            display: table-cell;
            width: 70px;
            vertical-align: middle;
        }
        .member-info-cell {
            display: table-cell;
            padding-left: 20px;
            vertical-align: middle;
        }
        .member-photo {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #ffffff;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .member-name {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 4px;
        }
        .member-meta {
            font-size: 14px;
            color: #64748b;
            font-weight: 500;
        }

        .details-grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 15px;
        }
        .detail-label {
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            width: 140px;
        }
        .detail-value {
            font-size: 16px;
            font-weight: 600;
            color: #1e293b;
        }
        .emergency-type {
            color: #ef4444;
            font-size: 18px;
        }

        .divider {
            height: 1px;
            background-color: #e2e8f0;
            margin: 30px 0;
        }

        .btn-container {
            text-align: center;
            margin-top: 10px;
        }
        .btn { 
            display: inline-block; 
            padding: 16px 32px; 
            background-color: #3b82f6; 
            color: #ffffff !important; 
            text-decoration: none; 
            border-radius: 12px; 
            font-weight: 700; 
            font-size: 16px; 
            box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
            transition: all 0.2s ease;
        }
        
        .footer { 
            padding: 30px; 
            text-align: center; 
            font-size: 13px; 
            color: #94a3b8; 
            background-color: #f8fafc; 
            border-top: 1px solid #e2e8f0; 
        }
        .brush-font {
            font-family: 'Brush Script MT', cursive;
            font-size: 1.4em;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <span class="alert-icon">🚨</span>
                <h1 class="alert-title">Emergency Alert</h1>
                <p class="alert-subtitle">Immediate assistance requested</p>
            </div>
            
            <div class="content">
                <p style="font-size: 16px; margin-top: 0;">Hello <span class="brush-font">Kuya</span>,</p>
                <p style="color: #64748b; margin-bottom: 25px;">A fellow kuya has just submitted an Alalayang Agila help request and needs your support.</p>
                
                <div class="member-card">
                    <div class="member-photo-cell">
                        @if($quickResponse->user->profile_photo)
                            <img src="{{ $message->embed(storage_path('app/public/' . $quickResponse->user->profile_photo)) }}" class="member-photo">
                        @else
                            <div style="width: 70px; height: 70px; border-radius: 50%; background-color: #e2e8f0; display: table-cell; vertical-align: middle; text-align: center; border: 3px solid #ffffff; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                                <span style="font-size: 24px;">👤</span>
                            </div>
                        @endif
                    </div>
                    <div class="member-info-cell">
                        <div class="member-name">Kuya {{ $quickResponse->user->fullname }}</div>
                        <div class="member-meta">{{ $quickResponse->user->club->name ?? 'No Club' }}</div>
                        <div class="member-meta">{{ $quickResponse->user->region->name ?? 'No Region' }}</div>
                    </div>
                </div>
                
                <table class="details-grid">
                    <tr>
                        <td class="detail-label">Type</td>
                        <td class="detail-value emergency-type">{{ $quickResponse->libHelp->name }}</td>
                    </tr>
                    <tr>
                        <td class="detail-label" style="vertical-align: top;">Message</td>
                        <td class="detail-value" style="font-weight: 500; line-height: 1.5;">{{ $quickResponse->details }}</td>
                    </tr>
                    @if($quickResponse->location)
                    <tr>
                        <td class="detail-label">Coordinates</td>
                        <td class="detail-value" style="font-family: monospace; font-size: 14px; color: #64748b;">{{ $quickResponse->location }}</td>
                    </tr>
                    @endif
                </table>

                <div class="divider"></div>

                <div class="btn-container">
                    @if($quickResponse->location)
                        <a href="https://www.google.com/maps?q={{ $quickResponse->location }}" class="btn">Navigate to Location</a>
                    @else
                        <a href="{{ route('dashboard') }}" class="btn">View Dashboard</a>
                    @endif
                </div>

                <p style="margin-top: 35px; font-size: 14px; color: #94a3b8; text-align: center; font-style: italic;">
                    Please respond immediately if you are near the requester's vicinity.
                </p>
            </div>
            
            <div class="footer">
                <div style="font-weight: 700; color: #64748b; margin-bottom: 5px;">CaragaDos Eagles Club</div>
                &copy; {{ date('Y') }} Community Response System. All rights reserved.
            </div>
        </div>
    </div>
</body>
</html>