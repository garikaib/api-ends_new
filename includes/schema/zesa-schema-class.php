<?php
use Yoast\WP\SEO\Generators\Schema\Abstract_Schema_Piece;

if (!class_exists('ZESATariffsSchema')) {
    class ZESATariffsSchema extends Abstract_Schema_Piece
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
        public $identifier = 'zp_prices_schema';

        /**
         * ZPPricesSchema constructor.
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
            return ($this->context->id === 9870 || strpos(site_url(), 'localhost') !== false);
        }

        /**
         * Add exchange rate piece of the graph.
         *
         * @return mixed
         */
        public function generate()
        {
            try {
                $data = [];

                //Limit products to 3 now for testing

                if (array_key_exists("prices", $this->api_data) && is_array($this->api_data["prices"])) {
                    foreach ($this->api_data["prices"]["produce_prices"] as $mbare_price) {

                        $quantity_and_unit = $this->mbare_get_quantity($mbare_price);

                        $valid_until = date('Y-m-d', strtotime($this->api_data["prices"]["Date"] . ' + 60 days'));

                        $prices_schema = [
                            "@type" => "Offer",
                            "price" => (float) $mbare_price["max_price"],
                            "priceCurrency" => "USD",
                            "validFrom" => $this->api_data["prices"]["Date"],
                            "priceValidUntil" => $valid_until,
                            "seller" => [
                                "@type" => "Organization",
                                "name" => "ZETDC",
                            ],
                            "itemOffered" => [
                                "@type" => "Product",
                                "name" => $mbare_price["descr"],
                                "aggregateRating" => [
                                    "@type" => "AggregateRating",
                                    "ratingValue" => "5",
                                    "ratingCount" => "10",
                                    "bestRating" => "5",
                                    "worstRating" => "1",
                                ],
                            ],
                        ];

                        $data[] = $prices_schema;

                    }
                }

                return $data;
            } catch (Exception $e) {
                error_log("Exception thrown!");
                return [];
            }
        }

        public function mbare_get_quantity($mbare_price)
        {
            if (strpos($mbare_price['est_qty'], 'kg') !== false) {
                if (stripos($mbare_price["m_unit"], 'bucket') !== false || stripos($mbare_price["m_unit"], 'gallon') !== false) {
                    return [intval(str_replace(" kg", "", $mbare_price['est_qty'])), 'LTR'];
                }

                return [intval(str_replace(" kg", "", $mbare_price['est_qty'])), 'KG'];
            } else if (strpos($mbare_price['m_unit'], 'Crate') !== false) {
                return [30, 'C62'];
            } else if (strpos($mbare_price['est_qty'], 'g') !== false) {
                return [intval(str_replace(" g", "", $mbare_price['est_qty'])), 'GRM'];
            } else {
                return [1, 'EA'];
            }
        }

    }
}
