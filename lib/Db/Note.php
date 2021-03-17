<?php

namespace OCA\NotesTutorial\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class Note extends Entity implements JsonSerializable {
	protected $title;
	protected $content;
	protected $userId;
	protected $to;
	protected $formno;
	protected $agency;
	protected $policeno;
	protected $policeemail;
	protected $packagetype;

	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
			'title' => $this->title,
			'content' => $this->content,
			'to' => $this->to,
			'formno' => $this->formno,
			'agency' => $this->agency,
			'policeno' => $this->policeno,
			'policeemail' => $this->policeemail,
			'packagetype' => $this->packagetype
		];
	}

	public function setId($id): void {
		$this->id = $id;
	}

	public function setFormno($formno): void {
		$this->formno = $formno;
	}

	public function setReady($flag): void {
		if ($flag) {
			$this->policeemail = '@';
		} else {
			$this->policeemail = '';
		}
	}
}
