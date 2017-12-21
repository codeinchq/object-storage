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
// Time:     22:23
// Project:  lib-objectstorage
//
namespace CodeInc\ObjectStorage\Plateforms\LocalStorage\Exceptions;
use CodeInc\ObjectStorage\Plateforms\Interfaces\Exceptions\StoreContainerExceptionInterface;
use CodeInc\ObjectStorage\Plateforms\Interfaces\StoreContainerInterface;
use CodeInc\ObjectStorage\Plateforms\LocalStorage\LocalDirectory;
use Throwable;


/**
 * Class LocalDirectoryException
 *
 * @package CodeInc\ObjectStorage\Plateforms\LocalStorage
 * @author Joan Fabrégat <joan@codeinc.fr>
 */
class LocalDirectoryException extends LocalStorageException implements StoreContainerExceptionInterface {
	/**
	 * @var LocalDirectory
	 */
	private $container;

	/**
	 * LocalDirectoryException constructor.
	 *
	 * @param LocalDirectory $directory
	 * @param string $message
	 * @param Throwable|null $previous
	 */
	public function __construct(LocalDirectory $directory, string $message, Throwable $previous = null) {
		$this->container = $directory;
		parent::__construct($message, $previous);
	}

	/**
	 * @return LocalDirectory
	 */
	public function getContainer():StoreContainerInterface {
		return $this->container;
	}
}