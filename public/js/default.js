const slider = document.querySelector('.product_slider');
const prevButton = document.querySelector('.prev');
const nextButton = document.querySelector('.next');

prevButton.addEventListener('click', () => {
    slider.scrollLeft -= slider.offsetWidth;
});

nextButton.addEventListener('click', () => {
    slider.scrollLeft += slider.offsetWidth;
});

const sliderArrival = document.querySelector('.product_slider_arrival');
const prevArrival = document.querySelector('.prev_arrival');
const nextArrival = document.querySelector('.next_arrival');

prevArrival.addEventListener('click', () => {
    sliderArrival.scrollLeft -= sliderArrival.offsetWidth;
});

nextArrival.addEventListener('click', () => {
    sliderArrival.scrollLeft += sliderArrival.offsetWidth;
});

const sliderSellers = document.querySelector('.product_slider_sellers');
const prevSellers = document.querySelector('.prev_sellers');
const nextSellers = document.querySelector('.next_sellers');

prevSellers.addEventListener('click', () => {
    sliderSellers.scrollLeft -= sliderSellers.offsetWidth;
});

nextSellers.addEventListener('click', () => {
    sliderSellers.scrollLeft += sliderSellers.offsetWidth;
});

// document.addEventListener('DOMContentLoaded', function() {
//     document.getElementById('cart-add-link').addEventListener('click', function(event) {
//         {% if app.user %}
//         {% else %}
//             event.preventDefault(); 
//             alert('Veuillez vous connecter ou créer un compte pour ajouter des produits au panier.');
//         {% endif %}
//     });
// });

document.addEventListener('DOMContentLoaded', function() {
    var myCarousel = new bootstrap.Carousel(document.querySelector('#carouselExample'), {
        interval: 5000,
        wrap: true
    });
});