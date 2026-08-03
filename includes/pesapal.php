<?php
/**
 * NursesPro Academy - Pesapal API v3 helpers
 * Thin wrapper around Pesapal's REST API: https://developer.pesapal.com/how-to-integrate/e-commerce/api-30-json/api-reference
 */

require_once __DIR__ . '/../config/pesapal.php';

class PesapalException extends Exception {}

function pesapal_request(string $method, string $path, array $body = [], ?string $token = null): array {
  $ch = curl_init(PESAPAL_BASE_URL . $path);
  $headers = ['Content-Type: application/json', 'Accept: application/json'];
  if ($token) $headers[] = 'Authorization: Bearer ' . $token;

  $opts = [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => $method,
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_TIMEOUT => 25,
    CURLOPT_SSL_VERIFYPEER => true,
  ];
  if ($method === 'POST') $opts[CURLOPT_POSTFIELDS] = json_encode($body);
  curl_setopt_array($ch, $opts);

  $response = curl_exec($ch);
  $err = curl_error($ch);
  curl_close($ch);

  if ($err) throw new PesapalException('Could not reach Pesapal: ' . $err);
  $data = json_decode($response, true);
  if (!is_array($data)) throw new PesapalException('Unexpected response from Pesapal.');
  return $data;
}

function pesapal_get_token(): string {
  $res = pesapal_request('POST', '/api/Auth/RequestToken', [
    'consumer_key' => PESAPAL_CONSUMER_KEY,
    'consumer_secret' => PESAPAL_CONSUMER_SECRET,
  ]);
  if (empty($res['token'])) {
    throw new PesapalException($res['error']['message'] ?? ($res['message'] ?? 'Pesapal authentication failed.'));
  }
  return $res['token'];
}

// Registers our IPN endpoint with Pesapal once, caching the resulting ipn_id
// in a flat file so repeat orders don't re-register every time.
function pesapal_get_ipn_id(string $token, string $ipnUrl): string {
  $cacheFile = __DIR__ . '/../config/pesapal_ipn_id.txt';
  if (is_file($cacheFile)) {
    $cached = trim(file_get_contents($cacheFile));
    if ($cached !== '') return $cached;
  }

  $res = pesapal_request('POST', '/api/URLSetup/RegisterIPN', [
    'url' => $ipnUrl,
    'ipn_notification_type' => 'GET',
  ], $token);

  if (empty($res['ipn_id'])) {
    throw new PesapalException($res['error']['message'] ?? 'Could not register the IPN URL with Pesapal.');
  }
  file_put_contents($cacheFile, $res['ipn_id']);
  return $res['ipn_id'];
}

function pesapal_submit_order(string $token, array $order): array {
  $res = pesapal_request('POST', '/api/Transactions/SubmitOrderRequest', $order, $token);
  if (empty($res['redirect_url'])) {
    throw new PesapalException($res['error']['message'] ?? 'Could not create the Pesapal payment order.');
  }
  return $res;
}

function pesapal_get_transaction_status(string $token, string $orderTrackingId): array {
  return pesapal_request('GET', '/api/Transactions/GetTransactionStatus?orderTrackingId=' . urlencode($orderTrackingId), [], $token);
}

// Builds the app's own base URL (scheme + host + app folder) for callback/IPN URLs.
function app_base_url(): string {
  $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
  $host = $_SERVER['HTTP_HOST'];
  $dir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
  // Normalize: api/*.php scripts live one level below the app root.
  if (basename($dir) === 'api') $dir = dirname($dir);
  return $scheme . '://' . $host . $dir;
}
