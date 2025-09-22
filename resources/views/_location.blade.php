<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>ขอตำแหน่งที่อยู่</title>
    <script src="https://static.line-scdn.net/liff/edge/2/sdk.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            min-height: 100dvh;
            /* Dynamic viewport height for mobile */
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 15px;
            overflow-x: hidden;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px 25px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            width: 100%;
            transform: translateY(0);
            transition: all 0.3s ease;
            margin: 10px auto;
        }

        .container:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.25);
        }

        .icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #ff6b6b, #ee5a6f);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            box-shadow: 0 10px 30px rgba(255, 107, 107, 0.3);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        .icon svg {
            width: 35px;
            height: 35px;
            fill: white;
        }

        h1 {
            color: #333;
            font-size: 24px;
            margin-bottom: 12px;
            font-weight: 600;
            line-height: 1.3;
        }

        .subtitle {
            color: #666;
            font-size: 15px;
            margin-bottom: 25px;
            line-height: 1.4;
            padding: 0 5px;
        }

        .location-btn {
            background: linear-gradient(135deg, #00c851, #007e33);
            color: white;
            border: none;
            padding: 16px 24px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(0, 200, 81, 0.3);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            min-height: 54px;
            touch-action: manipulation;
            -webkit-tap-highlight-color: transparent;
        }

        .location-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(0, 200, 81, 0.4);
        }

        .location-btn:active {
            transform: translateY(0);
        }

        .location-btn:disabled {
            background: linear-gradient(135deg, #ccc, #aaa);
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .status {
            margin-top: 20px;
            padding: 15px;
            border-radius: 10px;
            font-size: 14px;
            display: none;
        }

        .status.success {
            background: rgba(0, 200, 81, 0.1);
            border: 1px solid rgba(0, 200, 81, 0.3);
            color: #007e33;
        }

        .status.error {
            background: rgba(255, 107, 107, 0.1);
            border: 1px solid rgba(255, 107, 107, 0.3);
            color: #cc0000;
        }

        .status.info {
            background: rgba(33, 150, 243, 0.1);
            border: 1px solid rgba(33, 150, 243, 0.3);
            color: #1976d2;
        }

        .location-info {
            text-align: left;
            margin-top: 15px;
            font-size: 13px;
            line-height: 1.4;
        }

        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Mobile specific optimizations */
        @media (max-width: 480px) {
            body {
                padding: 10px;
            }

            .container {
                padding: 25px 20px;
                margin: 5px auto;
                border-radius: 16px;
                max-width: calc(100vw - 20px);
            }

            .icon {
                width: 60px;
                height: 60px;
                margin-bottom: 20px;
            }

            .icon svg {
                width: 30px;
                height: 30px;
            }

            h1 {
                font-size: 22px;
                margin-bottom: 10px;
            }

            .subtitle {
                font-size: 14px;
                margin-bottom: 20px;
            }

            .location-btn {
                padding: 14px 20px;
                font-size: 15px;
                min-height: 50px;
            }

            .status {
                padding: 12px;
                font-size: 13px;
            }

            .location-info {
                font-size: 12px;
            }
        }

        /* Extra small screens */
        @media (max-width: 320px) {
            .container {
                padding: 20px 15px;
            }

            h1 {
                font-size: 20px;
            }

            .subtitle {
                font-size: 13px;
            }

            .location-btn {
                font-size: 14px;
                padding: 12px 18px;
            }
        }

        /* Landscape mobile */
        @media (max-height: 600px) and (orientation: landscape) {
            body {
                padding: 10px;
            }

            .container {
                padding: 20px;
                margin: 5px auto;
            }

            .icon {
                width: 50px;
                height: 50px;
                margin-bottom: 15px;
            }

            .icon svg {
                width: 25px;
                height: 25px;
            }

            h1 {
                font-size: 20px;
                margin-bottom: 8px;
            }

            .subtitle {
                font-size: 13px;
                margin-bottom: 15px;
            }
        }

        /* Safe area for notched phones */
        @supports (padding: max(0px)) {
            body {
                padding-top: max(15px, env(safe-area-inset-top));
                padding-bottom: max(15px, env(safe-area-inset-bottom));
                padding-left: max(15px, env(safe-area-inset-left));
                padding-right: max(15px, env(safe-area-inset-right));
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="icon">
            <svg viewBox="0 0 24 24">
                <path
                    d="M12,2C8.13,2 5,5.13 5,9C5,14.25 12,22 12,22C12,22 19,14.25 19,9C19,5.13 15.87,2 12,2M12,7A2,2 0 0,1 14,9A2,2 0 0,1 12,11A2,2 0 0,1 10,9A2,2 0 0,1 12,7Z" />
            </svg>
        </div>

        <h1>ขอตำแหน่งที่อยู่</h1>
        <p class="subtitle">กดปุ่มด้านล่างเพื่อแชร์ตำแหน่งปัจจุบันของคุณ</p>

        <button class="location-btn" id="getLocationBtn">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path
                    d="M12,8A4,4 0 0,1 16,12A4,4 0 0,1 12,16A4,4 0 0,1 8,12A4,4 0 0,1 12,8M3.05,13H1V11H3.05C3.5,6.83 6.83,3.5 11,3.05V1H13V3.05C17.17,3.5 20.5,6.83 20.95,11H23V13H20.95C20.5,17.17 17.17,20.5 13,20.95V23H11V20.95C6.83,20.5 3.5,17.17 3.05,13M12,5A7,7 0 0,0 5,12A7,7 0 0,0 12,19A7,7 0 0,0 19,12A7,7 0 0,0 12,5Z" />
            </svg>
            ขอตำแหน่งที่อยู่
        </button>

        <div class="status" id="statusDiv"></div>
    </div>

    <script>
        let isLiffInitialized = false;
        let userId = null;
        let userProfile = null;

        async function initializeLiff() {
            try {
                await liff.init({
                    liffId: '2007553661-7o6lnaAj'
                });

                isLiffInitialized = true;
                console.log('LIFF initialized successfully');

                if (!liff.isLoggedIn()) {
                    console.log('User not logged in, redirecting to login...');
                    showStatus('กำลังเข้าสู่ระบบ LINE...', 'info');
                    liff.login();
                } else {
                    try {
                        userProfile = await liff.getProfile();
                        userId = userProfile.userId;
                        console.log('User profile:', userProfile);
                        showStatus(`สวัสดี ${userProfile.displayName}! พร้อมใช้งานแล้ว`, 'success');
                    } catch (profileError) {
                        console.error('getProfile error:', profileError);
                        showStatus('ไม่สามารถดึงข้อมูลโปรไฟล์ได้', 'error');
                    }
                }
            } catch (error) {
                console.error('LIFF initialization failed:', error);
                showStatus('ไม่สามารถเชื่อมต่อ LINE LIFF ได้: ' + error.message, 'error');
            }
        }

        function showStatus(message, type) {
            const statusDiv = document.getElementById('statusDiv');
            statusDiv.className = `status ${type}`;
            statusDiv.innerHTML = message;
            statusDiv.style.display = 'block';
        }

        function getBrowserLocation() {
            return new Promise((resolve, reject) => {
                if (!navigator.geolocation) {
                    reject(new Error('เบราว์เซอร์ของคุณไม่รองรับการขอตำแหน่งที่อยู่'));
                    return;
                }

                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        resolve({
                            latitude: position.coords.latitude,
                            longitude: position.coords.longitude,
                            accuracy: position.coords.accuracy
                        });
                    },
                    (error) => {
                        let errorMessage = '';
                        switch (error.code) {
                            case error.PERMISSION_DENIED:
                                errorMessage = 'คุณได้ปฏิเสธการแชร์ตำแหน่งที่อยู่';
                                break;
                            case error.POSITION_UNAVAILABLE:
                                errorMessage = 'ไม่สามารถหาตำแหน่งที่อยู่ได้';
                                break;
                            case error.TIMEOUT:
                                errorMessage = 'หมดเวลาในการค้นหาตำแหน่งที่อยู่';
                                break;
                            default:
                                errorMessage = 'เกิดข้อผิดพลาดในการหาตำแหน่งที่อยู่';
                                break;
                        }
                        reject(new Error(errorMessage));
                    }, {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 60000
                    }
                );
            });
        }

        async function handleLocationRequest() {
            const btn = document.getElementById('getLocationBtn');
            btn.disabled = true;
            btn.innerHTML = '<div class="loading"></div> กำลังขอตำแหน่ง...';

            try {
                let location;

                if (isLiffInitialized && liff.isApiAvailable('shareTargetPicker')) {
                    try {
                        location = await getBrowserLocation();
                    } catch (liffError) {
                        console.log('LIFF location failed, trying browser location:', liffError);
                        location = await getBrowserLocation();
                    }
                } else {
                    location = await getBrowserLocation();
                }

                const locationInfo = `
                    <div class="location-info">
                        <strong>ตำแหน่งที่อยู่ของคุณ:</strong><br>
                        ${userProfile ? `ผู้ใช้: ${userProfile.displayName}<br>` : ''}
                        ละติจูด: ${location.latitude.toFixed(6)}<br>
                        ลองจิจูด: ${location.longitude.toFixed(6)}<br>
                        ความแม่นยำ: ${Math.round(location.accuracy)} เมตร<br>
                        <br>
                        <a href="https://maps.google.com/?q=${location.latitude},${location.longitude}" target="_blank" style="color: #007e33; text-decoration: underline;">
                            ดูใน Google Maps
                        </a>
                    </div>
                `;

                showStatus('ได้ตำแหน่งที่อยู่แล้ว!' + locationInfo, 'success');


                const url =
                    `https://nominatim.openstreetmap.org/reverse?lat=${location.latitude}&lon=${location.longitude}&format=json&accept-language=th`;

                const r = await fetch(url);
                const data = await r.json();
                let province = data.address.province || '';
                province = province.replace('จังหวัด', '').trim();

                const locationData = {
                    userId: userId,
                    userProfile: userProfile,
                    latitude: location.latitude,
                    longitude: location.longitude,
                    location: location,
                    timestamp: new Date().toISOString(),
                    province: province
                };

                // console.log('Location data with user info:', locationData);
                await sendLocationToServer(locationData);

            } catch (error) {
                // console.error('Location error:', error);
                showStatus(error.message, 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = `
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12,8A4,4 0 0,1 16,12A4,4 0 0,1 12,16A4,4 0 0,1 8,12A4,4 0 0,1 12,8M3.05,13H1V11H3.05C3.5,6.83 6.83,3.5 11,3.05V1H13V3.05C17.17,3.5 20.5,6.83 20.95,11H23V13H20.95C20.5,17.17 17.17,20.5 13,20.95V23H11V20.95C6.83,20.5 3.5,17.17 3.05,13M12,5A7,7 0 0,0 5,12A7,7 0 0,0 12,19A7,7 0 0,0 19,12A7,7 0 0,0 12,5Z"/>
                    </svg>
                    ขอตำแหน่งที่อยู่
                `;
            }
        }

        async function sendLocationToServer(locationData) {
            try {
                const response = await fetch('/api/line-location', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(locationData)
                });

                const result = await response.json();

                if (response.ok) {
                    console.log('Location sent to server successfully:', result);
                    liff.closeWindow();
                } else {
                    console.error('Failed to send location to server:', result);
                }
            } catch (error) {
                console.error('Error sending location to server:', error);
            }
        }

        document.getElementById('getLocationBtn').addEventListener('click', handleLocationRequest);

        window.addEventListener('load', () => {
            if (typeof liff !== 'undefined') {
                initializeLiff();
            } else {
                showStatus('กำลังใช้งานในโหมดปกติ (ไม่ใช่ LINE LIFF)', 'info');
            }
        });
    </script>
</body>

</html>
