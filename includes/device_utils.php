<?php
function getLocationName($lat, $lon) {
    if (!$lat || !$lon) return 'N/A';
    $url = "https://nominatim.openstreetmap.org/reverse?lat=$lat&lon=$lon&format=json&zoom=16&addressdetails=1";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'ZouhairElearning/1.0');
    $response = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($response, true);
    if (isset($data['address'])) {
        $address = $data['address'];
        $neighborhood = $address['suburb'] ?? $address['neighbourhood'] ?? '';
        $city = $address['city'] ?? $address['town'] ?? $address['village'] ?? '';
        if ($neighborhood && $city) return "$neighborhood, $city";
        elseif ($city) return $city;
        elseif ($address['country']) return $address['country'];
    }
    return 'Unknown Location';
}

function simplifyDeviceInfo($userAgent) {
    if (preg_match('/(Chrome|Firefox|Safari|Edge|Opera)/i', $userAgent, $browser)) {
        $browser = $browser[1];
    } else {
        $browser = 'Unknown Browser';
    }
    if (preg_match('/(Windows|Macintosh|Linux|Android|iPhone|iPad)/i', $userAgent, $os)) {
        $os = $os[1];
        if ($os == 'Macintosh') $os = 'Mac';
    } else {
        $os = 'Unknown OS';
    }
    return "$browser on $os";
}
?>