import Alpine from "alpinejs";
import { createIcons, icons } from "lucide";

window.Alpine = Alpine;

document.addEventListener("alpine:init", () => {
    Alpine.data("deviceControl", () => ({
        devices: {
            aerator: {
                label: "Aerator",
                description: "Mengontrol suplai oksigen ke kolam.",
                active: false,
            },
            pump: {
                label: "Pompa Air",
                description: "Mengontrol sirkulasi atau pengurasan air.",
                active: false,
            },
            feeder: {
                label: "Feeder Otomatis",
                description: "Mengatur pemberian pakan lobster.",
                active: false,
            },
        },

        init() {
            this.$nextTick(() => createIcons({ icons, replace: true }));
        },

        toggleDevice(key) {
            this.devices[key].status = this.devices[key].status === "ON" ? "OFF" : "ON";
            this.$nextTick(() => createIcons({ icons, replace: true }));
        },

        handleSchedule() {
            alert("Atur jadwal perangkat (fitur dalam pengembangan)...");
        },

        saveSettings() {
            const result = Object.entries(this.devices)
                .map(([key, dev]) => `${dev.label}: ${dev.active ? "ON" : "OFF"}`)
                .join("\n");

            alert(`Pengaturan disimpan:\n${result}`);
        },
    }));
});

Alpine.start();
