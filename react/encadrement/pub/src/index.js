import {
	Component,
	render
} from '@wordpress/element';
import './main.scss';
import $ from "jquery";

class Dirigeants extends Component {
	constructor(props) {
		super(props);
		this.state = {
			loading: true,
		};
	};

	componentDidMount() {
		const me = this;
		$.ajax({
			url: easqy.ajaxurl,
			method: "GET",
			data: {
				action: "easqy_dirigeants",
				security: easqy.security
			},
			success: function (data) {
				if (data.data.status === 'ok') {
					me.dirigeants = data.data.dirigeants;
					me.setState({
						loading: false,
					});
				}
				else
					me.setState({ loading: false });
			},
			error: (data) => {
				console.log("error", data);
				me.setState({ loading: false });
			},
		});
	}

	render() {
		const { loading } = this.state;

		if (loading || (!this.dirigeants))
			return <></>;

		const getTopo = (t) => {
			return (t ? t : '').split("\n");
		};

		console.log(this.dirigeants);
		return <div className={'teams'}>
			{
				this.dirigeants.map(d => {
					return (
						<div className='member'>
						
							{ d.img ?
							(
								<div class="img-container">
									<img src={d.img} alt="" />
								</div>
							):(
								<svg xmlns="http://www.w3.org/2000/svg" width="300" height="300" viewBox="0 0 24 24">
									<path
										fill="rgba(0,0,0,0.5)"
										d="M18.75 17.4c-1.08-.36-3.6-1.35-3.6-1.35-.81-.27-.81-.99-.9-1.8v-.09c1.26-1.08 2.25-2.88 2.25-4.86
											0-4.23-1.8-5.85-4.5-5.85-1.89 0-4.5 1.08-4.5 5.85 0 1.89.99 3.69 2.25 4.86v.09c0 .81-.09 1.53-.9
											1.8 0 0-2.61.99-3.6 1.35-1.17.36-2.25.9-2.25 2.25v.9h18v-.9c0-1.08-.72-1.8-2.25-2.25z"
									/>
								</svg>								
							)
							}
						<div className='card'>
							<div className='name'>{d.prenom}&nbsp;{d.nom}</div>
							<div className='title'>{d.dirigeant_poste}</div>
							<div className='topo'>{getTopo(d.topo).map(line => <p>{line}</p>)}</div>
						</div>
					</div>
					)
				})
			}
		</div>;
	}
}

(function () {
	const elt = document.getElementById('easqy-dirigeants');
	if (elt)
		render(<Dirigeants />, elt);
})();
