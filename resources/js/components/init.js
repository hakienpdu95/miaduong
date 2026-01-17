import('bootstrap').then(({ default: bootstrap }) => {
    window.bootstrap = bootstrap;
});

// Hàm hỗ trợ định dạng số (thêm 0 nếu số < 10)
function getNumberStr(e) {
    return e < 10 ? "0" + e : e;
}

// Hàm lấy tên ngày trong tuần bằng tiếng Việt
function getWeekDay(e) {
    switch (e) {
        case 1:
            return "Thứ Hai";
        case 2:
            return "Thứ Ba";
        case 3:
            return "Thứ Tư";
        case 4:
            return "Thứ Năm";
        case 5:
            return "Thứ Sáu";
        case 6:
            return "Thứ Bảy";
        case 0:
            return "Chủ Nhật";
    }
}

// Hàm cập nhật ngày giờ động
function todayTime() {
    if ($("#todayTime").length > 0) {
        const today = new Date();
        const dayName = getWeekDay(today.getDay());
        const day = getNumberStr(today.getDate());
        const month = getNumberStr(today.getMonth() + 1); // Tháng bắt đầu từ 0
        const year = today.getFullYear();
        $("#todayTime").html(`${dayName}, ${day}/${month}/${year}`);
    }
}

$(document).ready(function() {
    var n, a, e;
    todayTime();

    // Back to Top
    if ($(".back-to-top").length) {
        var backToTop = $(".back-to-top");

        $(window).scroll(function() {
            if ($(window).scrollTop() > 800) {
                backToTop.addClass("show");
            } else {
                backToTop.removeClass("show");
            }
        });

        backToTop.on("click", function(e) {
            e.preventDefault();
            $("html, body").animate({ scrollTop: 0 }, 300);
        });
    }
});