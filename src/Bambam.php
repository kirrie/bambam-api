<?php
declare(strict_types=1);

namespace Bambam;

use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;

class Bambam {
	const HOSTNAME = 'dobambam.com';
	const TIMEOUT = 5.0;

	private $teamname = '';
	private $username = '';
	private $password = '';
	private $hostname = '';
	private $requestMode = 'sync';
	private $client = null;

	public function __construct(string $teamname, string $username, string $password, ?array $options = []) {
		$this->teamname = $teamname;
		$this->username = $username;
		$this->password = $password;
		$this->hostname = $options['hostname'] ?? self::HOSTNAME;
		$this->client = new Client([
			'base_uri' => $this->getBaseUri(),
			'timeout' => $options['timeout'] ?? self::TIMEOUT
		]);
	}

	public function getBaseUri(): string {
		return 'https://' . $this->teamname . '.' . $this->hostname . '/api/';
	}

	public function getEndpointWithBaseUri(string $endpoint): string {
		return $this->getBaseUri() . trim($endpoint, '/');
	}

	public function getCredentialHash(): string {
		return base64_encode($this->username . ':' . $this->password);
	}

	public function getDefaultRequestHeaders(): array {
		return [
			'Accept' => 'application/json',
			'Content-Type' => 'application/json',
			'Authorization' => $this->getCredentialHash()
		];
	}

	public function getRequestOptions(?array $query = [], ?array $form_params = [], ?array $options = []): array {
		return array_merge([
			'headers' => $this->getDefaultRequestHeaders(),
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
