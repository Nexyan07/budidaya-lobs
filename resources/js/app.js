import './bootstrap';
import Alpine from 'alpinejs';
import { createIcons, icons } from 'lucide';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;
window.createIcons = createIcons
window.lucideIcons = icons

document.addEventListener('DOMContentLoaded', () => {
  createIcons({ icons })
})

document.addEventListener('alpine:init', () => {
  Alpine.data('dashboard', (population) => ({
    alerts: [
      { id: 1, type: "DO_LOW", message: "Kadar DO rendah (<4 mg/L)", time: "09:12" },
      { id: 2, type: "pH_WARNING", message: "pH sedikit turun (6.8)", time: "08:50" },
    ],
    filter: 'daily',

    init() {
      this.$nextTick(() => {
        createIcons({ icons });
        this.renderChart();
      });
    },

    renderChart() {
      const ctx = document.getElementById('biomassChart');
      if (!ctx) return;

      // Data contoh biomassa per hari
      const labels = population.map(p => new Date(p.waktu).toLocaleDateString('id-ID', { day: '2-digit', month: 'short' })).reverse();
      const data = population.map(p => p.biomassa).reverse()
      console.log(data)

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
  }));

  Alpine.data("deviceControl", (initialDevices) => ({
    devices: initialDevices,

    init() {
      this.$nextTick(() => createIcons({ icons }));
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

  Alpine.data('historyViewer', () => ({
    histories: [],
    level: 'month',
    parent: null,
    stack: [],

    async init() {
      this.histories = window.historyData || []
      this.level = window.historyLevel || 'month'
    },

    async loadNext(waktu) {
      try {
        this.stack.push({
          level:this.level,
          parent: this.parent,
        })
        let nextLevel = this.level
        if (this.level === 'month') nextLevel = 'day'
        else if (this.level === 'day') nextLevel = 'hour'

        const res = await fetch(`/show-histories?level=${nextLevel}&parent=${waktu}`)
        if (!res.ok) throw new Error(`HTTP ${res.status}`)

        const json = await res.json()
        this.histories = json.histories
        this.level = nextLevel      // ubah ke level berikutnya
        this.parent = waktu         // parent adalah yang diklik
      } catch (err) {
        console.error('Gagal load data:', err)
      }
    },

    async goBack() {
      if (this.stack.length === 0) return;

      // Ambil state terakhir
      const prev = this.stack.pop();

      const res = await fetch(`/show-histories?level=${prev.level}&parent=${prev.parent}`);
      const json = await res.json();

      this.histories = json.histories;
      this.level = prev.level;
      this.parent = prev.parent;
    },

    formatLabel(waktu) {
      if (this.level === 'month') {
        const [y, m] = waktu.split('-')
        const monthNames = [
          'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
          'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ]
        return monthNames[parseInt(m) - 1] + ' ' + y
      }
      return waktu
    }
  }))

});

Alpine.start();
