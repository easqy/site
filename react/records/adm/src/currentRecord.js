import { Component } from '@wordpress/element';
import { Button, Modal } from '@wordpress/components';


class DeleteButton extends Component {

	constructor(props) {
		super(props);
		this.state = {
			deleteModalConformOpen: false
		}
	}

	render() {
		return <>
			<Button
				isDestructive
				onClick={() => { this.setState({ deleteModalConformOpen: true }) }}
			>Supprimer</Button>
			{
				(this.state.deleteModalConformOpen) && <Modal
					title="Confirmation"
					onRequestClose={() => { this.setState({ deleteModalConformOpen: false }) }}
				>
					<div>Supprimer ce record ?</div>
					<div className='easqy-button-bar'>
						<Button isPrimary onClick={() => { this.setState({ deleteModalConformOpen: false }) }}>Annuler</Button>
						<Button isDestructive onClick={() => { if (this.props.doDel) this.props.doDel(); this.setState({ deleteModalConformOpen: false }) }}>Supprimer</Button>
					</div>
				</Modal>
			}
		</>
	}
}


export default class CurrentRecord extends Component {

	constructor(props) {
		super(props);
		this.state = {
		}
	}

	render() {
		const {
			record,
			ra,
			athletes,
			categories
		} = this.props;

		const key = record.i;
		const l = ra.find(ra => ra.r === record.i);
		const date = new Date(record.d)
		const a = athletes.filter(a => (a.i === l.a))

		return (
			<div key={key} id={'easqy-current-record'}>
				{ record && (<table><tbody>
					<tr><td className='label'>Date du record :</td><td className='value'>{date.toLocaleDateString('fr-FR', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</td></tr>
					<tr><td className='label'>Performance :</td><td className='value'>{record.p}</td></tr>
					<tr><td className='label'>Informations :</td><td className='value'>{record.f}</td></tr>
					<tr><td className='label'>Lieu :</td><td className='value'>{record.l}</td></tr>
					<tr><td className='label'>Athlete(s) :</td><td className='value'>
						{a.map(a => {
							return <div>
								{a.p}&nbsp;{a.n}
								{l.c && <span>&nbsp;({categories[l.c]})</span>}
							</div>
						})}
					</td></tr>
				</tbody></table>
				)}
				<div className='easqy-button-bar'>
					<DeleteButton doDel={() => { if (this.props.doDel) this.props.doDel() }} />
					<Button isPrimary>Modifier</Button>
					{/*
					<input type="submit" id="records-delete-submit" class="button-secondary is-destructive" value="Supprimer" />
					<input type="submit" id="records-modify-submit" class="button-primary" value="Modifier" />
					*/}
				</div>
			</div>
		)
	}
}
