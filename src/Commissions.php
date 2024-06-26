<?php

namespace src;

class Commissions
{
    // Define EU countries
    private array $euCountries = [
        'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GR', 'HR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PO', 'PT', 'RO', 'SE', 'SI', 'SK'
    ];

    // Define rates
    public array $rates = [];

    // Define bins
    public array $bins = [];

    // Define rate API access params
    private string $transactionBaseURL = 'https://lookup.binlist.net/';

    // The rates API was changed because https://api.exchangeratesapi.io has no free plans with access to https
    private string $rateAccessURL = 'https://api.apilayer.com/exchangerates_data/latest?base=EUR';
    private string $rateAccessKey = 'r13wej52cEupEPzW56Vhw8Jd3d4kWiB1';

    /**
     * Get commissions based on transactions from a file
     *
     * @param string $transactionsFileName
     * @return array
     */
    public function getCommissions(string $transactionsFileName): ?array
    {
        $filePath = __DIR__ . '/data/' . $transactionsFileName;

        if (!file_exists($filePath)) {
            return ['The file ' . $transactionsFileName . ' does not exist.'];
        }

        $transactionsFile = file_get_contents($filePath);
        if ($transactionsFile === false) {
            return ['Failed to read the file: ' . $transactionsFileName];
        }

        if(empty($this->rates)) {
            $ratesFile = $this->getApiData($this->rateAccessURL, ['apikey' => $this->rateAccessKey]);
            if ($ratesFile === false) {
                return ['Failed to read the rates file.'];
            }

            $ratesFileJson = json_decode($ratesFile, true);
            $this->rates = $ratesFileJson['rates'] ?? [];
        }

        $commissions = [];
        $transactions = explode("\n", $transactionsFile);

        foreach ($transactions as $transactionString) {
            if (empty($transactionString)) {
                continue;
            }

            $transaction = json_decode($transactionString, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $commissions[] = 'Invalid transaction JSON: ' . $transactionString;
                continue;
            }

            $binResultsFile = !empty($this->bins)
                ? $this->bins[$transaction['bin']]
                : $this->getApiData($this->transactionBaseURL . $transaction['bin']);
            if ($binResultsFile === false) {
                $commissions[] = 'Transaction #' . $transaction['bin'] . '. An error occurred while making the API request, please try later.';
                continue;
            }

            $binResultsJson = json_decode($binResultsFile, true);
            if (json_last_error() !== JSON_ERROR_NONE || empty($binResultsJson['country']['alpha2'])) {
                $commissions[] = 'Transaction #' . $transaction['bin'] . '. The bin data is not exists or invalid, please try later.';
                continue;
            }

            $amountFixed = ($transaction['currency'] === 'EUR' || empty($this->rates[$transaction['currency']]))
                ? $transaction['amount']
                : $transaction['amount'] / $this->rates[$transaction['currency']];
            $taxCoefficient = in_array($binResultsJson['country']['alpha2'], $this->euCountries) ? 0.01 : 0.02;
            $commissions[] = round($amountFixed * $taxCoefficient, 2);
        }

        return $commissions;
    }

    /**
     * Get data from an API with optional headers
     *
     * @param string $url
     * @param array $headerParams
     * @return bool|string
     */
    public function getApiData(string $url, array $headerParams = []): bool|string
    {
        $ch = curl_init($url);

        $headers = [
            'Content-Type: application/json',
        ];

        foreach ($headerParams as $key => $value) {
            $headers[] = $key . ': ' . $value;
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);

        if ($response === false) {
            echo 'cURL error: ' . curl_error($ch);
        }

        curl_close($ch);

        return $response;
    }
}
