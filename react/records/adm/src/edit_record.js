import { Component } from '@wordpress/element';
import { Button, TextControl, Modal, ComboboxControl, DateTimePicker, Popover, SelectControl } from '@wordpress/components';
import { EasqySelect } from './utils';


class DateBlock extends Component {
	constructor(props) {
		super(props);
		this.state = {
			date: this.props.date || new Date(),
			openDatePopup: false
		}
	}

	render() {

		return <div className="components-dropdown">
			<label for='easqy-add-record-date'>Date du record : </label>
			<Button id='easqy-add-record-date' isLink={true} onClick={() => {
				console.log(this.state)
				this.setState({ openDatePopup: true })

			}}>
				{this.state.date.toLocaleDateString('fr-FR', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}
			</Button>
			{this.state.openDatePopup && (
				<Popover position="bottom" onClose={() => { this.setState({ openDatePopup: false }) }}>
					<DateTimePicker
						label="My Date/Time Picker"
						currentDate={this.state.date}
						onChange={(d) => { console.log(d, typeof d); this.setState({ date: new Date(d) }) }}
						is12Hour={false}
					/>
				</Popover>
			)}
		</div>

	}
}

class AthleteModal extends Component {
	constructor(props) {
		super(props);
		this.state = {
			athleteId: this.props.athleteId || -1,
			newAthlete: this.props.newAthlete || '',
			modalIsOpen: false
		}
	}

	render() {

		const {
			athleteId,
			newAthlete,
			modalIsOpen
		} = this.state;

		const {
			athletes, onAddAthlete
		} = this.props;

		const athleteOptions = athletes.map(a => { return { label: a.p + ' ' + a.n, value: a.i } })

		return <div>
			<Button
				isPrimary
				disabled={modalIsOpen}
				onClick={() => { this.setState({ modalIsOpen: true }) }}
			>
				Ajouter un(e) athlète ...
				</Button>
			{
				(modalIsOpen) && (<Modal
					title="Ajouter un(e) athlète ..."
					onRequestClose={() => { this.setState({ modalIsOpen: false }) }}
				>
					<div>
						<ComboboxControl
							label="Athlete"
							value={athleteId}
							onFilterValueChange={(v) => { this.setState({ newAthlete: v.trim() }) }}
							onChange={(a) => { this.setState({ athleteId: a }) }}
							options={athleteOptions}
						/>
					</div>

					<div style={{ display: 'flex', flexDirection: 'row', justifyContent: 'space-between', alignItems: 'baseline' }}>
						<Button
							isSecondary
							onClick={() => { this.setState({ modalIsOpen: false }) }}
						>
							{'Annuler'}
						</Button>
						<Button
							isPrimary
							onClick={() => {
								onAddAthlete((newAthlete === '') ? athleteId : newAthlete);
								this.setState({ modalIsOpen: false })
							}}
						>
							{'Ajouter'}
						</Button>
					</div>
				</Modal>)
			}
		</div>

	}
}

class AthleteDuRecord extends Component {
	constructor(props) {
		super(props);
		this.state = {
			athlete: this.props.athlete || -1,
			catWhenPerf: this.props.catWhenPerf || -1,
		}
	}

	render() {

		const { athlete, catWhenPerf } = this.state;
		const { athletes, categories } = this.props;

		const name = () => {
			if (athlete === parseInt(athlete, 10)) {
				const found = athletes.find((_) => _.i === athlete);
				return found.p + ' ' + found.n;
			}
			else {
				return athlete
			}
		}

		return <tr>
			<td><strong>{name()}</strong></td>
			<td>
				<SelectControl
					label="Catégorie de l'athlète lors du record :"
					options={[{ label: '-', value: -1 }, ...categories.map((c, i) => { return { label: c, value: i } })]}
					value={catWhenPerf}
					labelPosition='top'
					onChange={(c) => {
						this.props.onSetCategorie(this.props.index, parseInt(c))
						this.setState({ catWhenPerf: parseInt(c) })
					}}
				/>
			</td>
			<td><Button isSmall isDestructive
				onClick={() => { this.props.onDelAthlete(this.props.index) }}
			>X</Button></td>
		</tr>;
	}
}



export default class EditRecord extends Component {

	constructor(props) {
		super(props);

		let adr = [];
		if (this.props.record && this.props.ra && this.props.athletes) {
			const ra = this.props.ra.filter(ra => ra.r === this.props.record.i)
			adr = ra.map(ra => { return { athlete: ra.a, catWhenPerf: ra.c || -1 } });
		}


		this.state = {
			categorie: (this.props.record && this.props.record.c) || 0,
			epreuve: (this.props.record && this.props.record.e) || 0,
			genre: (this.props.record && this.props.record.g) || 0,
			io: (this.props.record && this.props.record.in) || 0,
			perf: (this.props.record && this.props.record.p) || '',
			infos: (this.props.record && this.props.record.i) || '',
			lieu: (this.props.record && this.props.record.l) || '',
			date: (this.props.record && new Date(this.props.record.d)) || new Date(),

			athletesDuRecord: [...adr],
		}
	}

