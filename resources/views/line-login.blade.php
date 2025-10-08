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
        z-index:9;
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
        min-height: 42px;
        transform: translateZ(0);
    }

    .icon-right {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        right: 0;
        display: flex;
        align-items: center;
        padding-right: 14px;
        z-index:9;
        cursor:pointer;
    }

    .button {
        background-color: #9b237b;
        color: white;
        padding: 10px;
        width: 100%;
        border: 1px solid #9b237b;
        border-radius: 6px;
    }

    .password-mask {
        -webkit-text-security: disc;
        -moz-webkit-text-security: disc;
        -moz-text-security: disc;
    }

    /*.icon-eye-toggle svg:nth-child(2) {
        display: none;
    }

    .icon-eye-toggle.show svg:nth-child(1) {
        display: none;
    }

    .icon-eye-toggle.show svg:nth-child(2) {
        display: inline;
    }*/

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
                <img src="{{ asset(env('APP_URL')) }}./Logo/5.png" class="w-13" />
                <div class="">
                    <div class="">องค์การบริหารส่วนจังหวัดเชียงราย</div>
                    <div class="">Chiangrai Provincial Administrative Organization</div>
                </div>
            </div>
            {{-- <small class="small" id="error_tag" style="color: red">error message here</small> --}}
            <div class="alert" id="alertBox">ขออภัย โปรดกรอกข้อมูลให้ครบ</div>
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
                <input class="input" id="username" placeholder="ชื่อผู้ใช้งาน" autocomplete="on" />
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
                <input class="input password-mask" id="password" type="text" placeholder="รหัสผ่าน" autocomplete="on" />
                <div class="icon-right icon-eye-toggle" id="icon_eye">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="18" viewBox="0 0 20 18"
                        fill="none" style="display: inline;">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M18.5303 1.53033C18.8232 1.23744 18.8232 0.762563 18.5303 0.46967C18.2374 0.176777 17.7626 0.176777 17.4697 0.46967L1.46967 16.4697C1.17678 16.7626 1.17678 17.2374 1.46967 17.5303C1.76256 17.8232 2.23744 17.8232 2.53033 17.5303L5.56535 14.4953C6.88317 15.1032 8.39185 15.5 10 15.5C12.6178 15.5 14.972 14.4486 16.6684 13.1466C17.5177 12.4948 18.2146 11.7709 18.7035 11.0663C19.1831 10.3752 19.5 9.64847 19.5 9C19.5 8.35153 19.1831 7.62481 18.7035 6.93368C18.2146 6.22913 17.5177 5.50519 16.6684 4.85335C16.3978 4.64567 16.1104 4.44437 15.808 4.25269L18.5303 1.53033ZM13.1287 6.93196L12.0307 8.02994C12.1713 8.32366 12.25 8.65264 12.25 9C12.25 10.2426 11.2426 11.25 10 11.25C9.65264 11.25 9.32366 11.1713 9.02994 11.0307L7.93196 12.1287C8.52478 12.5213 9.23569 12.75 10 12.75C12.0711 12.75 13.75 11.0711 13.75 9C13.75 8.23569 13.5213 7.52478 13.1287 6.93196Z"
                            fill="#555055"></path>
                        <path
                            d="M10.6692 5.30953C10.753 5.32462 10.8395 5.2998 10.8997 5.2396L12.8493 3.29007C12.9816 3.15772 12.9267 2.93281 12.7464 2.88252C11.879 2.64055 10.9571 2.5 10 2.5C7.38223 2.5 5.02801 3.55139 3.33162 4.85335C2.48232 5.50519 1.78544 6.22913 1.29649 6.93368C0.816861 7.62481 0.5 8.35153 0.5 9C0.5 9.64847 0.816861 10.3752 1.29649 11.0663C1.7138 11.6676 2.28257 12.2831 2.96829 12.8558C3.06614 12.9375 3.20993 12.9294 3.30008 12.8393L6.2396 9.89974C6.2998 9.83954 6.32462 9.75296 6.30953 9.66917C6.27042 9.45203 6.25 9.2284 6.25 9C6.25 6.92893 7.92893 5.25 10 5.25C10.2284 5.25 10.452 5.27042 10.6692 5.30953Z"
                            fill="#555055"></path>
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="14" viewBox="0 0 20 14"
                        fill="none" style="display: none;">
                        <path
                            d="M10 4.75C8.75736 4.75 7.75 5.75736 7.75 7C7.75 8.24264 8.75736 9.25 10 9.25C11.2426 9.25 12.25 8.24264 12.25 7C12.25 5.75736 11.2426 4.75 10 4.75Z"
                            fill="#555055"></path>
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M10 0.5C7.38223 0.5 5.02801 1.55139 3.33162 2.85335C2.48232 3.50519 1.78544 4.22913 1.29649 4.93368C0.816861 5.62481 0.5 6.35153 0.5 7C0.5 7.64847 0.816861 8.37519 1.29649 9.06632C1.78544 9.77087 2.48232 10.4948 3.33162 11.1466C5.02801 12.4486 7.38223 13.5 10 13.5C12.6178 13.5 14.972 12.4486 16.6684 11.1466C17.5177 10.4948 18.2146 9.77087 18.7035 9.06632C19.1831 8.37519 19.5 7.64847 19.5 7C19.5 6.35153 19.1831 5.62481 18.7035 4.93368C18.2146 4.22913 17.5177 3.50519 16.6684 2.85335C14.972 1.55139 12.6178 0.5 10 0.5ZM6.25 7C6.25 4.92893 7.92893 3.25 10 3.25C12.0711 3.25 13.75 4.92893 13.75 7C13.75 9.07107 12.0711 10.75 10 10.75C7.92893 10.75 6.25 9.07107 6.25 7Z"
                            fill="#555055"></path>
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
        {{-- <p class="text-center">
            <strong>LINE User ID:</strong> <span id="userId">กำลังโหลด...</span>
        </p> --}}
    </form>
