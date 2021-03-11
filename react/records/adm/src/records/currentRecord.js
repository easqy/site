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
		const raFiltered = ra.filter(ra => ra.r === record.i);
		const date = new Date(record.d)

		const Athlete = ({ athleteId }) => {
			const found = athletes.find(a => a.i === athleteId);

			if (found) {
				return <span>{found.p}&nbsp;{found.n}</span>
			}
			else
				return <span></span>
		}

		const CatWhenPerf = ({ cat }) => {
			if (cat) { return <span>&nbsp;({categories[cat]})</span> }
			return <></>;
		}

		return (
			<div key={key} id={'easqy-current-record'}>
				{ record && (<table><tbody>
					<tr><td className='label'>Date du record :</td><td className='value'>{date.toLocaleDateString('fr-FR', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</td></tr>
					<tr><td className='label'>Performance :</td><td className='value'>{record.p}</td></tr>
					<tr><td className='label'>Informations :</td><td className='value'>{record.f}</td></tr>
					<tr><td className='label'>Lieu :</td><td className='value'>{record.l}</td></tr>
					<tr><td className='label'>Athlete(s) :</td><td className='value'>
						{raFiltered.map(ra => {
							return <div>
								<Athlete athleteId={ra.a} />
								<CatWhenPerf cat={ra.c} />
							</div>
						})}
					</td></tr>
				</tbody></table>
				)}
				<div className='easqy-button-bar'>
					<DeleteButton doDel={() => { if (this.props.doDel) this.props.doDel() }} />
					<Button
						onClick={() => {
							this.props.doModify && this.props.doModify(record)
						}}
						isPrimary
					>
						Modifier
					</Button>
				</div>
			</div>
		)
	}
}
