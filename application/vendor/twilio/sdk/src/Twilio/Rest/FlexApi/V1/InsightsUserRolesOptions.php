<?php

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Rest\FlexApi\V1;

use Twilio\Options;
use Twilio\Values;

abstract class InsightsUserRolesOptions {
    /**
     * @param string $token The Token HTTP request header
     * @return FetchInsightsUserRolesOptions Options builder
     */
    public static function fetch(string $token = Values::NONE): FetchInsightsUserRolesOptions {
        return new FetchInsightsUserRolesOptions($token);
    }
}

class FetchInsightsUserRolesOptions extends Options {
    /**
     * @param string $token The Token HTTP request header
     */
    public function __construct(string $token = Values::NONE) {
        $this->options['token'] = $token;
    }

    /**
     * The Token HTTP request header
     *
     * @param string $token The Token HTTP request header
     * @return $this Fluent Builder
     */
    public function setToken(string $token): self {
        $this->options['token'] = $token;
        return $this;
    }

    /**
     * Provide a friendly representation
     *
     * @return string Machine friendly representation
     */
    public function __toString(): string {
        $options = \http_build_query(Values::of($this->options), '', ' ');
        return '[Twilio.FlexApi.V1.FetchInsightsUserRolesOptions ' . $options . ']';
    }
}