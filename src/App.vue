<template>
	<div id="content" class="app-notestutorial">
		<AppNavigation>
			<AppNavigationNew v-if="!loading"
				:text="t('notestutorial', 'New impoundment form')"
				:disabled="false"
				button-id="new-notestutorial-button"
				button-class="icon-add"
				@click="newNote" />
			<ul>
				<AppNavigationItem v-for="note in notes"
					:key="note.id"
					:title="note.formno ? note.formno : t('notestutorial', 'New impoundment form')"
					:class="{active: currentNoteId === note.id}"
					@click="openNote(note)">
					<template slot="actions">
						<ActionButton v-if="note.id === -1"
							icon="icon-close"
							@click="cancelNewNote(note)">
							{{ t('notestutorial', 'Cancel impoundment form creation') }}
						</ActionButton>
						<ActionButton v-else
							icon="icon-delete"
							@click="deleteNote(note)">
							{{ t('notestutorial', 'Delete impoundment form') }}
						</ActionButton>
					</template>
				</AppNavigationItem>
			</ul>
		</AppNavigation>
		<AppContent
			:allow-swipe-navigation="false">
			<div v-if="currentNote" id="current-note">
				<div>
					<input type="button"
						class="primary"
						:value="t('notestutorial', 'Send')"
						:disabled="true">
					<input type="button"
						class="primary"
						:value="t('notestutorial', 'Preview')"
						:disabled="true">
					<input type="button"
						class="primary"
						:value="t('notestutorial', 'Check Recipients')"
						:disabled="true">
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
						:value="t('notestutorial', 'Save As Draft')"
						:disabled="updating || !savePossible"
						@click="saveNote">
					<input type="button"
						class="primary"
						:value="t('notestutorial', 'Save As Template')"
						:disabled="true">
				</div>
				<div class="form-group">
					<div class="form-control">
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
					</div>
					<div>
						<label for="formno">Prohibition # / VI #</label>
						<input id="formno"
							v-model="currentNote.formno"
							type="text"
							class="form-control">
					</div>
					<div>
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
					</div>
					<div>
						<label for="policeno">Police File #</label>
						<input id="policeno"
							v-model="currentNote.policeno"
							type="text"
							class="form-control">
					</div>
					<div>
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
				</div>
				<vue-dropzone :options="dropzoneOptions" />
				<div>
					<input type="button"
						class="primary"
						:value="t('notestutorial', 'Send')"
						:disabled="true">
					<input type="button"
						class="primary"
						:value="t('notestutorial', 'Preview')"
						:disabled="true">
					<input type="button"
						class="primary"
						:value="t('notestutorial', 'Check Recipients')"
						:disabled="true">
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
						:value="t('notestutorial', 'Save As Draft')"
						:disabled="updating || !savePossible"
						@click="saveNote">
					<input type="button"
						class="primary"
						:value="t('notestutorial', 'Save As Template')"
						:disabled="true">
				</div>
			</div>
			<div v-else id="emptycontent">
				<div class="icon-file" />
				<h2>{{ t('notestutorial', 'Create a new impountment form to get started') }}</h2>
			</div>
		</AppContent>
	</div>
</template>

<script>
import ActionButton from '@nextcloud/vue/dist/Components/ActionButton'
import AppContent from '@nextcloud/vue/dist/Components/AppContent'
import AppNavigation from '@nextcloud/vue/dist/Components/AppNavigation'
import AppNavigationItem from '@nextcloud/vue/dist/Components/AppNavigationItem'
import AppNavigationNew from '@nextcloud/vue/dist/Components/AppNavigationNew'

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
		vueDropzone: vue2Dropzone,
	},
	data() {
		return {
			notes: [],
			currentNoteId: null,
			updating: false,
			loading: true,
			dropzoneOptions: {
				url: '/post',
				thumbnailWidth: 100,
				addRemoveLinks: true,
				dictDefaultMessage: "<i class='fa-file-upload' /> Drop files to upload or use <strong><u>Upload Files</u></strong> dialog.",
			},
			tolist: [
				{
					name: 'dps',
					label: 'DPS',
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
			return this.notes.find((note) => note.id === this.currentNoteId)
		},

		/**
		 * Returns true if a note is selected and its formno is not empty
		 * @returns {Boolean}
		 */
		savePossible() {
			return this.currentNote && this.currentNote.formno !== ''
		},
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
			showError(t('notestutorial', 'Could not fetch impoundment forms'))
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
			this.$nextTick(() => {
				this.$refs.content.focus()
			})
		},
		/**
		 * Action tiggered when clicking the save button
		 * create a new note or save
		 */
		saveNote() {
			if (this.currentNoteId === -1) {
				this.createNote(this.currentNote)
			} else {
				this.updateNote(this.currentNote)
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
				this.notes.push({
					id: -1,
					title: '',
					formno: '',
					content: '',
					to: 'dps',
					agency: 'saanichpd',
					policeno: '',
					policeemail: '',
					packagetype: 'vi',
				})
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
		},
		/**
		 * Create a new note by sending the information to the server
		 * @param {Object} note Note object
		 */
		async createNote(note) {
			this.updating = true
			try {
				const response = await axios.post(generateUrl('/apps/notestutorial/notes'), note)
				const index = this.notes.findIndex((match) => match.id === this.currentNoteId)
				this.$set(this.notes, index, response.data)
				this.currentNoteId = response.data.id
			} catch (e) {
				console.error(e)
				showError(t('notestutorial', 'Could not create the impoundment form'))
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
				await axios.put(generateUrl(`/apps/notestutorial/notes/${note.id}`), note)
			} catch (e) {
				console.error(e)
				showError(t('notestutorial', 'Could not update the impoundment form'))
			}
			this.updating = false
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
				}
				showSuccess(t('notestutorial', 'Impoundment form deleted'))
			} catch (e) {
				console.error(e)
				showError(t('notestutorial', 'Could not delete the impoundment form'))
			}
		},
		/**
		 * Close current note
		 */
		async closeCurrentNote() {
			this.currentNoteId = null
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
</style>
