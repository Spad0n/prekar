import noUiSlider from 'nouislider';
import 'nouislider/dist/nouislider.css';

document.addEventListener("DOMContentLoaded", function () {
    const priceSlider = document.getElementById('price-slider');

    if (priceSlider) {  // Check if the slider exists on the page
        const maxPrice = parseInt(document.getElementById('max_price').value) || 0; // Default to 0 if empty

        noUiSlider.create(priceSlider, {
            start: [
                parseInt(document.getElementById('min_price').value),
                maxPrice
            ],
            connect: true,
            range: {
                'min': 90,
                'max': maxPrice
            },
            step: 10,
            format: {
                to: function (value) {
                    return Math.round(value);
                },
                from: function (value) {
                    return Number(value);
                }
            }
        });

        const minInput = document.getElementById('min_price');
        const maxInput = document.getElementById('max_price');
        const minDisplay = document.getElementById('price-min');
        const maxDisplay = document.getElementById('price-max');

        priceSlider.noUiSlider.on('update', function (values) {
            minInput.value = values[0];
            maxInput.value = values[1];
            minDisplay.innerText = values[0];
            maxDisplay.innerText = values[1];
        });
    }
});
