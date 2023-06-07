<?php
declare(strict_types=1);

return [
  'providers' => [
      'stripe' => [
          'key' => 'pk_test',
          'secret' => env('STRIPE_SECRET', '#'),
          'webhook' => env('STRIPE_WEBHOOK_SECRET', '#'),
      ]
  ]
];
