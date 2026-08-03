<?php
/**
 * NursesPro Academy - Pesapal API v3 configuration (template)
 *
 * Copy this file to config/pesapal.php and fill in real values.
 * config/pesapal.php is gitignored so real credentials are never committed.
 *
 * Get your Consumer Key/Secret from:
 * - Sandbox (testing): https://developer.pesapal.com
 * - Live (production):  your Pesapal merchant account settings
 *
 * Until they're set, the "Pay Now" flow falls back to a clearly-labeled demo
 * grant (see api/pesapal_initiate.php) so the rest of the app stays testable.
 */

const PESAPAL_CONSUMER_KEY = '';     // TODO: paste your Pesapal Consumer Key
const PESAPAL_CONSUMER_SECRET = '';  // TODO: paste your Pesapal Consumer Secret

// 'sandbox' while testing, 'live' once you have production credentials.
const PESAPAL_ENV = 'sandbox';

const PESAPAL_BASE_URL = PESAPAL_ENV === 'live'
  ? 'https://pay.pesapal.com/v3'
  : 'https://cybqa.pesapal.com/pesapalv3';

const PESAPAL_CURRENCY = 'UGX';
const PESAPAL_AMOUNT = 18500;
const PESAPAL_ACCESS_MONTHS = 6;

function pesapal_is_configured(): bool {
  return PESAPAL_CONSUMER_KEY !== '' && PESAPAL_CONSUMER_SECRET !== '';
}
