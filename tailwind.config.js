/** @type {import('tailwindcss').Config} */
export default {
    content: [
	"./assets/**/*.js|ts",
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
		'oceanTheme': {
		    'lightBlue':    '#D4EBF8',
		    'deepBlue':     '#1F509A',
		    'navyBlue':     '#0A3981',
		    'orange':       '#E38E49'
		},
		'skyTheme': {
		    'skyBlue':      '#78B3CE',
		    'lightCyan':    '#C9E6F0',
		    'cream':        '#FBF8EF',
		    'darkOrange':   '#F96E2A'
		},
		'sunsetTheme': {
		    'indigo':       '#4335A7',
		    'lightBlue':    '#80C4E9',
		    'paleYellow':   '#FFF6E9',
		    'brightOrange': '#FF7F3E'
		}
	    }
	},
    },
    plugins: [],
}

