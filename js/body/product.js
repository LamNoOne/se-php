
$(".multiple-product-slider").slick({
    infinite: true,
    slidesToShow: 5,
    slidesToScroll: 2,
    rows: 2,
    slidesPerRow: 1,
    autoplay: true,
    autoplaySpeed: 2000,
    responsive: [
        {
            breakpoint: 1400,
            settings: {
                slidesToShow: 5,
            },
        },
        {
            breakpoint: 1200,
            settings: {
                slidesToShow: 4,
            },
        },
        {
            breakpoint: 990,
            settings: {
                slidesToShow: 3,
            },
        },
        {
            breakpoint: 760,
            settings: {
                slidesToShow: 2,
            },
        },
        {
            breakpoint: 496,
            settings: {
                slidesToShow: 1,
            },
        },
    ],
});