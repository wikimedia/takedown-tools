<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\File;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesserInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @Route(service="app.controller_file")
 */
class FileController {

	/**
	 * @var Filesystem
	 */
	protected $filesystem;

	/**
	 * @var ExtensionGuesserInterface
	 */
	protected $extensionGuesser;

	/**
	 * @var RegistryInterface
	 */
	protected $doctrine;

	/**
	 * @var string
	 */
	protected $dir;

	/**
	 * File Controller.
	 *
	 * @param Filesystem $filesystem File System.
	 * @param ExtensionGuesserInterface $extensionGuesser Extension Guesser.
	 * @param RegistryInterface $doctrine Doctrine.
	 * @param string $dir Files Directory.
	 */
	public function __construct(
		Filesystem $filesystem,
		ExtensionGuesserInterface $extensionGuesser,
		RegistryInterface $doctrine,
		string $dir
	) {
		$this->filesystem = $filesystem;
		$this->extensionGuesser = $extensionGuesser;
		$this->doctrine = $doctrine;
		$this->doctrine = $doctrine;
		$this->dir = $dir;
	}

	/**
	 * Get File
	 *
	 * @Route("/api/file/{file}.{_format}")
	 * @Method({"GET"})
	 *
	 * @param File $file File
	 * @param Request $request Request
	 *
	 * @return BinaryFileResponse|File
	 */
	public function showAction( File $file, Request $request ) {
		if ( $request->query->has( 'metadata' ) ) {
			return $file;
		}

		return new BinaryFileResponse(
			$this->dir .  '/' . $file->getPath(),
			200,
			[],
			false
		);
	}

	/**
	 * Create File
	 *
	 * @Route("/api/file/{name}", defaults={"_format" = "json"})
	 * @Method({"POST"})
	 *
	 * @param Request $request Request
	 * @param string|null $name Name
	 *
	 * @return User
	 */
	public function createAction( Request $request, ?string $name = null ) : File {
		$ext = $this->extensionGuesser->guess( $request->headers->get( 'Content-Type' ) );

		if ( !$ext ) {
			throw new BadRequestHttpException( 'File Format Not Recognized' );
		}

		$content = $request->getContent( true );

		// Create the directories.
		$date = new \DateTime();
		$fileDir = $date->format( 'Y' ) . '/' . $date->format( 'm' ) . '/' . $date->format( 'd' );
		$this->filesystem->mkdir( $this->dir . '/' . $fileDir );

		// Create the file.
		$fileName = md5( uniqid() );
		$filePath = $fileDir . '/' . $fileName . '.' . $ext;
		$path =  $this->dir .  '/' . $filePath;
		$this->filesystem->touch( $path );

		// Copy to the file.
		if ( false === stream_copy_to_stream( $content, fopen( $path, 'w' ) ) ) {
			throw new \Exception( 'Could not copy file' );
		}

		// Save a record in the database.
		$em = $this->doctrine->getEntityManager();

		$file = new File( [
				'path' => $filePath,
				'name' => $name,
		] );

		$em->persist( $file );
		$em->flush();

		return $file;
	}

	/**
	 * Delete File
	 *
	 * @Route("/api/file/{file}")
	 * @Method({"DELETE"})
	 *
	 * @param File $file File
	 *
	 * @return BinaryFileResponse
	 */
	public function deleteAction( File $file ) : string {
		$this->filesystem->remove( $this->dir . '/' . $file->getPath() );

		$em = $this->doctrine->getEntityManager();
		$em->remove( $file );
		$em->flush();

		return '';
	}

}
