/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./src/Resources/**/*.blade.php",
        "./src/Resources/**/*.js",
        "../../../resources/themes/smart-home/views/**/*.blade.php",
        "../../../resources/views/**/*.blade.php",
        "../../../app/**/*.php",
        "../../../storage/framework/views/**/*.php"
    ],

    theme: {
        container: {
            center: true,

            screens: {
                "2xl": "1240px",
            },

            padding: {
                DEFAULT: "90px",
            },
        },

        screens: {
            sm: "525px",
            md: "768px",
            lg: "1024px",
            xl: "1280px",
            "2xl": "1536px",
            1180: "1180px",
            1060: "1060px",
            991: "991px",
            868: "868px",
        },

        extend: {
            colors: {
                navyBlue: "#060C3B",
                lightOrange: "#F6F2EB",
                darkGreen: '#40994A',
                darkBlue: '#0044F2',
                darkPink: '#F85156',
                blue: {
                    DEFAULT: '#33a1fd',
                    500: '#33a1fd',
                },
                green: {
                    DEFAULT: '#4caf50',
                    500: '#00a15e',
                },
                sentraRed: '#981c1c',
                sentraRedHover: '#DF4E4E',
            },

            fontFamily: {
                poppins: ["Poppins"],
                inter: ["Inter", "system-ui", "sans-serif"],
                //dmserif: ["DM Serif Display"],
            },
        }
    },

    plugins: [],

    safelist: [
    // icon-* (bijv. icon-home)
    { pattern: /^icon-/ },

    // Display utils
    { pattern: /^(flex|block|grid|hidden)$/ },
    { pattern: /^(flex|block|grid|hidden)$/, variants: ['sm', 'md', 'lg'] },

    // Width utils
    { pattern: /^w-(full|1\/2|2\/5|1\/4|2\/4)$/ },
    { pattern: /^w-(1\/2|2\/5|1\/4|2\/4)$/, variants: ['sm', 'md', 'lg'] },

    // Spacing
    { pattern: /^space-[xy]-(0|4)$/ },
    { pattern: /^space-[xy]-(0|4)$/, variants: ['sm', 'md', 'lg'] },

    // Text wrapping
    'break-words',
    'break-all',
    'break-normal',
    'hyphens-auto',
    'hyphens-manual',
    'hyphens-none',

    // Flex direction
    { pattern: /^(flex-col|flex-row)$/ },
    { pattern: /^flex-(col|row)$/, variants: ['md', 'lg'] },
    'sm:hidden',
    'sm:block',
    'md:hidden',
    'md:block',
    'lg:hidden',
    'lg:block',
    'md:container',
    'lg:container',
    'lg:px-0',
    'lg:p-4',
    'order-1',
    'order-2',
    'order-3',
    'lg:order-1',
    'lg:order-2',
    'lg:order-3',
    'lg:h-[429px]',
    'md:mt-0'
]
};
