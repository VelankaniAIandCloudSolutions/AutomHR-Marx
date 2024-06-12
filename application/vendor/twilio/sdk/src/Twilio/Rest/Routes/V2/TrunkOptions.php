<?php

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Rest\Routes\V2;

use Twilio\Options;
use Twilio\Values;

abstract class TrunkOptions {
    /**
     * @param string $voiceRegion The Inbound Processing Region used for this SIP
     *                            Trunk for voice
     * @param string $friendlyName A human readable description of this resource.
     * @return UpdateTrunkOptions Options builder
     */
    public static function update(string $voiceRegion = Values::NONE, string $friendlyName = Values::NONE): UpdateTrunkOptions {
        return new UpdateTrunkOptions($voiceRegion, $friendlyName);
    }
}

class UpdateTrunkOptions extends Options {
    /**
     * @param string $voiceRegion The Inbound Processing Region used for this SIP
     *                            Trunk for voice
     * @param string $friendlyName A human readable description of this resource.
     */
    public function __construct(string $voiceRegion = Values::NONE, string $friendlyName = Values::NONE) {
        $this->options['voiceRegion'] = $voiceRegion;
        $this->options['friendlyName'] = $friendlyName;
    }

    /**
     * The Inbound Processing Region used for this SIP Trunk for voice
     *
     * @param string $voiceRegion The Inbound Processing Region used for this SIP
     *                            Trunk for voice
     * @return $this Fluent Builder
     */
    public function setVoiceRegion(string $voiceRegion): self {
        $this->options['voiceRegion'] = $voiceRegion;
        return $this;
    }

    /**
     * A human readable description of this resource, up to 64 characters.
     *
     * @param string $friendlyName A human readable description of this resource.
     * @return $this Fluent Builder
     */
    public function setFriendlyName(string $friendlyName): self {
        $this->options['friendlyName'] = $friendlyName;
        return $this;
    }

    /**
     * Provide a friendly representation
     *
     * @return string Machine friendly representation
     */
    public function __toString(): string {
        $options = \http_build_query(Values::of($this->options), '', ' ');
        return '[Twilio.Routes.V2.UpdateTrunkOptions ' . $options . ']';
    }
}