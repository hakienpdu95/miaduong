function popupwindow(name, url) {
    var left = screen.width / 2 - 400;
    var top = screen.height / 2 - 300;
    window.open(url, name, "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=800, height=600, top=" + top + ", left=" + left).focus();
}

document.addEventListener('DOMContentLoaded', function() {
    // Check nếu window.socialConfig tồn tại (fallback nếu không inject)
    if (!window.socialConfig) {
        console.log('Social config not loaded. Check Blade injection.');
        return;
    }

    document.querySelectorAll('.social-login a').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            if (link.classList.contains('login-fb')) {
                if (window.socialConfig.facebook) {
                    popupwindow("Đăng nhập Facebook", window.socialConfig.facebook.redirect);
                } else {
                    console.error('Facebook config missing.');
                }
            } else if (link.classList.contains('login-zl')) {
                if (window.socialConfig.zalo) {
                    popupwindow("Đăng nhập Zalo", window.socialConfig.zalo.redirect);
                } else {
                    console.error('Zalo config missing.');
                }
            } else if (link.classList.contains('login-gg')) {
                if (window.socialConfig.google) {
                    popupwindow("Đăng nhập Google", window.socialConfig.google.redirect);
                } else {
                    console.error('Google config missing.');
                }
            }
        });
    });
});