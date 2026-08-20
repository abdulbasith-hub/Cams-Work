(function () {
    "use strict";

    function setRootFontSize(size) {
        document.documentElement.style.fontSize = size + "px";
        window.localStorage.setItem("camsFontSize", String(size));
    }

    function initAccessibilityTools() {
        var sizes = [14, 15, 16, 17, 18];
        var savedSize = parseInt(window.localStorage.getItem("camsFontSize") || "16", 10);

        if (sizes.indexOf(savedSize) === -1) {
            savedSize = 16;
        }

        setRootFontSize(savedSize);

        $(".increase").on("click", function () {
            var current = parseInt(window.localStorage.getItem("camsFontSize") || "16", 10);
            var index = sizes.indexOf(current);
            setRootFontSize(sizes[Math.min(index + 1, sizes.length - 1)]);
        });

        $(".decrease").on("click", function () {
            var current = parseInt(window.localStorage.getItem("camsFontSize") || "16", 10);
            var index = sizes.indexOf(current);
            setRootFontSize(sizes[Math.max(index - 1, 0)]);
        });

        $(".resetMe").on("click", function () {
            setRootFontSize(16);
        });

    }

    function setLanguage(value, triggerChange) {
        var language = value === "ta" ? "ta" : "en";
        var label = language === "ta" ? "Tamil" : "English";

        $("#translate").val(language);
        $("#camsLanguageLabel").text(label);

        $(".cams-language-menu .dropdown-item")
            .removeClass("is-active")
            .attr("aria-pressed", "false");

        $('.cams-language-menu .dropdown-item[data-lang-value="' + language + '"]')
            .addClass("is-active")
            .attr("aria-pressed", "true");

        window.localStorage.setItem("lang", language);

        if (triggerChange) {
            $("#translate").trigger("change");
        }
    }

    function initLanguageDropdown() {
        var savedLanguage = window.localStorage.getItem("lang") || $("#translate").val() || "en";

        setLanguage(savedLanguage, false);

        $(".cams-language-menu .dropdown-item").on("click", function () {
            setLanguage($(this).data("lang-value") || "en", true);
        });

        $("#translate").on("change", function () {
            setLanguage(this.value || "en", false);
        });
    }

    function applyTheme(themeName) {
        var themes = {
            ocean: {
                navy: "#08213f",
                navy2: "#0f3158",
                navyRgb: "8, 33, 63",
                navy2Rgb: "15, 49, 88",
                ink: "#132238",
                muted: "#5d6c81",
                soft: "#f5f8fb",
                header: "linear-gradient(90deg, #08213f 0%, #0b3b68 52%, #0d6b73 100%)",
                action: "linear-gradient(135deg, #0d9488 0%, #0f5f76 48%, #08213f 100%)",
                softGradient: "linear-gradient(135deg, #eefbfa 0%, #f7fbff 54%, #fff8e8 100%)",
                teal: "#0d9488",
                tealDark: "#08776e",
                tealRgb: "13, 148, 136",
                tealSoft: "#a7f3d0",
                gold: "#c99a2e",
                goldRgb: "201, 154, 46",
                goldSoft: "#fff6dc",
                footer: "#071a33"
            },
            emerald: {
                navy: "#073b3a",
                navy2: "#0f766e",
                navyRgb: "7, 59, 58",
                navy2Rgb: "15, 118, 110",
                ink: "#102f2c",
                muted: "#55716c",
                soft: "#f1fbf8",
                header: "linear-gradient(90deg, #073b3a 0%, #0f766e 52%, #c99a2e 100%)",
                action: "linear-gradient(135deg, #0f766e 0%, #0d9488 48%, #073b3a 100%)",
                softGradient: "linear-gradient(135deg, #e7fbf7 0%, #f7fbff 54%, #fff7df 100%)",
                teal: "#0f766e",
                tealDark: "#075f58",
                tealRgb: "15, 118, 110",
                tealSoft: "#b7f3e8",
                gold: "#c99a2e",
                goldRgb: "201, 154, 46",
                goldSoft: "#fff7df",
                footer: "#062b2b"
            },
            royal: {
                navy: "#06163a",
                navy2: "#123e78",
                navyRgb: "6, 22, 58",
                navy2Rgb: "18, 62, 120",
                ink: "#14233d",
                muted: "#5d6881",
                soft: "#f4f7ff",
                header: "linear-gradient(90deg, #06163a 0%, #123e78 52%, #a97913 100%)",
                action: "linear-gradient(135deg, #123e78 0%, #0d6b73 48%, #06163a 100%)",
                softGradient: "linear-gradient(135deg, #eef4ff 0%, #f8fbff 54%, #fff6dd 100%)",
                teal: "#0d6b73",
                tealDark: "#0b5960",
                tealRgb: "13, 107, 115",
                tealSoft: "#b6e5ea",
                gold: "#a97913",
                goldRgb: "169, 121, 19",
                goldSoft: "#fff6dd",
                footer: "#06163a"
            }
        };

        var selectedThemeName = themes[themeName] ? themeName : "ocean";
        var selected = themes[selectedThemeName];
        var root = document.documentElement;

        root.style.setProperty("--cams-navy", selected.navy);
        root.style.setProperty("--cams-navy-2", selected.navy2);
        root.style.setProperty("--cams-navy-rgb", selected.navyRgb);
        root.style.setProperty("--cams-navy-2-rgb", selected.navy2Rgb);
        root.style.setProperty("--cams-ink", selected.ink);
        root.style.setProperty("--cams-muted", selected.muted);
        root.style.setProperty("--cams-soft", selected.soft);
        root.style.setProperty("--cams-header-gradient", selected.header);
        root.style.setProperty("--cams-action-gradient", selected.action);
        root.style.setProperty("--cams-soft-gradient", selected.softGradient);
        root.style.setProperty("--cams-teal", selected.teal);
        root.style.setProperty("--cams-teal-dark", selected.tealDark);
        root.style.setProperty("--cams-teal-rgb", selected.tealRgb);
        root.style.setProperty("--cams-teal-soft", selected.tealSoft);
        root.style.setProperty("--cams-gold", selected.gold);
        root.style.setProperty("--cams-gold-rgb", selected.goldRgb);
        root.style.setProperty("--cams-gold-soft", selected.goldSoft);
        root.style.setProperty("--cams-footer-bg", selected.footer);

        $(".cams-theme-swatch")
            .removeClass("is-active")
            .attr("aria-pressed", "false");

        $('.cams-theme-swatch[data-cams-theme="' + selectedThemeName + '"]')
            .addClass("is-active")
            .attr("aria-pressed", "true");

        window.localStorage.setItem("camsTheme", selectedThemeName);
    }

    function initThemePicker() {
        var savedTheme = window.localStorage.getItem("camsTheme") || "ocean";
        applyTheme(savedTheme);

        $(".cams-theme-swatch").on("click", function () {
            applyTheme($(this).data("cams-theme") || "ocean");
        });
    }

    function initBackToTop() {
        var button = $(".cams-back-to-top");

        if (!button.length) {
            return;
        }

        function toggleButton() {
            button.toggleClass("is-visible", $(window).scrollTop() > 360);
        }

        toggleButton();

        $(window).on("scroll", toggleButton);

        button.on("click", function () {
            $("html, body").animate({ scrollTop: 0 }, 450);
        });
    }

    function showError(message) {
        var text = message || "Unable to process login. Please try again.";
        $("#display_error")
            .html('<i class="fas fa-exclamation-circle" aria-hidden="true"></i><span>' + text + "</span>")
            .removeClass("fade hide_this")
            .addClass("show is-error");
    }

    function clearLoginError() {
        var box = $("#display_error");
        var message = box.data("default-message") || "For security, keep your credentials confidential and complete OTP verification when prompted.";

        box.html('<i class="fas fa-info-circle" aria-hidden="true"></i><span>' + message + "</span>")
            .removeClass("show is-error fade hide_this");
    }

    function loginConfig() {
        return window.CAMS_PUBLIC_LOGIN || null;
    }

    function activeRoleConfig() {
        var config = loginConfig();
        if (!config) {
            return null;
        }
        return config.roles[config.currentRole] || config.roles.auditor;
    }

    function setLoginRole(role) {
        var config = loginConfig();
        if (!config || !config.roles[role]) {
            return;
        }

        config.currentRole = role;
        var roleConfig = activeRoleConfig();

        $(".login-role-link").removeClass("active").attr("aria-pressed", "false");
        $('.login-role-link[data-login-role="' + role + '"]').addClass("active").attr("aria-pressed", "true");
        $(".cams-login-copy-item").removeClass("is-active").attr("aria-hidden", "true");
        $('.cams-login-copy-item[data-login-panel="' + role + '"]').addClass("is-active").attr("aria-hidden", "false");
        $(".login-card-header h3").text(roleConfig.title);
        $(".login-card-header p").text(roleConfig.copy);
        $(".cams-forgot-link").attr("href", roleConfig.forgotUrl);
        $("#username, #password, #captcha").val("");
        clearLoginError();
        refreshCaptcha();
    }

    window.refreshCaptcha = function () {
        var box = document.getElementById("captcha-box");

        if (!box) {
            return;
        }

        fetch("/captcha-text", {
            headers: { "Accept": "application/json" }
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                box.innerText = data.code || "";
                $("#captcha").val("");
            })
            .catch(function () {
                box.innerText = "Reload";
            });
    };

    function showLoader() {
        $("#ajax-loader").css("display", "flex");
    }

    function hideLoader() {
        $("#ajax-loader").hide();
    }

    window.loginuser = async function () {
        var roleConfig = activeRoleConfig();

        if (!roleConfig) {
            return;
        }

        var encryptedPassword = await encryptPassword($("#password").val());

        $.ajax({
            url: roleConfig.loginUrl,
            type: "POST",
            data: {
                username: $("#username").val(),
                encryptedPassword: encryptedPassword,
                captcha: $("#captcha").val()
            },
            success: function (response) {
                if (response.success && response.redirect_url) {
                    window.location.href = response.redirect_url;
                    return;
                }

                if (response.status === "success" && response.message === "OTP has been sent successfully.") {
                    showLoginOtpModal();
                    return;
                }

                showError(response.message || "Login failed.");
                refreshCaptcha();
            },
            error: function (xhr) {
                var errors = xhr.responseJSON && xhr.responseJSON.errors;
                var message = "";

                if (errors) {
                    $.each(errors, function (_key, value) {
                        if (value === "validation.captcha" || (Array.isArray(value) && value.indexOf("validation.captcha") !== -1)) {
                            message += "Invalid Captcha!";
                            $("#captcha").val("");
                            return;
                        }

                        message += Array.isArray(value) ? value.join("<br>") : value + "<br>";
                    });
                } else {
                    message = (xhr.responseJSON && xhr.responseJSON.message) || "Login failed. Please verify the details and try again.";
                }

                showError(message);
                refreshCaptcha();
            }
        });
    };

    window.showLoginOtpModal = function () {
        var otpContent =
            '<div id="otp_div" class="cams-otp-box">' +
            '<h5 class="text-center mb-2"><b>Verify Your OTP</b></h5>' +
            '<span class="text-center mb-3 d-block">Enter the 6-digit verification code sent to your mobile number.</span>' +
            '<div class="d-flex justify-content-center gap-2 mb-3" id="otp-box-wrapper">' +
            '<input type="text" class="form-control text-center otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*">' +
            '<input type="text" class="form-control text-center otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*">' +
            '<input type="text" class="form-control text-center otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*">' +
            '<input type="text" class="form-control text-center otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*">' +
            '<input type="text" class="form-control text-center otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*">' +
            '<input type="text" class="form-control text-center otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*">' +
            '</div>' +
            '<div id="otp_error" class="text-danger mt-1 mb-2 text-center" style="font-size:12px;"></div>' +
            '<button type="button" id="verify_otp_button_login" class="btn btn-primary w-100">Verify OTP</button>' +
            '<small class="text-center mt-3 d-block">Did not receive the code? <b id="resend_otp_link_login" style="color:var(--cams-teal);cursor:pointer;">Resend OTP</b></small>' +
            '</div>';

        $("#confirmation_alert .modal-footer").hide();
        $("#process_button").html("Verify OTP");

        passing_alert_value("Verify OTP", otpContent, "confirmation_alert", "alert_header", "alert_body", "forward_alert");
        $("#confirmation_alert").modal("show");

        var timer = 30;
        var resend = $("#resend_otp_link_login");
        resend.css({ "pointer-events": "none", "opacity": "0.5" }).text("Resend OTP in (30)");

        var interval = setInterval(function () {
            timer--;
            resend.text("Resend OTP (" + timer + ")");
            if (timer <= 0) {
                clearInterval(interval);
                resend.css({ "pointer-events": "auto", "opacity": "1" }).text("Resend OTP");
            }
        }, 1000);
    };

    function initLoginPage() {
        if (!$("#login-form").length) {
            return;
        }

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            }
        });

        refreshCaptcha();

        $(".login-role-link").on("click", function (event) {
            event.preventDefault();
            setLoginRole($(this).data("login-role") || "auditor");
        });

        $("#login-form").validate({
            rules: {
                username: { required: true },
                password: { required: true },
                captcha: { required: true }
            },
            messages: {
                username: { required: "Enter username" },
                password: { required: "Enter password" },
                captcha: { required: "Enter Captcha" }
            },
            submitHandler: function () {
                loginuser();
            }
        });

        $(document).on("input", "#username, #password, #captcha", clearLoginError);

        $(document).on("click", ".toggle-password", function () {
            var target = $($(this).data("target"));
            var icon = $(this).find("i");
            var isPassword = target.attr("type") === "password";
            target.attr("type", isPassword ? "text" : "password");
            icon.toggleClass("fa-eye", !isPassword).toggleClass("fa-eye-slash", isPassword);
            $(this).attr("aria-label", isPassword ? "Hide password" : "Show password");
        });

        $(document).on("input", ".otp-input", function () {
            var value = this.value.replace(/\D/g, "");
            this.value = value;
            if (value.length === 1) {
                $(this).next(".otp-input").focus();
            }
        });

        $(document).on("keydown", ".otp-input", function (event) {
            if (event.key === "Backspace" && $(this).val() === "") {
                $(this).prev(".otp-input").focus();
            }
        });

        $(document).on("paste", ".otp-input", function (event) {
            var pasted = (event.originalEvent || event).clipboardData.getData("text") || "";
            if (!/^\d+$/.test(pasted)) {
                event.preventDefault();
                return;
            }

            var digits = pasted.replace(/\D/g, "").slice(0, 6).split("");
            $(".otp-input").each(function (index) {
                $(this).val(digits[index] || "");
            });
            event.preventDefault();
            $(".otp-input").eq(Math.min(digits.length, 5)).focus();
        });

        $(document).on("click", "#resend_otp_link_login", function (event) {
            event.preventDefault();
            $("#otp_error").hide();
            loginuser();
        });

        $(document).on("click", "#verify_otp_button_login", async function () {
            var roleConfig = activeRoleConfig();
            var otp = "";

            $(".otp-input").each(function () {
                otp += $(this).val();
            });

            if (!/^\d{6}$/.test(otp)) {
                $("#otp_error").html("Please enter a valid 6-digit OTP.").show();
                return;
            }

            var encryptedPassword = await encryptPassword($("#password").val());

            $.ajax({
                url: roleConfig.verifyUrl || (loginConfig() && loginConfig().defaultVerifyUrl),
                type: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content"),
                    username: $("#username").val(),
                    encryptedPassword: encryptedPassword,
                    otp: otp
                },
                success: function (response) {
                    if (response.success && response.redirect_url) {
                        window.location.href = response.redirect_url;
                    } else {
                        $("#otp_error").html(response.message || "Incorrect OTP.").show();
                    }
                },
                error: function (xhr) {
                    var message = "OTP verification failed. Please try again.";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    $("#otp_error").html(message).show();
                }
            });
        });
    }

    $(function () {
        initAccessibilityTools();
        initLanguageDropdown();
        initThemePicker();
        initBackToTop();
        initLoginPage();
        $(document).ajaxStart(showLoader);
        $(document).ajaxStop(hideLoader);
    });
})();
