<template>
	<div id="content" class="app-notestutorial">
		<AppNavigation>
			<AppNavigationNew
				:text="t('notestutorial', 'New impoundment package')"
				:disabled="false"
				button-id="new-notestutorial-button"
				button-class="icon-add"
				@click="newNote" />
			<ul>
				<strong><AppNavigationItem :key="-999" :title="'Draft'" /></strong>
				<div v-if="notes.length > 0 && notes.filter((n) => { return !n.policeemail }).length > 0"
					style="margin-left:2.5em">
					<AppNavigationItem v-for="note in notes.filter((n) => { return !n.policeemail })"
						:key="note.id"
						:title="note.formno ? note.formno : t('notestutorial', 'New impoundment package')"
						:class="{active: currentNoteId === note.id}"
						@click="openNote(note)">
						<template slot="actions">
							<ActionButton v-if="note.id === -1"
								icon="icon-close"
								@click="cancelNewNote(note)">
								{{ t('notestutorial', 'Cancel impoundment package creation') }}
							</ActionButton>
							<ActionButton v-else
								icon="icon-delete"
								@click="deleteNote(note)">
								{{ t('notestutorial', 'Delete impoundment package') }}
							</ActionButton>
						</template>
					</AppNavigationItem>
				</div>
				<div v-else
					style="margin-left:2.5em">
					<i><AppNavigationItem
						:title="'Empty'" /></i>
				</div>
				<strong><AppNavigationItem :key="999" :title="'Ready'" /></strong>
				<div v-if="notes.length > 0 && notes.filter((n) => { return n.policeemail && n.policeemail.length > 0 }).length > 0"
					style="margin-left:2.5em">
					<AppNavigationItem v-for="note in notes.filter((n) => { return n.policeemail && n.policeemail.length > 0 })"
						:key="note.id"
						:title="note.formno ? note.formno : t('notestutorial', 'New impoundment package')"
						:class="{active: currentNoteId === note.id}" />
				</div>
				<div v-else
					style="margin-left:2.5em">
					<i><AppNavigationItem
						:title="'Empty'" /></i>
				</div>
			</ul>
			<hr>
			<AppNavigationNew
				:text="t('notestutorial', 'Settings')"
				:disabled="false"
				button-id=""
				button-class="icon-settings"
				@click="openSettings" />
			<!--Actions>
				<ActionButton
					icon="icon-settings"
					@click="openSettings">
					Settings
				</ActionButton>
			</Actions-->
		</AppNavigation>
		<AppContent
			:allow-swipe-navigation="false">
			<div v-if="currentNote" id="current-note">
				<div>
					<!--input v-if="currentNote.id !== -1"
						type="button"
						class="primary"
						:value="t('notestutorial', 'Move to Ready')"
						:disabled="true"-->
					<!-- -->
					<!--input type="button"
						class="primary"
						:value="t('notestutorial', 'Preview')"
						:disabled="true"-->
					<!-- -->
					<!--input v-if="currentNote.id === -1"
						type="button"
						class="primary"
						:value="t('notestutorial', 'Cancel')"
						:disabled="false"
						@click="cancelNewNote(currentNote)">
					<input v-else
						type="button"
						class="primary"
						:value="t('notestutorial', 'Close')"
						:disabled="false"
						@click="closeCurrentNote()">
					<input type="button"
						class="primary"
						:value="currentNote.id === -1 ? t('notestutorial', 'Save As Draft') : 'Save'"
						:disabled="updating || !savePossible"
						@click="saveNote"-->
				</div>
				<div class="form-group">
					<!--div class="form-control">
						<label for="to">To</label>
						<select id="to"
							v-model="currentNote.to"
							class="form-control">
							<option
								v-for="(to, index) in tolist"
								:key="index"
								:value="to.name">
								{{ to.label }}
							</option>
						</select>
					</div-->
					<div>
						<label for="formno">Prohibition # / VI #</label>
						<input id="formno"
							v-model="currentNote.formno"
							type="text"
							class="form-control">
					</div>
					<!--div>
						<label for="agency">Agency</label>
						<select id="agency"
							v-model="currentNote.agency"
							class="form-control">
							<option
								v-for="(agency, index) in agencylist"
								:key="index"
								:value="agency.name">
								{{ agency.label }}
							</option>
						</select>
					</div-->
					<vue-dropzone v-if="currentNote.id !== -1"
						:disabled="savePossible"
						:options="dropzoneOptions" />
					<div v-if="currentNote.id !== -1">
						<label for="policeno">Total number of pages</label>
						<input id="policeno"
							v-model="currentNote.policeno"
							type="text"
							class="form-control"
							@keypress="isNumber($event)">
					</div>
					<!--div>
						<label for="policeemail">Police Email</label>
						<input id="policeemail"
							v-model="currentNote.policeemail"
							type="text"
							class="form-control">
					</div>
					<div>
						<label for="packagetype">Type of package</label>
						<select id="packagetype"
							v-model="currentNote.packagetype"
							class="form-control">
							<option
								v-for="(packagetype, index) in packagetypelist"
								:key="index"
								:value="packagetype.name">
								{{ packagetype.label }}
							</option>
						</select>
					</div>
					<div>
						<label for="formjson">Debugging window</label>
						<textarea id="formjson"
							class="form-control"
							:value="JSON.stringify(currentNote)" />
					</div-->
				</div>
				<div>
					<input v-if="currentNote.id !== -1"
						type="button"
						class="primary"
						:value="t('notestutorial', 'Move to Ready')"
						:disabled="currentNote.policeno.length <= 0"
						@click="moveNoteToReady(currentNote)">
					<!--input type="button"
						class="primary"
						:value="t('notestutorial', 'Preview')"
						:disabled="true"-->
					<input v-if="currentNote.id === -1"
						type="button"
						class="primary"
						:value="t('notestutorial', 'Cancel')"
						:disabled="false"
						@click="cancelNewNote(currentNote)">
					<input v-else
						type="button"
						class="primary"
						:value="t('notestutorial', 'Close')"
						:disabled="false"
						@click="closeCurrentNote()">
					<input type="button"
						class="primary"
						:value="currentNote.id === -1 ? t('notestutorial', 'Save As Draft') : 'Save'"
						:disabled="updating || !savePossible || (currentNote.id !== -1 && currentNote.policeno.length <= 0)"
						@click="saveNote">
				</div>
			</div>
			<div v-else id="emptycontent">
				<div class="icon-file" />
				<h2>{{ t('notestutorial', 'Create a new impountment folder') }}</h2>
			</div>
		</AppContent>
		<Modal v-if="updatingSettings"
			title="'Settings'"
			@close="closeSettings">
			<div class="modal__content">
				Some content
				<div>
					<input
						type="button"
						class="primary"
						:value="t('notestutorial', 'Close')"
						:disabled="false"
						@click="closeSettings">
				</div>
			</div>
		</Modal>
	</div>
