<?php

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Rest\FrontlineApi\V1;

use Twilio\Options;
use Twilio\Values;

/**
 * PLEASE NOTE that this class contains beta products that are subject to change. Use them with caution.
 */
abstract class UserOptions {
    /**
     * @param string $friendlyName The string that you assigned to describe the User
     * @param string $avatar The avatar URL which will be shown in Frontline
     *                       application
     * @param string $state Current state of this user
     * @param bool $isAvailable Whether the User is available for new conversations
     * @return UpdateUserOptions Options builder
     */
    public static function update(string $friendlyName = Values::NONE, string $avatar = Values::NONE, string $state = Values::NONE, bool $isAvailable = Values::NONE): UpdateUserOptions {
        return new UpdateUserOptions($friendlyName, $avatar, $state, $isAvailable);
    }
}

class UpdateUserOptions extends Options {
    /**
     * @param string $friendlyName The string that you assigned to describe the User
     * @param string $avatar The avatar URL which will be shown in Frontline
     *                       application
     * @param string $state Current state of this user
     * @param bool $isAvailable Whether the User is available for new conversations
     */
    public function __construct(string $friendlyName = Values::NONE, string $avatar = Values::NONE, string $state = Values::NONE, bool $isAvailable = Values::NONE) {
        $this->options['friendlyName'] = $friendlyName;
        $this->options['avatar'] = $avatar;
        $this->options['state'] = $state;
        $this->options['isAvailable'] = $isAvailable;
    }

    /**
     * The string that you assigned to describe the User.
     *
     * @param string $friendlyName The string that you assigned to describe the User
     * @return $this Fluent Builder
     */
    public function setFriendlyName(string $friendlyName): self {
        $this->options['friendlyName'] = $friendlyName;
        return $this;
    }

    /**
     * The avatar URL which will be shown in Frontline application.
     *
     * @param string $avatar The avatar URL which will be shown in Frontline
     *                       application
     * @return $this Fluent Builder
     */
    public function setAvatar(string $avatar): self {
        $this->options['avatar'] = $avatar;
        return $this;
    }

    /**
     * Current state of this user. Can be either `active` or `deactivated`.
     *
     * @param string $state Current state of this user
     * @return $this Fluent Builder
     */
    public function setState(string $state): self {
        $this->options['state'] = $state;
        return $this;
    }

    /**
     * Whether the User is available for new conversations. Set to `false` to prevent User from receiving new inbound conversations if you are using [Pool Routing](https://www.twilio.com/docs/frontline/handle-incoming-conversations#3-pool-routing).
     *
     * @param bool $isAvailable Whether the User is available for new conversations
     * @return $this Fluent Builder
     */
    public function setIsAvailable(bool $isAvailable): self {
        $this->options['isAvailable'] = $isAvailable;
        return $this;
    }

    /**
     * Provide a friendly representation
     *
     * @return string Machine friendly representation
     */
    public function __toString(): string {
        $options = \http_build_query(Values::of($this->options), '', ' ');
        return '[Twilio.FrontlineApi.V1.UpdateUserOptions ' . $options . ']';
    }
}