<?php

namespace App\Services;

use App\Models\Alert;
use Illuminate\Support\Facades\Http;

class AlertService
{
  protected array $limits = [
    'suhu' => ['min' => 25, 'max' => 32],
    'do' => ['min' => 4, 'max' => 8],
    'ph' => ['min' => 6.5, 'max' => 8.5],
    'amonia' => ['min' => 0, 'max' => 0.02],
    'kekeruhan' => ['min' => 0, 'max' => 50],
  ];

  public function check(array $sensors)
  {
    foreach ($this->limits as $type => $range) {
      $value = $sensors[$type] ?? null;

      if ($value === null) continue;

      if ($value < $range['min']) {
        $this->createAlert($type, ucfirst($type) . " terlalu rendah: {$value}", $value);
      } elseif ($value > $range['max']) {
        $this->createAlert($type, ucfirst($type) . " terlalu tinggi: {$value}", $value);
      }
    }
  }

  protected function createAlert($type, $message, $value)
  {
    $alert = Alert::create([
      'type' => $type,
      'message' => $message,
      'value' => $value
    ]);

    $this->sendWhatsAppNotification($alert);
  }

  protected function sendWhatsAppNotification(Alert $alert)
  {
    $token = env('FONNTE_TOKEN');
    $phone = env('USER_PHONE');

    $response = Http::withHeaders([
      'Authorization' => $token,
    ])->post('https://api.fonnte.com/send', [
      'target' => $phone,
      'message' => 'ALERT : {$alert->message}\nNilai: {$alert->value}\nWaktu: {$alert->created_at->format("d M Y H:i")}',
    ]);

    if ($response->successful()) {
      $alert->update(['is_sent' => true]);
    }
  }
}
