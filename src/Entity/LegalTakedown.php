<?php

namespace App\Entity;

// @TODO Add validation.
class LegalTakedown {

	/**
	 * @var bool
	 */
	private $sendCe;

	/**
	 * @var bool
	 */
	private $test;

	/**
	 * @var array
	 */
	private $involvedUsers;

	/**
	 * @var string
	 */
	private $project;

	/**
	 * @var string
	 */
	private $contentType;

	/**
	 * @var array
	 */
	private $loggingMetadata;

	/**
	 * @var array
	 */
	private $strikeNotes;

	/**
	 * Set Send CE
	 *
	 * @param bool $sendCe Send CE
	 *
	 * @return self
	 */
	public function setSendCe( bool $sendCe ) : self {
		$this->sendCe = $sendCe;

		return $this;
	}

	/**
	 * Is Send CE
	 *
	 * @return bool
	 */
	public function isSendCe() :? bool {
		return $this->sendCe;
	}

	/**
	 * Set Test
	 *
	 * @param bool $test Test
	 *
	 * @return self
	 */
	public function setTest( bool $test ) : self {
		$this->test = $test;

		return $this;
	}

	/**
	 * Is Test
	 *
	 * @return bool
	 */
	public function isTest() :? bool {
		return $this->test;
	}

	/**
	 * Set Involved Users
	 *
	 * @param array $involvedUsers Usernames
	 *
	 * @return self
	 */
	public function setInvolvedUsers( array $involvedUsers ) : self {
		$this->involvedUsers = $involvedUsers;

		return $this;
	}

	/**
	 * Involved Users
	 *
	 * @return array
	 */
	public function getInvolvedUsers() :? array {
		return $this->involvedUsers;
	}

	/**
	 * Set Content Type
	 *
	 * @param string $contentType contentType
	 *
	 * @return self
	 */
	public function setContentType( string $contentType ) : self {
		$this->contentType = $contentType;

		return $this;
	}

	/**
	 * Content Type
	 *
	 * @return string
	 */
	public function getContentType() :? string {
		return $this->contentType;
	}

	/**
	 * Set Logging Metadata
	 *
	 * @param array $loggingMetadata Metadata
	 *
	 * @return self
	 */
	public function setLoggingMetadata( array $loggingMetadata ) : self {
		$this->loggingMetadata = $loggingMetadata;

		return $this;
	}

	/**
	 * Logging Metadata
	 *
	 * @return array
	 */
	public function getLoggingMetadata() :? array {
		return $this->loggingMetadata;
	}

	/**
	 * Set Strike Notes
	 *
	 * @param array $strikeNotes Notes
	 *
	 * @return self
	 */
	public function setStrikeNotes( array $strikeNotes ) : self {
		$this->strikeNotes = $strikeNotes;

		return $this;
	}

	/**
	 * Logging Metadata
	 *
	 * @return array
	 */
	public function getStrikeNotes() :? array {
		return $this->strikeNotes;
	}
}
