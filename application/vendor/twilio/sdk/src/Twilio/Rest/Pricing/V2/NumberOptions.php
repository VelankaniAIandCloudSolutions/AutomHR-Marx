<?php

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Rest\Pricing\V2;

use Twilio\Options;
use Twilio\Values;

abstract class NumberOptions {
    /**
     * @param string $originationNumber The origination number for which to fetch
     *                                  pricing information
     * @return FetchNumberOptions Options builder
     */
    public static function fetch(string $originationNumber = Values::NONE): FetchNumberOptions {
        return new FetchNumberOptions($originationNumber);
    }
}

class FetchNumberOptions extends Options {
    /**
     * @param string $originationNumber The origination number for which to fetch
     *                                  pricing information
     */
    public function __construct(string $originationNumber = Values::NONE) {
        $this->options['originationNumber'] = $originationNumber;
    }

    /**
     * The origination phone number, in [E.164](https://www.twilio.com/docs/glossary/what-e164) format, for which to fetch the origin-based voice pricing information. E.164 format consists of a + followed by the country code and subscriber number.
     *
     * @param string $originationNumber The origination number for which to fetch
     *                                  pricing information
     * @return $this Fluent Builder
     */
    public function setOriginationNumber(string $originationNumber): self {
        $this->options['originationNumber'] = $originationNumber;
        return $this;
    }

    /**
     * Provide a friendly representation
     *
     * @return string Machine friendly representation
     */
    public function __toString(): string {
        $options = \http_build_query(Values::of($this->options), '', ' ');
        return '[Twilio.Pricing.V2.FetchNumberOptions ' . $options . ']';
    }
}