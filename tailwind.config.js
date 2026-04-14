import defaultTheme from "tailwindcss/defaultTheme";

/** @type {import('tailwindcss').Config} */
export default {
    content: ["./resources/**/*.blade.php", "./resources/**/*.js", "./resources/**/*.vue"],
    theme: {
        extend: {
            colors: {
                primary: "rgb(var(--color-primary) / <alpha-value>)",
                secondary: "rgb(var(--color-secondary) / <alpha-value>)",
                // secondary: "#E5B121",
                hijau: "rgb(var(--color-hijau) / <alpha-value>)",
                goldLight: "rgb(var(--color-gold-light) / <alpha-value>)",
                goldDark: "rgb(var(--color-gold-dark) / <alpha-value>)",
                grayLight: "#F5F5F5",
                grayDark: "#333333",
            },
            fontFamily: {
                poppins: ["Poppins", "sans-serif"],
            },
        },
    },
    plugins: [require("@tailwindcss/typography")],
};
