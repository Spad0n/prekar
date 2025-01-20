//import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
//import './styles/app.css';

//console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

const aboutToggle = document.getElementById('about-toggle') as HTMLButtonElement;
const aboutDropdown = document.getElementById('about-dropdown') as HTMLUListElement;

aboutToggle.addEventListener('click', () => {
    if (aboutDropdown.classList.contains('hidden')) {
	aboutDropdown.classList.remove('hidden');
    } else {
	aboutDropdown.classList.add('hidden');
    }
});

window.addEventListener('click', (event: MouseEvent) => {
    if (!aboutToggle.contains(event.target as Node) && !aboutDropdown.contains(event.target as Node)) {
	aboutDropdown.classList.add('hidden');
    }
});
