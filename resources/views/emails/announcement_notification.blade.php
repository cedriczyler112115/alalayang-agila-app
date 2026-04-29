<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Announcement - CaragaDos Eagles Club</title>
    <style>
        body { 
            font-family: 'Inter', system-ui, -apple-system, sans-serif; 
            line-height: 1.6; 
            color: #1e293b; 
            background-color: #f1f5f9; 
            margin: 0; 
            padding: 0; 
        }
        .wrapper {
            padding: 40px 10px;
        }
        .container { 
            max-width: 600px; 
            margin: 0 auto; 
            background: #ffffff; 
            border-radius: 12px; 
            overflow: hidden; 
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); 
        }
        .header { 
            background-color: #2563eb; 
            padding: 30px 20px; 
            text-align: center; 
            color: #ffffff; 
        }
        .icon {
            font-size: 40px;
            margin-bottom: 8px;
            display: block;
        }
        .title { 
            font-size: 24px; 
            font-weight: 800; 
            margin: 0; 
            letter-spacing: -0.01em;
            text-transform: uppercase;
        }
        .content { 
            padding: 30px 25px; 
        }
        
        .announcement-card {
            background-color: #f8fafc;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
            border: 1px solid #e2e8f0;
        }
        .announcement-title {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 12px;
            line-height: 1.4;
        }
        .announcement-body {
            font-size: 15px;
            color: #334155;
            line-height: 1.6;
        }
        .announcement-body img {
            max-width: 100%;
            height: auto !important;
            display: block;
            margin: 15px 0;
            border-radius: 8px;
        }
        .announcement-body table {
            width: 100% !important;
            border-collapse: collapse;
            margin: 15px 0;
        }
        .announcement-body table td, .announcement-body table th {
            border: 1px solid #e2e8f0;
            padding: 8px;
        }
        .announcement-body blockquote {
            border-left: 4px solid #2563eb;
            padding-left: 15px;
            margin: 15px 0;
            font-style: italic;
            color: #64748b;
        }
        
        .meta-info {
            font-size: 13px;
            color: #64748b;
            margin-top: 15px;
            padding-top: 12px;
            border-top: 1px solid #e2e8f0;
        }

        .btn-container {
            text-align: center;
            margin-top: 10px;
        }
        .btn { 
            display: inline-block; 
            padding: 14px 28px; 
            background-color: #2563eb; 
            color: #ffffff !important; 
            text-decoration: none; 
            border-radius: 8px; 
            font-weight: 600; 
            font-size: 15px; 
        }
        
        .footer { 
            padding: 25px; 
            text-align: center; 
            font-size: 12px; 
            color: #94a3b8; 
            background-color: #f8fafc; 
            border-top: 1px solid #e2e8f0; 
        }
        .brush-font {
            font-family: 'Brush Script MT', cursive;
            font-size: 1.3em;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <span class="icon">📢</span>
                <h1 class="title">New Announcement</h1>
            </div>
            
            <div class="content">
                <p style="font-size: 16px; margin-top: 0;">Hello <span class="brush-font">Kuya</span>,</p>
                <p style="color: #64748b; margin-bottom: 25px;">A new announcement has been published for all CaragaDos Eagles Club members.</p>
                
                <div class="announcement-card">
                    <div class="announcement-title">{{ $announcement->title }}</div>
                    <div class="announcement-body">
                        {!! $announcement->content !!}
                    </div>
                    <div class="meta-info">
                        <strong>Published by:</strong> Kuya {{ $announcement->user->fullname }}<br>
                        <strong>Date:</strong> {{ $announcement->published_at ? $announcement->published_at->format('M d, Y') : date('M d, Y') }}
                    </div>
                </div>

                <div class="btn-container">
                    <a href="{{ route('dashboard') }}" class="btn">View on Dashboard</a>
                </div>
            </div>
            
            <div class="footer">
                <div style="font-weight: 700; color: #64748b; margin-bottom: 5px;">CaragaDos Eagles Club</div>
                &copy; {{ date('Y') }} Community Response System. All rights reserved.
            </div>
        </div>
    </div>
</body>
</html>