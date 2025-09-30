<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Komanda Dəvəti</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .button {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 10px;
            font-weight: bold;
        }
        .button:hover {
            background: #218838;
        }
        .button.secondary {
            background: #6c757d;
        }
        .button.secondary:hover {
            background: #5a6268;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎉 Komanda Dəvəti</h1>
        <p>{{ $invitation->invitedBy->name }} sizi komandasına dəvət edir!</p>
    </div>
    
    <div class="content">
        <h2>Salam!</h2>
        
        <p><strong>{{ $invitation->invitedBy->name }}</strong> sizi <strong>{{ $invitation->project->name }}</strong> layihəsinə komanda üzvü kimi dəvət edir.</p>
        
        <p><strong>Layihə haqqında:</strong></p>
        <ul>
            <li><strong>Ad:</strong> {{ $invitation->project->name }}</li>
            <li><strong>Təsvir:</strong> {{ $invitation->project->description }}</li>
            <li><strong>Rol:</strong> {{ ucfirst($invitation->role) }}</li>
        </ul>
        
        <p>Dəvəti qəbul etmək üçün aşağıdakı düyməyə klikləyin:</p>
        
        <div style="text-align: center;">
            <a href="{{ route('team.accept', $invitation->token) }}" class="button">
                ✅ Dəvəti Qəbul Et
            </a>
            <a href="{{ route('team.decline', $invitation->token) }}" class="button secondary">
                ❌ Rədd Et
            </a>
        </div>
        
        <p><small><strong>Qeyd:</strong> Bu dəvətin müddəti 7 gündür və {{ $invitation->expires_at->format('d.m.Y H:i') }} tarixində bitəcək.</small></p>
    </div>
    
    <div class="footer">
        <p>Bu email Mini SaaS Task Manager sistemindən göndərilmişdir.</p>
        <p>Əgər bu dəvəti siz göndərməmisinizsə, bu email-i görməzlikdən gəlin.</p>
    </div>
</body>
</html>
