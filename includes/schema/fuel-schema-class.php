<?php
use Yoast\WP\SEO\Generators\Schema\Abstract_Schema_Piece;

if (!class_exists('FuelPricesSchema')) {
    class FuelPricesSchema extends Abstract_Schema_Piece
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
        public $identifier = 'fuel_prices_schema';

        /**
         * MbarePricesSchema constructor.
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
 * Add exchange rate piece of the graph.
 *
 * @return mixed
 */
        public function generate()
        {
            try {
                $data = [];

                $valid_until = date('Y-m-d', strtotime($this->api_data["prices"]["Date"] . ' + 30 days'));

                // Petrol price schema
                if (array_key_exists("prices", $this->api_data) && array_key_exists("Petrol_USD", $this->api_data["prices"])) {
                    $petrol_price = $this->api_data["prices"]["Petrol_USD"];

                    $petrol_schema = [
                        "@type" => "Offer",
                        "price" => number_format($petrol_price, 2),
                        "priceCurrency" => "USD",
                        "validFrom" => $this->api_data["prices"]["Date"],
                        "priceValidUntil" => $valid_until,
                        "seller" => [
                            "@type" => "Organization",
                            "name" => "ZERA Zimbabwe",
                        ],
                        "itemOffered" => [
                            "@type" => "Product",
                            "name" => "Petrol",
                            "offeredQuantity" => [
                                "@type" => "QuantitativeValue",
                                "value" => 1,
                                "unitCode" => "LTR",
                            ],
                            "aggregateRating" => [
                                "@type" => "AggregateRating",
                                "ratingValue" => "5",
                                "ratingCount" => "10",
                                "bestRating" => "5",
                                "worstRating" => "1",
                            ],
                        ],
                    ];

                    $data[] = $petrol_schema;
                }

                // Diesel price schema
                if (array_key_exists("prices", $this->api_data) && array_key_exists("Diesel_USD", $this->api_data["prices"])) {
                    $diesel_price = $this->api_data["prices"]["Diesel_USD"];

                    $diesel_schema = [
                        "@type" => "Offer",
                        "price" => $diesel_price,
                        "priceCurrency" => "USD",
                        "validFrom" => $this->api_data["prices"]["Date"],
                        "priceValidUntil" => $valid_until,
                        "seller" => [
                            "@type" => "Organization",
                            "name" => "ZERA Zimbabwe",
                        ],
                        "itemOffered" => [
                            "@type" => "Product",
                            "name" => "Diesel",
                            "offeredQuantity" => [
                                "@type" => "QuantitativeValue",
                                "value" => 1,
                                "unitCode" => "LTR",
                            ],
                            "aggregateRating" => [
                                "@type" => "AggregateRating",
                                "ratingValue" => "5",
                                "ratingCount" => "10",
                                "bestRating" => "5",
                                "worstRating" => "1",
                            ],
                        ],
                    ];

                    $data[] = $diesel_schema;
                }

                return $data;
            } catch (Exception $e) {
                error_log("Exception thrown!");
                return [];
            }
        }
    }}
