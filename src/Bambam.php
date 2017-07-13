<?php
declare(strict_types=1);

namespace Bambam;

use GuzzleHttp\Client;
use GuzzleHttp\Promise\Promise;

class Bambam {
	const HOSTNAME = '.dobambam.com';
	private $teamname = '';
	private $username = '';
	private $password = '';
	private $client = null;

	public function __construct(string $teamname, string $username, string $password) {
		$this->teamname = $teamname;
		$this->username = $username;
		$this->password = $password;
		$this->client = new Client([
			'base_uri' => $this->getEndpointBaseUri(),
			'timeout' => 5.0
		]);
	}

	public function getEndpointBaseUri(): string {
		return 'https://' . $this->teamname . self::HOSTNAME . '/api/';
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

	public function get(string $endpoint): Promise {
		return $this->client->getAsync($endpoint, [
			'headers' => $this->getDefaultRequestHeaders()
		]);
	}
}
// EOF
