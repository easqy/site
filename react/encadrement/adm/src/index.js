import {
	Component,
	render
} from '@wordpress/element';
import './main.scss';
import $ from "jquery";

class Vignette extends Component {
	constructor(props) {
		super(props);
		this.state = {
			loading: true,
		};
	};

	componentDidMount() {
		this.imgTag = '<div>No preview</div>';
		const me = this;
		if (me.props.imgId)
			$.ajax({
				url: easqy.ajaxurl,
				method: "GET",
				data: {
					action: "easqy_image_from_media_lib",
					security: easqy.security,
					id: me.props.imgId,
					size: [128, 128]
				}
			})
				.done((data) => {
					console.log('done', data)
					if (data.success)
						me.imgTag = data.data.image;
				})
				.fail((data) => {
					console.log("error", data);
				})
				.always((data) => {
					console.log('always', data)
					me.setState({ loading: false });
				});
		else
			me.setState({ loading: false });
	}

	render() {
		const { loading } = this.state;

		if (loading)
			return <div>Chargement ...</div>;

		return <div className="vignette" dangerouslySetInnerHTML={{ __html: this.imgTag }}></div>;
	}

}


class Trombinoscope extends Component {
	constructor(props) {
		super(props);
		this.state = {
			loading: true,
		};
		this.trombis = [];
	};

	componentDidMount() {
		const me = this;
		$.ajax({
			url: easqy.ajaxurl,
			method: "GET",
			data: {
				action: "easqy_adm_trombi",
				security: easqy.security,
				command: 'list_all'
			}
		})
			.done((data) => {
				console.log('done', data)
				if (data.success)
					me.trombis = data.data;
			})
			.fail((data) => {
				console.log("error", data);
			})
			.always((data) => {
				console.log('always', data)
				me.setState({ loading: false });
			});
	}

	render() {
		const { loading } = this.state;

		if (loading)
			return <div>Chargement ...</div>;

		// Create a new media frame
		const frame = wp.media({
			title: 'Choisir une image',
			button: {
				text: 'Utiliser cette image'
			},
			library: {
				type: ['image']
			},
			multiple: false  // Set to true to allow multiple files to be selected
		});
		frame.open();

		return <div className={'trombis'}>
			<table>
				{
					this.trombis.map(t => {
						return <tr>
							<td><Vignette imgId={t.photo_id} /></td>
							<td>{t.prenom}</td>
							<td>{t.nom}</td>
							<td>{t.topo}</td>
						</tr>;
					})
				}
			</table>
		</div>;
	}
}

(function () {
	const elt = document.getElementById('easqy-trombinoscope-adm');
	if (elt)
		render(<Trombinoscope />, elt);
})();
