<?php

namespace OCA\NotesTutorial\Controller;

use OCA\NotesTutorial\AppInfo\Application;
use OCA\NotesTutorial\Service\NoteService;
use OCA\NotesTutorial\Db\Note;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\Files\IAppData;
use OCP\Files\IRootFolder;
use OCP\Files\Folder;
use OCP\IRequest;

class NoteController extends Controller {
	/** @var NoteService */
	private $service;

	/** @var string */
	private $userId;

    /** @var IAppData */
    private $appData;

	/** @var IRootFolder */
	private $rootFolder;

	use Errors;

	public function __construct(IRequest $request,
								NoteService $service,
								IAppData $appData,
								IRootFolder $rootFolder,
								$userId) {
		parent::__construct(Application::APP_ID, $request);
		$this->service = $service;
		$this->userId = $userId;
		$this->appData = $appData;
		$this->rootFolder = $rootFolder;
	}

	/**
	 * @NoAdminRequired
	 */
	public function index(): DataResponse {
		//return new DataResponse($this->service->findAll($this->userId));

		$a = array();

		$n = new Note();

		$n->setId(1);
		$n->setFormno('ABC');

		array_push($a, $n);

		$n = new Note();

		$n->setId(2);
		$n->setFormno('123');
		$n->setReady(true);

		array_push($a, $n);

		$n = new Note();

		$n->setId(2);
		$n->setFormno('456');
		$n->setReady(true);

		array_push($a, $n);

		$n = new Note();

		$n->setId(2);
		$n->setFormno('789');
		$n->setReady(true);

		array_push($a, $n);

		return new DataResponse($a);
	}

	/**
	 * @NoAdminRequired
	 */
	public function show(int $id): DataResponse {
		return $this->handleNotFound(function () use ($id) {
			return $this->service->find($id, $this->userId);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function create(string $title, string $content,
		string $to, string $formno, string $agency, string $policeno, string $policeemail, string $packagetype): DataResponse {

		// create folder
		//$this->appData->newFolder($title);
		
		//$policeemail = sizeof($this->appData->getDirectoryListing()) + 100;

		//$this->appData->newFolder('app-settings');

		//$policeno = Folder::getUserFolder($this->userid)->newFolder($formno);
		//$this->rootFolder->newFolder($formno);

		// https://help.nextcloud.com/t/how-create-a-folder-file-in-nextcloud-with-php/85409/2
		$userFolder = $this->rootFolder->getUserFolder($this->userId);

		try {
			try {
				$folderPath = '/VicPD Draft/' . $formno;
				if ($userFolder->nodeExists($folderPath) === FALSE) {
					$folder = $userFolder->newFolder($folderPath);
				} else {
					$folder = $userFolder->get($folderPath);
				}

				if ($folder->nodeExists('metadata.txt') === FALSE) {
					$file = $folder->newFile('metadata.txt');
				} else {
					$file = $folder->get('metadata.txt');
				}
			} catch (\OCP\Files\NotFoundException $e) {
			}

			// the id can be accessed by $file->getId();
			$file->putContent('pages: ' . $policeno);
		} catch(\OCP\Files\NotPermittedException $e) {
			// you have to create this exception by yourself
		}
		
		return new DataResponse($this->service->create($title, $content,
			$this->userId,
			$to, $formno, $agency, $policeno, $policeemail, $packagetype));
	}

	/**
	 * @NoAdminRequired
	 */
	public function update(int $id, string $title, string $content,
		string $to, string $formno, string $agency, string $policeno, string $policeemail, string $packagetype): DataResponse {

		// https://help.nextcloud.com/t/how-create-a-folder-file-in-nextcloud-with-php/85409/2
		$userFolder = $this->rootFolder->getUserFolder($this->userId);

		try {
			try {
				if (empty($policeemail)) {
					// update
					$folderPath = '/VicPD Draft/' . $formno;
					if ($userFolder->nodeExists($folderPath) === FALSE) {
						$folder = $userFolder->newFolder($folderPath);
					} else {
						$folder = $userFolder->get($folderPath);
					}

					if ($folder->nodeExists('metadata.txt') === FALSE) {
						$file = $folder->newFile('metadata.txt');
					} else {
						$file = $folder->get('metadata.txt');
					}

					// the id can be accessed by $file->getId();
					$file->putContent('pages: ' . $policeno);
				} else {
					// move
					$folderPath = '/VicPD Draft/' . $formno;
					$readyFolderPath = '/VicPD Ready/';

					if ($userFolder->nodeExists($readyFolderPath) === FALSE) {
						$readyFolderFullPath = $userFolder->newFolder($readyFolderPath)->getPath();
					} else {
						$readyFolderFullPath = $userFolder->get($readyFolderPath)->getPath();
					}

					$folder = $userFolder->get($folderPath);

					//$folder->newFile('2nd file.txt');

					$folder->move($readyFolderFullPath . '/' . $formno);
					//$readyFolder->copy($folderPath);
				}
			} catch (\OCP\Files\NotFoundException $e) {
			}
		} catch(\OCP\Files\NotPermittedException $e) {
			// you have to create this exception by yourself
		}

		return $this->handleNotFound(function () use ($id, $title, $content, $to, $formno, $agency, $policeno, $policeemail, $packagetype) {
			return $this->service->update($id, $title, $content, $this->userId,
				$to, $formno, $agency, $policeno, $policeemail, $packagetype);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function destroy(int $id): DataResponse {
		return $this->handleNotFound(function () use ($id) {
			return $this->service->delete($id, $this->userId);
		});
	}
}
