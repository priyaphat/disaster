<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ขอตำแหน่งที่อยู่</title>
    <script src="https://static.line-scdn.net/liff/edge/2/sdk.js"></script>
    <style>
        body {
            background-color: rgb(161, 5, 132);
            margin: 0;
            padding: 0;
        }

        .container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .box {
            display: flex;
            flex-direction: column;
            gap: 20px;
            align-items: center;
        }

        .box-text {
            background: #ffffff26;
            padding: 6px 34px;
            border-radius: 5px;
            display: flex;
            flex-direction: column;
            gap: 4px;
            text-align: center;
            color: #fff;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
        }

        .box-text:hover {
            background: #ffffff60;
        }

        .title {
            font-size: 20px;
            font-weight: bold;
        }

        .sub-title {
            font-size: 14px;
        }

        .status {
            font-size: 14px;
            color: red;
            display: none;
            /* ซ่อนก่อน */
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="box">
            <img src="{{ asset(env('APP_URL')) }}/Logo/location.png" width="150" style="height: auto" />
            <button class="box-text" id="getLocationBtn">
                <div class="title">ระบุตำแหน่ง</div>
                <div class="sub-title">(Send Location)</div>
            </button>
            <div class="status" id="statusDiv"></div>
        </div>
    </div>
    <script>
        let isLiffInitialized = false;
        let userId = null;
        let userProfile = null;
        const liff_id = "{{ env('LIFF_LOCATIOIN') }}";

        async function initializeLiff() {
            try {
                await liff.init({
                    liffId: liff_id,
                });

                isLiffInitialized = true;
                console.log("LIFF initialized successfully");

                if (!liff.isLoggedIn()) {
                    console.log("User not logged in, redirecting to login...");
                    showStatus("กำลังเข้าสู่ระบบ LINE...", "info");
                    liff.login();
                } else {
                    try {
                        userProfile = await liff.getProfile();
                        userId = userProfile.userId;
                        // console.log("User profile:", userProfile);
                        // showStatus(
                        //     `สวัสดี ${userProfile.displayName}! พร้อมใช้งานแล้ว`,
                        //     "success"
                        // );
                    } catch (profileError) {
                        // console.error("getProfile error:", profileError);
                        showStatus("ไม่สามารถดึงข้อมูลโปรไฟล์ได้", "error");
                    }
                }
            } catch (error) {
                console.error("LIFF initialization failed:", error);
                showStatus(
                    "ไม่สามารถเชื่อมต่อ LINE LIFF ได้: " + error.message,
                    "error"
                );
            }
        }

        function showStatus(message, type) {
            const statusDiv = document.getElementById("statusDiv");
            statusDiv.className = `status ${type}`;
            statusDiv.innerHTML = message;
            statusDiv.style.display = "block";
        }

        function getBrowserLocation() {
            return new Promise((resolve, reject) => {
                if (!navigator.geolocation) {
                    reject(new Error("เบราว์เซอร์ของคุณไม่รองรับการขอตำแหน่งที่อยู่"));
                    return;
                }

                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        resolve({
                            latitude: position.coords.latitude,
                            longitude: position.coords.longitude,
                            accuracy: position.coords.accuracy,
                        });
                    },
                    (error) => {
                        let errorMessage = "";
                        switch (error.code) {
                            case error.PERMISSION_DENIED:
                                errorMessage = "คุณได้ปฏิเสธการแชร์ตำแหน่งที่อยู่";
                                break;
                            case error.POSITION_UNAVAILABLE:
                                errorMessage = "ไม่สามารถหาตำแหน่งที่อยู่ได้";
                                break;
                            case error.TIMEOUT:
                                errorMessage = "หมดเวลาในการค้นหาตำแหน่งที่อยู่";
                                break;
                            default:
                                errorMessage = "เกิดข้อผิดพลาดในการหาตำแหน่งที่อยู่";
                                break;
                        }
                        reject(new Error(errorMessage));
                    }, {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 60000,
                    }
                );
            });
        }

        async function handleLocationRequest() {
            const btn = document.getElementById("getLocationBtn");
            btn.disabled = true;
            btn.innerHTML = '<div class="loading"></div> กำลังขอตำแหน่ง...';

            try {
                let location;

                if (isLiffInitialized && liff.isApiAvailable("shareTargetPicker")) {
                    try {
                        location = await getBrowserLocation();
                    } catch (liffError) {
                        console.log(
                            "LIFF location failed, trying browser location:",
                            liffError
                        );
                        location = await getBrowserLocation();
                    }
                } else {
                    location = await getBrowserLocation();
                }

                const locationInfo = `
                    <div class="location-info">
                        <strong>ตำแหน่งที่อยู่ของคุณ:</strong><br>
                        ${
                          userProfile
                            ? `ผู้ใช้: ${userProfile.displayName}<br>`
                            : ""
                        }
                        ละติจูด: ${location.latitude.toFixed(6)}<br>
                        ลองจิจูด: ${location.longitude.toFixed(6)}<br>
                        ความแม่นยำ: ${Math.round(location.accuracy)} เมตร<br>
                        <br>
                        <a href="https://maps.google.com/?q=${
                          location.latitude
                        },${
            location.longitude
          }" target="_blank" style="color: #007e33; text-decoration: underline;">
                            ดูใน Google Maps
                        </a>
                    </div>
                `;

                // showStatus("ได้ตำแหน่งที่อยู่แล้ว!" + locationInfo, "success");

                const url =
                    `https://nominatim.openstreetmap.org/reverse?lat=${location.latitude}&lon=${location.longitude}&format=json&accept-language=th`;

                const r = await fetch(url);
                const data = await r.json();
                let province = data.address.province || "";
                province = province.replace("จังหวัด", "").trim();

                const locationData = {
                    userId: userId,
                    userProfile: userProfile,
                    latitude: location.latitude,
                    longitude: location.longitude,
                    location: location,
                    timestamp: new Date().toISOString(),
                    province: province,
                };

                // console.log('Location data with user info:', locationData);
                await sendLocationToServer(locationData);
            } catch (error) {
                // console.error('Location error:', error);
                showStatus(error.message, "error");
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
                const response = await fetch("/api/line-location", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                    },
                    body: JSON.stringify(locationData),
                });

                const result = await response.json();

                if (response.ok) {
                    console.log("Location sent to server successfully:", result);
                    liff.closeWindow();
                } else {
                    console.error("Failed to send location to server:", result);
                }
            } catch (error) {
                console.error("Error sending location to server:", error);
            }
        }

        document
            .getElementById("getLocationBtn")
            .addEventListener("click", handleLocationRequest);

        window.addEventListener("load", () => {
            if (typeof liff !== "undefined") {
                initializeLiff();
            } else {
                showStatus("กำลังใช้งานในโหมดปกติ (ไม่ใช่ LINE LIFF)", "info");
            }
        });
    </script>
</body>

</html>