</body>

</html>
<script>
    let userId = null;
    const alertBox = document.getElementById("alertBox");
    alertBox.style.display = "none";

    // liff.init({
    //         // liffId: "2007547242-Rmgq4X9x",
    //         liffId: "2007553661-G93edr6l",
    //     })
    //     .then(() => {
    //         if (!liff.isLoggedIn()) {
    //             liff.login();
    //         } else {
    //             liff.getProfile()
    //                 .then((profile) => {
    //                     userId = profile.userId;
    //                     // document.getElementById("userId").innerText = userId;
    //                 })
    //                 .catch((err) => {
    //                     console.error("getProfile error", err);
    //                     // document.getElementById("userId").innerText =
    //                     //     "Error loading user ID";
    //                 });
    //         }
    //     });
    //

    document.addEventListener("DOMContentLoaded", function() {
        const passwordInput = document.getElementById("password");
        const iconEye = document.getElementById("icon_eye");
        showPassword = false;

        if (passwordInput && iconEye) {
            iconEye.addEventListener("click", function() {
                showPassword = !showPassword;
                iconEye.children[0].style.display = showPassword ? 'none' : 'inline';
                iconEye.children[1].style.display = showPassword ? 'inline' : 'none';
                passwordInput.classList.toggle('password-mask');
                // console.log("icon clicked", iconEye.children)
                // const isHidden = passwordInput.type === "password";
                console.log(passwordInput)
                iconEye.classList.toggle("show", showPassword);
            });
        }
    });

    document.getElementById("loginForm").addEventListener("submit", function(e) {
        e.preventDefault();

        const username = document.getElementById("username").value;
        const password = document.getElementById("password").value;

        if (!username || !password) {
            if (alertBox) {
                alertBox.innerText = "ขออภัย โปรดกรอกข้อมูลให้ครบ";
                alertBox.style.display = "block";
            }
            return;
        }

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
                    // alert(data.message || "เข้าสู่ระบบไม่สำเร็จ");
                    if (data.message == 'ready') {
                        alertBox.innerText =
                            "เชื่อมต่อได้ 1 เครื่อง กรุณากด บล็อคที่เครื่องเดิมก่อน";
                    } else {
                        alertBox.innerText =
                            "ขออภัย ชื่อผู้ใช้งาน หรือรหัสผ่าน ไม่ถูกต้อง โปรดตรวจสอบอีกครั้ง";

                    }
                    alertBox.style.display = "block";
                }
            })
            .catch((error) => {
                alertBox.innerText = "เกิดข้อผิดพลาดในการเชื่อมต่อ";
                alertBox.style.display = "block";
                // alert("เกิดข้อผิดพลาดในการเชื่อมต่อ");
            });
    });
</script>
