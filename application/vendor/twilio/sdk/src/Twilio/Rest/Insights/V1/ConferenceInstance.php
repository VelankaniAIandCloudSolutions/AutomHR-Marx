<?php

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Rest\Insights\V1;

use Twilio\Deserialize;
use Twilio\Exceptions\TwilioException;
use Twilio\InstanceResource;
use Twilio\Rest\Insights\V1\Conference\ConferenceParticipantList;
use Twilio\Values;
use Twilio\Version;

/**
 * @property string $conferenceSid
 * @property string $accountSid
 * @property string $friendlyName
 * @property \DateTime $createTime
 * @property \DateTime $startTime
 * @property \DateTime $endTime
 * @property int $durationSeconds
 * @property int $connectDurationSeconds
 * @property string $status
 * @property int $maxParticipants
 * @property int $maxConcurrentParticipants
 * @property int $uniqueParticipants
 * @property string $endReason
 * @property string $endedBy
 * @property string $mixerRegion
 * @property string $mixerRegionRequested
 * @property bool $recordingEnabled
 * @property array $detectedIssues
 * @property string[] $tags
 * @property array $tagInfo
 * @property string $processingState
 * @property string $url
 * @property array $links
 */
class ConferenceInstance extends InstanceResource {
    protected $_conferenceParticipants;

    /**
     * Initialize the ConferenceInstance
     *
     * @param Version $version Version that contains the resource
     * @param mixed[] $payload The response payload
     * @param string $conferenceSid Conference SID.
     */
    public function __construct(Version $version, array $payload, string $conferenceSid = null) {
        parent::__construct($version);

        // Marshaled Properties
        $this->properties = [
            'conferenceSid' => Values::array_get($payload, 'conference_sid'),
            'accountSid' => Values::array_get($payload, 'account_sid'),
            'friendlyName' => Values::array_get($payload, 'friendly_name'),
            'createTime' => Deserialize::dateTime(Values::array_get($payload, 'create_time')),
            'startTime' => Deserialize::dateTime(Values::array_get($payload, 'start_time')),
            'endTime' => Deserialize::dateTime(Values::array_get($payload, 'end_time')),
            'durationSeconds' => Values::array_get($payload, 'duration_seconds'),
            'connectDurationSeconds' => Values::array_get($payload, 'connect_duration_seconds'),
            'status' => Values::array_get($payload, 'status'),
            'maxParticipants' => Values::array_get($payload, 'max_participants'),
            'maxConcurrentParticipants' => Values::array_get($payload, 'max_concurrent_participants'),
            'uniqueParticipants' => Values::array_get($payload, 'unique_participants'),
            'endReason' => Values::array_get($payload, 'end_reason'),
            'endedBy' => Values::array_get($payload, 'ended_by'),
            'mixerRegion' => Values::array_get($payload, 'mixer_region'),
            'mixerRegionRequested' => Values::array_get($payload, 'mixer_region_requested'),
            'recordingEnabled' => Values::array_get($payload, 'recording_enabled'),
            'detectedIssues' => Values::array_get($payload, 'detected_issues'),
            'tags' => Values::array_get($payload, 'tags'),
            'tagInfo' => Values::array_get($payload, 'tag_info'),
            'processingState' => Values::array_get($payload, 'processing_state'),
            'url' => Values::array_get($payload, 'url'),
            'links' => Values::array_get($payload, 'links'),
        ];

        $this->solution = ['conferenceSid' => $conferenceSid ?: $this->properties['conferenceSid'], ];
    }

    /**
     * Generate an instance context for the instance, the context is capable of
     * performing various actions.  All instance actions are proxied to the context
     *
     * @return ConferenceContext Context for this ConferenceInstance
     */
    protected function proxy(): ConferenceContext {
        if (!$this->context) {
            $this->context = new ConferenceContext($this->version, $this->solution['conferenceSid']);
        }

        return $this->context;
    }

    /**
     * Fetch the ConferenceInstance
     *
     * @return ConferenceInstance Fetched ConferenceInstance
     * @throws TwilioException When an HTTP error occurs.
     */
    public function fetch(): ConferenceInstance {
        return $this->proxy()->fetch();
    }

    /**
     * Access the conferenceParticipants
     */
    protected function getConferenceParticipants(): ConferenceParticipantList {
        return $this->proxy()->conferenceParticipants;
    }

    /**
     * Magic getter to access properties
     *
     * @param string $name Property to access
     * @return mixed The requested property
     * @throws TwilioException For unknown properties
     */
    public function __get(string $name) {
        if (\array_key_exists($name, $this->properties)) {
            return $this->properties[$name];
        }

        if (\property_exists($this, '_' . $name)) {
            $method = 'get' . \ucfirst($name);
            return $this->$method();
        }

        throw new TwilioException('Unknown property: ' . $name);
    }

    /**
     * Provide a friendly representation
     *
     * @return string Machine friendly representation
     */
    public function __toString(): string {
        $context = [];
        foreach ($this->solution as $key => $value) {
            $context[] = "$key=$value";
        }
        return '[Twilio.Insights.V1.ConferenceInstance ' . \implode(' ', $context) . ']';
    }
}