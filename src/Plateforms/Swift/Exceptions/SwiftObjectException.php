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
// Time:     22:15
// Project:  lib-objectstorage
//
namespace CodeInc\ObjectStorage\Plateforms\Swift\Exceptions;
use CodeInc\ObjectStorage\Plateforms\Interfaces\Exceptions\StoreObjectExceptionInterface;
use CodeInc\ObjectStorage\Plateforms\Interfaces\StoreObjectInterface;
use CodeInc\ObjectStorage\Plateforms\Swift\SwiftObject;
use Throwable;


/**
 * Class SwiftObjectException
 *
 * @package CodeInc\ObjectStorage\Plateforms\Swift\Exceptions
 * @author Joan Fabrégat <joan@codeinc.fr>
 */
class SwiftObjectException extends SwiftException implements StoreObjectExceptionInterface {
	/**
	 * @var SwiftObject
	 */
	private $object;

	/**
	 * SwiftObjectException constructor.
	 *
	 * @param SwiftObject $object
	 * @param string $message
	 * @param Throwable|null $previous
	 */
	public function __construct(SwiftObject $object, string $message, Throwable $previous = null) {
		$this->object = $object;
		parent::__construct($message, $previous);
	}

	/**
	 * @return SwiftObject
	 */
	public function getObject():StoreObjectInterface {
		return $this->object;
	}
}