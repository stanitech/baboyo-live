(function ($) {
    "use strict";

    jQuery(document).ready(function ($) {
        // initialize live clock
        try {
            const clock = new Clock();
            clock.start();
        } catch (error) { }

        // banner slider
        // var testimonialCarousel = $('.banner-slider');
        // testimonialCarousel.owlCarousel({
        //     loop: true,
        //     dots: true,
        //     nav: true,
        //     autoplay: true,
        //     navText: ['<i class="fas fa-angle-left"></i>','<i class="fas fa-angle-right"></i>'],
        //     startPosition: 2,
        //     autoplayTimeout: 4000,
        //     autoplayHoverPause: true,
        //     responsive: {
        //         0: {
        //             items: 1
        //         },
        //         768: {
        //             items: 1
        //         },
        //         960: {
        //             items: 1
        //         },
        //         1200: {
        //             items: 1
        //         },
        //         1920: {
        //             items: 1
        //         }
        //     }
        // });

        // // testimonial slider
        // var testimonialCarousel = $('.testimonial-slider');
        // testimonialCarousel.owlCarousel({
        //     loop: true,
        //     dots: true,
        //     nav: false,
        //     margin: 30,
        //     autoplay: true,
        //     startPosition: 2,
        //     autoplayTimeout: 4000,
        //     autoplayHoverPause: true,
        //     responsive: {
        //         0: {
        //             items: 1
        //         },
        //         768: {
        //             items: 1
        //         },
        //         960: {
        //             items: 1
        //         },
        //         1200: {
        //             items: 1
        //         },
        //         1920: {
        //             items: 1
        //         }
        //     }
        // });
        // $('.testimonial-slider').on('translate.owl.carousel', function(){
        //     $(this).find('.owl-item').find('.single-testimonial').find('.part-img').removeClass('add-anim').css('opacity', '0');
        // });
        // $('.testimonial-slider').on('translated.owl.carousel', function(){
        //     $(this).find('.owl-item.active').find('.single-testimonial').find('.part-img').addClass('add-anim').css('opacity', '1');
        // });

        // $('body').css('padding-right', '0');
        // $('.number-of-stake').val(1);
    });

    // lock screen title
    function lockScroll() {
        var scrollPosition = [
            self.pageXOffset ||
            document.documentElement.scrollLeft ||
            document.body.scrollLeft,
            self.pageYOffset ||
            document.documentElement.scrollTop ||
            document.body.scrollTop,
        ];
        var html = jQuery("html"); // it would make more sense to apply this to body, but IE7 won't have that
        html.data("scroll-position", scrollPosition);
        html.data("previous-overflow", html.css("overflow"));
        html.css("overflow", "hidden");
    }
    function unlockScroll() {
        var html = jQuery("html");
        var scrollPosition = html.data("scroll-position");
        html.css("overflow", html.data("previous-overflow"));
        window.scrollTo(scrollPosition[0], scrollPosition[1]);
    }

    $(window).on("load", function () {
        var preLoder = $(".preloader");
        preLoder.fadeOut(1000);
    });

    // fixed navbar
    window.onscroll = function () {
        fixedNavbar(), fixedFilterMenu();
    };
    var navbar = document.getElementById("navbar");

    var fixNav = navbar?.offsetTop;
    function fixedNavbar() {
        try {
            if (window.pageYOffset >= fixNav) {
                navbar.classList.add("fadeInDown");
                navbar.classList.add("navbar-fixed");
                navbar.classList.add("animated");
            } else {
                navbar.classList.remove("fadeInDown");
                navbar.classList.remove("navbar-fixed");
                navbar.classList.remove("animated");
            }
        } catch (error) { }
    }

    var filtermenu = document.getElementById("filter-menu");
    function fixedFilterMenu() {
        try {
            let stylelist = ["animated", "fadeInTop", "fixed-top", "bg-white"];
            if (window.pageYOffset >= filtermenu.clientHeight) {
                filtermenu.classList.add(...stylelist);
                try {
                    navbar.classList.add("text-hide");
                } catch (error) {
                }
                
            } else {
                filtermenu.classList.remove(...stylelist);
                try {
                    navbar.classList.remove("text-hide");
                } catch (error) {
                    
                }
                
            }
        } catch (error) { }
    }

    // count down
    var nodes = $(".timer");
    $.each(nodes, function (_index, value) {
        var date = $(this).data("date");

        setInterval(() => {
            var endTime = new Date(date);
            endTime = Date.parse(endTime) / 1000;

            var now = new Date();
            now = Date.parse(now) / 1000;

            var timeLeft = endTime - now;

            var days = Math.floor(timeLeft / 86400);
            var hours = Math.floor((timeLeft - days * 86400) / 3600);
            var minutes = Math.floor((timeLeft - days * 86400 - hours * 3600) / 60);
            var seconds = Math.floor(
                timeLeft - days * 86400 - hours * 3600 - minutes * 60
            );

            if (hours < "10") {
                hours = "0" + hours;
            }
            if (minutes < "10") {
                minutes = "0" + minutes;
            }
            if (seconds < "10") {
                seconds = "0" + seconds;
            }

            $(value).find(".day").html(days);
            $(value).find(".hour").html(hours);
            $(value).find(".minute").html(minutes);
            $(value).find(".second").html(seconds);
        }, 1000);
    });
})(jQuery);