	onAddAthlete(a) {
		this.setState({
			athletesDuRecord: [...this.state.athletesDuRecord, { athlete: a, catWhenPerf: -1 }]
		})
	}

	onSetCategorie(index, cat) {
		const newA = [... this.state.athletesDuRecord];
		newA[index].catWhenPerf = cat;
		this.setState({
			athletesDuRecord: newA
		})
	}

	onDelAthlete(index) {
		const newA = [... this.state.athletesDuRecord];
		newA.splice(index, 1);
		this.setState({ athletesDuRecord: newA })
	}

	render() {
		const {
			categories,
			epreuves,
			genres,
			records,
			ra,
			athletes,
			createMode
		} = this.props;
		const { categorie, epreuve, genre, io, athletesDuRecord } = this.state;

		const recordFound = records.find(r =>
			(r.c === categorie) && (r.e === epreuve) && (r.g === genre) && (r.in === io)
		);

		const disableOk = (recordFound) || (this.state.perf === '') || (this.state.lieu === '') || (athletesDuRecord.length === 0);

		return (
			<div>
				{createMode && <h2>Ajouter un nouveau record</h2>}
				<div>
					{createMode && <table>
						<tr>
							<td>
								<EasqySelect
									disabled={!createMode}
									label='Compétition :'
									options={categories}
									setState={(v) => { this.setState({ categorie: v }) }}
									value={categorie} />
							</td>
							<td>
								<EasqySelect
									disabled={!createMode}
									label='&nbsp;'
									options={epreuves}
									setState={(e) => { this.setState({ epreuve: e }) }}
									value={epreuve} />
							</td>
							<td>
								<EasqySelect
									disabled={!createMode}
									label='&nbsp;'
									options={genres}
									setState={(g) => { this.setState({ genre: g }) }}
									value={genre} />
							</td>
							<td>
								<EasqySelect
									disabled={!createMode}
									label='&nbsp;'
									options={['Outdoor', 'Indoor']}
									setState={(io) => { this.setState({ io }) }}
									value={io} />
							</td>
						</tr>
					</table>
					}
					<hr />
					<DateBlock date={this.state.date} />
					<hr />
					<div style={{ display: 'flex', flexDirection: 'row', justifyContent: 'space-between', alignItems: 'baseline' }}>
						<TextControl
							style={{ marginRight: '1em' }}
							label="Preformance :"
							value={this.state.perf}
							labelPosition='side'
							onChange={(c) => { console.log(c); this.setState({ perf: c }) }}
						/>
						<div>&nbsp;</div>
						<TextControl
							label="Information complémentaire :"
							help={'Vitesse du vent, poids, ...'}
							value={this.state.infos}
							onChange={(c) => { console.log(c); this.setState({ infos: c }) }}
						/>
					</div>
					<hr />
					<TextControl
						label="Lieu :"
						value={this.state.lieu}
						labelPosition='side'
						onChange={(c) => { console.log(c); this.setState({ lieu: c }) }}
					/>
					<hr />
					<h3>Athletes :</h3>
					<table width='100%'><tbody>

						{this.state.athletesDuRecord.map((a, i) => {

							return <AthleteDuRecord
								key={'AthleteDuRecord-' + i}
								index={i}
								categories={categories}
								athletes={athletes}
								athlete={a.athlete}
								catWhenPerf={a.catWhenPerf}
								onDelAthlete={(index) => { this.onDelAthlete(index) }}
								onSetCategorie={(index, cat) => { this.onSetCategorie(index, cat) }}
							/>
						})}

					</tbody></table>
					<hr />
					<AthleteModal
						athletes={this.props.athletes}
						onAddAthlete={(a) => { this.onAddAthlete(a) }}
					/>
					<hr />
					<div>
						<div id="easqy-record-add-buttons">
							<Button
								type="button"
								id="records-add-submit"
								className="button-secondary"
								value="Annuler"
								onClick={() => this.props.onCancel()}
							>Annuler</Button>
							<Button
								id="records-back-submit"
								className="button-primary"
								value="Enregistrer"
								disabled={disableOk}
								onClick={() => { console.log(this.state) }}
							>Enregistrer</Button>
						</div>
					</div>
				</div>
			</div>
		)
	}
}
