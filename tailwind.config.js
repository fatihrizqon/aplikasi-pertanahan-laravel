import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.js",
        "./node_modules/preline/**/*.js",
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: [
                    "Geist",
                    ...require("tailwindcss/defaultTheme").fontFamily.sans,
                ],
            },
        },
    },
    plugins: [forms],
};
