$(document).ready(function () {
    // Navbar
    $('.navbar-light .menu').hover(function () {
        $(this).find('.sm-menu').first().stop(true, true).slideDown(150);
    }, function () {
        $(this).find('.sm-menu').first().stop(true, true).slideUp(105);
    });

    // Carousel
    $('.accueil-slide').owlCarousel({
        loop:true,
        margin: 10,
        dots: false,
        nav: true,
        mouseDrag: false,
        autoplay: true,
        animateOut: 'slideOutUp',
        responsive: {
            0:{ items:1},
            600:{ items: 1},
            1000: {items:1}
        }
    });

    // Aumonier
    $('.aumonier-carousel').owlCarousel({
        autoPlay: 3000, //Set AutoPlay to 3 seconds

        items : 4,
    });

    // Corporation
    $('.corporation-logo').slick({
        slidesToShow: 6,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 1500,
        arrows: false,
        dots: false,
        pauseOnHover: false,
        responsive: [{
            breakpoint: 768,
            settings: {
                slidesToShow: 4
            }
        }, {
            breakpoint: 520,
            settings: {
                slidesToShow: 3
            }
        }]
    });


})