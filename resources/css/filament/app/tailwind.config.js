import preset from "../../../../vendor/filament/filament/tailwind.config.preset";

export default {
    presets: [preset],
    content: [
        "./app/Filament/**/*.php",
        "./resources/views/filament/**/*.blade.php",
        "./vendor/filament/**/*.blade.php",
    ],
    theme: {
        extend: {
            colors: {
                // Ganti primary dengan kode warna hijau kamu
                primary: {
                    50: "#f0fdf9",
                    100: "#ccfbf0",
                    200: "#99f6e1",
                    300: "#5eead1",
                    400: "#20c896", // Warna utama kamu (#20C896)
                    500: "#10b981",
                    600: "#059669",
                    700: "#047857",
                    800: "#065f46",
                    900: "#064e3b",
                    950: "#022c22",
                },
            },
        },
    },
};
