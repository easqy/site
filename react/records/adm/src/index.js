
import { Component, render } from '@wordpress/element';
import { Button } from '@wordpress/components';

import Records from './records/records';
import Users from './users/users';
import './main.scss';

const ViewType = {
	admin: 0,
	users: 1,
	records: 2,
};
Object.freeze(ViewType);


class AdminView extends Component {
	render() {
		const {
			onViewChange
		} = this.props;

		return <>
			<h1 class="wp-heading-inline">Records - Administration</h1>
			<div className="easqy-button-bar">
				<div className={'admin-descriptif'}>
					<div className={'admin-descriptif-text'}>
						Donner ou retirer les droits d'édition des records aux membres
					</div>
					<Button
						isSecondary
						onClick={() => { onViewChange(ViewType.users) }}
					>Gérer les utilisateurs</Button>
				</div>
				<div className={'admin-descriptif'}>
					<div className={'admin-descriptif-text'}>
						Ajouter, modifier ou supprimer les records visibles sur <a href="/competitions/les-records-du-club/">la page des records du club</a>
					</div>
					<Button
						isSecondary
						onClick={() => { onViewChange(ViewType.records) }}
					>Gérer les records</Button>
				</div>
			</div>
		</>
	}
}

class RecordsAdministration extends Component {

	constructor(props) {
		super(props);
		this.state = {
			viewType: ViewType.admin
		}
	}

	render() {
		const Content = () => {

			switch (this.state.viewType) {

				case ViewType.admin:
					return <AdminView onViewChange={(v) => { this.setState({ viewType: v }) }} />

				case ViewType.records:
					return <Records />

				case ViewType.users:
					return <Users />

				default:
					return null;
			}
		}

		return <div className="wrap"><Content /></div>
	}
}




(function () {
	const eltUsers = document.getElementById('easqy-records-adm');
	if (eltUsers)
		render(<RecordsAdministration />, eltUsers);
	/*
	const elt = document.getElementById('easqy-records-adm');
	if (elt)
		render(<Records />, elt);
	*/
})();

