/** @type {import('tailwindcss').Config} */
export default {
    content: [
	"./assets/**/*.{js|ts}",
	"./templates/**/*.html.twig",
    ],
    theme: {
	extend: {
	    padding: {
		'10px': '10px',
	    },
	    fontFamily: {
		sans: ['ui-sans-serif', 'system-ui', 'sans-serif'],
	    },
	    colors: {
		'custom-black': '#282828',
		'custom-white': '#e2e2e2',
	    }
	},
    },
    plugins: [],
}

