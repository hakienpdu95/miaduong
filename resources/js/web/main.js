import '../components/init.js';
import '../components/social-login.js';
import { reinitializeLazySizes } from '../lazysizes-config.js'; 

function getTitle(e) {
    e = e.toString();
    return e.includes('|') ? e.substr(0, e.lastIndexOf('|') - 1) : e;
}

function socialShare() {
    $(".sendmail").attr("href", "mailto:email@domain.com?subject=" + window.encodeURIComponent(document.title) + "&body=" + window.encodeURIComponent(window.location.toString()));
    
    $(".sendprint").click(function(e) {
        e.preventDefault();
        window.open("/print.html", "_blank", "height=600,width=1000,status=yes,location=no,menubar=no,resizable=yes,scrollbars=yes,titlebar=yes,top=50,left=100", true);
    });

    if ($(".article__source .getlink").length > 0) {
        $(".article__source .btn").on("click", function() {
            $(".article__source .source-toggle").toggleClass("show");
        });
        const input = $("<input>");
        const link = $(".article__source .source-toggle a").attr("href");
        $(".article__source .getlink").on("click", function() {
            $("body").append(input);
            input.val(link).select();
            document.execCommand("copy");
            input.remove();
            $(".article__source .getlink").html("Đã copy");
        });
    }

    $(".article__social .item,.short-social a").click(function(e) {
        e.preventDefault();
        let t = "";
        const n = $(this).attr("data-href");
        const a = $(this).attr("data-title");
        const rel = $(this).attr("data-rel");
        
        try {
            switch (rel) {
                case "facebook": t = "https://www.facebook.com/sharer.php?u=" + (n || window.location.href) + "&p[title]=" + (a || getTitle(document.title)); break;
                case "twitter": t = "https://twitter.com/share?url=" + (n || window.location.href) + "&title=" + (a || getTitle(document.title)); break;
                case "linkedin": t = "https://www.linkedin.com/sharing/share-offsite/?url=" + (n || window.location.href) + "&title=" + (a || getTitle(document.title)); break;
                case "pinterest": t = "http://pinterest.com/pin/create/button/?url=" + (n || window.location.href) + "&description=" + (a || getTitle(document.title)); break;
                case "telegram": t = "https://t.me/share/url?url=" + (n || window.location.href) + "&text=" + (a || getTitle(document.title)); break;
                case "tumblr": t = "https://www.tumblr.com/widgets/share/tool?canonicalUrl=" + (n || window.location.href) + "&caption=" + (a || getTitle(document.title)); break;
                case "gmail": t = "https://mail.google.com/mail/u/0/?view=cm&to&bcc&cc&fs=1&tf=1&body=" + (n || window.location.href) + "&su=" + (a || getTitle(document.title)); break;
                case "messenger": t = "https://www.facebook.com/dialog/send?app_id=1610792242748820&display=popup&link=" + (n || window.location.href) + "&redirect_uri=" + (n || window.location.href); break;
                case "copy": {
                    const i = $("<input>");
                    $("body").append(i);
                    i.val(n || window.location.href).select();
                    document.execCommand("copy");
                    i.remove();
                    break;
                }
            }
            if (t) window.open(t, "", "_blank,resizable=yes,width=800,height=450");
        } catch (err) {
            console.error("Share error:", err);  // Error handling cho debug
        }
        return false;
    });
}

function stickyBox(a, i, r, o, s = 65) {
    if ($(a).length <= 0 || $(a).children().length <= 0) return;
    
    const $article = $(a);
    const $socialContainer = $(i);
    const $social = $(o);
    let articleHeight = $article.height();
    let socialHeight = $social.outerHeight();
    let articleTop = $article.offset().top;
    let socialOriginalLeft = $social.offset().left;
    let parentPosition = $socialContainer.css('position');
    
    if (parentPosition !== 'relative' && parentPosition !== 'absolute') {
        $socialContainer.css('position', 'relative');
    }
    
    // Debounce scroll (throttle bằng rAF → tối ưu, giảm lag khi scale)
    let ticking = false;
    $(window).scroll(function() {
        if (!ticking) {
            window.requestAnimationFrame(function() {
                const socialWidth = $socialContainer.width();
                const scrollTop = $(window).scrollTop();
                let n = scrollTop - articleTop + s;
                if (r && $(r).length > 0) {
                    n = scrollTop - $(r).offset().top - $(r).height();
                }
                let t = scrollTop - articleTop - (articleHeight - socialHeight);
                
                if (n > 0) {
                    if (t < 0) {
                        $social.css({
                            position: 'fixed',
                            top: s + 'px',
                            width: socialWidth + 'px',
                            left: socialOriginalLeft,
                            margin: '0'
                        });
                    } else {
                        $social.css({
                            position: 'absolute',
                            top: (articleHeight - socialHeight) + 'px',
                            width: socialWidth + 'px',
                            left: '0',
                            margin: '0'
                        });
                    }
                } else {
                    $social.removeAttr('style');
                }
                ticking = false;
            });
            ticking = true;
        }
    });
    
    // Re-calc on resize (debounce để giảm call dư thừa)
    $(window).resize(debounce(function() {
        articleHeight = $article.height();
        socialHeight = $social.outerHeight();
        articleTop = $article.offset().top;
        socialOriginalLeft = $social.offset().left;
    }, 200));  // Debounce 200ms
}

// Helper: Debounce func (native, không lib, tối ưu perf)
function debounce(func, wait) {
    let timeout;
    return function() {
        clearTimeout(timeout);
        timeout = setTimeout(func, wait);
    };
}

function buildSticky() {
    if ($(".social-bar").length > 0 && $(".article__body").length > 0) {
        stickyBox(".article__body", ".social-bar", "", ".social-bar .social", 65);
    }
}

$(document).ready(function() {
    socialShare();
    buildSticky();
    reinitializeLazySizes(); 
    
    $('.menu-btn').on('click', function() {
        $('.menu .dropdown-menu').fadeToggle('fast');
        $('.menu').toggleClass('active');
        $('body').toggleClass('backdrop');
    });
    
    $('.menu-heading .ic-chevron-down').on('click', function(e) {
        e.preventDefault();
        $(this).toggleClass('ic-chevron-up');
        const submenu = $(this).parent().siblings('.submenu').first();
        submenu.slideToggle('fast');
    });
    
    if ($('.navigation').length > 0) {
        const navOffset = $('.navigation').offset().top;
        $(window).on('scroll', function() {
            const scrollTop = $(window).scrollTop();
            $('.navigation').toggleClass('fixed', navOffset <= scrollTop);
        });
    }
    
    $(window).on('resize', buildSticky);
});

$(window).on('load', buildSticky);