</template>

<script>
import ActionButton from '@nextcloud/vue/dist/Components/ActionButton'
import AppContent from '@nextcloud/vue/dist/Components/AppContent'
import AppNavigation from '@nextcloud/vue/dist/Components/AppNavigation'
import AppNavigationItem from '@nextcloud/vue/dist/Components/AppNavigationItem'
import AppNavigationNew from '@nextcloud/vue/dist/Components/AppNavigationNew'
// import ActionSeparator from '@nextcloud/vue/dist/Components/ActionSeparator'
import Modal from '@nextcloud/vue/dist/Components/Modal'

import '@nextcloud/dialogs/styles/toast.scss'
import { generateUrl } from '@nextcloud/router'
import { showError, showSuccess } from '@nextcloud/dialogs'
import axios from '@nextcloud/axios'
import vue2Dropzone from 'vue2-dropzone' // https://rowanwins.github.io/vue-dropzone/docs/dist/#/iconDemo
import 'vue2-dropzone/dist/vue2Dropzone.min.css'

export default {
	name: 'App',
	components: {
		ActionButton,
		AppContent,
		AppNavigation,
		AppNavigationItem,
		AppNavigationNew,
		// ActionSeparator,
		Modal,
		vueDropzone: vue2Dropzone,
	},
	data() {
		return {
			notes: [],
			tempNote: null,
			currentNoteId: null,
			updating: false,
			loading: true,
			updatingSettings: false,
			dropzoneOptions: {
				url: '/post',
				thumbnailWidth: 20,
				addRemoveLinks: true,
				dictDefaultMessage: "<i class='fa-file-upload' /> Drop files to upload or use <strong><u>Upload Files</u></strong> dialog.",
			},
			tolist: [
				{
					name: 'rsbc',
					label: 'RSBC',
				},
			],
			agencylist: [
				{
					name: 'vicpd',
					label: 'Vic PD',
				},
				{
					name: 'saanichpd',
					label: 'SannichPD',
				},
				{
					name: 'irsu',
					label: 'IRSU',
				},
			],
			packagetypelist: [
				{
					name: 'adp',
					label: 'ADP',
				},
				{
					name: 'irp',
					label: 'IRP',
				},
				{
					name: 'vi',
					label: 'VI',
				},
			],
		}
	},
	computed: {
		/**
		 * Return the currently selected note object
		 * @returns {Object|null}
		 */
		currentNote() {
			if (this.currentNoteId === null) {
				return null
			}

			return this.tempNote
		},

		/**
		 * Returns true if a note is selected and its formno is not empty
		 * @returns {Boolean}
		 */
		savePossible() {
			return this.currentNote && this.currentNote.formno !== ''
		},
	},
	created() {
		this.internval = setInterval(this.refreshList, 5000)
	},
	/**
	 * Fetch list of notes when the component is loaded
	 */
	async mounted() {
		try {
			const response = await axios.get(generateUrl('/apps/notestutorial/notes'))
			this.notes = response.data
		} catch (e) {
			console.error(e)
			showError(t('notestutorial', 'Could not fetch impoundment packages'))
		}
		this.loading = false
	},

	methods: {
		/**
		 * Create a new note and focus the note content field automatically
		 * @param {Object} note Note object
		 */
		openNote(note) {
			if (this.updating) {
				return
			}
			this.currentNoteId = note.id
			this.tempNote = note
			this.$nextTick(() => {
				this.$refs.policeno.focus()
			})
		},
		/**
		 * Action tiggered when clicking the save button
		 * create a new note or save
		 */
		saveNote() {
			if (this.currentNoteId === -1) {
				this.createNote(this.tempNote)
			} else {
				this.updateNote(this.tempNote)
			}
		},
		/**
		 * Create a new note and focus the note content field automatically
		 * The note is not yet saved, therefore an id of -1 is used until it
		 * has been persisted in the backend
		 */
		newNote() {
			if (this.currentNoteId !== -1) {
				this.currentNoteId = -1
				this.tempNote = {
					id: -1,
					title: '',
					formno: '',
					content: '',
					to: 'rsbc',
					agency: 'vicpd',
					policeno: '',
					policeemail: '',
					packagetype: 'vi',
				}
				this.notes.push(this.tempNote)
				this.$nextTick(() => {
					this.$refs.formno.focus()
				})
			}
		},
		/**
		 * Abort creating a new note
		 */
		cancelNewNote() {
			this.notes.splice(this.notes.findIndex((note) => note.id === -1), 1)
			this.currentNoteId = null
			this.tempNote = null
		},
		openSettings() {
			this.updatingSettings = true
		},
		closeSettings() {
			this.updatingSettings = false
		},
		isNumber(evt) {
			// if (!evt) {
			// evt = window.event;
			// }

			const charCode = (evt.which) ? evt.which : evt.keyCode
			if ((charCode > 31 && (charCode < 48 || charCode > 57)) && charCode !== 46) {
				evt.preventDefault()
			} else {
				return true
			}
		},
		async refreshList() {
			try {
				const response = await axios.get(generateUrl('/apps/notestutorial/notes'))
				this.notes = response.data
				if (this.tempNote && this.tempNote.id === -1) {
					this.notes.push(this.tempNote)
				}
			} catch (e) {
				console.error(e)
				showError(t('notestutorial', 'Could not fetch impoundment packages'))
			}
			this.loading = false
		},
		/**
		 * Create a new note by sending the information to the server
		 * @param {Object} note Note object
		 */
		async createNote(note) {
			this.updating = true
			try {
				const response = await axios.post(generateUrl('/apps/notestutorial/notes'), this.tempNote)
				const index = this.notes.findIndex((match) => match.id === this.currentNoteId)
				this.$set(this.notes, index, response.data)
				this.currentNoteId = response.data.id
				this.tempNote = response.data
			} catch (e) {
				console.error(e)
				showError(t('notestutorial', 'Could not create the impoundment package'))
			}
			this.updating = false
		},
		/**
		 * Update an existing note on the server
		 * @param {Object} note Note object
		 */
		async updateNote(note) {
			this.updating = true
			try {
				await axios.put(generateUrl(`/apps/notestutorial/notes/${note.id}`), this.tempNote)
			} catch (e) {
				console.error(e)
				showError(t('notestutorial', 'Could not update the impoundment package'))
			}
			this.updating = false
		},
		/**
		 * move the existing to note to ready folder
		 * @param {Object} note Note object
		 */
		async moveNoteToReady(note) {
			this.updating = true

			// signal the move by assigning a value to policeemail
			note.policeemail = '@'

			try {
				await axios.put(generateUrl(`/apps/notestutorial/notes/${note.id}`), note)
			} catch (e) {
				console.error(e)
				showError(t('notestutorial', 'Could not update the impoundmentpackage'))
			}
			this.updating = false
			this.tempNote = null

			await this.refreshList()
		},
		/**
		 * Delete a note, remove it from the frontend and show a hint
		 * @param {Object} note Note object
		 */
		async deleteNote(note) {
			try {
				await axios.delete(generateUrl(`/apps/notestutorial/notes/${note.id}`))
				this.notes.splice(this.notes.indexOf(note), 1)
				if (this.currentNoteId === note.id) {
					this.currentNoteId = null
					this.tempNote = null
				}

				await this.refreshList()

				showSuccess(t('notestutorial', 'Impoundment package deleted'))
			} catch (e) {
				console.error(e)
				showError(t('notestutorial', 'Could not delete the impoundment package'))
			}
		},
		/**
		 * Close current note
		 */
		async closeCurrentNote() {
			this.currentNoteId = null
			this.tempNote = null
		},
	},
}
</script>
<style scoped>
	@import url('https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');

	#app-content > div {
		margin-top: 3rem;
		width: 100%;
		height: 100%;
		padding: 20px;
		display: flex;
		flex-direction: column;
		flex-grow: 1;
	}

	#current-note {
		margin-top: 3rem;
	}

	input[type='text'] {
		width: 100%;
	}

	textarea {
		flex-grow: 1;
		width: 100%;
	}

	.modal__content {
		width: 50vw;
		text-align: center;
		margin: 10vw 0;
	}

	hr {
		margin-top: 1rem;
		margin-bottom: 1rem;
		border: 0;
		border-top: 1px solid rgba(0, 0, 0, 0.1);
	}
</style>
