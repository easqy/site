
import { Component, render } from '@wordpress/element';
import { Button } from '@wordpress/components';

import Records from './records';
import './main.scss';

const ViewType = {
	admin: 0,
	users: 1,
	records: 2,
};
Object.freeze(ViewType);


class RecordsAdminUsers extends Component {
	render() {
		return <div className="wrap">
			<h1 class="wp-heading-inline">Records</h1>
			TODO
			</div>
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
					return <div className="wrap">
						<h1 class="wp-heading-inline">Records</h1>
						<div className="easqy-button-bar">
							<Button
								isSecondary
								onClick={() => { this.setState({ viewType: ViewType.users }) }}
							>Gerer les utilisateurs</Button>
							<Button
								isSecondary
								onClick={() => { this.setState({ viewType: ViewType.records }) }}
							>Gerer les records</Button>
						</div>
					</div>

				case ViewType.records:
					return <Records />

				case ViewType.users:
					return <RecordsAdminUsers />

				default:
					return null;
			}
		}

		return <div className="wrap"><Content /></div>
	}
}




(function () {
	const eltUsers = document.getElementById('easqy-records-adm-users');
	if (eltUsers)
		render(<RecordsAdministration />, eltUsers);

	const elt = document.getElementById('easqy-records-adm');
	if (elt)
		render(<Records />, elt);
})();

