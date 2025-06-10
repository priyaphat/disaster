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
        padding-left: 48px;
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
        /* pointer-events: none; */
    }

    .button {
        background-color: #9b237b;
        color: white;
        padding: 10px;
        width: 100%;
        border: 1px solid #9b237b;
        border-radius: 6px;
    }

    img {
        width: 45;
        height: auto;
    }
</style>

<body>
    <form class="form" id="loginForm">
        <div class="wrapper">
            <div class="banner">
                <img src="{{ asset(env('APP_URL')) }}./Logo/5.png" class="w-13" />
                <div class="">
                    <div class="">องค์การบริหารส่วนจังหวัดเชียงราย</div>
                    <div class="">Chiangrai Provincial Administrative Organization</div>
                </div>
            </div>
            {{-- <small class="small" id="error_tag" style="color: red">error message here</small> --}}
            <div class="relative">
                <div class="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 100 100"
                        fill="#B7B2B5">
                        <path
                            d="M49.9999 15.625C41.3705 15.625 34.3749 22.6206 34.3749 31.25C34.3749 39.8794 41.3705 46.875 49.9999 46.875C58.6294 46.875 65.6249 39.8794 65.6249 31.25C65.6249 22.6206 58.6294 15.625 49.9999 15.625Z"
                            fill="#B7B2B5" />
                        <path
                            d="M33.3333 55.2083C24.7038 55.2083 17.7083 62.2039 17.7083 70.8333V75.7845C17.7083 78.923 19.9828 81.5989 23.0802 82.1047C40.9087 85.0154 59.0912 85.0154 76.9196 82.1047C80.0171 81.5989 82.2916 78.923 82.2916 75.7845V70.8333C82.2916 62.2039 75.296 55.2083 66.6666 55.2083H65.2463C64.4776 55.2083 63.7137 55.3299 62.9829 55.5685L59.3766 56.7461C53.2838 58.7356 46.716 58.7356 40.6232 56.7461L37.0169 55.5685C36.2861 55.3299 35.5223 55.2083 34.7535 55.2083H33.3333Z"
                            fill="#B7B2B5" />
                    </svg>
                </div>
                <input class="input" id="username" required placeholder="ชื่อผู้ใช้งาน" autocomplete="on" />
            </div>
            <div class="relative">
                <div class="icon password-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M7.30627 7.75828L7.64933 10.8458L6.95511 10.9013C5.98701 10.9785 5.19651 11.7063 5.03958 12.6647C4.67789 14.8736 4.67789 17.1266 5.03958 19.3355C5.19651 20.2939 5.98701 21.0217 6.95511 21.0989L8.45124 21.2184C10.8134 21.407 13.1868 21.407 15.5489 21.2184L17.0451 21.0989C18.0132 21.0217 18.8037 20.2939 18.9606 19.3355C19.3223 17.1266 19.3223 14.8736 18.9606 12.6647C18.8037 11.7063 18.0132 10.9785 17.0451 10.9013L16.3507 10.8458L16.6938 7.75828C16.7342 7.3943 16.7342 7.02696 16.6938 6.66298L16.671 6.45809C16.4295 4.2847 14.7209 2.56504 12.5492 2.30953C12.1843 2.26661 11.8157 2.26661 11.4509 2.30953C9.27913 2.56504 7.57053 4.2847 7.32904 6.45809L7.30627 6.66298C7.26583 7.02696 7.26583 7.3943 7.30627 7.75828ZM12.3739 3.79926C12.1255 3.77004 11.8746 3.77004 11.6262 3.79926C10.1475 3.97322 8.98428 5.14402 8.81986 6.62373L8.7971 6.82863C8.76889 7.08252 8.76889 7.33875 8.7971 7.59263L9.1459 10.7318C11.0467 10.6099 12.9534 10.6099 14.8542 10.7318L15.203 7.59263C15.2312 7.33874 15.2312 7.08252 15.203 6.82863L15.1802 6.62373C15.0158 5.14402 13.8525 3.97322 12.3739 3.79926ZM12.0001 14.5001C11.1716 14.5001 10.5001 15.1717 10.5001 16.0001C10.5001 16.8285 11.1716 17.5001 12.0001 17.5001C12.8285 17.5001 13.5001 16.8285 13.5001 16.0001C13.5001 15.1717 12.8285 14.5001 12.0001 14.5001Z"
                            fill="#B7B2B5" />
                    </svg>
                </div>
                <input class="input" id="password" type="password" required placeholder="รหัสผ่าน"
                    autocomplete="on" />
                <div class="icon-right" id="icon_eye">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M20.5303 4.53033C20.8232 4.23744 20.8232 3.76256 20.5303 3.46967C20.2374 3.17678 19.7626 3.17678 19.4697 3.46967L3.46967 19.4697C3.17678 19.7626 3.17678 20.2374 3.46967 20.5303C3.76256 20.8232 4.23744 20.8232 4.53033 20.5303L7.56535 17.4953C8.88317 18.1032 10.3919 18.5 12 18.5C14.6178 18.5 16.972 17.4486 18.6684 16.1466C19.5177 15.4948 20.2146 14.7709 20.7035 14.0663C21.1831 13.3752 21.5 12.6485 21.5 12C21.5 11.3515 21.1831 10.6248 20.7035 9.93368C20.2146 9.22913 19.5177 8.50519 18.6684 7.85335C18.3978 7.64567 18.1104 7.44437 17.808 7.25269L20.5303 4.53033ZM15.1287 9.93196L14.0307 11.0299C14.1713 11.3237 14.25 11.6526 14.25 12C14.25 13.2426 13.2426 14.25 12 14.25C11.6526 14.25 11.3237 14.1713 11.0299 14.0307L9.93196 15.1287C10.5248 15.5213 11.2357 15.75 12 15.75C14.0711 15.75 15.75 14.0711 15.75 12C15.75 11.2357 15.5213 10.5248 15.1287 9.93196Z"
                            fill="#555055" />
                        <path
                            d="M12.6692 8.30953C12.753 8.32462 12.8395 8.2998 12.8997 8.2396L14.8493 6.29007C14.9816 6.15772 14.9267 5.93281 14.7464 5.88252C13.879 5.64055 12.9571 5.5 12 5.5C9.38223 5.5 7.02801 6.55139 5.33162 7.85335C4.48232 8.50519 3.78544 9.22913 3.29649 9.93368C2.81686 10.6248 2.5 11.3515 2.5 12C2.5 12.6485 2.81686 13.3752 3.29649 14.0663C3.7138 14.6676 4.28257 15.2831 4.96829 15.8558C5.06614 15.9375 5.20993 15.9294 5.30008 15.8393L8.2396 12.8997C8.2998 12.8395 8.32462 12.753 8.30953 12.6692C8.27042 12.452 8.25 12.2284 8.25 12C8.25 9.92893 9.92893 8.25 12 8.25C12.2284 8.25 12.452 8.27042 12.6692 8.30953Z"
                            fill="#555055" />
                    </svg>
                </div>
            </div>
            <button type="submit" class="button">เข้าสู่ระบบ</button>
            <footer class="banner">
                <img src="{{ asset(env('APP_URL')) }}./Logo/5.png" width="40" height="40" />
                <img src="{{ asset(env('APP_URL')) }}./Logo/6.png" width="40" height="40" />
                <img src="{{ asset(env('APP_URL')) }}./Logo/1.png" width="40" height="40" />
                <img src="{{ asset(env('APP_URL')) }}./Logo/2.png" width="40" height="40" />
                <img src="{{ asset(env('APP_URL')) }}./Logo/3.png" width="40" height="40" />
                <img src="{{ asset(env('APP_URL')) }}./Logo/4.png" width="40" height="40" />
            </footer>
        </div>
        <p class="text-center">
            <strong>LINE User ID:</strong> <span id="userId">กำลังโหลด...</span>
        </p>
    </form>
