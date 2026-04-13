<?php
/**
 * Moonlight Digital - Currency Engine
 * Handles dynamic conversion from MMK database values to frontend display values.
 */

function get_exchange_rates($pdo) {
    // In production, you might want to cache this in Redis or memcached
    $stmt = $pdo->query("SELECT currency_code, mmk_value FROM md_exchange_rates");
    $rates = [];
    while ($row = $stmt->fetch()) {
        $rates[$row['currency_code']] = (float) $row['mmk_value'];
    }
    return $rates;
}

function convert_price($base_mmk, $target_currency, $rates) {
    if (!isset($rates[$target_currency])) {
        return $base_mmk; // Fallback to MMK if currency not found
    }
    $rate = $rates[$target_currency];
    return $base_mmk / $rate;
}

function format_price($amount, $currency) {
    switch ($currency) {
        case 'USD': return '$' . number_format($amount, 2);
        case 'EUR': return '€' . number_format($amount, 2);
        case 'THB': return '฿' . number_format($amount, 2);
        case 'SGD': return 'S$' . number_format($amount, 2);
        case 'MMK': 
        default: return number_format($amount, 0) . ' Ks';
    }
}
?>