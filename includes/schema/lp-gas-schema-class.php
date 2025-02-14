<?php
use Yoast\WP\SEO\Generators\Schema\Abstract_Schema_Piece;

if (!class_exists('LPGasPriceSchema')) {
    class LPGasPriceSchema extends Abstract_Schema_Piece
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
        public $identifier = 'lpgas_prices_schema';

        /**
         * LPGasPriceSchema constructor.
         *
         * @param WPSEO_Schema_Context $context Value object with context variables.
         * @param array $data API data.
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
            return ($this->context->id === 5321);
        }

        /**
         * Add LPGas price piece of the graph.
         *
         * @return mixed
         */
        public function generate()
        {
            try {
                $data = [];

                $valid_until = date('Y-m-d', strtotime($this->api_data["prices"]["Date"] . ' + 30 days'));

                // LPGas price schema
                if (array_key_exists("prices", $this->api_data) && array_key_exists("LPGas_USD", $this->api_data["prices"])) {

                    $lpg_price = $this->api_data["prices"]["LPGas_USD"];

                    $lpg_schema = [
                        "@type" => "Offer",
                        "price" => number_format($lpg_price, 2),
                        "priceCurrency" => "USD",
                        "validFrom" => $this->api_data["prices"]["Date"],
                        "priceValidUntil" => $valid_until,
                        "seller" => [
                            "@type" => "Organization",
                            "name" => "ZERA Zimbabwe",
                        ],
                        "itemOffered" => [
                            "@type" => "Product",
                            "name" => "Liquid Petroleum Gas (LP Gas)",
                            "offeredQuantity" => [
                                "@type" => "QuantitativeValue",
                                "value" => 1,
                                "unitCode" => "KG",
                            ],
                            "aggregateRating" => [
                                "@type" => "AggregateRating",
                                "ratingValue" => "5",
                                "ratingCount" => "10",
                                "bestRating" => "5",
                                "worstRating" => "1",
                            ],
                            "priceSpecification" => [
                                "@type" => "PriceSpecification",
                                "price" => number_format($lpg_price, 2),
                                "priceCurrency" => "USD",
                                "valueAddedTaxIncluded" => false,
                            ],
                        ],
                    ];

                    // Black Market price schema
                    if (array_key_exists("BM_USD", $this->api_data["prices"]) && array_key_exists("BM_ZWL", $this->api_data["prices"])) {
                        $bm_usd_price = $this->api_data["prices"]["BM_USD"];

                        $bm_schema = [
                            "@type" => "Offer",
                            "price" => number_format($bm_usd_price, 2),
                            "priceCurrency" => "USD",
                            "validFrom" => $this->api_data["prices"]["Date"],
                            "priceValidUntil" => $valid_until,
                            "seller" => [
                                "@type" => "Organization",
                                "name" => "Black Market Dealers",
                            ],
                            "itemOffered" => [
                                "@type" => "Product",
                                "name" => "Liquid Petroleum Gas (LP Gas)",
                                "offeredQuantity" => [
                                    "@type" => "QuantitativeValue",
                                    "value" => 1,
                                    "unitCode" => "KG",
                                ],
                                "priceSpecification" => [
                                    "@type" => "PriceSpecification",
                                    "price" => number_format($bm_usd_price, 2),
                                    "priceCurrency" => "USD",
                                    "valueAddedTaxIncluded" => true,
                                ], "aggregateRating" => [
                                    "@type" => "AggregateRating",
                                    "ratingValue" => "5",
                                    "ratingCount" => "10",
                                    "bestRating" => "5",
                                    "worstRating" => "1",
                                ],
                            ],
                        ];

                        $data[] = $bm_schema;
                    }

                    $data[] = $lpg_schema;
                }

                return $data;
            } catch (Exception $e) {
                error_log("Exception thrown!");
                return [];
            }
        }

    }
}
