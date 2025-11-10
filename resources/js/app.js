import './bootstrap';
import Alpine from 'alpinejs';
import { createIcons, icons } from 'lucide';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

document.addEventListener('alpine:init', () => {
  Alpine.data('dashboard', () => ({
    // Data sensor
    sensors: [
      { icon: 'thermometer', label: 'Suhu', value: '27 °C', status: 'normal' },
      { icon: 'droplets', label: 'DO', value: '5.9 mg/L', status: 'warning' },
      { icon: 'activity', label: 'pH', value: '7.3', status: 'normal' },
      { icon: 'beaker', label: 'Amonia', value: '0.03 mg/L', status: 'danger' },
      { icon: 'cloud', label: 'Kekeruhan', value: '12 NTU', status: 'normal' },
    ],
    devices: [
      { label: 'Aerator', active: true },
      { label: 'Pompa', active: false },
      { label: 'Feeder', active: true },
    ],
    dataHistory: [
      { date: "2025-11-05", temperature: 28.4, DO: 5.8, pH: 7.3, ammonia: 0.02, turbidity: 12 },
      { date: "2025-11-06", temperature: 29.1, DO: 6.2, pH: 7.1, ammonia: 0.04, turbidity: 10 },
      { date: "2025-11-07", temperature: 27.9, DO: 5.9, pH: 7.4, ammonia: 0.03, turbidity: 11 },
    ],
    filter: 'daily',
    alerts: [
      { id: 1, type: "DO_LOW", message: "Kadar DO rendah (<4 mg/L)", time: "09:12" },
      { id: 2, type: "pH_WARNING", message: "pH sedikit turun (6.8)", time: "08:50" },
    ],

    init() {
      this.$nextTick(() => {

        createIcons({ icons });
        this.renderChart();
      })
    },

    renderChart() {
      const ctx = document.getElementById('biomassChart');
      if (!ctx) return;

      // Data contoh biomassa per hari
      const labels = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
      const data = [1.4, 1.5, 1.6, 1.7, 1.72, 1.78, 1.82];

      new Chart(ctx, {
        type: 'line',
        data: {
          labels,
          datasets: [{
            label: 'Biomassa (kg)',
            data,
            borderColor: '#2563eb',
            backgroundColor: '#2563eb',
            tension: 0.35,
            borderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6,
            pointBackgroundColor: '#2563eb',
            pointHoverBackgroundColor: '#1e40af',
            fill: {
              target: 'origin',
              above: 'rgba(37, 99, 235, 0.05)' // area shading biru muda
            }
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            },
            tooltip: {
              enabled: true,
              backgroundColor: 'rgba(37, 99, 235, 0.9)',
              titleColor: '#fff',
              bodyColor: '#fff',
              titleFont: { size: 13, weight: 'bold' },
              bodyFont: { size: 12 },
              displayColors: false,
              callbacks: {
                label: function (context) {
                  return `Biomassa: ${context.parsed.y} kg`;
                },
                title: function (context) {
                  return `Hari: ${context[0].label}`;
                }
              }
            }
          },
          scales: {
            x: {
              grid: {
                display: false
              },
              ticks: {
                color: '#6b7280', // abu-abu elegan
                font: { size: 12 }
              }
            },
            y: {
              grid: {
                color: 'rgba(0,0,0,0.05)'
              },
              ticks: {
                color: '#6b7280',
                font: { size: 12 },
                callback: function (value) {
                  return value + ' kg';
                }
              }
            }
          },
          animation: {
            tension: {
              duration: 1000,
              easing: 'easeOutQuart',
              from: 0.4,
              to: 0.35,
              loop: false
            }
          }
        }
      });
    },

    handleExport() {
      alert(`Export data ${this.filter} ke Excel...`);
    }
  }));
});

Alpine.start();
