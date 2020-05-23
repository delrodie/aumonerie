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
    $('.corporation').owlCarousel({
        autoplay: true,
        //center: true,
        //loop: true,
        responsive: {
            0:{ items:3},
            600:{ items: 4},
            1200: { items: 6}
        }
    });
})