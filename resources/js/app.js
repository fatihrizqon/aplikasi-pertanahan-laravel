import "./bootstrap";

import $ from "jquery";
window.$ = $;
window.jQuery = $;

import "preline";
import "preline/preline";
import { createIcons, icons } from "lucide";

import ApexCharts from 'apexcharts';
window.ApexCharts = ApexCharts

document.addEventListener("DOMContentLoaded", () => {
    window.HSOverlay.autoInit();
    window.HSDropdown?.autoInit();
    window.HSAccordion?.autoInit();
    window.HSStaticMethods?.autoInit();
    window.HSRemoveElement?.autoInit();

    createIcons({ icons });
});

import "./main";
import "./echo";
