<?php
declare(strict_types=1);

namespace Bambam;

use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;

class Bambam {
	private $hostname = 'dobambam.com';
	private $timeout = 2.0;

	private $teamname = '';
	private $credential = null;
	private $client = null;

	public function __construct(string $teamname, ?array $options = []) {
		$this->teamname = $teamname;

		$this->credential = $options['credential'] ?? null;
		if(!is_null($this->credential) && !($this->credential instanceof Credential)) {
			throw new \InvalidArgumentException('credential is not instance of Credential class.');
		}

		$this->client = new Client([
			'base_uri' => $this->getBaseUri(),
			'timeout' => $options['timeout'] ?? $this->timeout
		]);
	}

	public function setDefaultCredential(Credential $credential): self {
		$this->credential = $credential;
	}

	public function getBaseUri(): string {
		return 'https://' . $this->teamname . '.' . $this->hostname . '/api/';
	}

	public function getEndpointWithBaseUri(string $endpoint): string {
		return $this->getBaseUri() . trim($endpoint, '/');
	}

	public function getRequestOptions(?array $query = [], ?array $form_params = [], ?array $options = []): array {
		$credential = $options['credential'] ?? $this->credential;
		if(!($credential instanceof Credential)) {
			throw new \InvalidArgumentException('credential is not instance of Credential class.');
		}

		return array_merge([
			'headers' => [
				'Accept' => 'application/json',
				'Content-Type' => 'application/json',
				'Authorization' => sprintf('Basic %s', $credential->getHash())
			],
			'query' => $query,
			'form_params' => $form_params
		], $options);
	}

	public function createPool(callable $callback, ?array $options = []): Pool {
		return new Pool($this->client, $callback->bindTo($this)(), $options);
	}

	public function batchPool(callable $callback, ?array $options = []): array {
		return Pool::batch($this->client, $callback->bindTo($this)(), $options);
	}

	public function createRequest(string $httpMethod, string $endpoint) {
		return new Request(strtoupper($httpMethod), $this->getEndpointWithBaseUri($endpoint));
	}

	public function get(string $endpoint, ?array $query = [], ?array $options = []) {
		return $this->client->send(
			$this->createRequest('get', $endpoint),
			$this->getRequestOptions($query, [], $options)
		);
	}

	public function post(string $endpoint, ?array $form_params = [], ?array $query = [], ?array $options = []) {
		return $this->client->send(
			$this->createRequest('post', $endpoint),
			$this->getRequestOptions($query, $form_params, $options)
		);
	}

	public function getAsync(string $endpoint, ?array $query = [], ?array $options = []) {
		return $this->client->sendAsync(
			$this->createRequest('get', $endpoint),
			$this->getRequestOptions($query, [], $options)
		);
	}

	public function postAsync(string $endpoint, ?array $form_params = [], ?array $query = [], ?array $options = []) {
		return $this->client->sendAsync(
			$this->createRequest('post', $endpoint),
			$this->getRequestOptions($query, $form_params, $options)
		);
	}
}
// EOF
