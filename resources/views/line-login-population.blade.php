<html>
<meta charset="UTF-8" />
<title>เข้าสู่ระบบผ่าน LINE</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="csrf-token" content="{{ csrf_token() }}" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
<script src="https://static.line-scdn.net/liff/edge/2/sdk.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<style>
    .form {
        background-color: white;
        width: 100%;
        padding: 30px 50px;
        border-radius: 20px;
    }

    .relative {
        position: relative;
    }

    .banner {
        display: flex;
        align-items: center;
        margin-left: auto;
        margin-right: auto;
        gap: 10px;
        font-size: 12px;
    }

    .icon {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        left: 0;
        display: flex;
        align-items: center;
        padding-left: 14px;
        pointer-events: none;
    }

    .wrapper {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .input {
        width: 100%;
        font-size: 14px;
        text-overflow: ellipsis;
        padding: 12px;
        /* padding-left: 48px; */
        border: 1px solid #b7b2b5;
        border-radius: 6px;
    }

    .icon-right {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        right: 0;
        display: flex;
        align-items: center;
        padding-right: 14px;
    }

    .button {
        background-color: #9b237b;
        color: white;
        padding: 10px;
        width: 100%;
        border: 1px solid #9b237b;
        border-radius: 6px;
    }

    .icon-eye-toggle svg:nth-child(2) {
        display: none;
    }

    .icon-eye-toggle.show svg:nth-child(1) {
        display: none;
    }

    .icon-eye-toggle.show svg:nth-child(2) {
        display: inline;
    }

    input::-ms-reveal,
    input::-ms-clear {
        display: none;
    }

    input::-webkit-credentials-auto-fill-button {
        visibility: hidden;
        display: none !important;
        pointer-events: none;
        position: absolute;
        right: 0;
    }

    input[type="password"]::-webkit-textfield-decoration-container {
        display: none !important;
    }

    img {
        width: 45;
        height: auto;
    }

    .alert {
        text-align: center;
        background-color: #e74c3c;
        color: white;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 4px;
    }
</style>

<body>
    <form class="form" id="loginForm">
        <div class="wrapper">
            <div class="banner">
                <img src="{{ asset(env('APP_URL')) }}/Logo/7.png" class="w-13" />
                <div class="">
                    <div class="">ระบบภูมิสารสนเทศทางสาธารณสุข</div>
                    <div class="">สำหรับศูนย์ปฏิบัติการภาวะฉุกเฉิน (จีโอเฮลท์ ศปฉ.)</div>
                </div>
            </div>
            {{-- <small class="small" id="error_tag" style="color: red">error message here</small> --}}
            <div class="alert" id="alertBox">ขออภัย โปรดกรอกข้อมูลให้ครบ</div>

            <p style="font-size: 20px; margin-bottom: -15px;">กรอกเบอร์โทรศัพท์</p>
            <input class="input" id="phone" placeholder="เบอร์โทรศัพท์" maxlength="10" autocomplete="on" />
            <button type="submit" class="button">ยืนยันตัวตน</button>
            <footer class="banner">
                <img src="{{ asset(env('APP_URL')) }}/Logo/5.png" width="40" height="40" />
                <img src="{{ asset(env('APP_URL')) }}/Logo/6.png" width="40" height="40" />
                <img src="{{ asset(env('APP_URL')) }}/Logo/1.png" width="40" height="40" />
                <img src="{{ asset(env('APP_URL')) }}/Logo/2.png" width="40" height="40" />
                <img src="{{ asset(env('APP_URL')) }}/Logo/3.png" width="40" height="40" />
                <img src="{{ asset(env('APP_URL')) }}/Logo/4.png" width="40" height="40" />
            </footer>
        </div>
    </form>
</body>

</html>
<script>
    let userId = null;
    const alertBox = document.getElementById("alertBox");
    alertBox.style.display = "none";

    liff.init({
            liffId: "2007553661-9a0VMwxr",
        })
        .then(() => {
            if (!liff.isLoggedIn()) {
                liff.login();
            } else {
                liff.getProfile()
                    .then((profile) => {
                        userId = profile.userId;
                    })
                    .catch((err) => {
                        console.error("getProfile error", err);
                    });
            }
        });


    document.getElementById("loginForm").addEventListener("submit", function(e) {
        e.preventDefault();

        const phone = document.getElementById("phone").value;

        if (!phone) {
            if (alertBox) {
                alertBox.innerText = "ขออภัย โปรดกรอกข้อมูลให้ครบ";
                alertBox.style.display = "block";
            }
            return;
        }

        fetch("/api/line-login-population", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    phone,
                    userId,
                }),
            })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    liff.closeWindow();
                } else {
                    if (data.code == 422) {
                        alertBox.innerText = data.message;
                    } else {
                        alertBox.innerText = "ขออภัย ไม่พบเบอร์โทรศัพท์นี้ โปรดตรวจสอบอีกครั้ง";
                    }
                    alertBox.style.display = "block";
                }
            })
            .catch((error) => {
                alertBox.innerText = "เกิดข้อผิดพลาดในการเชื่อมต่อ";
                alertBox.style.display = "block";
            });
    });
</script>
