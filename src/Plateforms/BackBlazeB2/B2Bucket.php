<?php
//
// +---------------------------------------------------------------------+
// | CODE INC. SOURCE CODE                                               |
// +---------------------------------------------------------------------+
// | Copyright (c) 2017 - Code Inc. SAS - All Rights Reserved.           |
// | Visit https://www.codeinc.fr for more information about licensing.  |
// +---------------------------------------------------------------------+
// | NOTICE:  All information contained herein is, and remains the       |
// | property of Code Inc. SAS. The intellectual and technical concepts  |
// | contained herein are proprietary to Code Inc. SAS are protected by  |
// | trade secret or copyright law. Dissemination of this information or |
// | reproduction of this material  is strictly forbidden unless prior   |
// | written permission is obtained from Code Inc. SAS.                  |
// +---------------------------------------------------------------------+
//
// Author:   Joan Fabrégat <joan@codeinc.fr>
// Date:     19/12/2017
// Time:     18:48
// Project:  lib-objectstorage
//
namespace CodeInc\ObjectStorage\Plateforms\BackBlazeB2;
use CodeInc\ObjectStorage\Plateforms\StoreContainerInterface;
use ChrisWhite\B2\Client;
use ChrisWhite\B2\File;
use CodeInc\ObjectStorage\Plateforms\StoreObjectInterface;


/**
 * Class B2Bucket
 *
 * @package CodeInc\ObjectStorage\Plateforms\BackBlazeB2
 * @author Joan Fabrégat <joan@codeinc.fr>
 */
class B2Bucket implements StoreContainerInterface {
	const RETRY_ON_FAILURE = 3; // times
	const WAIT_BETWEEN_FAILURES = 5; // seconds

	/**
	 * @var Client
	 */
	private $b2Client;

	/**
	 * @var string
	 */
	private $bucketName;

	/**
	 * B2Bucket constructor.
	 *
	 * @param string $bucketName
	 * @param Client $b2Client
	 * @throws B2BucketException
	 */
	public function __construct(string $bucketName, Client $b2Client) {
		$this->setBucketName($bucketName);
		$this->setB2Client($b2Client);
	}

	/**
	 * @param string $bucketName
	 * @throws B2BucketException
	 */
	protected function setBucketName(string $bucketName) {
		if (empty($bucketName)) {
			throw new B2BucketException($this,"The bucket name can not be empty");
		}
		$this->bucketName = $bucketName;
	}

	/**
	 * @param Client $b2Client
	 */
	protected function setB2Client(Client $b2Client) {
		$this->b2Client = $b2Client;
	}

	/**
	 * @return Client
	 */
	public function getB2Client():Client {
		return $this->b2Client;
	}

	/**
	 * @return string
	 */
	public function getBucketName():string {
		return $this->bucketName;
	}

	/**
	 * @param int $retryOnFailure
	 * @return StoreObjectInterface[]
	 * @throws B2BucketException
	 */
	public function listObjects(int $retryOnFailure = self::RETRY_ON_FAILURE):array {
		try {
			$objects = [];
			foreach ($this->b2Client->listFiles(['BucketName' => $this->bucketName]) as $file) {
				/** @var File $file */
				$objects[$file->getName()] = new B2Object($file, $this);
			}

			return $objects;
		}
		catch (\Throwable $exception) {
			if ($retryOnFailure > 0) {
				sleep(self::WAIT_BETWEEN_FAILURES);
				return $this->listObjects(--$retryOnFailure);
			}
			else {
				throw new B2BucketException($this,
					"Unable to list the objects of the B2 bucket \"$this->bucketName\"",
					$exception);
			}
		}
	}

	/**
	 * @param StoreObjectInterface $cloudStorageObject
	 * @param int|null $retryOnFailure
	 * @throws B2BucketException
	 */
	public function putObject(StoreObjectInterface $cloudStorageObject, int $retryOnFailure = self::RETRY_ON_FAILURE) {
		try {
			$this->b2Client->upload([
				'BucketName' => $this->bucketName,
				'FileName' => $cloudStorageObject->getName(),
				'Body' => $cloudStorageObject->getContent()
			]);
		}
		catch (\Throwable $exception) {
			if ($retryOnFailure > 0) {
				sleep(self::WAIT_BETWEEN_FAILURES);
				$this->putObject($cloudStorageObject, --$retryOnFailure);
			}
			else {
				throw new B2BucketException($this,
					"Error while uploading the object \"{$cloudStorageObject->getName()}\" "
					."to the B2 bucket \"$this->bucketName\"",
					$exception);
			}
		}
	}

	/**
	 * @param string $objectName
	 * @param int $retryOnFailure
	 * @return StoreObjectInterface
	 * @throws B2BucketException
	 */
	public function getObject(string $objectName, int $retryOnFailure = self::RETRY_ON_FAILURE):StoreObjectInterface {
		try {
			return new B2Object(
				$this->b2Client->getFile([
					'BucketName' => $this->bucketName,
					'FileName' => $objectName
				]),
				$this
			);
		}
		catch (\Throwable $exception) {
			if ($retryOnFailure > 0) {
				sleep(self::WAIT_BETWEEN_FAILURES);
				return $this->getObject($objectName, --$retryOnFailure);
			}
			else {
				throw new B2BucketException($this,
					"Error while downloading the object \"$objectName\" from the B2 bucket \"$this->bucketName\"",
					$exception);
			}
		}
	}

	/**
	 * @param string $objectName
	 * @return bool
	 * @throws B2BucketException
	 */
	public function hasObject(string $objectName):bool {
		try {
			return $this->b2Client->fileExists([
				'BucketName' => $this->bucketName,
				'FileName' => $objectName
			]);
		}
		catch (\Throwable $exception) {
			throw new B2BucketException($this,
				"Error while checking for the object \"$objectName\" in the B2 bucket \"$this->bucketName\"",
				$exception);
		}
	}

}