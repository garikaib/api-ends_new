<?php
use Yoast\WP\SEO\Generators\Schema\Abstract_Schema_Piece;

if (!class_exists('ZPRatesSchema')) {
    class ZPRatesSchema extends Abstract_Schema_Piece
    {
        /**
         * A value object with context variables.
         *
         * @var WPSEO_Schema_Context
         */
        public $context;

        private $api_data;

        /**
         * Identifier for custom schema node.
         *
         * @var string
         */
        public $identifier = 'zp_exchange_rate_schema';

        /**
         * ZPRatesSchema constructor.
         *
         * @param WPSEO_Schema_Context $context Value object with context variables.
         */
        public function __construct(\WPSEO_Schema_Context$context, array $data = [])
        {
            $this->context = $context;
            $this->api_data = $data;
        }

        /**
         * Determines whether or not a piece should be added to the graph.
         *
         * @return bool
         */
        public function is_needed()
        {
            //Tests are done elsewhere for now we just need true
            return true;
        }

        /**
         * Add exchange rate piece of the graph.
         *
         * @return mixed
         */
        public function generate()
        {
            $data = [];
            $exchange_rate_schema = [
                "@type" => "ExchangeRateSpecification",
                "fromCurrency" => "USD",
                "toCurrency" => "ZWL",
                "currentExchangeRate" => (float) round($this->api_data["rates"]["ZIPIT"], 2),
                "validFrom" => $this->api_data["rates"]["Date"],
                "source" => "Zimpricecheck",
                "exchangeRateType" => "Zimbabwe Unofficial Market Rate",
                "unitText" => "per usd",
                // "bidPrice" => $this->api_data["rates"]["WBWS_Buy"],
                // "askPrice" => $this->api_data["rates"]["WBWS_Sell"],

            ];

            $data[] = $exchange_rate_schema;

            return $data;
        }
    }
}
