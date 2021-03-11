import { Component } from '@wordpress/element';
import { Button, TextControl, Modal, ComboboxControl, DateTimePicker, Popover, SelectControl } from '@wordpress/components';
import $ from "jquery";

const ViewType = {
	loading: 0,
	error: 1,
	list: 2,
	modify: 4
};
Object.freeze(ViewType);

export default class Users extends Component {
	constructor(props) {
		super(props);
		this.state = {
			viewType: ViewType.loading
		};
		this._k = 1;
	}

	componentDidMount() {
		this.loadUsers();
	}

	loadUsers() {

		this._k = this._k + 1;
		this.setState({ viewType: ViewType.loading });
		this._k = this._k + 1;

		const me = this;
		$.ajax({
			url: easqy_records_adm.ajaxurl,
			method: "GET",
			data: {
				action: "easqy_record_users",
				security: easqy_records_adm.security
			},
			success: function (data) {
				if (data.success) {
					me.users = data.data.users;
					me.setState({ viewType: ViewType.list })
				}
			},
			error: (data) => {
				console.log("error", data);
				me.setState({ viewType: ViewType.error });
			},
		});
	}

	onAdd(userId) {
		this.setState({ viewType: ViewType.loading });
		const me = this;
		$.ajax({
			url: easqy_records_adm.ajaxurl,
			method: "POST",
			data: {
				action: "easqy_record_user_add",
				security: easqy_records_adm.security,
				userId: userId
			},
			success: function (data) {
				//console.log(data);
				if (data.success) {
					me.loadUsers();
				}
			},
			error: (data) => {
				console.log("error", data);
				me.setState({ viewType: ViewType.error });
			},
		});
	}

	onRemove(userId) {
		this.setState({ viewType: ViewType.loading });
		const me = this;
		$.ajax({
			url: easqy_records_adm.ajaxurl,
			method: "POST",
			data: {
				action: "easqy_record_user_remove",
				security: easqy_records_adm.security,
				userId: userId
			},
			success: function (data) {
				if (data.success) {
					me.loadUsers();
				}
			},
			error: (data) => {
				console.log("error", data);
				me.setState({ viewType: ViewType.error });
			},
		});
	}

	render() {

		const {
			users
		} = this;

		const Admins = () => {
			const admins = users.filter(u => u.a);
			return <>

				{admins.map(a => (<tr><td>&nbsp;</td><td>{a.d}</td></tr>))}

			</>
		}
		const Others = () => {
			const others = users.filter(u => (!u.a) && (u.c));
			return <>
				{others.map(u => (<tr>
					<td>
						<Button isSmall isDestructive
							onClick={() => { this.onRemove(u.i) }}
						>X</Button>
					</td>
					<td>{u.d}</td>
				</tr>
				))}
			</>
		}

		return <div className="wrap">
			<h1 class="wp-heading-inline">Records - Gestion des utilisateurs</h1>
			{(this.state.viewType === ViewType.list) && (
				<div style={{ maxWidth: '30em' }}>
					<div>
						<ComboboxControl
							label="Ajouter"
							value={0}
							onFilterValueChange={(v) => {
								//console.log('onFilterValueChange', v)
							}}
							onChange={(userId) => {
								this.onAdd(userId)
								//console.log('onChange', userId)
							}}
							options={users.filter(u => {
								return (!((u.a) || (u.c)))
							}).map(u => { return { label: u.d, value: u.i } })}
						/>
					</div>
					<table className={'easqy-record-users-table'}>
						<Admins />
						<Others />
					</table>
				</div>
			)}
		</div>
	}

}
