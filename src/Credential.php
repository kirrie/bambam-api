<?php
declare(strict_types=1);

namespace Bambam;

class Credential {
	private $username = '';
	private $password = '';

	public function __construct(string $username, string $password) {
		$this->username = $username;
		$this->password = $password;
	}

	public function getHash(): string {
		return base64_encode($this->username . ':' . $this->password);
	}
}
// EOF
