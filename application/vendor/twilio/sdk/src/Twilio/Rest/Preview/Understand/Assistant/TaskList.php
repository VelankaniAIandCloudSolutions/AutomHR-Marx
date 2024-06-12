<?php

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Rest\Preview\Understand\Assistant;

use Twilio\Exceptions\TwilioException;
use Twilio\ListResource;
use Twilio\Options;
use Twilio\Serialize;
use Twilio\Stream;
use Twilio\Values;
use Twilio\Version;

/**
 * PLEASE NOTE that this class contains preview products that are subject to change. Use them with caution. If you currently do not have developer preview access, please contact help@twilio.com.
 */
class TaskList extends ListResource {
    /**
     * Construct the TaskList
     *
     * @param Version $version Version that contains the resource
     * @param string $assistantSid The unique ID of the Assistant.
     */
    public function __construct(Version $version, string $assistantSid) {
        parent::__construct($version);

        // Path Solution
        $this->solution = ['assistantSid' => $assistantSid, ];

        $this->uri = '/Assistants/' . \rawurlencode($assistantSid) . '/Tasks';
    }

    /**
     * Streams TaskInstance records from the API as a generator stream.
     * This operation lazily loads records as efficiently as possible until the
     * limit
     * is reached.
     * The results are returned as a generator, so this operation is memory
     * efficient.
     *
     * @param int $limit Upper limit for the number of records to return. stream()
     *                   guarantees to never return more than limit.  Default is no
     *                   limit
     * @param mixed $pageSize Number of records to fetch per request, when not set
     *                        will use the default value of 50 records.  If no
     *                        page_size is defined but a limit is defined, stream()
     *                        will attempt to read the limit with the most
     *                        efficient page size, i.e. min(limit, 1000)
     * @return Stream stream of results
     */
    public function stream(int $limit = null, $pageSize = null): Stream {
        $limits = $this->version->readLimits($limit, $pageSize);

        $page = $this->page($limits['pageSize']);

        return $this->version->stream($page, $limits['limit'], $limits['pageLimit']);
    }

    /**
     * Reads TaskInstance records from the API as a list.
     * Unlike stream(), this operation is eager and will load `limit` records into
     * memory before returning.
     *
     * @param int $limit Upper limit for the number of records to return. read()
     *                   guarantees to never return more than limit.  Default is no
     *                   limit
     * @param mixed $pageSize Number of records to fetch per request, when not set
     *                        will use the default value of 50 records.  If no
     *                        page_size is defined but a limit is defined, read()
     *                        will attempt to read the limit with the most
     *                        efficient page size, i.e. min(limit, 1000)
     * @return TaskInstance[] Array of results
     */
    public function read(int $limit = null, $pageSize = null): array {
        return \iterator_to_array($this->stream($limit, $pageSize), false);
    }

    /**
     * Retrieve a single page of TaskInstance records from the API.
     * Request is executed immediately
     *
     * @param mixed $pageSize Number of records to return, defaults to 50
     * @param string $pageToken PageToken provided by the API
     * @param mixed $pageNumber Page Number, this value is simply for client state
     * @return TaskPage Page of TaskInstance
     */
    public function page($pageSize = Values::NONE, string $pageToken = Values::NONE, $pageNumber = Values::NONE): TaskPage {
        $params = Values::of(['PageToken' => $pageToken, 'Page' => $pageNumber, 'PageSize' => $pageSize, ]);

        $response = $this->version->page('GET', $this->uri, $params);

        return new TaskPage($this->version, $response, $this->solution);
    }

    /**
     * Retrieve a specific page of TaskInstance records from the API.
     * Request is executed immediately
     *
     * @param string $targetUrl API-generated URL for the requested results page
     * @return TaskPage Page of TaskInstance
     */
    public function getPage(string $targetUrl): TaskPage {
        $response = $this->version->getDomain()->getClient()->request(
            'GET',
            $targetUrl
        );

        return new TaskPage($this->version, $response, $this->solution);
    }

    /**
     * Create the TaskInstance
     *
     * @param string $uniqueName A user-provided string that uniquely identifies
     *                           this resource as an alternative to the sid. Unique
     *                           up to 64 characters long.
     * @param array|Options $options Optional Arguments
     * @return TaskInstance Created TaskInstance
     * @throws TwilioException When an HTTP error occurs.
     */
    public function create(string $uniqueName, array $options = []): TaskInstance {
        $options = new Values($options);

        $data = Values::of([
            'UniqueName' => $uniqueName,
            'FriendlyName' => $options['friendlyName'],
            'Actions' => Serialize::jsonObject($options['actions']),
            'ActionsUrl' => $options['actionsUrl'],
        ]);

        $payload = $this->version->create('POST', $this->uri, [], $data);

        return new TaskInstance($this->version, $payload, $this->solution['assistantSid']);
    }

    /**
     * Constructs a TaskContext
     *
     * @param string $sid A 34 character string that uniquely identifies this
     *                    resource.
     */
    public function getContext(string $sid): TaskContext {
        return new TaskContext($this->version, $this->solution['assistantSid'], $sid);
    }

    /**
     * Provide a friendly representation
     *
     * @return string Machine friendly representation
     */
    public function __toString(): string {
        return '[Twilio.Preview.Understand.TaskList]';
    }
}