</body>

</html>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const passwordInput = document.getElementById("password");
        const iconEye = document.getElementById("icon_eye");

        if (passwordInput && iconEye) {
            iconEye.addEventListener("click", function() {
                const isHidden = passwordInput.type === "password";
                passwordInput.type = isHidden ? "text" : "password";
                iconEye.classList.toggle("show", isHidden);
            });
        }
    });

    let userId = null;

    liff.init({
            liffId: "2007547242-Rmgq4X9x",
        })
        .then(() => {
            if (!liff.isLoggedIn()) {
                liff.login();
            } else {
                liff.getProfile()
                    .then((profile) => {
                        userId = profile.userId;
                        document.getElementById("userId").innerText = userId;
                    })
                    .catch((err) => {
                        console.error("getProfile error", err);
                        document.getElementById("userId").innerText =
                            "Error loading user ID";
                    });
            }
        });

    document.getElementById("loginForm").addEventListener("submit", function(e) {
        e.preventDefault();

        const username = document.getElementById("username").value;
        const password = document.getElementById("password").value;

        fetch("/api/line-login", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    username,
                    password,
                    userId,
                }),
            })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    liff.closeWindow();
                } else {
                    // document.getElementById("error_tag").innerText =
                    //     "Error loading user ID";
                    alert(data.message || "เข้าสู่ระบบไม่สำเร็จ");
                }
            })
            .catch((error) => {
                console.error("Login error:", error);
                alert("เกิดข้อผิดพลาดในการเชื่อมต่อ");
            });
    });

    // function unhiden() {
    //     const input = document.getElementById('password');
    //     if (input.type === 'password') {
    //         input.type = 'text';
    //     } else {
    //         input.type = 'password';
    //     }
    // }
</script>
